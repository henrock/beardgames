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

class ControllerModuleMobileAssistantConnector extends Controller {
    private $is_ver20;
    private $call_function;
    private $callback;
    private $hash;
    private $s;
    private $currency;

    private $show;
    private $page;
    private $search_order_id;
    private $orders_from;
    private $orders_to;
    private $customers_from;
    private $customers_to;
//    private $date_from;
//    private $date_to;
    private $graph_from;
    private $graph_to;
    private $stats_from;
    private $stats_to;
    private $products_to;
    private $products_from;
    private $order_id;
    private $user_id;
    private $params;
    private $val;
    private $search_val;
    private $statuses;
    private $sort_by;
    private $product_id;
    private $get_statuses;
    private $cust_with_orders;
    private $data_for_widget;
    private $registration_id;
    private $registration_id_old;
    private $api_key;
    private $push_new_order;
    private $push_order_statuses;
    private $push_new_customer;
    private $app_connection_id;
    private $push_currency_code;
    private $action;
    private $custom_period;
    private $store_id;
    private $new_status;
    private $currency_code;
    private $notify_customer;
    private $change_order_status_comment;

    const PUSH_TYPE_NEW_ORDER           = "new_order";
    const PUSH_TYPE_CHANGE_ORDER_STATUS = "order_changed";
    const PUSH_TYPE_NEW_CUSTOMER        = "new_customer";
    const DEBUG_MODE = true;


    public function index() {
        $this->is_ver20 = version_compare(VERSION, '2.0.0.0', '>=');

        $this->load->model('mobileassistant/setting');

        $this->s = $this->model_mobileassistant_setting->getSetting('mobassist');

        if(!isset($this->s['mobassist_status'])) {
            $this->generate_output('module_disabled');
        }

        $request = $this->request->request;

        if(isset($request['callback']) && strlen($request['callback']) > 0) {
            $this->callback = $request['callback'];
        }

        if(isset($request['call_function']) && strlen($request['call_function']) > 0) {
            $this->call_function = $request['call_function'];
        }

        if(isset($request['hash']) && strlen($request['hash']) > 0) {
            $this->hash = $request['hash'];
        }

        if(empty($this->call_function)) {
            $this->run_self_test();
        }

        if(!$this->check_auth()) {
            $this->generate_output('auth_error');
        }

        if($this->call_function == 'test_config') {
            $this->generate_output(array('test' => 1));
        }

        $params = $this->_validate_types($request, array(
            'show' => 'INT',
            'page' => 'INT',
            'search_order_id' => 'STR',
            'orders_from' => 'STR',
            'orders_to' => 'STR',
            'customers_from' => 'STR',
            'customers_to' => 'STR',
            'date_from' => 'STR',
            'date_to' => 'STR',
            'graph_from' => 'STR',
            'graph_to' => 'STR',
            'stats_from' => 'STR',
            'stats_to' => 'STR',
            'products_to' => 'STR',
            'products_from' => 'STR',
            'order_id' => 'INT',
            'user_id' => 'INT',
            'params' => 'STR',
            'val' => 'STR',
            'search_val' => 'STR',
            'statuses' => 'STR',
            'sort_by' => 'STR',
            'last_order_id' => 'STR',
            'product_id' => 'INT',
            'get_statuses' => 'INT',
            'cust_with_orders' => 'INT',
            'data_for_widget' => 'INT',
            'registration_id' => 'STR',
            'registration_id_old' => 'STR',
            'api_key' => 'STR',
            'push_new_order' => 'INT',
            'push_order_statuses' => 'STR',
            'push_new_customer' => 'INT',
            'app_connection_id' => 'STR',
            'push_currency_code' => 'STR',
            'action' => 'STR',
            'carrier_code' => 'STR',
            'custom_period' => 'INT',
            'store_id' => 'STR',
            'new_status' => 'INT',
            'notify_customer' => 'INT',
            'currency_code' => 'STR',
            'fc' => 'STR',
            'module' => 'STR',
            'controller' => 'STR',
            'change_order_status_comment' => 'STR',
        ));

        foreach($params as $k => $value) {
            $this->{$k} = $value;
        }

        if(empty($this->currency_code) || $this->currency_code == 'not_set') {
            $this->currency = '';

        } else if($this->currency_code == 'base_currency') {
            $this->currency = $this->config->get('config_currency');

        } else {
            $this->currency = $this->currency_code;
        }

        if(empty($this->push_currency_code) || $this->push_currency_code == 'not_set') {
            $this->push_currency_code = '';
        }

        if($this->store_id == '') {
            $this->store_id = -1;
        }

        $this->store_id = intval($this->store_id);

        if(!method_exists($this, $this->call_function)) {
            $this->generate_output('old_module');
        }
        $result = call_user_func(array($this, $this->call_function));
        $this->generate_output($result);
    }

