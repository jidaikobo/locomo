<?php
return array(
	'_root_'  => 'sys/home',
	'_404_'   => 'sys/404',

	// pg
	'pg/create'               => 'pg/create',
	'pg/index_(:any)'         => 'pg/index_$1',
	'pg/edit/(:num)'          => 'pg/edit/$1',
	'pg/delete/(:num)'        => 'pg/delete/$1',
	'pg/purge_confirm/(:num)' => 'pg/purge_confirm/$1',
	'pg/purge/(:num)'         => 'pg/purge/$1',
	'pg/(:any)'               => 'pg/view/$1',
);