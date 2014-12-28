<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

/* [Searchanise] */

class ModelModuleSearchanise extends Model {
	public function searchaniseCreateModuleTables() {
		$this->searchaniseRemoveTables();
		
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "se_settings` ( "
				. "`name` varchar(32) NOT NULL default '', "
				. "`store_id` int(11) NOT NULL DEFAULT '0', "
				. "`lang_code` char(5) NOT NULL default 'en', "
				. "`value` varchar(255) NOT NULL default '', "
				. "PRIMARY KEY  (`name`, `store_id`, `lang_code`) "
			. ") ENGINE=MyISAM DEFAULT CHARSET=UTF8"
		);

		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "se_queue` ( "
				. "`queue_id` mediumint NOT NULL auto_increment, "
				. "`data` text NOT NULL, "
				. "`action` varchar(32) NOT NULL default '', "
				. "`store_id` int(11) NOT NULL DEFAULT '0', "
				. "`lang_code` char(5) NOT NULL default 'en', "
				. "`started` int(11) NOT NULL DEFAULT '0', "
				. "`error_count` int NOT NULL default 0, "
				. "`status` enum('pending', 'processing') default 'pending', "
				. "PRIMARY KEY  (`queue_id`), "
				. "KEY (`status`) "
			. ") ENGINE=MyISAM DEFAULT CHARSET=UTF8"
		);
	}

	public function searchaniseRemoveTables() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "se_queue`");
	}
}

?>