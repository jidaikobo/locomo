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

	public function after_delete(\Orm\Model $obj) {
		\DB::delete("lcm_scdls_members")->where("schedule_id", $obj->__get('id'))->execute();
		\DB::delete("lcm_scdls_buildings")->where("schedule_id", $obj->__get('id'))->execute();
	}

	public function after_save(\Orm\Model $obj)
	{
		// start_dateがない場合は本登録処理などの違う処理
		if (\Input::post("start_date")) {
			// schedule_membersへの登録
			$members = explode("/", \Input::post("hidden_members"));

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
}