    private function run_self_test() {
        $html = '<h2>Mobile Assistant Connector Self Test Tool</h2>'
            . '<div style="padding: 5px; margin: 10px 0;">This tool checks your website to make sure there are no issues in your hosting configuration.<br />Your hosting support can solve all issues found here.</div><br/>'
            . '<table cellpadding=4><tr><th>Test Title</th><th>Result</th></tr>'
            . '<tr><td>Module Version</td><td>' . $this->s['mobassist_module_version']. '</td><td></td></tr>'
            . '<tr><td>Cart Version</td><td>' . VERSION . '</td><td></td></tr>'
            . '<tr><td>Default Login and Password Changed</td><td>'
            . (( $res = $this->test_default_password_is_changed() ) ? '<span style="color: #008000;">Yes</span>' : '<span style="color: #ff0000;">Fail</span>') . '</td>';

        if(!$res) {
            $html .= '<td>Change your login credentials in Mobile Assistant Connector to make your connection secure</td>';
        }

        $html .= '</table><br/>'
            . '<div style="margin-top: 15px; font-size: 13px;">Mobile Assistant Connector by <a href="http://emagicone.com" target="_blank" style="color: #15428B">eMagicOne</a></div>';

        die($html);
    }

    private function test_default_password_is_changed() {
        return !($this->s['mobassist_login'] == '1' && $this->s['mobassist_pass'] == 'c4ca4238a0b923820dcc509a6f75849b');
    }

    private function generate_output($data) {
        $add_bridge_version = false;

        if(in_array($this->call_function, array("test_config", "get_store_title", "get_store_stats", "get_data_graphs"))) {
            if(is_array($data) && $data != 'auth_error' && $data != 'connection_error' && $data != 'old_bridge') {
                $add_bridge_version = true;
            }
        }

        if(!is_array($data)) {
            $data = array($data);
        }

        if(is_array($data)) {
            array_walk_recursive($data, array($this, 'reset_null'));
        }

        if($add_bridge_version) {
            $data['module_version'] = $this->s['mobassist_module_code'];
        }

        $data = json_encode($data);

        if($this->callback) {
            header('Content-Type: text/javascript;charset=utf-8');
            die($this->callback . '(' . $data . ');');
        } else {
            header('Content-Type: text/javascript;charset=utf-8');
            die($data);
        }
    }


    private function reset_null(&$item) {
        if(empty($item) && $item != 0) {
            $item = '';
        }
        $item = trim($item);
    }


    private function check_auth() {
        if (md5($this->s['mobassist_login'] . $this->s['mobassist_pass']) == $this->hash) {
            return true;
        }

        return false;
    }

    private function _validate_types($array, $names) {
        foreach ($names as $name => $type) {
            if (isset($array["$name"])) {
                switch ($type) {
                    case 'INT':
                        $array["$name"] = intval($array["$name"]);
                        break;
                    case 'FLOAT':
                        $array["$name"] = floatval($array["$name"]);
                        break;
                    case 'STR':
                        $array["$name"] = str_replace(array("\r", "\n"), ' ', addslashes(htmlspecialchars(trim(urldecode($array["$name"])))));
                        break;
                    case 'STR_HTML':
                        $array["$name"] = addslashes(trim(urldecode($array["$name"])));
                        break;
                    default:
                        $array["$name"] = '';
                }
            } else {
                $array["$name"] = '';
            }
        }

//        foreach ($array as $key => $value) {
//            if (!isset($names[$key]) && $key != "call_function" && $key != "hash") {
//                $array[$key] = "";
//            }
//        }

        return $array;
    }

//===============================================================================

    private function get_stores() {
        $this->load->model('setting/store');
        $all_stores[] = array('store_id' => 0, 'name' => $this->config->get('config_name'));

        $stores = $this->model_setting_store->getStores();

        foreach($stores as $store) {
            $all_stores[] = array('store_id' => $store['store_id'], 'name' => $store['name']);
        }

        return $all_stores;
    }


