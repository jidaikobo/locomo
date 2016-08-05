<?php
/*
 * Base のコントローラー 直接は呼ばない
 */
namespace Locomo;
class Controller_Frmt extends \Controller_Base
{

	public $model_name = '\Locomo\Model_Frmt';
	public $table_model_name = false; // '\Locomo\Model_Frmt_Table';

	public $output_url;

	/**
	 * pdf_edit()
	 */
	protected function pdf_edit($id = null)
	{
		$model = $this->model_name;

		\Asset::css('frmt/pdf/edit.css', array(), 'css');
		\Asset::js('frmt/pdf/edit.js', array(), 'js');
		$this->_content_template = $this->_content_template ?: 'frmt/pdf/edit';
		$obj = parent::edit($id, false);

		// アップロードの保存
		if (\Input::post() && $obj)
		{
			$dir = $model::$_upload_path;
			$id = $obj->id;

			if ( ! \Input::file()) return null;

			$errors = array();
			if ( ! is_dir(LOCOMOUPLOADPATH.DS.$dir))
			{
				mkdir(LOCOMOUPLOADPATH.DS.$dir, 0777);
			}
			$dir = \Inflector::add_tailing_slash($dir);
			$upload_path = LOCOMOUPLOADPATH.DS.$dir.$id;
			$save_path = 'uploads'.DS.$dir.$id.DS;

			if ( ! is_dir($upload_path))
			{
				mkdir($upload_path, 0777);
			}
			$config = array(
				'path' => $upload_path,
				'auto_rename' => false,
				'overwrite' => true,
			);
			\Upload::process($config);
			\Upload::register('before', function (&$file){$file['filename'] = urlencode($file['filename']);});

			// upload
			$files = \Upload::get_files();
			\Upload::save($upload_path, array_keys($files));

			$files = \Upload::get_files(); // save_as を取る
			if ($files)
			{
				foreach ($files as $file)
				{
					$file = $file['saved_as'];
					if( ! in_array(substr(strtolower($file), -4, 4), array('.jpg','jpeg','.gif','.png'))) continue;
					if(in_array(substr(strtolower($file), -7, 7), array('_lg.jpg','_sm.jpg','_tn.jpg'))) continue;

					$img_path = $upload_path.DS.$file;
					$img_file = \Image::load($img_path);
					$exif = @exif_read_data( $img_path);
					if (isset($exif['Orientation']))
					{
						switch ($exif['Orientation'])
						{
						case 3: // 180
							$img_file->rotate(180);
							$img_file->save($img_path);
							break;
						case 6: // 時計回りに90
							$img_file->rotate(90);
							$img_file->save($img_path);
							break;
						case 8: // 半時計回りに90
							$img_file->rotate(-90);
							$img_file->save($img_path);
							break;
						}
					}

					$sizes = \Image::sizes($img_path);
					// thumbnail
					$img_file
						->crop_resize(400, 400)
						->config('bgcolor', '#ffffff')
						->save_pa('', '_tn', 'jpg');
				}
			}
			// -> file save

			// unlink
			$unlinks = \Input::post('unlink'); // TODO 汎用化が進んだら消す
			$results = array();
			if (! is_array($unlinks) ) $unlinks = array($unlinks);
			if ($unlinks)
			{
				foreach ($unlinks as $path)
				{
					if ( ! is_file($path) and ! is_link($path)) continue;

					\File::delete($path);
					// 自動生成される画像の削除
					foreach(array('_lg.jpg','_sm.jpg','_tn.jpg') as $suffix)
					{
						$ext = substr($path, strrpos($path, '.'));
						$pathtemp = str_replace(substr($path, strrpos($path, '.')), $suffix, $path);
						if (file_exists($pathtemp)) \File::delete($pathtemp);
						// $results['failed'][] = $pathtemp; // 削除に失敗したものを保存予定
						$results['deleted'][] = $pathtemp;
					}
				}
			}
			// -> unlinks

			\Response::redirect(static::$base_url.'pdf_edit/'.$obj->id);
		}
	}

