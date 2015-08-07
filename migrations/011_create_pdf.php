<?php
namespace Fuel\Migrations;
class Create_Pdf
{
	public function up()
	{
		echo "create lcm_pdf_formats table.\n";
		\DBUtil::create_table('lcm_pdf_formats', array(
			'id'               => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true, 'unsigned' => true),
			'is_visible'       => array('type' => 'bool',     'default' => 0,),
			'name'             => array('type' => 'varchar',  'default' => '', 'constraint' => 255),
			'seq'              => array('type' => 'int',      'default' => 0,  'constraint' => 11,),
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
		), array('id'));

		echo "create lcm_pdf_elements table.\n";
		\DBUtil::create_table('lcm_pdf_elements', array(
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
		), array('id'));

		echo "create lcm_pdf_eav table.\n";
		\DBUtil::create_table('lcm_pdf_eav', array(
			'id'         => array('constraint' => 11,  'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'format_id'  => array('constraint' => 11,  'type' => 'int', 'default' => 0),
			'key'        => array('constraint' => 255, 'type' => 'varchar', 'default' => ''),
			'value'      => array('type' => 'text', 'default' => ''),
		), array('id'));


	}

	public function down()
	{
		echo "drop lcm_pdf_formats table.\n";
		\DBUtil::drop_table('lcm_pdf_formats');
		echo "drop lcm_pdf_elements table.\n";
		\DBUtil::drop_table('lcm_pdf_elements');
		echo "drop lcm_pdf_eav table.\n";
		\DBUtil::drop_table('lcm_pdf_eav');
	}
}
