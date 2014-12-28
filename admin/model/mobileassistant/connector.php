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

    public function create_push_table() {
        $this->db->query("
		  CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mobileassistant_push_settings` (
              `setting_id` int(11) NOT NULL AUTO_INCREMENT,
              `registration_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `app_connection_id` int(5) NOT NULL,
              `store_id` int(5) NOT NULL,
              `push_new_order` tinyint(1) NOT NULL DEFAULT '0',
              `push_order_statuses` text COLLATE utf8_unicode_ci NOT NULL,
              `push_new_customer` tinyint(1) NOT NULL DEFAULT '0',
              `push_currency_code` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`setting_id`)
		)");
    }


    public function drop_push_table() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mobileassistant_push_settings`");
    }

}