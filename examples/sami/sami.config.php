<?php
/**
 * Run SAMI on itself
 * php sami.php update examples/sami/sami.config.php
 */

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
									->files()
									->name('*.php')
									->exclude('Tests')
									->in(__DIR__ .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Sami');

$config_options = [
	'title'     => 'SAMI Doc',
	'build_dir' => __DIR__ . DIRECTORY_SEPARATOR .'docs',
	'cache_dir' => __DIR__ . DIRECTORY_SEPARATOR .'cache',

	// Config Options with current known defaults:

	//'default_opened_level' => 2,
	//'cache_dir' => 'PATH',
	//'include_parent_data'=>true, #	include properties and methods from anscestors on class pages
	//'insert_todos'=>false, # Include @todo tags in documentation
	//'sort_class_constants'=>false, #	Sort alphabetical
	//'sort_class_interfaces'=>false, #	Sort alphabetical
	//'sort_class_methods'=>false, # Sort alphabetical
	//'sort_class_properties'=>false, #	Sort alphabetical
	//'sort_class_traits'=>false, # Sort alphabetical
	//'theme'=>'default', # Theme to use

];
$sami = new Sami($iterator, $config_options);

return $sami;