<?php

class Statistics
{
    private $object_factory;

    public function __construct(ObjectFactory $object_factory)
    {
        $this->object_factory = $object_factory;
        $this->mysql = $this->object_factory->getObject(
            'Mysql',
            HOSTNAME, USERNAME, PASSWORD, DB
        );
    }

    public function getProductCount()
    {
        $query = "
            SELECT COUNT(`product_id`)
            FROM `product`
        ";

        return array_values($this->mysql->query($query)->fetchArray())[0];
    }

    public function getProductCountBelowAveragePrice()
    {
        $query = "
            SELECT COUNT(`product_id`)
            FROM `product`
            WHERE `list_price` < ?
        ";

        $avg_price = $this->getProductPriceAverage();

        return array_values($this->mysql->query($query, $avg_price)->fetchArray())[0];
    }

    public function getProductCountAboveAveragePrice()
    {
        $query = "
            SELECT COUNT(`product_id`)
            FROM `product`
            WHERE `list_price` > ?
        ";

        $avg_price = $this->getProductPriceAverage();

        return array_values($this->mysql->query($query, $avg_price)->fetchArray())[0];
    }

    public function getProductPriceAverage()
    {
        $query = "
            SELECT AVG(`list_price`)
            FROM `product`
        ";

        return (float) array_values($this->mysql->query($query)->fetchArray())[0];
    }

    public function getProductPriceMin()
    {
        $query = "
            SELECT MIN(`list_price`)
            FROM `product`
        ";

        return array_values($this->mysql->query($query)->fetchArray())[0];
    }

    public function getProductPriceMax()
    {
        $query = "
            SELECT MAX(`list_price`)
            FROM `product`
        ";

        return array_values($this->mysql->query($query)->fetchArray())[0];
    }

    public function getOrderCount()
    {
        $query = "
            SELECT COUNT(`order_id`)
            FROM `order`
        ";

        return array_values($this->mysql->query($query)->fetchArray())[0];
    }

    public function getTotalOrderPrice()
    {
        $query = "
            SELECT
                `quantity`,
                `list_price`,
                `discount`

            FROM `order_item`
        ";

        $result = $this->mysql->query($query)->fetchAll();
        $total = 0;

        foreach ($result as $r => $row) {

            $discounted_price = ($row['list_price'] - ($row['list_price'] * $row['discount']));

            $total = $total + ($row['quantity'] * $discounted_price);

        }

        return $total;
    }

    // public function getMostSpendingCustomer()
    // {
    //     $query = "
    //         SELECT MIN(`list_price`)
    //         FROM `product`
    //     ";

    //     return array_values($this->mysql->query($query)->fetchArray())[0];
    // }

    public function getOrderPriceMax()
    {
        $query = "
            SELECT *

            FROM `order_item`
        ";

        $data = $this->mysql->query($query)->fetchAll();

        $orders = [];
        $order_price_max = [
            'total' => 0,
            'order_id' => null,
        ];

        foreach ($data as $order) {

            $item_price_total = ($order['list_price'] * (1 - $order['discount'])) * $order['quantity'];

            if (!isset($orders[$order['order_id']])) $orders[$order['order_id']] = 0;

            $orders[$order['order_id']] += $item_price_total;

            if ($orders[$order['order_id']] > $order_price_max['total']) {

                $order_price_max['total'] = $orders[$order['order_id']];
                $order_price_max['order_id'] = $order['order_id'];

            }

        }

        return '$ ' . number_format($order_price_max['total'], 2, ',', ' ') . ' (order: ' . $order_price_max['order_id'] . ')';
    }