    private function get_currencies() {
        $this->load->model('localisation/currency');

        $currencies = $this->model_localisation_currency->getCurrencies();

        $all_currencies = array();

        foreach ($currencies as $currency) {
            $all_currencies[] = array('code' => $currency['code'], 'name' => $currency['title']);
        }

        return $all_currencies;
    }


    private function get_store_title() {
        if ($this->store_id > -1) {
            $this->load->model('setting/setting');
            $settings = $this->model_setting_setting->getSetting('config', $this->store_id);
            $title = $settings['config_name'];

        } else {
            $title = $this->config->get('config_name');
        }

        return array('test' => 1, 'title' => $title);
    }

    private function get_store_stats() {
        $data_graphs = '';
        $order_status_stats = array();
        $store_stats = array('count_orders' => "0", 'total_sales' => "0", 'count_customers' => "0", 'count_products' => "0", "last_order_id" => "0", "new_orders" => "0");
        $today = date("Y-m-d", time());
        $date_from = $date_to = $today;

        $data = array();

        if(!empty($this->stats_from)) {
            $date_from = $this->stats_from;
        }

        if(!empty($this->stats_to)) {
            $date_to = $this->stats_to;
        }

        if(isset($this->custom_period) && strlen($this->custom_period) > 0) {
            $custom_period = $this->get_custom_period($this->custom_period);

            $date_from = $custom_period['start_date'];
            $date_to = $custom_period['end_date'];
        }

        if(!empty($date_from)) {
            $data['date_from'] = $date_from . " 00:00:00";
        }

        if(!empty($date_to)) {
            $data['date_to'] = $date_to . " 23:59:59";
        }

        if(!empty($this->statuses)) {
            $data['statuses'] = $this->get_filter_statuses($this->statuses);
        }

        if ($this->store_id > -1) {
            $data['store_id'] = $this->store_id;
        }

        if (!empty($this->currency)) {
            $data['currency_code'] = $this->currency;
        }

        $this->load->model('mobileassistant/connector');


        $orders_stats = $this->model_mobileassistant_connector->getTotalOrders($data);
        $store_stats = array_merge($store_stats, $orders_stats);

        $customers_stats = $this->model_mobileassistant_connector->getTotalCustomers($data);
        $store_stats = array_merge($store_stats, $customers_stats);

        $products_stats = $this->model_mobileassistant_connector->getTotalSoldProducts($data);
        $store_stats = array_merge($store_stats, $products_stats);


        if(!isset($this->data_for_widget) || empty($this->data_for_widget) || $this->data_for_widget != 1) {
            $data_graphs = $this->get_data_graphs();
        }

        if (!isset($this->data_for_widget) || $this->data_for_widget != 1) {
            $order_status_stats = $this->get_status_stats();
        }

        $result = array_merge($store_stats, array('data_graphs' => $data_graphs), array('order_status_stats' => $order_status_stats));

        return $result;
    }


    private function get_data_graphs() {
        $data = array();

        if (empty($this->graph_from)) {
            $this->graph_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
        }
        $data['graph_from'] = $this->graph_from . " 00:00:00";

        if (empty($this->graph_to)) {
            if(!empty($this->stats_to)) {
                $this->graph_to = $this->stats_to;
            } else {
                $this->graph_to = date("Y-m-d", time());
            }
        }
        $data['graph_to'] = $this->graph_to . " 23:59:59";

        if(isset($this->custom_period) && strlen($this->custom_period) > 0) {
            $data['custom_period'] = $this->custom_period;
            $data['custom_period_date'] = $this->get_custom_period($this->custom_period);
        }

        if ($this->store_id > -1) {
            $data['store_id'] = $this->store_id;
        }

        if (!empty($this->statuses)) {
            $data['statuses'] = $this->get_filter_statuses($this->statuses);
        }

        $this->load->model('mobileassistant/connector');
        $chart_data= $this->model_mobileassistant_connector->getChartData($data);

        return $chart_data;
    }


    private function get_status_stats() {
        $today = date("Y-m-d", time());
        $date_from = $date_to = $today;

        $data = array();

        if(!empty($this->stats_from)) {
            $date_from = $this->stats_from;
        }

        if(!empty($this->stats_to)) {
            $date_to = $this->stats_to;
        }

        if(isset($this->custom_period) && strlen($this->custom_period) > 0) {
            $custom_period = $this->get_custom_period($this->custom_period);

            $date_from = $custom_period['start_date'];
            $date_to = $custom_period['end_date'];
        }

        if(!empty($date_from)) {
            $data['date_from'] = $date_from . " 00:00:00";
        }

        if(!empty($date_to)) {
            $data['date_to'] = $date_to . " 23:59:59";
        }

        if ($this->store_id > -1) {
            $data['store_id'] = $this->store_id;
        }

        if (!empty($this->currency)) {
            $data['currency_code'] = $this->currency;
        }


        $this->load->model('mobileassistant/connector');
        $order_statuses = $this->model_mobileassistant_connector->getOrderStatusStats($data);

        return $order_statuses;
    }


