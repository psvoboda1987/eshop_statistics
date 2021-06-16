<?php

require_once('config.php');
require_once('../../init.php');

$menu_data[] = [
    'method' => 'getProductsUnsold',
    'text' => 'Unsold items'
];
$menu_data[] = [
    'method' => 'getPriceProductsInStock',
    'text' => 'Price of all stock items'
];
$menu_data[] = [
    'method' => 'getLeastOrderedItems',
    'text' => 'Least ordered items'
];
$menu_data[] = [
    'method' => 'getMostOrderedItems',
    'text' => 'Most ordered items'
];
$menu_data[] = [
    'method' => 'getProductPriceAverage',
    'text' => 'Average item price'
];
$menu_data[] = [
    'method' => 'getProductPriceMin',
    'text' => 'Min item price'
];
$menu_data[] = [
    'method' => 'getProductPriceMax',
    'text' => 'Max item price'
];
$menu_data[] = [
    'method' => 'getOrderPriceMax',
    'text' => 'Max order price'
];
$menu_data[] = [
    'method' => 'getOrderCount',
    'text' => 'All orders count'
];
$menu_data[] = [
    'method' => 'getTotalOrderPrice',
    'text' => 'All orders price'
];
$menu_data[] = [
    'method' => 'getProductCount',
    'text' => 'All items count'
];
$menu_data[] = [
    'method' => 'getProductCountBelowAveragePrice',
    'text' => 'Items below average price'
];
$menu_data[] = [
    'method' => 'getProductCountAboveAveragePrice',
    'text' => 'Items above average price'
];

$menu_items = new ListTemplate('menu_items', $menu_data);

$placeholders = ['menu_items' => $menu_items->getHtml()];

$layout = new Template('index', $placeholders, 'layout');

echo $layout->getHtml();
