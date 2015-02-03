<?php
if (!defined ('_JEXEC')) {
	die('Direct Access to ' . basename (__FILE__) . ' is not allowed.');
}

/**
 *
 * EU Recapitulative Statement Model
 *
 * @author Reinhold Kainhofer
 * @version $Id$
 * @package VirtueMart
 * @subpackage EU Recapitulative Statement
 * @copyright Copyright (C) 2011 - 2014VirtueMart Team - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://www.open-tools.net
 */

if (!class_exists ('VmModel')) {
	require(VMPATH_ADMIN . DS . 'helpers' . DS . 'vmmodel.php');
}

class VirtuemartModelEuRecap_config extends VmModel {
	protected $_settings = array();
	static $element = 'eurecap';
	static $folder = 'vmextended';
	static $type = 'plugin';

	function __construct () {
		parent::__construct ();
	}

	function getConfig() {
		if (!$this->_settings) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('custom_data'))
				->from('#__extensions')
				->where($db->quoteName('type') . ' = ' . $db->quote(self::$type))
				->where($db->quoteName('folder') . ' = ' . $db->quote(self::$folder))
				->where($db->quoteName('element') . ' = ' . $db->quote(self::$element));
			$db->setQuery($query);

			$config_str = $db->loadResult();
			$registry = new JRegistry;
			$registry->loadString($config_str);
			$this->_settings = $registry->toArray();
		}
		return $this->_settings;
	}
	
	function saveConfig($data) {
		$params = array_merge ($this->getConfig(), $data['settings']);
		$this->_settings = $params;

		$registry = new JRegistry();
		$registry->loadArray($params);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__extensions'))
			->set($db->quoteName('custom_data') . ' = ' . $db->quote($registry->toString()))
			->where($db->quoteName('type') . ' = ' . $db->quote(self::$type))
			->where($db->quoteName('folder') . ' = ' . $db->quote(self::$folder))
			->where($db->quoteName('element') . ' = ' . $db->quote(self::$element));
		$db->setQuery($query);

		$result = $db->execute();
		return $result;
	}

}
