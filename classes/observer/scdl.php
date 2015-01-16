<?php
namespace Locomo;
class Observer_Scdl extends \Orm\Observer
{
	/**
	 * __construct
	 */
	public function __construct($class)
	{
	}


	/**
	 * before_insert()
	 */
	public function before_insert(\Orm\Model $obj)
	{
		// checkbox値
		$columns = array('provisional_kb', 'unspecified_kb', 'allday_kb', 'private_kb', 'overlap_kb', 'attend_flg');
		foreach ($columns as $v) {
			if (!\Input::post($v)) {
				$obj->__set($v, 0);
			}
		}
	}

	/**
	 * before_save()
	 */
	public function before_save(\Orm\Model $obj)
	{
		// checkbox値
		$columns = array('provisional_kb', 'unspecified_kb', 'allday_kb', 'private_kb', 'overlap_kb', 'attend_flg');
		foreach ($columns as $v) {
			if (!\Input::post($v)) {
				$obj->__set($v, 0);
			}
		}
	}
	public function after_save(\Orm\Model $obj)
	{
		// schedule_membersへの登録
		$members = explode("/", \Input::post("hidden_members"));
//		$schedule_members = Model_Schedule_Members::find('all', array(
//		    'where' => array(
//		        array('schedule_id', $obj->__get('id')),
//		    ),
//		));
//		$schedule_members->purge();
		\DB::delete("lcm_scdls_members")->where("schedule_id", $obj->__get('id'))->execute();

		foreach ($members as $v) {
			if (!$v) { continue; }
			$schedule_members = Model_Scdl_Member::forge();
			$schedule_members->schedule_id = $obj->__get('id');
			$schedule_members->user_id = $v;
			$schedule_members->save();
		}
		// schedule_building
		$buildings = explode("/", \Input::post("hidden_buildings"));
//		$schedule_buildings = Model_Schedule_Buildings::find('all', array(
//		    'where' => array(
//		        array('schedule_id', $obj->__get('id')),
//		    ),
//		));
//		$schedule_buildings->purge();
		\DB::delete("lcm_scdls_buildings")->where("schedule_id", $obj->__get('id'))->execute();
		
		foreach ($buildings as $v) {
			if (!$v) { continue; }
			$schedule_buildings = Model_Scdl_Building::forge();
			$schedule_buildings->schedule_id = $obj->__get('id');
			$schedule_buildings->building_id = $v;
			$schedule_buildings->save();
		}

	}
}
