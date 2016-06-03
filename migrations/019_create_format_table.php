<?php
namespace Fuel\Migrations;
class Create_Format_Table
{
	public function up()
	{
		// frmt の has_many のテーブル
		echo "create lcm_frmt_tables table.\n";
		\DBUtil::create_table('lcm_frmt_tables', array(
			'id'               => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'is_draft'         => array('type' => 'bool',     'default' => 1,),
			'is_print_header'  => array('type' => 'bool',     'default' => 1,),
			'name'             => array('type' => 'varchar',  'default' => '', 'constraint' => 255),
			'seq'              => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'model'            => array('type' => 'varchar',  'default' => '', 'constraint' => 255),
			'relation'         => array('type' => 'varchar',  'default' => '', 'constraint' => 255),

			'header_min_h'          => array('type' => 'double',   'default' => 0.0,),
			'header_h_adjustable'   => array('type' => 'bool',     'default' => 0),
			'header_padding_left'   => array('type' => 'double',   'default' => 0.0,),
			'header_padding_top'    => array('type' => 'double',   'default' => 0.0,),
			'header_padding_right'  => array('type' => 'double',   'default' => 0.0,),
			'header_padding_bottom' => array('type' => 'double',   'default' => 0.0,),
			'header_font_size'      => array('type' => 'double',   'default' => 0.0,),
			'header_font_family'    => array('type' => 'varchar',  'default' => '', 'constraint' => 50,),
			'header_align'          => array('type' => 'varchar',  'default' => '', 'constraint' => 50,),

			/*
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
			 */

			'created_at'        => array('type' => 'datetime', 'null' => true),
			'updated_at'        => array('type' => 'datetime', 'null' => true),
			'deleted_at'        => array('type' => 'datetime', 'null' => true),
			'creator_id'        => array('constraint' => 5, 'type' => 'int'),
			'updater_id'        => array('constraint' => 5, 'type' => 'int'),
		), array('id'));

		echo "create lcm_frmt_table_elements table.\n";
		\DBUtil::create_table('lcm_frmt_table_elements', array(
			'id'             => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'frmt_table_id'  => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'name'           => array('type' => 'varchar',  'default' => '', 'constraint' => 255),
			'label'          => array('type' => 'varchar',  'default' => '', 'constraint' => 255),
			'seq'            => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
			'txt'            => array('type' => 'text',     'default' => ''),
			'w'              => array('type' => 'double',   'default' => 0.0,),
			'font_size'      => array('type' => 'double',   'default' => 0.0,),
			'font_family'    => array('type' => 'varchar',  'default' => '', 'constraint' => 50,),
			'fitcell_type'   => array('type' => 'varchar',  'default' => '', 'constraint' => 50,),
			'align'          => array('type' => 'varchar',  'default' => '', 'constraint' => 50,),

			'min_h'          => array('type' => 'double',   'default' => 0.0,),
			'padding_left'   => array('type' => 'double',   'default' => 0.0,),
			'padding_top'    => array('type' => 'double',   'default' => 0.0,),
			'padding_right'  => array('type' => 'double',   'default' => 0.0,),
			'padding_bottom' => array('type' => 'double',   'default' => 0.0,),

			/*
			'x'              => array('type' => 'double',   'default' => 0.0,),
			'ln_x'           => array('type' => 'bool',     'default' => 0),
			'y'              => array('type' => 'double',   'default' => 0.0,),
			'ln_y'           => array('type' => 'bool',     'default' => 0),
			'h'              => array('type' => 'double',   'default' => 0.0,),
			'h_adjustable'   => array('type' => 'bool',     'default' => 0),
			'padding_left'   => array('type' => 'double',   'default' => 0.0,),
			'padding_top'    => array('type' => 'double',   'default' => 0.0,),
			'padding_right'  => array('type' => 'double',   'default' => 0.0,),
			'padding_bottom' => array('type' => 'double',   'default' => 0.0,),
			'margin_left'    => array('type' => 'double',   'default' => 0.0,),
			'margin_top'     => array('type' => 'double',   'default' => 0.0,),
			'border_width'   => array('type' => 'double',   'default' => 0.0,),
			'border_left'    => array('type' => 'bool',     'default' => 0),
			'border_top'     => array('type' => 'bool',     'default' => 0),
			'border_right'   => array('type' => 'bool',     'default' => 0),
			'border_bottom'  => array('type' => 'bool',     'default' => 0),
			 */

			'created_at'        => array('type' => 'datetime', 'null' => true),
			'updated_at'        => array('type' => 'datetime', 'null' => true),
			'deleted_at'        => array('type' => 'datetime', 'null' => true),
			'creator_id'        => array('constraint' => 5, 'type' => 'int'),
			'updater_id'        => array('constraint' => 5, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		echo "drop lcm_frmt_tables table.\n";
		\DBUtil::drop_table('lcm_frmt_tables');
		echo "drop lcm_frmt_table_elements table.\n";
		\DBUtil::drop_table('lcm_frmt_table_elements');
	}
}
