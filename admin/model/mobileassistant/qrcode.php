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

include_once (dirname(__FILE__) . '/phpqrcode/qrlib.php');

class Modelmobileassistantqrcode extends Model {

    public function get_QR_img() {
        $this->load->model('setting/setting');

        $s = $this->model_setting_setting->getSetting('mobassist');

        if(empty($s)) {
            return '';
        }

        $filedir = dirname(__FILE__) . '/phpqrcode/img/';
        //@chmod($filedir, 777);

        if(!is_writable($filedir)) {
            return false;
        }

        $this->clearCache($filedir);

        $config = array(
            'url' => HTTP_CATALOG,
            'login' => $s['mobassist_login'],
            'password' => $s['mobassist_pass'],
        );

        $config = base64_encode(json_encode($config));

        $file_name = md5(time() . 'mobileassistantqrcode') . '.png';

        $filepath = $filedir . $file_name;

        QRcode::png($config, $filepath, QR_ECLEVEL_L, 5, 0);

        return HTTP_CATALOG . "admin/model/mobileassistant/phpqrcode/img/" . $file_name;
    }


    private function clearCache($filedir) {
        $dir = dir($filedir);
        while (false !== ($entry = $dir->read())) {
            if ( $entry != '.' && $entry != '..'&& $entry != 'index.php') {
                @unlink($filedir . '/' . $entry);
            }
        }
    }
}

?>