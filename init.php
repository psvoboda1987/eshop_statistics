<?php

require_once('config.php');

$protocol = $_SERVER['HTTPS'] ?? 'http://';
$server_port = $_SERVER['SERVER_PORT'] ?? '';
$port = in_array($server_port, [80, 443]) ? '' : ":$server_port";
$host = $_SERVER['HTTP_HOST'];
$server_root = $protocol . $host . $port;
$project_path = str_replace(['\\'], ['/'], realpath(dirname(__FILE__)));
$root = preg_replace('/.*\/htdocs\//', $server_root . '/', $project_path);
$app_path = (defined('APP_PATH') ? $project_path . '/' . APP_PATH : $project_path);
$app_url = (defined('APP_URL') ? APP_URL : $root);

if (!defined('ROOT')) define('ROOT', $root);
if (!defined('PATH')) define('PATH', $project_path);
if (!defined('APP_PATH')) define('APP_PATH', $app_path);
if (!defined('APP_URL')) define('APP_URL', $app_url);
if (!defined('CLASS_PATH')) define('CLASS_PATH', PATH . '/class');
if (!defined('FRAMEWORK_PATH')) define('FRAMEWORK_PATH', PATH . '/framework');
if (!defined('MODEL_PATH')) define('MODEL_PATH', PATH . '/model');
if (!defined('ENTITY_PATH')) define('ENTITY_PATH', PATH . '/entity');
if (!defined('LIB_PATH')) define('LIB_PATH', PATH . '/lib');

if (!defined('VIEWS_DIR')) define('VIEWS_DIR', ROOT . '/content');
if (!defined('JS_DIR')) define('JS_DIR', ROOT . '/js');
if (!defined('CSS_DIR')) define('CSS_DIR', ROOT . '/css');
if (!defined('DATA_DIR')) define('DATA_DIR', ROOT . '/data');

function myAutoloader()
{
    $classes = [
        glob(FRAMEWORK_PATH . '/*.class.php'),
        glob(CLASS_PATH . '/*.class.php'),
        glob(MODEL_PATH . '/*.class.php'),
        glob(ENTITY_PATH . '/*.class.php')
    ];

    foreach ($classes as $class_name) {

        foreach ($class_name as $c => $class) {

            if (!file_exists($class_name[$c])) continue;

            require_once($class_name[$c]);

        }

    }
}

spl_autoload_register('myAutoloader');