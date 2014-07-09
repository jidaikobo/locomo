<?php
<?php
namespace Post;
class Model_Post extends \Kontiki\Model
{
	protected static $_table_name = 'Post';

	protected static $_properties = array(
		'title',
		'body',
		'user_id',
	);
}