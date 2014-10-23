<?php
namespace Locomo_Core;

class Bulk {

	protected $name = 'bulk';

	protected $forms = array();

	protected $models = array();


	public function __construct ($name) {
		$this->name = $name;
	}

	public static function forge($name = 'bulk_form') {
		return new static($name);
	}


	public function name() {
		return $this->name;
	}



	public function add_model($model) {
		if(is_array($model)) {
			foreach ($model as $model_obj) {
				$this->add_model($model_obj);
			}

		} else {
			$key = 'bulk_' . $model[$model::primary_key()[0]];
			$this->models[$key] = $model;
			if (method_exists($model, 'bulk_form_definition')) {
				$this->forms[$key] = $model->bulk_form_definition($key, $model); // todo id factory nessesity?
			} elseif (method_exists($model, 'form_definition')) {
				$this->forms[$key] = $model->form_definition($key, $model); // todo id factory nessesity?
			} else {
				$this->forms[$key] = \Fieldset::forge($key)->add_model($model)->populate($model);
			}
			$this->forms[$key]->set_input_name_array($key);
		}

		if (! $model instanceof \Orm\Model) return false;

		return $this;
	}




	/*
	 * @param array  array of \Orm\Model
	 */
	public function build() {
		$output = '';
		foreach($this->forms as $form) {
			$form->set_config('form_template', "\n\t\t\n\t\t<table>\n{fields}\n\t\t</table>\n\t\t\n");
			$output .= $form->build();
		}
		\Config::load('develop::form');
		
		var_dump(\Config::get('form')); die();
		return $output;
	}


	/*
	 * @return validate
	 */
	public function save($use_transaction = true, $validation = true) {

		$validated = array();

		// transaction start
		if ($use_transaction)
		{
			$db = \Database_Connection::instance();
			$db->start_transaction();
		}

		try
		{
			foreach ($this->models as $key => $model) {
				if ($this->forms[$key]->populate(\Input::post())->validation()->run(\Input::post())) {
					$model->set(\Input::post($key));
					$model->save(null, false);
				} else {
					if ($validation) $validated[] = false;
				}
			}
		} // -> try

		// if catch error => rollback
		catch (\Exception $e)
		{
			$use_transaction and $db->rollback_transaction();
			throw $e;
		}
			//	var_dump(!in_array(false, $validated)) ; die();

		if (!in_array(false, $validated)) {
			// commit
			$use_transaction and $db->commit_transaction();
			return true;
		} else {
			// rollback
			$use_transaction and $db->rollback_transaction();
			return false;
		}
	}

	public static function connection($writeable = false)
	{
		$class = get_called_class();

		if ($writeable and property_exists($class, '_write_connection'))
		{
			return static::$_write_connection;
		}

		return property_exists($class, '_connection') ? static::$_connection : null;
	}

}

