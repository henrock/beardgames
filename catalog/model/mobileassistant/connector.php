<?php
/**
 *   This file is part of Mobile Assistant Connector.
 *
 *   Mobile Assistant Connector is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   Mobile Assistant Connector is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with Mobile Assistant Connector. If not, see <http://www.gnu.org/licenses/>.
 */

class Modelmobileassistantconnector extends Model {
    public function getTotalOrders($data = array()) {
        $sql = "SELECT COUNT(order_id) AS count_orders, SUM(total) AS total_sales FROM `" . DB_PREFIX . "order`";

        $query_where_parts = array();
        if (isset($data['date_from'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(date_added) >= '%d'", strtotime($data['date_from']));
        }

        if (isset($data['date_to'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(date_added) <= '%d'", strtotime($data['date_to']));
        }

        if (isset($data['statuses'])) {
            $query_where_parts[] = sprintf(" order_status_id IN ('%s')", $data['statuses']);
        }

        if (isset($data['store_id'])) {
            $query_where_parts[] = sprintf(" store_id = '%d'", $data['store_id']);
        }

        if(!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $query = $this->db->query($sql);

        $res = $query->row;

        $this->load->model('mobileassistant/helper');

        if(!isset($data['currency_code'])) {
            $data['currency_code'] = $this->config->get('config_currency');
        }

        $res['total_sales'] = $this->model_mobileassistant_helper->nice_price($res['total_sales'], $data['currency_code']);

        return $res;
    }


    public function getTotalCustomers($data = array()) {
        $sql = "SELECT COUNT(customer_id) AS count_customers FROM " . DB_PREFIX . "customer";

        if(isset($data['date_from'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(date_added) >= '%d'", strtotime($data['date_from']));
        }
        if(isset($data['date_to'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(date_added) <= '%d'", strtotime($data['date_to']));
        }

        if(!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $query = $this->db->query($sql);

        $row = $query->row;

        $this->load->model('mobileassistant/helper');
        $row['count_customers'] = $this->model_mobileassistant_helper->nice_count($row['count_customers']);

        return $row;
    }


    public function getTotalSoldProducts($data = array()) {
        $sql = "SELECT COUNT(op.product_id) AS count_products FROM `".DB_PREFIX."order_product` AS op
                  LEFT JOIN ".DB_PREFIX."order AS o ON o.order_id = op.order_id";

        $query_where_parts = array();
        if (isset($data['date_from'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(o.date_added) >= '%d'", strtotime($data['date_from']));
        }

        if (isset($data['date_to'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(o.date_added) <= '%d'", strtotime($data['date_to']));
        }

        if (isset($data['statuses'])) {
            $query_where_parts[] = sprintf(" o.order_status_id IN ('%s')", $data['statuses']);
        }

        if (isset($data['store_id'])) {
            $query_where_parts[] = sprintf(" o.store_id = '%d'", $data['store_id']);
        }

        if(!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $query = $this->db->query($sql);

        $res = $query->row;

        return $res;
    }


    public function getChartData($data = array()) {
        $orders = array();
        $customers = array();
        $average = array('avg_sum_orders' => 0, 'avg_orders' => 0, 'avg_customers' => 0, 'avg_cust_order' => '0.00', 'tot_orders' => 0, 'sum_orders' => '0.00', 'tot_customers' => 0, 'currency_symbol' => "");

        $startDate = $data['graph_from'];
        $endDate = $data['graph_to'];

        $plus_date = "+1 day";
        if(isset($data['custom_period']) && strlen($data['custom_period']) > 0) {
            $custom_period = $data['custom_period_date'];

            if($data['custom_period'] == 3) {
                $plus_date = "+3 day";
            } else if($data['custom_period'] == 4) {
                $plus_date = "+1 week";
            } else if($data['custom_period'] == 5 || $data['custom_period'] == 6 || $data['custom_period'] == 7) {
                $plus_date = "+1 month";
            }

            if($data['custom_period'] == 7) {
                $sql = "SELECT MIN(date_added) AS min_date_add, MAX(date_added) AS max_date_add FROM `".DB_PREFIX."order`";
                $query = $this->db->query($sql);
                if($query->num_rows) {
                    $row = $query->row;
                    $startDate = $row['min_date_add'];
                    $endDate = $row['max_date_add'];
                }

            } else {
                $startDate = $custom_period['start_date']." 00:00:00";
                $endDate = $custom_period['end_date']." 23:59:59";
            }
        }

        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        $date = $startDate;
        $d = 0;
        while ($date <= $endDate) {
            $d++;
            $sql = "SELECT COUNT(order_id) AS tot_orders, UNIX_TIMESTAMP(date_added) AS date_add, SUM(total) AS value
                      FROM `".DB_PREFIX."order`
                      WHERE UNIX_TIMESTAMP(date_added) >= '%d' AND UNIX_TIMESTAMP(date_added) < '%d'";
            $sql = sprintf($sql, $date, strtotime($plus_date, $date));

            if(isset($data['statuses'])) {
                $sql .= sprintf(" AND order_status_id IN ('%s')", $data['statuses']);
            }

            if(isset($data['store_id'])) {
                $sql .= sprintf(" AND store_id = '%d'", $data['store_id']);
            }
            $sql .= " GROUP BY DATE(date_added) ORDER BY date_added";

            $total_order_per_day = 0;
            $query = $this->db->query($sql);
            if($query->num_rows) {
                foreach($query->rows as $row) {
                    $total_order_per_day += $row['value'];

                    $average['tot_orders'] += $row['tot_orders'];
                    $average['sum_orders'] += $row['value'];
                }
            }

            if(!isset($data['currency_code'])) {
                $data['currency_code'] = $this->config->get('config_currency');
            }

            //$total_order_per_day = $this->currency->format($total_order_per_day, $data['currency_code'], '', false);

            $orders[] = array($date*1000, $total_order_per_day);

            $sql = "SELECT COUNT(customer_id) AS tot_customers, UNIX_TIMESTAMP(date_added) AS date_add FROM ".DB_PREFIX."customer
				  WHERE UNIX_TIMESTAMP(date_added) >= '%d' AND UNIX_TIMESTAMP(date_added) < '%d'";
            $sql = sprintf($sql, $date, strtotime($plus_date, $date));

            if(isset($data['store_id'])) {
                $sql .= sprintf(" AND store_id = '%d'", $data['store_id']);
            }
            $sql .= " GROUP BY DATE(date_added) ORDER BY date_added";

            $total_customer_per_day = 0;
            $query = $this->db->query($sql);
            if($query->num_rows) {
                foreach($query->rows as $row) {
                    $total_customer_per_day += $row['tot_customers'];

                    $average['tot_customers'] += $row['tot_customers'];
                }
            }
            $customers[] = array($date*1000, $total_customer_per_day);

            $date = strtotime($plus_date, $date);
        }

        // Add 2 additional element into array of orders for graph in mobile application
        if (count($orders) == 1) {
            $orders_tmp = $orders[0];
            $orders = array();
            $orders[0][] = strtotime(date("Y-m-d", $orders_tmp[0] / 1000) . "-1 month") * 1000;
            $orders[0][] = 0;
            $orders[1] = $orders_tmp;
            $orders[2][] = strtotime(date("Y-m-d", $orders_tmp[0] / 1000) . "+1 month") * 1000;
            $orders[2][] = 0;
        }

        // Add 2 additional element into array of customers for graph in mobile application
        if (count($customers) == 1) {
            $customers_tmp = $customers[0];
            $customers = array();
            $customers[0][] = strtotime(date("Y-m-d", $customers_tmp[0] / 1000) . "-1 month") * 1000;
            $customers[0][] = 0;
            $customers[1] = $customers_tmp;
            $customers[2][] = strtotime(date("Y-m-d", $customers_tmp[0] / 1000) . "+1 month") * 1000;
            $customers[2][] = 0;
        }

        if ($d <= 0) $d = 1;
        $average['avg_sum_orders'] = number_format($average['sum_orders']/$d, 2, '.', ' ');
        $average['avg_orders'] = number_format($average['tot_orders']/$d, 1, '.', ' ');
        $average['avg_customers'] = number_format($average['tot_customers']/$d, 1, '.', ' ');

        if ($average['tot_customers'] > 0) {
            $average['avg_cust_order'] = number_format($average['sum_orders']/$average['tot_customers'], 1, '.', ' ');
        }

        $average['sum_orders'] = number_format($average['sum_orders'], 2, '.', ' ');
        $average['tot_customers'] = number_format($average['tot_customers'], 1, '.', ' ');
        $average['tot_orders'] = number_format($average['tot_orders'], 1, '.', ' ');

        return array('orders' => $orders, 'customers' => $customers, 'average' => $average);
    }


    public function getOrderStatusStats($data = array()) {
        $order_statuses = array();
        $default_attrs = $this->_get_default_attrs();

        $sql = "SELECT COUNT(o.order_id) AS count,
                       SUM(o.total) AS total,
                       o.order_status_id AS code,
                       (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS name
                FROM `" . DB_PREFIX . "order` AS o";

        $query_where_parts = array();
        if (isset($data['date_from'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(o.date_added) >= '%d'", strtotime($data['date_from']));
        }

        if (isset($data['date_to'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(o.date_added) <= '%d'", strtotime($data['date_to']));
        }

        if (isset($data['store_id'])) {
            $query_where_parts[] = sprintf(" o.store_id = '%d'", $data['store_id']);
        }

        if(!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $sql .= " GROUP BY code ORDER BY total";

        $this->load->model('mobileassistant/helper');

        if(!isset($data['currency_code'])) {
            $data['currency_code'] = $this->config->get('config_currency');
        }

        $query = $this->db->query($sql);
        if($query->num_rows) {
//            if($query->num_rows == 1 && $query->row['count'] == 0) {
//                return $order_statuses;
//            }

            foreach($query->rows as $row) {
                if($query->row['count'] == 0) {
                    continue;
                }

                if($row['code'] == 0) {
                    $row['name'] = $default_attrs['text_missing'];
                }

                $row['total'] = $this->model_mobileassistant_helper->nice_price($row['total'], $data['currency_code']);

                $order_statuses[] = $row;
            }
        }

        return $order_statuses;
    }


    public function getOrders($data = array()) {
        $orders = array();
        $query_where_parts = array();
        $default_attrs = $this->_get_default_attrs();

        $select_currency_code = " o.currency ";
        $sql_currency_code = "SHOW COLUMNS FROM `".DB_PREFIX."order` WHERE `Field` = 'currency_code'";
        $res_currency_code = $this->db->query($sql_currency_code);
        if($res_currency_code->num_rows) {
            $select_currency_code = " o.currency_code ";
        }

        $sql = "SELECT
                    o.order_id AS id_order,
                    o.date_added AS date_add,
                    o.total AS total_paid,
                    ".$select_currency_code." AS currency_code,
                    CONCAT(o.firstname, ' ', o.lastname) AS customer,
                    (SELECT
                        os.name
                        FROM " . DB_PREFIX . "order_status os
                        WHERE os.order_status_id = o.order_status_id
                              AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
                    ) AS ord_status,
                    o.order_status_id AS status_code,
                    o.store_name AS shop_name,
                    (SELECT SUM(quantity) FROM " . DB_PREFIX . "order_product WHERE order_id = o.order_id) AS count_prods
                FROM `" . DB_PREFIX . "order` o";


        if (isset($data['store_id'])) {
            $query_where_parts[] = "o.store_id = " . $data['store_id'];
        }

        if(isset($data['statuses'])) {
            $query_where_parts[] = sprintf(" o.order_status_id IN ('%s')", $data['statuses']);
        }

        if(isset($data['search_order_id']) && preg_match('/^\d+(?:,\d+)*$/', $data['search_order_id'])) {
            $query_where_parts[] = sprintf("o.order_id IN (%s)", $data['search_order_id']);

        } elseif (isset($data['search_order_id'])) {
            $query_where_parts[] = sprintf(" CONCAT(o.firstname, ' ', o.lastname) LIKE '%%%s%%'", $data['search_order_id']);
        }

        if(isset($data['orders_from'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(o.date_added) >= '%d'", strtotime($data['orders_from']));
        }

        if(isset($data['orders_to'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(o.date_added) <= '%d'", strtotime($data['orders_to']));
        }


        if (!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $sql .= " ORDER BY ";
        switch ($data['sort_by']) {
            case 'id':
                $sql .= "o.order_id DESC";
                break;
            case 'date':
                $sql .= "o.date_added DESC";
                break;
            case 'name':
                $sql .= "customer ASC";
                break;
        }

        $sql .= sprintf(" LIMIT %d, %d", $data['page'], $data['show']);

        $this->load->model('mobileassistant/helper');

        $query = $this->db->query($sql);

        if($query->num_rows) {
            foreach($query->rows as $row) {
                if(isset($data['currency_code'])) {
                    $currency_code = $data['currency_code'];
                } else {
                    $currency_code = $row['currency_code'];
                }

                $row['total_paid'] = $this->model_mobileassistant_helper->nice_price($row['total_paid'], $currency_code);

                if($row['status_code'] == 0) {
                    $row['ord_status'] = $default_attrs['text_missing'];
                }

                $orders[] = $row;
            }
        }

        $orders_status = null;
        if(isset($data['get_statuses']) && $data['get_statuses'] == 1) {
            $orders_status = $this->getOrdersStatuses();
        }

        if(!isset($data['currency_code'])) {
            $data['currency_code'] = $this->config->get('config_currency');
        }

        $orders_total = $this->getOrdersTotal($query_where_parts, $data['currency_code']);

        return array("orders" => $orders,
            "orders_count" => $orders_total['count_ords'],
            "orders_total" => $orders_total['orders_total'],
            "orders_status" => $orders_status
        );
    }

    public function getOrdersTotal($query_where_parts = array(), $currency_code) {
        $this->load->model('mobileassistant/helper');

        $sql = "SELECT SUM(o.total) AS orders_total, COUNT(o.order_id) AS count_ords
				  FROM `" . DB_PREFIX . "order` AS o";

        if (!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $query = $this->db->query($sql);

        $row = $query->row;

        $row['orders_total'] = $this->model_mobileassistant_helper->nice_price($row['orders_total'], $currency_code);
        $row['count_ords'] = $this->model_mobileassistant_helper->nice_count($row['count_ords']);

        return $row;
    }


    public function getOrdersStatuses() {
        $default_attrs = $this->_get_default_attrs();
        $orders_status = array();
        $orders_status[] = array('st_id' => 0, 'st_name' => $default_attrs['text_missing']);

        $sql = "SELECT order_status_id AS st_id, name AS st_name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY name";
        $query = $this->db->query($sql);
        if($query->num_rows) {
            foreach($query->rows as $row) {
                $orders_status[] = array('st_id' => $row['st_id'], 'st_name' => $row['st_name']);
            }
        }
        return $orders_status;
    }


    public function getOrdersInfo($data = array()) {
        $default_attrs = $this->_get_default_attrs();
        $this->load->model('checkout/order');
        $this->load->model('account/order');
        $this->load->model('mobileassistant/helper');

        $order = $this->model_checkout_order->getOrder($data['order_id']);

        $order_info = array(
            'id_order'          => $order['order_id'],
            'id_customer'       => $order['customer_id'],
            'email'             => $order['email'],
            'telephone'         => $order['telephone'],
            'fax'               => $order['fax'],
            'customer'          => $order['firstname'] . ' ' . $order['lastname'],
            'date_added'        => $order['date_added'],
            'status_code'       => $order['order_status_id'],
            'status'            => $order['order_status'],
            'total'             => $order['total'],
            'currency_code'     => (isset($order['currency_code']) ? $order['currency_code'] : $order['currency']),
            'p_method'          => $order['payment_method'],
            'p_name'            => $order['payment_firstname'] . ' ' . $order['payment_lastname'],
            'p_company'         => $order['payment_company'],
            'p_address_1'       => $order['payment_address_1'],
            'p_address_2'       => $order['payment_address_2'],
            'p_city'            => $order['payment_city'],
            'p_postcode'        => $order['payment_postcode'],
            'p_country'         => $order['payment_country'],
            'p_zone'            => $order['payment_zone'],
            's_method'          => $order['shipping_method'],
            's_name'            => $order['shipping_firstname'] . ' ' . $order['shipping_lastname'],
            's_company'         => $order['shipping_company'],
            's_address_1'       => $order['shipping_address_1'],
            's_address_2'       => $order['shipping_address_2'],
            's_city'            => $order['shipping_city'],
            's_postcode'        => $order['shipping_postcode'],
            's_country'         => $order['shipping_country'],
            's_zone'            => $order['shipping_zone'],
            'comment'           => nl2br($order['comment']),
            'admin_comments'    => $this->model_account_order->getOrderHistories($data['order_id'])
        );

        if(isset($data['currency_code'])) {
            $currency_code = $data['currency_code'];
        } else if(isset($order_info['currency_code'])) {
            $currency_code = $order_info['currency_code'];
        } else {
            $currency_code = $this->config->get('config_currency');
        }

        $order_info['total'] = $this->model_mobileassistant_helper->nice_price($order_info['total'], $currency_code);

        if($order['order_status_id'] == 0) {
            $order_info['status'] = $default_attrs['text_missing'];
        }

        $order_info['currency_code'] = $order['currency_code'];

        return $order_info;
    }


    public function getOrderProducts($data) {
        $this->load->model('mobileassistant/helper');

        $sql = "SELECT order_id AS id_order, product_id, name, quantity, price AS product_price, model FROM " . DB_PREFIX . "order_product WHERE order_id = '%d' LIMIT %d, %d";
        $sql = sprintf($sql, $data['order_id'], $data['page'], $data['show']);

        if(!isset($data['currency_code'])) {
            $data['currency_code'] = $this->config->get('config_currency');
        }

        $order_products = array();
        $query = $this->db->query($sql);
        if($query->num_rows) {
            foreach($query->rows as $row) {
                $row['product_price'] = $this->model_mobileassistant_helper->nice_price($row['product_price'], $data['currency_code']);
                $row['product_quantity'] = intval($row['quantity']);
                $row['product_name'] = $row['name'];
                $order_products[] = $row;
            }
        }

        return $order_products;
    }


    public function getOrderCountProducts($data) {
        $sql = "SELECT COUNT(order_id) AS count_prods FROM " . DB_PREFIX . "order_product WHERE order_id = '%d'";
        $sql = sprintf($sql, $data['order_id']);

        $count_prods = 0;
        $query = $this->db->query($sql);

        $row = $query->row;
        if($row) {
            $count_prods = $row['count_prods'];
        }

        return $count_prods;
    }


    public function getOrderTotals($data) {
        $order_total = array();
        $this->load->model('mobileassistant/helper');

        $value_field = "text";
        if(version_compare(VERSION, '2.0.0.0', '>=')) {
            $value_field = "value";
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$data['order_id'] . "' ORDER BY sort_order";

        if(!isset($data['currency_code'])) {
            $data['currency_code'] = $this->config->get('config_currency');
        }

        $query = $this->db->query($sql);
        if($query->num_rows) {
            foreach($query->rows as $row) {
                if($value_field == "value") {
                    $row[$value_field] = $this->model_mobileassistant_helper->nice_price($row[$value_field], $data['currency_code']);
                }
                $order_total[] = array('title' => $row['title'], 'value' => $row[$value_field]);
            }
        }
        return $order_total;
    }


    public function getCustomers($data = array()) {
        $query_where_parts = array();

        $sql = "SELECT
                    c.customer_id AS id_customer,
                    c.firstname,
                    c.lastname,
                    CONCAT(c.firstname, ' ', c.lastname) AS full_name,
                    c.date_added AS date_add,
                    c.email,
                    IFNULL(tot.total_orders, 0) AS total_orders,
                    cgd.name AS customer_group
                FROM " . DB_PREFIX . "customer c
                LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                LEFT OUTER JOIN (SELECT COUNT(order_id) AS total_orders, customer_id FROM `" . DB_PREFIX . "order` GROUP BY customer_id) AS tot ON tot.customer_id = c.customer_id";

        $customers = array();
        if(isset($data['customers_from'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(c.date_added) >= '%d'", strtotime($data['customers_from']));
        }

        if(isset($data['customers_to'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(c.date_added) <= '%d'", strtotime($data['customers_to']));
        }

        if(isset($data['search_val']) && preg_match('/^\d+(?:,\d+)*$/', $data['search_val'])) {
            $query_where_parts[] = sprintf("c.customer_id IN (%s)", $data['search_val']);
        } elseif(isset($data['search_val'])) {
            $query_where_parts[] = sprintf("(c.email LIKE '%%%s%%' OR CONCAT(c.firstname, ' ', c.lastname) LIKE '%%%s%%')", $data['search_val'], $data['search_val']);
        }

        if(isset($data['cust_with_orders'])) {
            $query_where_parts[] = " tot.total_orders > 0";
        }

        if (!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $sql .= " ORDER BY ";
        switch ($data['sort_by']) {
            case 'id':
                $sql .= "c.customer_id DESC";
                break;
            case 'date':
                $sql .= "c.date_added DESC";
                break;
            case 'name':
                $sql .= "full_name ASC";
                break;
        }

        $sql .= sprintf(" LIMIT %d, %d", $data['page'], $data['show']);

        $this->load->model('mobileassistant/helper');

        $query = $this->db->query($sql);
        if($query->num_rows) {
            foreach($query->rows as $row) {
                $row['total_orders'] = intval($row['total_orders']);
                $customers[] = $row;
            }
        }

        $customers_total = $this->getCustomersTotal($query_where_parts);

        return array("customers_count" => intval($customers_total),
                     "customers" => $customers);
    }


    private function getCustomersTotal($query_where_parts = array()) {
        $this->load->model('mobileassistant/helper');

        $sql = "SELECT COUNT(c.customer_id) AS count_custs
						FROM " . DB_PREFIX . "customer AS c
						LEFT OUTER JOIN (SELECT COUNT(order_id) AS total_orders, customer_id FROM `" . DB_PREFIX . "order` GROUP BY customer_id) AS tot ON tot.customer_id = c.customer_id";

        if (!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $query = $this->db->query($sql);
        $row = $query->row;

        return $this->model_mobileassistant_helper->nice_count($row['count_custs']);
    }



    public function getCustomersInfo($data = array()) {
        $this->load->model('account/customer');

        $customer = $this->model_account_customer->getCustomer($data['user_id']);

        $user_info = array(
            'customer_id' => $customer['customer_id'],
            'email' => $customer['email'],
            'name' => $customer['firstname'] . ' ' . $customer['lastname'],
            'phone' => $customer['telephone'],
            'fax' => $customer['fax'],
            'date_add' => $customer['date_added'],
        );

        $user_info['address'] = $this->getAddress($customer['address_id']);

        $customer_orders = $this->getCustomerOrders($data);

        $customer_order_totals = $this->getCustomerOrderTotals($data);

        $customer_info = array("user_info" => $user_info, "customer_orders" => $customer_orders);
        $customer_info = array_merge($customer_info, $customer_order_totals);

        return $customer_info;
    }


    public function getAddress($address_id) {
        $address_query = $this->db->query("SELECT company, address_1, address_2, postcode, city, country_id, zone_id FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "'");

        if ($address_query->num_rows) {
            $country_query = $this->db->query("SELECT name, address_format FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

            if ($country_query->num_rows) {
                $address_query->row['country'] = $country_query->row['name'];
            } else {
                $address_query->row['country'] = '';
            }

            $zone_query = $this->db->query("SELECT name FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

            if ($zone_query->num_rows) {
                $address_query->row['zone'] = $zone_query->row['name'];
            } else {
                $address_query->row['zone'] = '';
            }

            $keys = array('company', 'address_1', 'address_2', 'postcode', 'city', 'zone', 'country');

            $new_arr = array();
            foreach($keys as $key) {
                if(isset($address_query->row[$key]) && !is_null($address_query->row[$key]) && $address_query->row[$key] != '') {
                    $new_arr[] = $address_query->row[$key];
                }
            }

            return implode(', ', $new_arr);
        }

        return '';
    }


    public function getCustomerOrders($data = array()) {
        $customer_orders = array();
        $default_attrs = $this->_get_default_attrs();
        $this->load->model('mobileassistant/helper');

        $select_currency_code = " o.currency ";
        $sql_currency_code = "SHOW COLUMNS FROM `" . DB_PREFIX . "order` WHERE `Field` = 'currency_code'";
        $res_currency_code = $this->db->query($sql_currency_code);
        if($res_currency_code->num_rows) {
            $select_currency_code = " o.currency_code ";
        }

        $sql = "SELECT o.order_id AS id_order, o.total AS total_paid, o.order_status_id, ".$select_currency_code." AS currency_code, os.name AS ord_status, o.date_added as date_add, (SELECT SUM(quantity) FROM " . DB_PREFIX . "order_product WHERE order_id = o.order_id) AS pr_qty
				    FROM `" . DB_PREFIX . "order` AS o
				    LEFT JOIN " . DB_PREFIX . "order_status AS os ON os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
				    WHERE o.customer_id = '%d' ORDER BY o.order_id DESC LIMIT %d, %d";
        $sql = sprintf($sql, $data['user_id'], $data['page'], $data['show']);

        $query = $this->db->query($sql);
        if($query->num_rows) {
            foreach($query->rows as $row) {
                if(isset($data['currency_code'])) {
                    $currency_code = $data['currency_code'];
                } else {
                    $currency_code = $row['currency_code'];
                }

                $row['total_paid'] = $this->model_mobileassistant_helper->nice_price($row['total_paid'], $currency_code);
                if($row['order_status_id'] == 0) {
                    $row['ord_status'] = $default_attrs['text_missing'];
                }
                $customer_orders[] = $row;
            }
        }

        return $customer_orders;
    }


    public function getCustomerOrderTotals($data) {
        $this->load->model('mobileassistant/helper');
        $sql = "SELECT COUNT(order_id) AS count_ords, SUM(total) AS sum_ords FROM `" . DB_PREFIX . "order` WHERE customer_id = '%d'";
        $sql = sprintf($sql, $data['user_id']);

        $sum_ords = 0;
        $count_ords = 0;
        $query = $this->db->query($sql);
        if($query->num_rows) {
            $row = $query->row;
            if(isset($row['sum_ords'])) $sum_ords = $row['sum_ords'];
            if(isset($row['count_ords'])) $count_ords = $row['count_ords'];
        }

        $row['sum_ords'] = $this->model_mobileassistant_helper->nice_price($sum_ords, $sum_ords);
        $row['count_ords'] = $this->model_mobileassistant_helper->nice_count($count_ords);

        return array("c_orders_count" => intval($count_ords), "sum_ords" => $sum_ords);
    }


    public function getProducts($data = array()) {
        $products = array();
        $query_where_parts = array();
        $query_params_parts = array();
        $this->load->model('mobileassistant/helper');

        $sql = "SELECT p.product_id AS main_id,
                         p.product_id AS product_id,
                         p.model,
                         p.sku,
                         pd.name,
                         p.price,
                         p.quantity
				  FROM " . DB_PREFIX . "product AS p
				  LEFT JOIN " . DB_PREFIX . "product_description AS pd ON pd.product_id = p.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if(isset($data['params']) && isset($data['val'])) {
            foreach($data['params'] as $param) {
                switch ($param) {
                    case 'pr_id':
                        $query_params_parts[] = sprintf(" p.product_id LIKE '%%%s%%'", $data['val']);
                        break;
                    case 'pr_sku':
                        $query_params_parts[] = sprintf(" p.model LIKE '%%%s%%'", $data['val']);
                        break;
                    case 'pr_name':
                        $query_params_parts[] = sprintf(" pd.name LIKE '%%%s%%'", $data['val']);
                        break;
                    case 'pr_desc':
                    case 'pr_short_desc':
                        $query_params_parts[] = sprintf(" pd.description LIKE '%%%s%%'", $data['val']);
                        break;
                }
            }
        }

        if (!empty($query_params_parts)) {
            $query_where_parts[] = " ( " . implode(" OR ", $query_params_parts) . " )";
        }

        if(isset($data['store_id'])) {
            $query_where_parts[] = sprintf(" p.product_id IN (SELECT product_id FROM " . DB_PREFIX . "product_to_store WHERE store_id = '%d')", $data['store_id']);
        }

        if (!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $sql .= " GROUP BY p.product_id ORDER BY ";
        switch ($data['sort_by']) {
            case 'id':
                $sql .= "p.product_id DESC";
                break;
            case 'name':
                $sql .= "pd.name ASC";
                break;
        }

        $sql .= sprintf(" LIMIT %d, %d", $data['page'], $data['show']);

        if(!isset($data['currency_code'])) {
            $data['currency_code'] = $this->config->get('config_currency');
        }

        $query = $this->db->query($sql);
        if($query->num_rows) {
            foreach($query->rows as $row) {
                $row['price'] = $this->model_mobileassistant_helper->nice_price($row['price'], $data['currency_code']);

                $products[] = $row;
            }
        }

        return array("products_count" => $this->getCountProducts($query_where_parts), "products" => $products);
    }


    public function getCountProducts($query_where_parts = array()) {
        $this->load->model('mobileassistant/helper');

        $sql = "SELECT COUNT(p.product_id) AS count_prods FROM " . DB_PREFIX . "product AS p
                       LEFT JOIN " . DB_PREFIX . "product_description AS pd ON pd.product_id = p.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $query = $this->db->query($sql);
        $row = $query->row;

        return $this->model_mobileassistant_helper->nice_count($row['count_prods']);
    }


    public function getOrderedProducts($data = array()) {
        $products = array();
        $query_where_parts = array();
        $query_params_parts = array();
        $this->load->model('mobileassistant/helper');

        $select_currency_code = " o.currency ";
        $sql_currency_code = "SHOW COLUMNS FROM `".DB_PREFIX."order` WHERE `Field` = 'currency_code'";
        $res_currency_code = $this->db->query($sql_currency_code);
        if($res_currency_code->num_rows) {
            $select_currency_code = " o.currency_code ";
        }

        $sql = "SELECT
                    op.order_id AS main_id,
                    op.order_id AS order_id,
                    op.product_id,
                    op.model,
                    op.name,
                    op.price,
                    op.quantity,
                    ".$select_currency_code." AS currency_code
				  FROM " . DB_PREFIX . "order_product AS op
				    LEFT JOIN `" . DB_PREFIX . "order` AS o ON o.order_id = op.order_id";

        if(isset($data['params']) && isset($data['val'])) {
            foreach($data['params'] as $param) {
                switch ($param) {
                    case 'pr_id':
                        if(isset($data['val']) && preg_match('/^\d+(?:,\d+)*$/', $data['val'])) {
                            $query_params_parts[] = sprintf(" op.product_id IN ('%s')", $data['val']);
                        } else {
                            $query_params_parts[] = sprintf(" op.product_id = '%d'", $data['val']);
                        }

                        break;
                    case 'pr_sku':
                        $query_params_parts[] = sprintf(" op.model LIKE '%%%s%%'", $data['val']);
                        break;
                    case 'pr_name':
                        $query_params_parts[] = sprintf(" op.name LIKE '%%%s%%'", $data['val']);
                        break;
                }
            }
        }

        if (!empty($query_params_parts)) {
            $query_where_parts[] = " ( " . implode(" OR ", $query_params_parts) . " )";
        }

        if(isset($data['products_from'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(o.date_added) >= '%d'", strtotime($data['products_from']));
        }

        if(isset($data['products_to'])) {
            $query_where_parts[] = sprintf(" UNIX_TIMESTAMP(o.date_added) <= '%d'", strtotime($data['products_to']));
        }

        if(isset($data['statuses'])) {
            $query_where_parts[] = sprintf(" o.order_status_id IN ('%s')", $data['statuses']);
        }

        if (isset($data['store_id'])) {
            $query_where_parts[] = sprintf(" o.store_id = '%d'", $data['store_id']);
        }

        if (!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $sql .= " ORDER BY ";
        switch ($data['sort_by']) {
            case 'id':
                $sql .= "op.product_id DESC";
                break;
            case 'name':
                $sql .= "op.name ASC";
                break;
        }

        $sql .= sprintf(" LIMIT %d, %d", $data['page'], $data['show']);

        $query = $this->db->query($sql);
        if($query->num_rows) {
            foreach($query->rows as $row) {
                if(isset($data['currency_code'])) {
                    $currency_code = $data['currency_code'];
                } else {
                    $currency_code = $row['currency_code'];
                }

                $row['price'] = $this->model_mobileassistant_helper->nice_price($row['price'], $currency_code);

                $products[] = $row;
            }
        }

        $total_ordered_products = $this->getTotalOrderedProducts($query_where_parts);

        return array("products_count" => $total_ordered_products['count_prods'], "products" => $products);
    }


    public function getTotalOrderedProducts($query_where_parts = array()) {
        $this->load->model('mobileassistant/helper');

        $sql = "SELECT COUNT(op.product_id) AS count_prods, SUM(op.quantity) AS total_ordered FROM " . DB_PREFIX . "order_product AS op
                    LEFT JOIN `" . DB_PREFIX . "order` AS o ON o.order_id = op.order_id";

        if (!empty($query_where_parts)) {
            $sql .= " WHERE " . implode(" AND ", $query_where_parts);
        }

        $query = $this->db->query($sql);
        $row = $query->row;

        $row['count_prods'] = $this->model_mobileassistant_helper->nice_count($row['count_prods']);
        $row['total_ordered'] = $this->model_mobileassistant_helper->nice_count($row['total_ordered']);

        return $row;
    }


    public function getProductTotalOrdered($product_id) {
        $query_where_parts[] = sprintf(" op.product_id = '%d'", $product_id);

        $total_ordered_products = $this->getTotalOrderedProducts($query_where_parts);

        return $total_ordered_products['total_ordered'];
    }


    public function getProductInfo($data = array()) {
        $this->load->model('tool/image');
        $this->load->model('mobileassistant/helper');

        $sql = "SELECT
					p.product_id AS id_product,
					p.product_id AS product_id,
					pd.name,
					p.model,
					p.sku,
					p.price,
					p.quantity,
					p.image,
					(SELECT SUM(quantity) FROM " . DB_PREFIX . "order_product WHERE product_id = p.product_id) AS total_ordered,
					(SELECT image FROM " . DB_PREFIX . "product_image WHERE product_id = p.product_id AND image != '' ORDER BY sort_order LIMIT 1) AS product_img,
					(IF(p.status = 1, 'Enabled', 'Disabled')) AS forsale
				FROM " . DB_PREFIX . "product AS p
				    LEFT JOIN " . DB_PREFIX . "product_description AS pd ON pd.product_id = p.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				WHERE p.product_id = '%d' GROUP BY p.product_id";
        $sql = sprintf($sql, $data['product_id']);

        $query = $this->db->query($sql);

        if($query->num_rows) {
            $product = $query->row;
            $product['total_ordered'] = intval($product['total_ordered']);

            $product['price'] = $this->model_mobileassistant_helper->nice_price($product['price'], $data['currency_code']);

            if ($product['image']) {
                $product['id_image'] = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
                $product['id_image_large'] = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_width'));
            } else {
                $product['id_image'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
            }

            unset($product['image']);

            return $product;
        } else {
            return false;
        }
    }


    public function getProductDescr($data = array()) {
        $sql = "SELECT description AS descr FROM " . DB_PREFIX . "product_description WHERE product_id = '%d' AND language_id = '" . (int)$this->config->get('config_language_id') . "'";
        $sql = sprintf($sql, $data['product_id']);

        $query = $this->db->query($sql);

        if($query->num_rows) {
            return $query->row;
        }

        return false;
    }


    public function savePushNotificationSettings($data = array()) {
        $query_values = array();
        $query_where = array();

        if(isset($data['registration_id_old'])) {
            $sql = "UPDATE " . DB_PREFIX . "mobileassistant_push_settings SET registration_id = '%s' WHERE registration_id = '%s'";
            $sql = sprintf($sql, $data['registration_id'], $data['registration_id_old']);
            $this->db->query($sql);
        }


        if(empty($data['push_new_order']) && empty($data['push_order_statuses']) && empty($data['push_new_customer'])) {
            $sql_del = "DELETE FROM " . DB_PREFIX . "mobileassistant_push_settings WHERE registration_id = '%s' AND app_connection_id = '%s'";
            $sql_del = sprintf($sql_del, $data['registration_id'], $data['app_connection_id']);

            $this->db->query($sql_del);

            return true;
        }


        $query_values[] = sprintf(" push_new_order = '%d'", $data['push_new_order']);

        $query_values[] = sprintf(" push_order_statuses = '%s'", $data['push_order_statuses']);

        $query_values[] = sprintf(" push_new_customer = '%d'", $data['push_new_customer']);

        $query_values[] = sprintf(" push_currency_code = '%s'", $data['push_currency_code']);

        $query_values[] = sprintf(" store_id = '%d'", $data['store_id']);


        $sql = "SELECT setting_id FROM " . DB_PREFIX . "mobileassistant_push_settings
                WHERE registration_id = '%s' AND app_connection_id = '%s'";

        $sql = sprintf($sql, $data['registration_id'], $data['app_connection_id']);

        $query = $this->db->query($sql);

        if($query->num_rows > 1 || $query->num_rows <= 0 || !$query->num_rows) {
            if($query->num_rows > 1) {
                foreach($query->rows as $row) {
                    $sql_del = "DELETE FROM " . DB_PREFIX . "mobileassistant_push_settings WHERE setting_id = '%d'";
                    $sql_del = sprintf($sql_del, $row['setting_id']);
                    $this->db->query($sql_del);
                }
            }

            $query_values[] = sprintf(" registration_id = '%s'", $data['registration_id']);
            $query_values[] = sprintf(" app_connection_id = '%s'", $data['app_connection_id']);

            $sql = "INSERT INTO `" . DB_PREFIX . "mobileassistant_push_settings` SET ";

            if (!empty($query_values)) {
                $sql .= implode(" , ", $query_values);
            }

            $this->db->query($sql);
            return true;

        } else {
            $query_where[] = sprintf(" registration_id = '%s'", $data['registration_id']);
            $query_where[] = sprintf(" app_connection_id = '%s'", $data['app_connection_id']);

            $sql = "UPDATE `" . DB_PREFIX . "mobileassistant_push_settings` SET ";

            if (!empty($query_values)) {
                $sql .= implode(" , ", $query_values);
            }

            if (!empty($query_where)) {
                $sql .= " WHERE " . implode(" AND ", $query_where);
            }

            $this->db->query($sql);
            return true;
        }

        return false;
    }


    public function addOrderHistory_156x($order_id, $order_status_id, $comment = '', $notify = false) {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);

        if ($order_info) {
            // Fraud Detection
            if ($this->config->get('config_fraud_detection')) {
                $this->load->model('checkout/fraud');

                $risk_score = $this->model_checkout_fraud->getFraudScore($order_info);

                if ($risk_score > $this->config->get('config_fraud_score')) {
                    $order_status_id = $this->config->get('config_fraud_status_id');
                }
            }

            // Ban IP
            $status = false;

            $this->load->model('account/customer');

            if ($order_info['customer_id']) {

                $results = $this->model_account_customer->getIps($order_info['customer_id']);

                foreach ($results as $result) {
                    if ($this->model_account_customer->isBanIp($result['ip'])) {
                        $status = true;

                        break;
                    }
                }
            } else {
                $status = $this->model_account_customer->isBanIp($order_info['ip']);
            }

            if ($status) {
                $order_status_id = $this->config->get('config_order_status_id');
            }

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

            $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");

            // Send out any gift voucher mails
            if ($this->config->get('config_complete_status_id') == $order_status_id) {
                $this->load->model('checkout/voucher');

                $this->model_checkout_voucher->confirm($order_id);
            }

            if ($notify) {
                $language = new Language($order_info['language_directory']);
                $language->load($order_info['language_filename']);
                $language->load('mail/order');

                $subject = sprintf($language->get('text_update_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

                $message  = $language->get('text_update_order') . ' ' . $order_id . "\n";
                $message .= $language->get('text_update_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

                $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

                if ($order_status_query->num_rows) {
                    $message .= $language->get('text_update_order_status') . "\n\n";
                    $message .= $order_status_query->row['name'] . "\n\n";
                }

                if ($order_info['customer_id']) {
                    $message .= $language->get('text_update_link') . "\n";
                    $message .= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
                }

                if ($comment) {
                    $message .= $language->get('text_update_comment') . "\n\n";
                    $message .= $comment . "\n\n";
                }

                $message .= $language->get('text_update_footer');

                $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->username = $this->config->get('config_smtp_username');
                $mail->password = $this->config->get('config_smtp_password');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');
                $mail->setTo($order_info['email']);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender($order_info['store_name']);
                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                $mail->send();
            }
        }
    }









//-------//-------//-------//-------//-------

    private function _get_default_attrs() {
        $default_attrs = array();
        $this->load->model('mobileassistant/helper');

        $this->load->model('localisation/language');
        $language = $this->model_localisation_language->getLanguage((int)$this->config->get('config_language_id'));

        $default_attrs['text_missing'] = 'Missing Orders';
        if(file_exists('./admin/language/' . $language['directory'] . '/sale/order.php')) {
            include('./admin/language/' . $language['directory'] . '/sale/order.php');

            if(isset($_['text_missing'])) {
                $default_attrs['text_missing'] = $_['text_missing'];
            }
        }

        return $default_attrs;
    }
}

?>