	/**
	 * pdf_edit_element()
	 */
	protected function pdf_edit_element($id)
	{
		$model = $this->model_name;

		$table_model = $this->table_model_name ?: $this->model_name.'_Table';

		\Asset::css('frmt/pdf/edit/element.css', array(), 'css');
		\Asset::js('frmt/pdf/edit/element.js', array(), 'js');

		$item = $model::find($id, $model::$_options);

		if (!$item)
		{
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

		if (\Input::post())
		{
			// element 既存列
			foreach ($item->element as $k => $v)
			{
				if (! \input::post('element.'.$k))
				{
					unset($item->element[$k]);
				}
				else
				{
					if ($item->element[$k])
					{
						$item->element[$k]->set(\input::post('element.'.$k));
					}
				}
			}
			// element 新規列
			if (\Input::post('element_new'))
			{
				foreach (\Input::post('element_new') as $k => $v)
				{
					$new_element = \Locomo\Model_Frmt_Element::forge($v);
					$new_element->form_key = $k;
					$item->element[] = $new_element;
				}
			}

			if ($item->save())
			{
				\Session::set_flash('success', '保存しました');
				\Response::redirect(\Uri::current());
			}
			else 
			{
				// var_dump(\Input::post()); die();
			}
		}
		$content = \Presenter::forge($this->_content_template ?: 'frmt/pdf/edit/element');
		$content->get_view()->set('item', $item);
		if ($item->is_multiple)
		{
			$content->get_view()->set('print_width', $item->cell_w);
			$content->get_view()->set('print_height', $item->cell_h);
		}
		else
		{
			$content->get_view()->set('print_width', $item->w);
			$content->get_view()->set('print_height', $item->h);
		}

		$content->get_view()->set('model_properties', $model::$_format_pdf_fields);

		// 挿入可能な image をセット
		$images = \File::get_attached_files($model::$_upload_path, false, array('image'));
		$content->get_view()->set('image_properties', $images);

		// 挿入可能なテーブルをセット
		$relation_tables = $table_model::find('all'); // TODO where 句
		$content->get_view()->set('relation_properties', $relation_tables);

		$this->template->set_safe('content', $content);
		$this->template->set_global('title', $item->name . ' 要素編集');
	}


	/**
	 * for @Override
	 * table_index
	 */
	protected function table_index()
	{
		$this->model_name = $this->table_model_name ?: $this->model_name.'_Table';
		$this->_content_template = $this->_content_template ?: static::$dir.'table_index';

		parent::index_admin();
	}

	/**
	 * table_edit()
	 */
	protected function table_edit($id)
	{
		$this->model_name = $this->table_model_name ?: $this->model_name.'_Table';

		$this->_content_template = $this->_content_template ?: 'frmt/table/edit';

		$obj = static::edit($id, false);

		if (!$obj)
		{
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

		if (\Input::post() && $obj)
		{
			if (\Input::post('submit_to_element'))
			{
				\Response::redirect(static::$base_url.'table_edit_element/'.$obj->id);
			}
			else
			{
				\Response::redirect(static::$base_url.'table_edit/'.$obj->id);
			}
		}
	}

	/**
	 * table_edit_element
	 */
	protected function table_edit_element($id = null)
	{
		\Asset::css('frmt/table/edit.css', array(), 'css');
		\Asset::js('frmt/table/edit.js', array(), 'js');

		$model = $this->table_model_name ?: $this->model_name.'_Table';

		$item = $model::find($id, $model::$_options);

		if (!$item)
		{
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

		if (\Input::post())
		{
			// element 既存列
			foreach ($item->element as $k => $v)
			{
				if (! \input::post('element.'.$k))
				{
					unset($item->element[$k]);
				}
				else
				{
					if ($item->element[$k])
					{
						$item->element[$k]->set(\input::post('element.'.$k));
					}
				}
			}
			// element 新規列
			if (\Input::post('element_new'))
			{
				foreach (\Input::post('element_new') as $k => $v)
				{
					$new_element = \Locomo\Model_Frmt_Table_Element::forge($v);
					$new_element->form_key = $k;
					$item->element[] = $new_element;
				}
			}

			if ($item->save())
			{
				\Session::set_flash('success', '保存しました');
				\Response::redirect(\Uri::current());
			}
			else 
			{
				// var_dump(\Input::post()); die();
			}
		}

		$content = \Presenter::forge($this->_content_template ?: 'frmt/table/edit/element');
		$content->get_view()->set('item', $item);



		$content->get_view()->set('model_properties', $model::$_format_table_fields[$item->relation]['fields']);
		$this->template->set_safe('content', $content);
		$this->template->set_global('title', $item->name . ' テーブル要素編集');
	}







	/*
	 * excel_edit
	 */
	protected function excel_edit($id = null)
	{
		$model = $this->model_name;

		$this->_content_template = $this->_content_template ?: 'frmt/excel/edit';

		$obj = static::edit($id, false);

		if (!$obj)
		{
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

		if (\Input::post() && $obj)
		{
			if (\Input::post('submit_to_element'))
			{
				\Response::redirect(static::$base_url.'excel_edit_element/'.$obj->id);
			}
			else
			{
				\Response::redirect(static::$base_url.'excel_edit/'.$obj->id);
			}
		}
	}

	/*
	 * edit_excel_element
	 */
	public function edit_excel_element($id)
	{
		$model = $this->model_name;

		\Asset::css('frmt/excel/edit/element.css', array(), 'css');
		\Asset::js('frmt/excel/edit/element.js', array(), 'js');

		$item = $model::find($id, $model::$_options);

		if (!$item)
		{
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

		if (\Input::post())
		{
			// element 既存列
			foreach ($item->element as $k => $v)
			{
				if (! \input::post('element.'.$k))
				{
					unset($item->element[$k]);
				}
				else
				{
					if ($item->element[$k])
					{
						$item->element[$k]->set(\input::post('element.'.$k));
					}
				}
			}
			// element 新規列
			if (\Input::post('element_new'))
			{
				foreach (\Input::post('element_new') as $k => $v)
				{
					$new_element = \Locomo\Model_Frmt_Element::forge($v);
					$new_element->form_key = $k;
					$item->element[] = $new_element;
				}
			}

			if ($item->save())
			{
				\Session::set_flash('success', '保存しました');
				\Response::redirect(\Uri::current());
			}
			else 
			{
				// var_dump(\Input::post()); die();
			}
		}

		$content = \Presenter::forge($this->_content_template ?: 'frmt/excel/edit/element');
		$content->get_view()->set('item', $item);

		$content->get_view()->set('model_properties', $model::$_format_excel_fields);
		$this->template->set_safe('content', $content);
		$this->template->set_global('title', $item->name . ' 要素編集');
	}

	/**
	 * template
	 */
	protected function template($template)
	{
	}


	/*
	 * action_copy()
	 */
	public function copy($id = null)
	{
		$model = $this->model_name;
		$parent = $model::find($id);

		if (! $parent)
		{
			\Session::set_flash('success', '不正なリクエストです。');
			\Response::redirect(\Input::referrer());
		}

		$copy = $model::forge($parent->to_array());
		unset($copy->id);
		$copy->name = $parent->name.'の複製';
		$copy->is_draft = true;
		$copy->deleted_at = null;
		if ($copy->save())
		{
			$element_model = $model::relations('element')->model_to;
			foreach ($parent->element as $element)
			{
				$elm_cpy = $element_model::forge($element->to_array());
				$elm_cpy->format_id = $copy->id;
				unset($elm_cpy->id);

				/**
				// タイプがテーブル
				if ($elm_cpy->type == 'table')
				{
					$origin_table_id = str_replace('id=', '', str_replace('{TABLE id="', '', str_replace('"}', '', $elm_cpy->txt)));
					$elm_cpy->txt  = copyTable($origin_table_id);
				}
				// タイプが image
				if ($elm_cpy->type == 'image')
				{
					$path = str_replace('{IMAGE path="', '', str_replace('"}', '', $format['txt']));
					$elm_cpy->txt  = copyImage($path);
				}
				 */

				$copy->element[] = $elm_cpy;
			}
			if ($copy->save())
			{
				\Session::set_flash('success', 'ID: '.$copy->id.'に、フォーマットID: '.$parent->id.' '.$parent->name.'の複製を作成しました。(使用する際は"下書き"から"使用中"に変更して下さい)');
				\Response::redirect(\Input::referrer());
			}
		}
		\Session::set_flash('error', '作成に失敗しました。もう一度やり直して下さい。');
		\Response::redirect(\Input::referrer());
	}

	/**
	 *
	 */
	protected function copyTable()
	{
	}

	protected function copyImage($path)
	{
		$file = APPPATH.'locomo'.DS.$path;
		$model::$_upload_path;
	}

	/* ==========
	 * actions
	========== */

	/*
	 * action_index_admin
	 */
	public function action_index_admin()
	{
		if (!$this->_content_template) $this->_content_template = 'frmt/index_admin';

		parent::index_admin();

		if ($this->output_url) $this->template->content->output_url = \Uri::create($this->output_url);
	}


	/*
	 * action_index_deleted
	 */
	public function action_index_deleted()
	{
		if (!$this->_content_template) $this->_content_template = 'frmt/index_admin';
		parent::index_deleted();
		$this->template->content->output_url = false;
	}


	/**
	 * action_table_index()
	 */
	public function action_table_index()
	{
		if (!$this->_content_template) $this->_content_template = 'frmt/table_index';
		static::table_index();
		if ($this->output_url) $this->template->content->output_url = \Uri::create($this->output_url);
	}

	/**
	 * action_table_create()
	 */
	public function action_table_create($id = null)
	{
		static::table_edit($id);
	}

	/**
	 * action_table_edit()
	 */
	public function action_table_edit($id = null)
	{
		static::table_edit($id);
	}

	/**
	 * action_table_edit_element()
	 */
	public function action_table_edit_element($id = null)
	{
		static::table_edit_element($id);
	}

	/*
	 * action_delete()
	 * @param Integer
	 */
	public function action_delete($id = null)
	{
		parent::delete($id);
	}

	/*
	 * action_undelete()
	 * @param Integer
	 */
	public function action_undelete($id = null)
	{
		parent::undelete($id);
	}

	/**
	 * action_pdf_create()
	 */
	public function action_pdf_create($id = null)
	{
		static::pdf_edit($id);
	}

	/**
	 * action_pdf_edit()
	 */
	public function action_pdf_edit($id = null)
	{
		static::pdf_edit($id);
	}

	/**
	 * action_pdf_edit()
	 */
	public function action_pdf_edit_element($id = null)
	{
		static::pdf_edit_element($id);
	}

	/**
	 * action_excel_create()
	 */
	public function action_excel_create($id = null)
	{
		$obj = static::excel_edit($id);
	}

	/**
	 * action_excel_edit()
	 */
	public function action_excel_edit($id = null)
	{
		$obj = static::excel_edit($id);
	}

	/**
	 * action_excel_edit_element()
	 */
	public function action_excel_edit_element($id = null)
	{
		static::edit_excel_element($id);
	}

	/*
	 * action_copy()
	 */
	public function action_copy($id = null)
	{
		static::copy($id);
	}
}


