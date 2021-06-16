<?php

require_once('../../config.php');
require_once('../../../../init.php');

$request = new Request();
$method = $request->getGet('method');
if (trim($method) == '') return;

$statistics = new Statistics(new ObjectFactory());
if (!method_exists($statistics, $method)) die;

echo json_encode($statistics->$method());