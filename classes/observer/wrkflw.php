<?php
namespace Locomo;
class Observer_Wrkflw extends \Orm\Observer
{
	// vals
	protected static $route_id;
	protected static $current_step;
	protected static $total_step;
	protected static $logs;
	protected static $workflow;

	/**
	 * __construct
	 */
	public function __construct($class)
	{
	}

	/**
	 * progress
	 */
	public static function progress($response)
	{
		// すべてのステップを取得
		$q = \DB::select('*');
		$q->from('lcm_wrkflw_steps');
		$q->where('workflow_id', static::$route_id);
		$q->order_by('seq', 'ASC');
		$steps = $q->execute()->as_array();

		// 一番最初のログは絶対に承認申請なのでスキップ
		unset(static::$logs[0]);

		// CSS
		$html = '';
		$html = "
		<style type=\"text/css\">
		table.stamp_area {
			float: right;
			margin: 5px 0 10px ;
			-webkit-box-sizing : border-box ;
			-moz-box-sizing : border-box ;
			box-sizing : border-box ;
			border-spacing: 0px;
			border-top: 1px #bbb solid;
			border-left: 1px #bbb solid;
		}
		table.stamp_area th,
		table.stamp_area td
		{
			width: 6em;
			overflow: hidden;
			white-space: nowrap;
			text-align: center;
			padding: 8px 5px;
			border-bottom: 1px #bbb solid;
			border-right: 1px #bbb solid;
		}
		table.stamp_area thead th,
		table.stamp_area .thead th {
			background-color: #f0f0f0;
			color: #111;
			text-align: center;
		}
		table.stamp_area td
		{
			height: 5em;
		}
		table.stamp_area * {
			margin: 0 auto;
		}
		</style>
		";

		$controller = \Inflector::add_head_backslash(\Request::active()->controller);

		// 承認のhtmlを生成
		$thead = '<thead><tr>';
		$tbody = '<tr>';
		foreach ($steps as $step)
		{
			$seq = $step['seq'];
			$thead.= '<th tabindex="0">'.mb_substr($step['name'],0,5).'<span class="skip">';
			if (isset(static::$logs[$seq]))
			{
				$thead.= '承認済み '.date('Y年n月j日', strtotime(static::$logs[$seq]->created_at)); 
				$tbody.= '<td><div style="border:1px red solid;border-radius: 50%;width: 5em;height: 5em;text-align:center; overflow: hidden;white-space: nowrap;color: red;font-family: \'YuMincho\'; line-height: 1.4;padding-top:.75em;padding-bottom:.5em;box-sizing: border-box;font-size:.9em"><div style="border-bottom:1px red solid;font-size: .8em;">'.$step['name'].'</div><div style="border-bottom:1px red solid;font-size:.8em;">'.\Model_Usr::get_display_name(static::$logs[$seq]->did_user_id).'</div>'.'<div style="font-size: 0.8em">'.date('y.m.d', strtotime(static::$logs[$seq]->created_at)).'</div></div></td>';
			} else {
				$thead.= '未承認';
				$tbody.= '<td></td>';
			}
			$thead.= '</span></th>';
		}
		$html.= '<table class="stamp_area">'.$thead.'</tr></thead>'.$tbody.'</tr></table>';
		return str_replace('<div class="contents">', '<div class="contents">'.$html, $response);
	}

	/**
	 * after_load()
	 */
	public function after_load(\Orm\Model $obj)
	{
		if (in_array(\Request::active()->action, ['view','edit']))
		{
			$controller = \Request::active()->controller;
			if ($obj->workflow_status == 'in_progress' && $obj->id == \Request::active()->id)
			{
				// vals
				$model = \Inflector::maybe_ctrl_to_model($controller);
				static::$route_id     = $model::get_route($controller, $obj->id);
				static::$logs         = $model::get_strait_route_logs($controller, $obj->id);
				static::$current_step = $model::get_current_step($controller, $obj->id);
				static::$total_step   = $model::get_total_step(static::$route_id);
				static::$workflow     = \Model_Wrkflwadmin::find(static::$route_id);
				$workflow = static::$workflow;
				$total_step = static::$total_step;
				$current_step = static::$current_step;

				// event
				\Event::register('locomo_after', '\Locomo\Observer_Wrkflw::progress');

				// set message
				if (static::$route_id)
				{
					\Session::set_flash('success', "承認進行中の項目です。（{$workflow->name}ルート {$total_step}段階中の{$current_step}）");
				} else {
					\Session::set_flash('error', "承認ルートを見失いました。経路設定からやり直してください。");
				}
			}

			// ルート設定してください
			if ($obj->workflow_status == 'before_progress' && $obj->id == \Request::active()->id)
			{
				$url = \Inflector::ctrl_to_dir(\Request::active()->controller);
				$id  = \Request::active()->id;
				\Session::set_flash('message', [['ルート設定してください。', \Uri::create($url.'/route/'.$id)]]);
			}

			// 承認申請してください
			if ($obj->workflow_status == 'init' && $obj->id == \Request::active()->id)
			{
				$url = \Inflector::ctrl_to_dir(\Request::active()->controller);
				$id  = \Request::active()->id;
				\Session::set_flash('message', [['承認申請してください。', \Uri::create($url.'/apply/'.$id)]]);
			}
		}
	}

	/**
	 * before_insert()
	 */
	public function before_insert(\Orm\Model $obj)
	{
		//ワークフロー管理下のコンテンツのworkflow_statusはbefore_progressで作成される
		$obj->workflow_status = 'before_progress';
	}

	/**
	 * before_save()
	 */
	public function before_save(\Orm\Model $obj)
	{
	}
}
