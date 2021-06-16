<?php

$protocol = $_SERVER['HTTPS'] ?? 'http://';
$server_port = $_SERVER['SERVER_PORT'] ?? '';
$port = in_array($server_port, [80, 443]) ? '' : ":$server_port";
$host = $_SERVER['HTTP_HOST'];
$server_root = $protocol . $host . $port;
$app_path = str_replace(['\\'], ['/'], realpath(dirname(__FILE__)));
$app_url = preg_replace('/.*\/htdocs\//', $server_root . '/', $app_path);

if (!defined('APP_PATH')) define('APP_PATH', $app_path);
if (!defined('APP_URL')) define('APP_URL', $app_url);
if (!defined('DB')) define('DB', 'bike_store');