    public function getLeastOrderedItems()
    {
        $query = "
            SELECT
                `order_item`.`product_id`,
                `order_item`.`quantity`,
                `order`.`order_date`,
                `product`.`product_name`

            FROM `order_item`

            JOIN `order` ON (
                `order_item`.`order_id` = `order`.`order_id`
            )

            JOIN `product` ON (
                `order_item`.`product_id` = `product`.`product_id`
            )
        ";

        $data = $this->mysql->query($query)->fetchAll();

        $orders_data = [];

        foreach ($data as $i => $item) {

            $year = date('Y', strtotime($item['order_date']));

            if (!isset($orders_data[$year])) $orders_data[$year] = null;

            if (!isset($orders_data[$year][$item['product_name']])) {
                $orders_data[$year][$item['product_name']] = null;
            }

            $orders_data[$year][$item['product_name']] += (int) $item['quantity'];

        }

        $least_ordered = [];
        $least_ordered_limit = 12;

        foreach ($orders_data as $year => $year_data) {

            foreach ($year_data as $product_name => $quantity) {

                if ($quantity < $least_ordered_limit) {

                    $least_ordered[$year][$product_name] = $quantity;

                }

            }

            if (isset($least_ordered[$year])) ksort($least_ordered[$year]);

        }

        $result = '';
        $ids = [];

        foreach ($least_ordered as $year => $values) {

            $result .= '<br><b>' . $year . '</b><br>';

            foreach ($values as $product_name => $quantity) {

                $ids[] = "$quantity x - $product_name";

            }

        }

        $result .= implode('<br>', $ids);

        return $result;
    }

    public function getMostOrderedItems()
    {
        $query = "
            SELECT
                `order_item`.`product_id`,
                `order_item`.`quantity`,
                `order`.`order_date`,
                `product`.`product_name`

            FROM `order_item`

            JOIN `order` ON (
                `order_item`.`order_id` = `order`.`order_id`
            )

            JOIN `product` ON (
                `order_item`.`product_id` = `product`.`product_id`
            )
        ";

        $data = $this->mysql->query($query)->fetchAll();

        $orders_data = [];

        foreach ($data as $i => $item) {

            $year = date('Y', strtotime($item['order_date']));

            if (!isset($orders_data[$year])) $orders_data[$year] = null;

            if (!isset($orders_data[$year][$item['product_name']])) {
                $orders_data[$year][$item['product_name']] = null;
            }

            $orders_data[$year][$item['product_name']] += (int) $item['quantity'];

        }

        $most_ordered = [];
        $most_ordered_limit = 48;

        foreach ($orders_data as $year => $year_data) {

            foreach ($year_data as $product_name => $quantity) {

                if ($quantity > $most_ordered_limit) {

                    $most_ordered[$year][$product_name] = $quantity;

                }

            }

            if (isset($most_ordered[$year])) ksort($most_ordered[$year]);

        }

        $result = '';
        $ids = [];

        foreach ($most_ordered as $year => $values) {

            $result .= '<br><b>' . $year . '</b><br>';

            foreach ($values as $product_name => $quantity) {

                $ids[] = "$quantity x - $product_name";

            }

            $result .= implode('<br>', $ids);

        }

        return $result;
    }

    public function getPriceProductsInStock()
    {
        $query = "
            SELECT
                `stock`.`product_id`,
                `stock`.`quantity`,
                `stock`.`store_id`,
                `product`.`list_price`,
                `store`.`store_name`

            FROM `stock`

            JOIN `product` ON (
                `product`.`product_id` = `stock`.`product_id`
            )

            JOIN `store` ON (
                `stock`.`store_id` = `store`.`store_id`
            )

            ORDER BY `store_id`, `product_id`
        ";

        $data = $this->mysql->query($query)->fetchAll();

        $stores = [];

        foreach ($data as $i => $item) {

            if (!isset($stores[$item['store_name']])) {

                $stores[$item['store_name']] = null;

            }

            $stores[$item['store_name']] += (int) $item['quantity'] * (int) $item['list_price'];

        }

        $result = [];

        foreach ($stores as $store => $stock_price) {

            $result[] = "$store total stock value: $ $stock_price";

        }

        return implode('<br>', $result);
    }

    public function getProductsUnsold()
    {
        $query = "
            SELECT DISTINCT
                `product`.`product_id`,
                `product`.`product_name`
            FROM `product`
            LEFT JOIN `order_item` ON (
                `product`.`product_id` = `order_item`.`product_id`
            )
            WHERE `order_item`.`product_id` IS NULL
        ";

        $data = $this->mysql->query($query)->fetchAll();

        $unsold = [];

        foreach ($data as $i => $item) {

            $unsold[] = $item['product_id'] . ': ' . $item['product_name'];

        }

        ksort($unsold);

        return implode('<br>', $unsold);
    }
}