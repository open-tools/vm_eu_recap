<?php
defined('_JEXEC') or die();

/**
*
* @package VirtueMart
* @subpackage EU Recapitulative Statement
* @copyright Copyright (C) 2015 Open Tools, Reinhold Kainhofer.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* http://www.open-tools.net
*/

JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');

class JFormFieldEUCountries extends JFormFieldList {
	var $type = 'euCountries';

	protected function getInput() {
		$this->multiple=true;
		return parent::getInput();
	}
	protected function getOptions() {
		$options = array();
		$this->multiple=true;

		$query = 'SELECT `virtuemart_country_id` AS value, `country_name` AS text FROM `#__virtuemart_countries`
               		WHERE `published` = 1 AND `country_2_code` IN ("AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SI", "SK", "ES", "SE", "GB") ORDER BY `country_name` ASC ';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$values = $db->loadObjectList();
		foreach ($values as $v) {
			$options[] = JHtml::_('select.option', $v->value, $v->text);
		}

		//BAD $class = 'multiple="true" size="10"';
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}