    private function get_orders() {
        $data = array();

        $this->load->model('mobileassistant/connector');

        if ($this->store_id > -1) {
            $data['store_id'] = $this->store_id;
        }

        if($this->statuses !== null && !empty($this->statuses)) {
            $data['statuses'] = $this->get_filter_statuses($this->statuses);
        }

        if (!empty($this->search_order_id)) {
            $data['search_order_id'] = $this->search_order_id;
        }

        if($this->orders_from !== null && !empty($this->orders_from)) {
            $data['orders_from'] = $this->orders_from . " 00:00:00";
        }

        if($this->orders_to !== null && !empty($this->orders_to)) {
            $data['orders_to'] = $this->orders_to . " 23:59:59";
        }

        if (!empty($this->currency)) {
            $data['currency_code'] = $this->currency;
        }

        if (!empty($this->get_statuses)) {
            $data['get_statuses'] = $this->get_statuses;
        }

        if($this->page !== null && !empty($this->page) && $this->show !== null && !empty($this->show)) {
            $data['page'] = ($this->page - 1) * $this->show;
            $data['show'] = $this->show;
        }

        if(!empty($this->sort_by)) {
            $data['sort_by'] = $this->sort_by;
        } else {
            $data['sort_by'] = "id";
        }

        $orders = $this->model_mobileassistant_connector->getOrders($data);
        return $orders;
    }

    private function get_orders_statuses() {
        $this->load->model('mobileassistant/connector');
        $order_statuses = $this->model_mobileassistant_connector->getOrdersStatuses();
        return $order_statuses;
    }


    private function get_orders_info() {
        $data = array();

        $this->load->model('mobileassistant/connector');

        $data['order_id'] = $this->order_id;
        $data['page'] = ($this->page - 1) * $this->show;
        $data['show'] = $this->show;

        if (!empty($this->currency)) {
            $data['currency_code'] = $this->currency;
        }

        $order_info = $this->model_mobileassistant_connector->getOrdersInfo($data);
        $order_products = $this->model_mobileassistant_connector->getOrderProducts($data);
        $count_prods = $this->model_mobileassistant_connector->getOrderCountProducts($data);
        $order_total = $this->model_mobileassistant_connector->getOrderTotals($data);

        $order_full_info = array("order_info" => $order_info, "order_products" => $order_products, "o_products_count" => $count_prods, "order_total" => $order_total);
        return $order_full_info;
    }


    private function get_customers() {
        $data = array();

        if (!empty($this->customers_from)) {
            $data['customers_from'] = $this->customers_from . " 00:00:00";
        }

        if (!empty($this->customers_to)) {
            $data['customers_to'] = $this->customers_to . " 23:59:59";
        }

        if(!empty($this->search_val)) {
            $data['search_val'] = $this->search_val;
        }

        if (!empty($this->cust_with_orders)) {
            $data['cust_with_orders'] = $this->cust_with_orders;
        }

        if ($this->store_id > -1) {
            $data['store_id'] = $this->store_id;
        }

        $data['page'] = ($this->page - 1) * $this->show;
        $data['show'] = $this->show;

        if(empty($this->sort_by)) {
            $data['sort_by'] = "id";
        } else {
            $data['sort_by'] = $this->sort_by;
        }


        $this->load->model('mobileassistant/connector');

        $customers = $this->model_mobileassistant_connector->getCustomers($data);

        return $customers;
    }


    private function get_customers_info() {
        $data = array();

        $data['page'] = ($this->page - 1) * $this->show;
        $data['show'] = $this->show;

        $data['user_id'] = $this->user_id;

        if (!empty($this->currency)) {
            $data['currency_code'] = $this->currency;
        }

        $this->load->model('mobileassistant/connector');

        return $this->model_mobileassistant_connector->getCustomersInfo($data);
    }


