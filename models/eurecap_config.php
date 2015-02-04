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

	/** 
	 * Process the passed settings array and load all missing values from the xml file
	 */
	function loadDefaults($settings_str) {
		$path = VMPATH_ROOT .DS. 'plugins' .DS. 'vmextended' . DS . 'eurecap' . DS . 'eurecap' . '.xml';
		if(!defined('VM_VERSION') or VM_VERSION < 3){
			if (!class_exists('vmParameters'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'parameterparser.php');
			$parameters = new vmParameters($settings_str, 'eurecap', 'plugin', 'vmextended');
// 			$parameters->loadString($settings_str);
			
			$settings = array();
			// The getParams returns the rendered representation, but also the value, including the default given in the XML.
			// AFAICS, this is the only way to correctly handle the default, apart from manually parsing the xml file.
			foreach ($parameters->getParams() as $param) {
				if (!empty($param[5]))
					$settings[$param[5]] = $param[4];
			}

		} else {
			// VM 3.x uses JForm
			$defaults = array();
			
			$registry = new JRegistry;
			$registry->loadString($settings_str);
			// Take the settings and load all missing values from the defaults in the xml file:
			$settings = $registry->toArray();

			$form = vmPlugin::loadConfigForm($path, 'eurecap');
			$form->bind(array('settings'=>$settings));
			$fieldSets = $form->getFieldsets();
			foreach ($fieldSets as $name => $fieldSet) {
				foreach ($form->getFieldset($name) as $field) {
					$fieldname = (string)$field->fieldname;
					$settings[$fieldname] = $field->value;
				}
			}
		}
		// Manually convert all default values for list params to lists (not handled automatically)
		if (!is_array($settings['order_status']))
			$settings['order_status'] = array_filter(explode(',', $settings['order_status']));
		if (!is_array($settings['vatid_userfield']))
			$settings['vatid_userfield'] = array_filter(explode(',', $settings['vatid_userfield']));
		if (!is_array($settings['countries']))
			$settings['countries'] = array_filter(explode(',', $settings['countries']));

		return $settings;
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
			// Take the settings and load all missing values from the defaults in the xml file:
			$this->_settings = $this->loadDefaults($config_str);
		}
		return $this->_settings;
	}
	
	function saveConfig($data) {
		$settings = array();
		if (isset($data['params'])) 
			$settings = $data['params'];
		if (isset($data['settings'])) 
			$settings = $data['settings'];
		$settings = array_merge ($this->getConfig(), $settings);
		$this->_settings = $settings;

		$registry = new JRegistry();
		$registry->loadArray($settings);

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
