<?php
namespace Fuel\Migrations;
class Create_Format
{
	public function up()
	{
		echo "create lcm_frmts table.\n";
		\DBUtil::create_table('lcm_frmts', array(
			'id'               => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'is_draft'         => array('type' => 'bool',     'default' => 1,),
			'name'             => array('type' => 'varchar',  'default' => '', 'constraint' => 255),
			'seq'              => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'rotation'         => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'w'                => array('type' => 'double',   'default' => 0.0,),
			'h'                => array('type' => 'double',   'default' => 0.0,),
			'margin_top'       => array('type' => 'double',   'default' => 0.0,),
			'margin_left'      => array('type' => 'double',   'default' => 0.0,),
			'margin_right'     => array('type' => 'double',   'default' => 0.0,),
			'margin_bottom'    => array('type' => 'double',   'default' => 0.0,),
			'is_multiple'      => array('type' => 'bool',     'default' => 0,),
			'cols'             => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'rows'             => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'cell_w'           => array('type' => 'double',   'default' => 0.0,),
			'cell_h'           => array('type' => 'double',   'default' => 0.0,),
			'space_horizontal' => array('type' => 'double',   'default' => 0.0,),
			'space_vertical'   => array('type' => 'double',   'default' => 0.0,),
			'type'             => array('type' => 'varchar',  'default' => '', 'constraint' => 255),
			'model'            => array('type' => 'varchar',  'default' => '', 'constraint' => 255),

			'created_at'        => array('type' => 'datetime', 'null' => true),
			'updated_at'        => array('type' => 'datetime', 'null' => true),
			'deleted_at'        => array('type' => 'datetime', 'null' => true),
			'creator_id'        => array('constraint' => 5, 'type' => 'int'),
			'updater_id'        => array('constraint' => 5, 'type' => 'int'),
		), array('id'));

		echo "create lcm_frmt_elements table.\n";
		\DBUtil::create_table('lcm_frmt_elements', array(
			'id'             => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'format_id'      => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'name'           => array('type' => 'varchar',  'default' => '', 'constraint' => 255),
			'seq'            => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'x'              => array('type' => 'double',   'default' => 0.0,),
			'ln_x'           => array('type' => 'bool',     'default' => 0),
			'y'              => array('type' => 'double',   'default' => 0.0,),
			'ln_y'           => array('type' => 'bool',     'default' => 0),
			'w'              => array('type' => 'double',   'default' => 0.0,),
			'h'              => array('type' => 'double',   'default' => 0.0,),
			'h_adjustable'   => array('type' => 'bool',     'default' => 0),
			'padding_left'   => array('type' => 'double',   'default' => 0.0,),
			'padding_top'    => array('type' => 'double',   'default' => 0.0,),
			'padding_right'  => array('type' => 'double',   'default' => 0.0,),
			'padding_bottom' => array('type' => 'double',   'default' => 0.0,),
			'margin_left'    => array('type' => 'double',   'default' => 0.0,),
			'margin_top'     => array('type' => 'double',   'default' => 0.0,),
			'txt'            => array('type' => 'text',     'default' => ''),
			'font_size'      => array('type' => 'double',   'default' => 0.0,),
			'font_family'    => array('type' => 'varchar',  'default' => '', 'constraint' => 50,),
			'align'          => array('type' => 'varchar',  'default' => '', 'constraint' => 50,),
			'valign'         => array('type' => 'varchar',  'default' => '', 'constraint' => 50,),
			'border_width'   => array('type' => 'double',   'default' => 0.0,),
			'border_left'    => array('type' => 'bool',     'default' => 0),
			'border_top'     => array('type' => 'bool',     'default' => 0),
			'border_right'   => array('type' => 'bool',     'default' => 0),
			'border_bottom'  => array('type' => 'bool',     'default' => 0),
			'type'             => array('type' => 'varchar',  'default' => '', 'constraint' => 255),

			'created_at'        => array('type' => 'datetime', 'null' => true),
			'updated_at'        => array('type' => 'datetime', 'null' => true),
			'deleted_at'        => array('type' => 'datetime', 'null' => true),
			'creator_id'        => array('constraint' => 5, 'type' => 'int'),
			'updater_id'        => array('constraint' => 5, 'type' => 'int'),
		), array('id'));


		echo "create lcm_frmt_eav table.\n";
		\DBUtil::create_table('lcm_frmt_eav', array(
			'id'         => array('constraint' => 11,  'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'format_id'  => array('constraint' => 11,  'type' => 'int', 'default' => 0),
			'key'        => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'value'      => array('type' => 'text', 'default' => ''),

			'created_at'        => array('type' => 'datetime', 'null' => true),
			'updated_at'        => array('type' => 'datetime', 'null' => true),
			'deleted_at'        => array('type' => 'datetime', 'null' => true),
			'creator_id'        => array('constraint' => 5, 'type' => 'int'),
			'updater_id'        => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		echo "drop lcm_frmts table.\n";
		\DBUtil::drop_table('lcm_frmts');
		echo "drop lcm_frmt_elements table.\n";
		\DBUtil::drop_table('lcm_frmt_elements');
		echo "drop lcm_frmt_eav table.\n";
		\DBUtil::drop_table('lcm_frmt_eav');
	}
}