    private function search_products($ordered = false) {
        $data = array();

        if(!empty($this->params)) {
            $data['params'] = explode("|", $this->params);
        }

        if(!empty($this->val)) {
            $data['val'] = $this->val;
        }

        if (!empty($this->products_from)) {
            $data['products_from'] = $this->products_from . " 00:00:00";
        }

        if (!empty($this->products_to)) {
            $data['products_to'] = $this->products_to . " 23:59:59";
        }

        if(empty($this->sort_by)) {
            $data['sort_by'] = "id";
        } else {
            $data['sort_by'] = $this->sort_by;
        }

        if (!empty($this->currency)) {
            $data['currency_code'] = $this->currency;
        }

        if ($this->store_id > -1) {
            $data['store_id'] = $this->store_id;
        }

        if (!empty($this->statuses)) {
            $data['statuses'] = $this->get_filter_statuses($this->statuses);
        }

        $data['page'] = ($this->page - 1) * $this->show;
        $data['show'] = $this->show;

        $this->load->model('mobileassistant/connector');

        if($ordered) {
            return $this->model_mobileassistant_connector->getOrderedProducts($data);
        }

        return $this->model_mobileassistant_connector->getProducts($data);
    }


    private function search_products_ordered() {
        return $this->search_products(true);
    }


    private function get_products_info() {
        $data = array('currency_code' => $this->currency, 'product_id' => $this->product_id);

        $this->load->model('mobileassistant/connector');

        return $this->model_mobileassistant_connector->getProductInfo($data);
    }


    private function get_products_descr() {
        $data = array('product_id' => $this->product_id);

        $this->load->model('mobileassistant/connector');

        return $this->model_mobileassistant_connector->getProductDescr($data);
    }

//-----------------------------------

    private function set_order_action() {
        $this->load->model('mobileassistant/helper');

        if ($this->order_id <= 0) {
            $error = 'Order ID cannot be empty!';
            $this->model_mobileassistant_helper->write_log('ORDER ACTION ERROR: ' . $error);
            return array('error' => $error);
        }

        if(empty($this->action)) {
            $error = 'Action is not set!';
            $this->model_mobileassistant_helper->write_log('ORDER ACTION ERROR: ' . $error);
            return array('error' => $error);
        }

        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($this->order_id);

        if(!$order) {
            $error = 'Order not found!';
            $this->model_mobileassistant_helper->write_log('ORDER ACTION ERROR: ' . $error);
            return array('error' => $error);
        }

        if($this->action == 'change_status') {
            if (!isset($this->new_status) || intval($this->new_status) < 0) {
                $error = 'New order status is not set!';
                $this->model_mobileassistant_helper->write_log('ORDER ACTION ERROR: ' . $error);
                return array('error' => $error);
            }

            $notify = false;
            if (isset($this->notify_customer) && $this->notify_customer == 1) {
                $notify = true;
            }


            if($this->is_ver20) {
                $this->model_checkout_order->addOrderHistory(
                    $this->order_id,
                    $this->new_status,
                    $this->change_order_status_comment,
                    $notify
                );
            } else {
                $this->load->model('mobileassistant/connector');

                $this->model_mobileassistant_connector->addOrderHistory_156x(
                    $this->order_id,
                    $this->new_status,
                    $this->change_order_status_comment,
                    $notify
                );
            }

            return array('success' => 'true');
        }

        $error = 'Unknown error!';
        $this->model_mobileassistant_helper->write_log('ORDER ACTION ERROR: ' . $error);
        return array('error' => $error);
    }


    private function push_notification_settings() {
        $data = array();
        $this->load->model('mobileassistant/helper');

        if(empty($this->registration_id)) {
            $error = 'Empty device ID';
            $this->model_mobileassistant_helper->write_log('PUSH SETTINGS ERROR: ' . $error);
            return array('error' => $error);
        }

        if(empty($this->app_connection_id)) {
            $error = 'Empty connection ID';
            $this->model_mobileassistant_helper->write_log('PUSH SETTINGS ERROR: ' . $error);
            return array('error' => $error);
        }

        if(empty($this->api_key)) {
            $error = 'Empty application API key';
            $this->model_mobileassistant_helper->write_log('PUSH SETTINGS ERROR: ' . $error);
            return array('error' => $error);
        }


        $this->load->model('mobileassistant/setting');
        $s = $this->model_mobileassistant_setting->getSetting('mobassist');

        $s['mobassist_api_key'] = $this->api_key;

        $this->model_mobileassistant_setting->editSetting('mobassist', $s);

        $data['registration_id'] = $this->registration_id;
        $data['app_connection_id'] = $this->app_connection_id;
        $data['store_id'] = $this->store_id;
        $data['push_new_order'] = $this->push_new_order;
        $data['push_order_statuses'] = $this->push_order_statuses;
        $data['push_new_customer'] = $this->push_new_customer;
        $data['push_currency_code'] = $this->push_currency_code;

        if(!empty($this->registration_id_old)) {
            $data['registration_id_old'] = $this->registration_id_old;
        }

        $this->load->model('mobileassistant/connector');

        if($this->model_mobileassistant_connector->savePushNotificationSettings($data)) {
            return array('success' => 'true');
        }

        $error = 'Unknown occurred!';
        $this->model_mobileassistant_helper->write_log('PUSH SETTINGS ERROR: ' . $error);
        return array('error' => $error);
    }


//-------------- push

