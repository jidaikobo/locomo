<?php
namespace Locomo;
trait Controller_Traits_Testdata
{
	/**
	 * action_add_testdata()
	 */
	public function action_add_testdata($num = 10)
	{
		//only at development
		if(\Fuel::$env != 'development') die();
		if(\Auth::get_user_id() !== -2) die('forbidden');

		//$test_datas
		$model = $this->model_name;
		$form = $model::form_definition('add_testdata');
		if(!$form):
			\Session::set_flash('error', 'form_definition failed.');
			\Response::redirect($this->request->module);
		endif;

		//save
		$args = array();
		for($n = 1; $n <= $num; $n++):
			foreach($form->field() as $property => $v):
				if(
					\Arr::search($v->rules, 'required', null, true) != true &&
					\Arr::search($v->rules, 'require_once', null, true) != true //original
				){
					continue;
				}

				$str = md5(microtime());
				/*
				//do nothing
				match_value
				match_pattern
				match_field
				valid_string
				min_length
				*/
				$rules = \Arr::assoc_to_keyval($v->rules, 0, 1);
	
				//exact_length
				if($each_rule = @$rules['exact_length']){
					$str = substr($str, 0, intval($exact_length[0]));
				}

				//max_length
				if($each_rule = @$rules['max_length']){
					$str = substr($str, 0, intval($each_rule[0]));
				}
	
				//valid_email
				$each_rule = isset($rules['valid_email']);
				$str.= $each_rule !== false ? '@example.com' : '' ;

				//valid_emails
				$each_rule = isset($rules['valid_emails']);
				$str.= $each_rule !== false ? '@example.com' : '' ;
	
				//valid_date
				$each_rule = isset($rules['valid_date']);
				$str = $each_rule !== false ? date('Y-m-d H:i:s') : $str ;

				//valid_url
				$each_rule = isset($rules['valid_url']);
				$str = $each_rule !== false ? 'http://example.com' : $str ;
	
				//valid_ip
				$each_rule = isset($rules['valid_ip']);
				$str = $each_rule !== false ? '1.1.1.1' : $str ;
	
				//numeric_min
				$each_rule = isset($rules['numeric_min']);
				$str = $each_rule !== false ? intval($each_rule[0]) : $str ;
	
				//numeric_max
				$each_rule = isset($rules['numeric_max']);
				$str = $each_rule !== false ? intval($each_rule[0]) : $str ;
	
				//numeric_between
				$each_rule = isset($rules['numeric_between']);
				$str = $each_rule !== false ? intval($each_rule[0]) : $str ;

				//bool
				if(substr($property, 0, 3) == 'is_'){
					$str = 1;//always true
				}
	
				$args[$property] = $str;
	
			endforeach;
			if(! $args) continue;
			$obj = $model::forge($args);
			$obj->save();
		endfor;

		if(! $args):
			\Session::set_flash('error', 'couldn not add datas');
		else:
			\Session::set_flash('success', 'added '.$num.' datas.');
		endif;
		return \Response::redirect($this->request->module.DS.'index_admin');
	}
}
