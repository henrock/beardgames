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
    const MODULE_CODE = 2;
    const MODULE_VERSION = '1.0.4';

    private $error = array();
    private $is_ver20;


    public function install() {
        $this->is_ver20 = version_compare(VERSION, '2.0.0.0', '>=');

        $this->load->model('setting/setting');

        $module_settings = array(
            'mobassist_installed' => '1',
            'mobassist_status' => '1',
            'mobassist_module_code' => self::MODULE_CODE,
            'mobassist_module_version' => self::MODULE_VERSION,
            'mobassist_login' => 1,
            'mobassist_pass' => md5(1)
        );

        $this->model_setting_setting->editSetting('mobassist', $module_settings);

        if($this->is_ver20) {
            if(version_compare(VERSION, '2.0.1.0', '>=')) {
                $this->load->model('extension/event');
                $this->model_extension_event->addEvent('mobileassistantconnector', 'post.order.add', 'module/mobileassistantconnector/push_new_order');
                $this->model_extension_event->addEvent('mobileassistantconnector', 'post.order.history.add', 'module/mobileassistantconnector/push_change_status');
                $this->model_extension_event->addEvent('mobileassistantconnector', 'post.customer.add', 'module/mobileassistantconnector/push_new_customer');

            } else {
                $this->load->model('tool/event');
                $this->model_tool_event->addEvent('mobileassistantconnector', 'post.order.add', 'module/mobileassistantconnector/push_new_order');
                $this->model_tool_event->addEvent('mobileassistantconnector', 'post.order.history.add', 'module/mobileassistantconnector/push_change_status');
                $this->model_tool_event->addEvent('mobileassistantconnector', 'post.customer.add', 'module/mobileassistantconnector/push_new_customer');
            }
        }

        $this->load->model('mobileassistant/connector');
        $this->model_mobileassistant_connector->create_push_table();
    }


    public function uninstall() {
        $this->is_ver20 = version_compare(VERSION, '2.0.0.0', '>=');

        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('mobassist');

        if($this->is_ver20) {
            if(version_compare(VERSION, '2.0.1.0', '>=')) {
                $this->load->model('extension/event');
                $this->model_extension_event->deleteEvent('mobileassistantconnector');

            } else {
                $this->load->model('tool/event');
                $this->model_tool_event->deleteEvent('mobileassistantconnector');
            }
        }

        $this->load->model('mobileassistant/connector');
        $this->model_mobileassistant_connector->drop_push_table();
    }


    public function index() {
        $this->is_ver20 = version_compare(VERSION, '2.0.0.0', '>=');

        $this->load->model('setting/setting');
        $s = $this->model_setting_setting->getSetting('mobassist');
        $s['mobassist_module_code'] = self::MODULE_CODE;
        $s['mobassist_module_version'] = self::MODULE_VERSION;

        $this->model_setting_setting->editSetting('mobassist', $s);

        $this->createForm();
    }


    private function createForm() {
        $this->load->language('module/mobileassistantconnector');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        $s = $this->model_setting_setting->getSetting('mobassist');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if(isset($s['mobassist_pass']) && isset($this->request->post['mobassist_pass'])) {
                if($this->request->post['mobassist_pass'] != "" && $s['mobassist_pass'] != $this->request->post['mobassist_pass']) {
                    $this->request->post['mobassist_pass'] = md5($this->request->post['mobassist_pass']);
                }
            }

            $this->model_setting_setting->editSetting('mobassist', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            if(isset($this->request->post['save_continue']) && $this->request->post['save_continue'] == 1) {
                $url = $this->url->link('module/mobileassistantconnector', 'token=' . $this->session->data['token'], 'SSL');
            } else {
                $url = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
            }

            if($this->is_ver20) {
                $this->response->redirect($url);
            } else {
                $this->redirect($url);
            }
        }

        $d = array();

        $d['is_ver20'] = $this->is_ver20;

        if (isset($this->session->data['success'])) {
            $d['saving_success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $d['saving_success'] = '';
        }

        if(!isset($s['mobassist_status'])) {
            $s['mobassist_status'] = 0;
        }
        if(!isset($s['mobassist_login'])) {
            $s['mobassist_login'] = 1;
        }
        if(!isset($s['mobassist_pass'])) {
            $s['mobassist_pass'] = md5(1);
        }

        $d['settings'] = $s;

        if ($s['mobassist_login'] == 1 && $s['mobassist_pass'] == md5(1)) {
            $d['message_info'] = $this->language->get('error_default_cred');;
        } else {
            $d['message_info'] = '';
        }

        $this->load->model('mobileassistant/qrcode');
        if($qrcode_url = $this->model_mobileassistant_qrcode->get_QR_img()) {
            $d['qrcode_url'] = $qrcode_url;
        }

        $d['heading_title'] = $this->language->get('heading_title');

        $d['text_enabled'] = $this->language->get('text_enabled');
        $d['text_disabled'] = $this->language->get('text_disabled');
        $d['text_edit'] = $this->language->get('text_edit');

        $d['entry_login'] = $this->language->get('entry_login');
        $d['help_login'] = $this->language->get('help_login');

        $d['entry_pass'] = $this->language->get('entry_pass');
        $d['help_pass'] = $this->language->get('help_pass');

        $d['entry_qr'] = $this->language->get('entry_qr');
        $d['help_qr'] = $this->language->get('help_qr');

        $d['entry_status'] = $this->language->get('entry_status');

        $d['module_version'] = $this->language->get('module_version');
        $d['connector_version'] = self::MODULE_VERSION;

        $d['useful_links'] = $this->language->get('useful_links');
        $d['check_new_version'] = $this->language->get('check_new_version');
        $d['submit_ticket'] = $this->language->get('submit_ticket');
        $d['documentation'] = $this->language->get('documentation');

        $d['button_save'] = $this->language->get('button_save');
        $d['button_save_continue'] = $this->language->get('button_save_continue');
        $d['button_cancel'] = $this->language->get('button_cancel');

        $d['error_login_details_changed'] = $this->language->get('error_login_details_changed');

        if (isset($this->error['warning'])) {
            $d['error_warning'] = $this->error['warning'];
        } else {
            $d['error_warning'] = '';
        }

        $d['breadcrumbs'] = array();

        $d['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $d['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $d['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('module/mobileassistantconnector', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $d['action'] = $this->url->link('module/mobileassistantconnector', 'token=' . $this->session->data['token'], 'SSL');

        $d['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');


        if($this->is_ver20) {
            if(!isset($data) || !is_array($data)) {
                $data = array();
            }
            $data = array_merge($data, $d);

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('module/mobileassistant.tpl', $data));

        } else {
            $this->data = array_merge($this->data, $d);

            $this->load->model('design/layout');

            $this->data['layouts'] = $this->model_design_layout->getLayouts();

            $this->template = 'module/mobileassistant.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );
            $this->response->setOutput($this->render());
        }
    }


    protected function validate() {
        $error = true;
        if (!$this->user->hasPermission('modify', 'module/mobileassistantconnector')) {
            $this->error['warning'] = $this->language->get('error_permission');
            $error = false;
        }

        if (isset($this->request->post['mobassist_login']) && strlen($this->request->post['mobassist_login']) <= 0) {
            $this->error['warning'] = $this->language->get('error_empty_login');
            $error = false;
        }

        return $error;
    }
}