    public function push_new_order($order_id) {
        if(!$this->check_module_installed()) {
            return;
        }

        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($order_id);

        if(!$order) {
            return;
        }

        $type = self::PUSH_TYPE_NEW_ORDER;
        $this->sendOrderPushMessage($order, $type);
    }


    public function push_new_order_156x($order_id, $total = 0) {
        if(!$this->check_module_installed()) {
            return;
        }

        $this->load->model('sale/order');
        $order = $this->model_sale_order->getOrder($order_id);

        if(!$order) {
            return;
        }

        if(!isset($order['total']) || $order['total'] == 0) {
            $order['total'] = $total;
        }

        if(!isset($order['order_status'])) {
            if($order['order_status_id'] == 0) {
                $this->load->model('mobileassistant/helper');

                $default_attrs = $this->model_mobileassistanthelper->_get_default_attrs();
                $order['order_status'] = $default_attrs['text_missing'];
            } else {
                $sql = "SELECT name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int) $this->config->get('config_language_id') . "' AND order_status_id = '" . $order['order_status_id'] . "'";
                $query = $this->db->query($sql);
                if($query->num_rows) {
                    $order['order_status'] = $query->row['name'];
                } else {
                    $order['order_status'] = '';
                }
            }
        }

        $type = self::PUSH_TYPE_NEW_ORDER;
        $this->sendOrderPushMessage($order, $type);
    }


    public function push_change_status($order_id) {
        if(!$this->check_module_installed()) {
            return;
        }

        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($order_id);

        if(!$order) {
            return;
        }

        $type = self::PUSH_TYPE_CHANGE_ORDER_STATUS;
        $this->sendOrderPushMessage($order, $type);
    }


    public function push_change_status_156x($order_id, $data) {
        if(!$this->check_module_installed()) {
            return;
        }

        $this->load->model('sale/order');
        $order = $this->model_sale_order->getOrder($order_id);

        if(!$order) {
            return;
        }

        $order['order_status_id'] = $data['order_status_id'];

        if(!isset($order['order_status'])) {
            if($order['order_status_id'] == 0) {
                $this->load->model('mobileassistant/helper');

                $default_attrs = $this->model_mobileassistanthelper->_get_default_attrs();
                $order['order_status'] = $default_attrs['text_missing'];
            } else {
                $sql = "SELECT name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int) $this->config->get('config_language_id') . "' AND order_status_id = '" . $order['order_status_id'] . "'";
                $query = $this->db->query($sql);
                if($query->num_rows) {
                    $order['order_status'] = $query->row['name'];
                } else {
                    $order['order_status'] = '';
                }
            }
        }

        $type = self::PUSH_TYPE_CHANGE_ORDER_STATUS;
        $this->sendOrderPushMessage($order, $type);
    }


    private function sendOrderPushMessage($order, $type) {
        $data = array("store_id" => $this->config->get('config_store_id'), "type" => $type);

        if($type == self::PUSH_TYPE_CHANGE_ORDER_STATUS) {
            $data['status'] = $order['order_status_id'];
        }

        $push_devices = $this->getPushDevices($data);

        if(!$push_devices || count($push_devices) <= 0) {
            return;
        }


        $this->load->model('mobileassistant/helper');

        $url = HTTP_SERVER;
        $url = str_replace("http://", "", $url);
        $url = str_replace("https://", "", $url);

        foreach($push_devices as $push_device) {
            if(empty($push_device['push_currency_code']) || $push_device['push_currency_code'] == 'not_set') {
                $currency_code = (isset($order['currency_code']) ? $order['currency_code'] : $order['currency']);

            } else if($push_device['push_currency_code'] == 'base_currency') {
                $currency_code = $this->config->get('config_currency');

            } else {
                $currency_code = $push_device['push_currency_code'];
            }

            $message = array(
                "push_notif_type" => $type,
                "order_id" => $order['order_id'],
                "customer_name" => $order['firstname'] . ' ' . $order['lastname'],
                "email" => $order['email'],
                "new_status" => $order['order_status'],
                "total" => $this->model_mobileassistant_helper->nice_price($order['total'], $currency_code),
                "store_url" => $url,
                "app_connection_id" => $push_device['app_connection_id']
            );

            $this->sendPush2Google($push_device['setting_id'], $push_device['registration_id'], $message);
        }
    }


