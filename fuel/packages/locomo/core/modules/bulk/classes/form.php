<?php
namespace Locomo_Core_Module\Bulk

class Bulk_Form {

	protected $name = 'bulk';

	protected $forms = array();

	protected $models = array();


	public function __construct ($name) {
		$this->name = $name;
	}

	public static function forge($name = 'bulk_form') {
		return new static($name);
	}

	/*
	 * @param \Fieldset
	 */
	public function add($form, $key) {
		// todo instanceof
		$this->forms[$key] = $form;
	}


	public function name() {
		return $this->name;
	}


	public function save($bulk_form, $input_post = null, $objects = array(), $use_transaction = false) {

		// if ()
		// $this -> Model_xxxx

		// $objects is Fieldset object

		// transaction start
		if ($use_transaction)
		{
			$db = \Database_Connection::instance(static::connection(true));
			$db->start_transaction();
		}

		try
		{

			foreach ($objects as $obj) {

				$obj->cascadeset();
				// todo cascade 要受け渡し?, transactionは強制的にfalse
				$this->save(null, false);
			}


			// commit
			$use_transaction and $db->commit_transaction();
		}

		// if catch error => rollback
		catch (\Exception $e)
		{
			$use_transaction and $db->rollback_transaction();
			throw $e;
		}

		return $returns;
	}




	/*
	 * @param array  array of \Orm\Model
	 */
	public static function build() {
		// todo 検証
		// if (is_array($objects) || !(reset($objects) instanceof \Orm\Model)) return false;

		$bulk = \Bulk\Bulkform::forge('bulk_name');

		foreach ($objects as $key => $obj) {
			if (method_exists($obj, 'bulk_form_definition')) {
				$bulk->add($obj->bulk_form_definition($key)); // todo id factory nessesity?
			} elseif (method_exists($obj, 'form_definition')) {
				$bulk->add($obj->form_definition($key)); // todo id factory nessesity?
			} else {
				$bulk->add(\Fieldset:forge($key)->add_model($obj)->populate($obj););
			}
		}
	}



	public function add_model($model) {
		if(is_array($model) {
			foreach ($model as $model_obj) {
				$this->add_model($model);
			}
		}

		if (! $model instanceof \Orm\Model) return false;

		

	}
}
