<?php
namespace Scaffold;
class Controller_Scaffold extends \Kontiki\Controller
{
	/**
	* @var string name for human
	*/
	public static $nicename = '足場組み';

	/**
	* @var array $_default_constraints
	*/
	private static $_default_constraints = array(
		'varchar' => 255,
		'char'    => 255,
		'int'     => 11
	);

	/**
	 * set_actionset()
	 */
	public function set_actionset()
	{
		parent::set_actionset();
		self::$actionset = array();
		self::$actionset_owner = array();
	}

	/**
	 * action_main()
	 * 
	 */
	public function action_main()
	{
		//only at development
		if(\Fuel::$env != 'development') die();

		//view
		$view = \View::forge('main');

		//scaffold
		if(\Input::method() == 'POST'):
//			if( ! \Security::check_token()) die();
			$cmd = \Input::post('cmd');
			$cmd = str_replace(array('php oil g '), '', $cmd);
			$cmds = explode(' ', $cmd);

			//migration
			$type       = array_shift($cmds);
			$name       = array_shift($cmds);
			$table_name = \Inflector::pluralize($name);
			$subjects   = array($table_name, $table_name);

			//molding - logic
			$migration  = self::migration($name, $subjects, $cmds);
			$controller = self::generate_controller($name);
			$model      = self::generate_model($name, $cmds);
			$viewmodel  = self::generate_view($name);

			//molding - view
			$tpl_index       = self::generate_views_index($name);
			$tpl_view        = self::generate_views_view($name, $cmds);
			$tpl_form        = self::generate_views_form($name, $cmds);
			$tpl_create      = self::generate_views_create($name);
			$tpl_edit        = self::generate_views_edit($name);

			//mkdir
			$scfldpath = PKGPATH.'kontiki/modules/'.$name;
			if( ! file_exists($scfldpath)) mkdir($scfldpath);

			//put files
			$name = strtolower($name);
			if( ! file_exists($scfldpath.'/migrations')) mkdir($scfldpath.'/migrations');
			self::putfiles($scfldpath.'/migrations/'.$name.'.php', $migration) ;
			if( ! file_exists($scfldpath.'/classes')) mkdir($scfldpath.'/classes');
			if( ! file_exists($scfldpath.'/classes/controller')) mkdir($scfldpath.'/classes/controller');
			self::putfiles($scfldpath.'/classes/controller/'.$name.'.php', $controller) ;
			if( ! file_exists($scfldpath.'/classes/model')) mkdir($scfldpath.'/classes/model');
			self::putfiles($scfldpath.'/classes/model/'.$name.'.php', $model) ;
			if( ! file_exists($scfldpath.'/classes/view')) mkdir($scfldpath.'/classes/view');
			self::putfiles($scfldpath.'/classes/view/'.$name.'.php', $viewmodel) ;
			if( ! file_exists($scfldpath.'/views')) mkdir($scfldpath.'/views');
			self::putfiles($scfldpath.'/views/index.html', $tpl_index) ;
			self::putfiles($scfldpath.'/views/index_admin.html', $tpl_index) ;
			self::putfiles($scfldpath.'/views/view.html', $tpl_view) ;
			self::putfiles($scfldpath.'/views/_form.html', $tpl_form) ;
			self::putfiles($scfldpath.'/views/create.html', $tpl_create) ;
			self::putfiles($scfldpath.'/views/edit.html', $tpl_edit) ;

			$explanation = <<<TXT
<p class="cmt">モジュールの基礎を生成しました。<br />
migrationファイルを適宜編集後、適切な場所に移し、<br />
php oil refine migrate:up --packages=kontiki<br />
で、refineしてください。</p>
TXT;
			$view->set('explanation', $explanation, false);
		endif;

		//view
		$view->set_global('title', '足場組み');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));