    public function push_new_customer($customer_id) {
        if(!$this->check_module_installed()) {
            return;
        }

        $this->load->model('account/customer');
        $customer = $this->model_account_customer->getCustomer($customer_id);

        if(!$customer) {
            return;
        }

        $this->sendCustomerPushMessage($customer);
    }

    public function push_new_customer_156x($customer_id) {
        if(!$this->check_module_installed()) {
            return;
        }

        $this->load->model('sale/customer');
        $customer = $this->model_sale_customer->getCustomer($customer_id);

        if(!$customer) {
            return;
        }

        $this->sendCustomerPushMessage($customer);
    }

    public function sendCustomerPushMessage($customer) {
        $type = self::PUSH_TYPE_NEW_CUSTOMER;
        $data = array("store_id" => $this->config->get('config_store_id'), "type" => $type);

        $push_devices = $this->getPushDevices($data);

        if(!$push_devices || count($push_devices) <= 0) {
            return;
        }


        $url = HTTP_SERVER;
        $url = str_replace("http://", "", $url);
        $url = str_replace("https://", "", $url);

        foreach($push_devices as $push_device) {
            $message = array(
                "push_notif_type" => $type,
                "customer_id" => $customer['customer_id'],
                "customer_name" => $customer['firstname'] . ' ' . $customer['lastname'],
                "email" => $customer['email'],
                "store_url" => $url,
                "app_connection_id" => $push_device['app_connection_id']
            );

            $this->sendPush2Google($push_device['setting_id'], $push_device['registration_id'], $message);
        }
    }


    private function sendPush2Google($setting_id, $registration_id, $message) {
        $this->load->model('mobileassistant/setting');
        $s = $this->model_mobileassistant_setting->getSetting('mobassist');

        $apiKey = $s['mobassist_api_key'];
        $headers = array('Authorization: key=' . $apiKey, 'Content-Type: application/json');

        $post_data = array(
            'registration_ids' => array($registration_id),
            'data' => array("message" => $message)
        );
        $post_data = json_encode($post_data);

        if(self::DEBUG_MODE) {
            $this->load->model('mobileassistant/helper');
            $this->model_mobileassistant_helper->write_log('PUSH REQUEST DATA: ' . $post_data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $response = curl_exec($ch);

        $info = curl_getinfo($ch);

        $this->onResponse($setting_id, $response, $info);
    }


    public function onResponse($setting_id, $response, $info) {
        $code = $info != null && isset($info['http_code']) ? $info['http_code'] : 0;

        $this->load->model('mobileassistant/helper');

        $codeGroup = (int)($code / 100);
        if ($codeGroup == 5) {
            $this->model_mobileassistant_helper->write_log('PUSH RESPONSE: code: '.$code.' :: GCM server not available');
            return;
        }
        if ($code !== 200) {
            $this->model_mobileassistant_helper->write_log('PUSH RESPONSE: code: '.$code);
            return;
        }
        if (!$response || strlen(trim($response)) == null) {
            $this->model_mobileassistant_helper->write_log('PUSH RESPONSE: null response');
            return;
        }

        if ($response) {
            $json = json_decode($response, true);
            if (!$json) {
                $this->model_mobileassistant_helper->write_log('PUSH RESPONSE: json decode error');
            }
        }

        $failure = isset($json['failure']) ? $json['failure'] : null;
        $canonicalIds = isset($json['canonical_ids']) ? $json['canonical_ids'] : null;

        if ($failure || $canonicalIds) {
            $results = isset($json['results']) ? $json['results'] : array();
            foreach($results as $result) {
                $newRegId = isset($result['registration_id']) ? $result['registration_id'] : null;
                $error = isset($result['error']) ? $result['error'] : null;
                if ($newRegId) {
                    $this->updatePushRegId($setting_id, $newRegId);

                } else if ($error) {
                    if ($error == 'NotRegistered' || $error == 'InvalidRegistration') {
                        $this->deletePushRegId($setting_id);
                    }
                    $this->model_mobileassistant_helper->write_log('PUSH RESPONSE: error: ' . $error);
                }
            }
        }
    }


    public function updatePushRegId($setting_id, $new_reg_id) {
        $sql = "UPDATE " . DB_PREFIX . "mobileassistant_push_settings SET registration_id = '%s' WHERE setting_id = '%d'";
        $sql = sprintf($sql, $new_reg_id, $setting_id);
        $this->db->query($sql);
    }

    public function deletePushRegId($setting_id) {
        $sql = "DELETE FROM " . DB_PREFIX . "mobileassistant_push_settings WHERE setting_id = '%d'";
        $sql = sprintf($sql, $setting_id);
        $this->db->query($sql);
    }

    public function getPushDevices($data = array()) {
        $sql = "SELECT setting_id, registration_id, app_connection_id, push_currency_code FROM " . DB_PREFIX . "mobileassistant_push_settings";

        switch ($data['type']) {
            case self::PUSH_TYPE_NEW_ORDER:
                $query_where[] = " push_new_order = '1' ";
                break;

            case self::PUSH_TYPE_CHANGE_ORDER_STATUS:
                $query_where[] = sprintf(" (push_order_statuses = '%s' OR push_order_statuses LIKE '%%|%s' OR push_order_statuses LIKE '%s|%%' OR push_order_statuses LIKE '%%|%s|%%' OR push_order_statuses = '-1') ", $data['status'], $data['status'], $data['status'], $data['status']);
                break;

            case self::PUSH_TYPE_NEW_CUSTOMER:
                $query_where[] = " push_new_customer = '1' ";
                break;

            default:
                return false;
        }

        $query_where[] = sprintf(" (store_id = '-1' OR store_id = '%d') ", $data['store_id']);

        if (!empty($query_where)) {
            $sql .= " WHERE " . implode(" AND ", $query_where);
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }



//-------//-------//-------//-------//-------

    private function check_module_installed() {
        $this->load->model('mobileassistant/setting');
        $s = $this->model_mobileassistant_setting->getSetting('mobassist');

        if($s && isset($s['mobassist_installed']) && $s['mobassist_installed'] == 1) {
            return true;
        }
        return false;
    }

    private function get_filter_statuses($statuses) {
        $statuses = explode("|", $statuses);
        if(!empty($statuses)) {
            $stat = array();
            foreach($statuses as $status) {
                if($status != "") {
                    $stat[] = $status;
                }
            }
            $parse_statuses = implode("','", $stat);
            return $parse_statuses;
        }

        return $statuses;
    }

    private function get_custom_period($period = 0) {
        $custom_period = array('start_date' => "", 'end_date' => "");
        $format = "m/d/Y";

        switch($period) {
            case 0: //3 days
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date("m"), date("d")-2, date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date("m"), date("d"), date("Y")));
                break;

            case 1: //7 days
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date("m"), date("d")-6, date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date("m"), date("d"), date("Y")));
                break;

            case 2: //Prev week
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date("n"), date("j")-6, date("Y")) - ((date("N"))*3600*24));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date("n"), date("j"), date("Y")) - ((date("N"))*3600*24));
                break;

            case 3: //Prev month
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date("m")-1, 1, date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date("m"), date("d")-date("j"), date("Y")));
                break;

            case 4: //This quarter
                $m = date("n");
                $start_m = 1;
                $end_m = 3;

                if($m <= 3) {
                    $start_m = 1;
                    $end_m = 3;
                } else if($m >= 4 && $m <= 6) {
                    $start_m = 4;
                    $end_m = 6;
                } else if($m >= 7 && $m <= 9) {
                    $start_m = 7;
                    $end_m = 9;
                } else if($m >= 10) {
                    $start_m = 10;
                    $end_m = 12;
                }

                $custom_period['start_date'] = date($format, mktime(0, 0, 0, $start_m, 1 , date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, $end_m+1, date(1)-1, date("Y")));
                break;

            case 5: //This year
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date(1), date(1), date("Y")));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date(1), date(1)-1, date("Y")+1));
                break;

            case 6: //Last year
                $custom_period['start_date'] = date($format, mktime(0, 0, 0, date(1), date(1), date("Y")-1));
                $custom_period['end_date'] = date($format, mktime(23, 59, 59, date(1), date(1)-1, date("Y")));
                break;
        }

        return $custom_period;
    }
}