/*
		//redirect
		$redirect_decode = $redirect ? base64_decode($redirect) : \URI::base() ;


		//view
		$view = \View::forge('login');
		$view->set('ret', $redirect);
		$view->set_global('title', 'ログイン');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
*/
	}

	/**
	 * migration()
	 * /packages/oil/classes/generate.php から移設
	 */
	public function migration($name, $subjects, $cmds)
	{
		// We always pass in fields to a migration, so lets sort them out here.
		$fields = array();
		foreach ($cmds as $field)
		{
			$field_array = array();
	
			// Each paramater for a field is seperated by the : character
			$parts = explode(":", $field);
	
			// We must have the 'name:type' if nothing else!
			if (count($parts) >= 2)
			{
				$field_array['name'] = array_shift($parts);
				foreach ($parts as $part_i => $part)
				{
					preg_match('/([a-z0-9_-]+)(?:\[([0-9a-z_\-\,\s]+)\])?/i', $part, $part_matches);
					array_shift($part_matches);
	
					if (count($part_matches) < 1)
					{
						// Move onto the next part, something is wrong here...
						continue;
					}
	
					$option_name = ''; // This is the name of the option to be passed to the action in a field
					$option = $part_matches;
	
					// The first option always has to be the field type
					if ($part_i == 0)
					{
						$option_name = 'type';
						$type = $option[0];
						if ($type === 'string')
						{
							$type = 'varchar';
						}
						else if ($type === 'integer')
						{
							$type = 'int';
						}
	
						if ( ! in_array($type, array('text', 'blob', 'datetime', 'date', 'timestamp', 'time')))
						{
							if ( ! isset($option[1]) || $option[1] == NULL)
							{
								if (isset(self::$_default_constraints[$type]))
								{
									$field_array['constraint'] = self::$_default_constraints[$type];
								}
							}
							else
							{
								// should support field_name:enum[value1,value2]
								if ($type === 'enum')
								{
									$values = explode(',', $option[1]);
									$option[1] = '"'.implode('","', $values).'"';
	
									$field_array['constraint'] = $option[1];
								}
								// should support field_name:decimal[10,2]
								elseif (in_array($type, array('decimal', 'float')))
								{
									$field_array['constraint'] = $option[1];
								}
								else
								{
									$field_array['constraint'] = (int) $option[1];
								}
	
							}
						}
						$option = $type;
					}
					else
					{
						// This allows you to put any number of :option or :option[val] into your field and these will...
						// ... always be passed through to the action making it really easy to add extra options for a field
						$option_name = array_shift($option);
						if (count($option) > 0)
						{
							$option = $option[0];
						}
						else
						{
							$option = true;
						}
					}
	
					// deal with some special cases
					switch ($option_name)
					{
						case 'auto_increment':
						case 'null':
						case 'unsigned':
							$option = (bool) $option;
							break;
					}
	
					$field_array[$option_name] = $option;
	
				}
				$fields[] = $field_array;
			}
			else
			{
				// Invalid field passed in
				continue;
			}
		}
		require(PKGPATH.'oil/classes/generate/migration/actions.php');
		$migration = call_user_func("\Oil\Generate_Migration_Actions::create", $subjects, $fields);
		list($up, $down) = $migration;
		$migration_name = ucfirst(strtolower($name));

		$migration = <<<MIGRATION
<?php
namespace Fuel\Migrations;
class {$migration_name}
{
	public function up()
	{
{$up}
	}

	public function down()
	{
{$down}
	}
}
MIGRATION;
		return $migration;
	}

	/**
	 * generate_controller()
	 */
	public function generate_controller($name)
	{
		$name = ucfirst($name);
		$str = <<<FILES
<?php
namespace {$name};
class Controller_{$name} extends \Kontiki\Controller
{
	/**
	* @var string name for human
	*/
	public static \$nicename = '{$name}';
}
FILES;
		return $str;
	}

	/**
	 * generate_model()
	 */
	public function generate_model($name, $cmds)
	{
		$name = ucfirst($name);
		$field_str = '';
		foreach($cmds as $field):
			list($field, $attr) = explode(':', $field);
			$field_str.= "\t\t'".$field."',\n";
		endforeach;

		$str = <<<FILES
<?php
namespace {$name};
class Model_{$name} extends \Kontiki\Model
{
	protected static \$_table_name = '{$name}';

	protected static \$_properties = array(
{$field_str}	);
}
FILES;
		return $str;
	}

	/**
	 * generate_view()
	 */
	public function generate_view($name)
	{
		$name = ucfirst($name);
		$str = <<<FILES
<?php
namespace {$name};
class View_{$name} extends \Kontiki\ViewModel
{
}
FILES;
		return $str;
	}

	/**
	 * generate_views_index()
	 */
	private static function generate_views_index($name)
	{
		//mold - read.html
		$val = file_get_contents(PKGPATH.'kontiki/modules/scaffold/classes/views/index.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_views_view()
	 */
	private static function generate_views_view($name, $cmds)
	{
		$fields = '' ;

		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			$fields.= '<tr>'."\n" ;
			$fields.= "\t<th>".$field."</th>\n" ;
			if(substr($field,0,3)=='is_'){
				$fields.= "\t".'<td><?php echo $item->'.$field.'; ?>Yes<?php else: ?>No<?php endif; ?></td>'."\n";
			}else{
				$fields.= "\t<td>".'<?php echo $item->'.$field.'; ?>'."</td>\n" ;
			}
			$fields.= '</tr>'."\n\n" ;
		}
		
		//mold - read.html
		$val = file_get_contents(PKGPATH.'kontiki/modules/scaffold/classes/views/view.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###fields###', $fields , $val) ;
	
		return $val;
	}

	/**
	 * generate_views_form()
	 */
	private static function generate_views_form($name, $cmds)
	{
		$fields = '' ;
		foreach($cmds as $field){
			list($field, $attr) = explode(':', $field);
			$fields.= '<tr>'."\n" ;
			//label
			$fields.= "\t<th><?php echo \Form::label('{$field}', '{$field}', array('class'=>'control-label')); ?></th>\n" ;
			
			//field
			if(substr($field,0,4)=='name'){//textarea - recently name tend to be multi line.
				$fields.= "\t".'<td><textarea class="subject" id="'.$field.'" name="'.$field.'"><{$vals.'.$field.'|escape}></textarea></td>'."\n" ;
			}elseif(substr($field,0,3)=='is_'){//checkbox
				$fields.= "\t\$checked = Input::post('{$field}') ? ' checked=\"checked\" : null;'\n";
				$fields.= "\t<td><label><?php echo \Form::checkbox(\"{$field}\", 1, array('class' => '', \$checked)).{$field}.' ?></label></td>\n";
			}else{//input
				$fields.= "\t<td><?php echo \Form::input('{$field}', Input::post('{$field}', isset(\$item) ? \$item->{$field} : ''), array('class' => 'col-md-4 form-control', 'placeholder' => '{$field}')); ?></td>\n" ;
			}
			$fields.= '</tr>'."\n\n" ;
		}
		
		//mold - read.html
		$val = file_get_contents(PKGPATH.'kontiki/modules/scaffold/classes/views/_form.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###fields###', $fields , $val) ;

		return $val;
	}

	/**
	 * generate_views_create()
	 */
	private static function generate_views_create($name)
	{
		//mold - read.html
		$val = file_get_contents(PKGPATH.'kontiki/modules/scaffold/classes/views/create.php');
		$val = self::replaces($name, $val);
		return $val;
	}

	/**
	 * generate_views_edit()
	 */
	private static function generate_views_edit($name)
	{
		//mold - read.html
		$val = file_get_contents(PKGPATH.'kontiki/modules/scaffold/classes/views/edit.php');
		$val = self::replaces($name, $val);
		return $val;
	}


	//===replaces===
	private static function replaces($name,$tpl)
	{
		$tpl = str_replace ('XXX', ucfirst($name) , $tpl);
		$tpl = str_replace ('xxx', strtolower($name) , $tpl);
		$tpl = str_replace ('YYY', $name , $tpl);
		return $tpl;
	}

	//===putfiles===
	private static function putfiles($path, $val)
	{
		touch($path) ;
		$fp = fopen($path, 'w');
		fwrite($fp, "<?php\n".$val);
//			fwrite($fp, pack('C*',0xEF,0xBB,0xBF));//BOM -> php is unacceptable BOM
		fclose($fp) ;
	}
}
