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

class VirtuemartModelEuRecap extends VmModel {

	public $from_date = '';
	public $until_date = '';
	public $from = '';
	public $until = '';
	protected $frequency = 3;

	function __construct () {

		parent::__construct ();
		$this->setMainTable ('orders');

		$app = JFactory::getApplication ();

// 		$this->setPeriod ();

		$this->removevalidOrderingFieldName ('virtuemart_order_id');
		$this->addvalidOrderingFieldName (array('`vatid`', '`order_ids`', '`company`', '`last_name`', '`countrycode`', '`sum_order_total`', '`sum_order_tax`'));
		$this->_selectedOrdering = '`vatid`';

	}

	function getFrequency() {
		return $this->frequency;
	}
	function setFrequency($freq = 3) {
		$this->frequency = $freq;
	}

	function correctTimeOffset(&$inputDate) {

		$config = JFactory::getConfig();
		$this->siteOffset = $config->get('offset');

		$date = new JDate($inputDate);

		$date->setTimezone($this->siteTimezone);
		$inputDate = $date->format('Y-m-d H:i:s',true);
	}

	/*
	* Set Start & end Date
	*/
	function  setPeriod ($year, $month, $frequency) {
		$this->setFrequency($frequency);

		$this->from = mktime(0,0,0, $month, 1, $year);
		$this->until = strtotime('+'.$this->frequency.' months -1 day', $this->from);

		$this->from_date = date ('Y-m-d', $this->from);
		$this->until_date = date ('Y-m-d', $this->until);

		$config = JFactory::getConfig();
		$siteOffset = $config->get('offset');
		$this->siteTimezone = new DateTimeZone($siteOffset);

		$this->correctTimeOffset($this->from_date);
		$this->correctTimeOffset($this->until_date);

	}

	function getEuRecap() {
		$user = JFactory::getUser();
		if($user->authorise('core.admin', 'com_virtuemart') or $user->authorise('core.manager', 'com_virtuemart')){
			$vendorId = vRequest::getInt('virtuemart_vendor_id');
		} else {
			$vendorId = VmConfig::isSuperVendor();
		}
		$settingsModel = VmModel::getModel("eurecap_config");
		$settings = $settingsModel->getConfig();

		$freq = $settings['frequency'];
		$month = floor( (date("m")-1)/$freq)*$freq+1;
		$year = vRequest::getVar ('year', date("Y"));
		$month = vRequest::getVar ('month', $month);
		$this->setPeriod($year, $month, $freq);

		$mainTable = "`#__virtuemart_orders` AS `o`";
		$joins = array();
		$joins[] = "LEFT JOIN `#__virtuemart_order_userinfos` AS `ui` ON (`o`.`virtuemart_order_id` = `ui`.`virtuemart_order_id` )";
		$joins[] = "LEFT JOIN `#__virtuemart_countries` AS `ctr` ON (`ui`.`virtuemart_country_id` = `ctr`.`virtuemart_country_id` )";

		$vatfields = array();
		foreach ($settings['vatid_userfield'] as $vatfield) {
			$vatfields[] = "`ui`.`$vatfield`";
		}
		$vatfields[] = "''"; // <= To make sure we have at least one entry!
		$select = array();
		$vatidexpr = "COALESCE(" . join(", ", $vatfields) . ")";
		$select[] = $vatidexpr . " AS `vatid`";
		$select[] = "GROUP_CONCAT( `o`.`virtuemart_order_id` ) AS `order_ids`";
		$select[] = "GROUP_CONCAT( `order_number` ) AS `order_numbers`";
		$select[] = "`o`.`virtuemart_user_id`";
		$select[] = "`company` AS `company`";
		$select[] = "`first_name`";
		$select[] = "`last_name`";
		$select[] = "`ctr`.`country_2_code` AS `countrycode`";

		$select[] = 'SUM( `order_total` ) AS `sum_order_total`';

		$select[] = 'SUM( `order_tax` ) AS `sum_order_tax`';


		$where = array();
		$where[] = $vatidexpr . " <> ''";
		if ($settings['include_free']==0) {
			$where[] = "`o`.`order_total` > 0";
		}
		if ($settings['include_taxed_orders']==0) {
			$where[] = "`o`.`order_tax` = 0";
		}
		$where[] = '`ui`.`address_type` = "BT"';

		// Order status:
		$ostatus = array();
		foreach ($settings['order_status'] as $s) {
			$ostatus[] = '`o`.`order_status` = "' . $s . '"';
		}
		if ($ostatus) {
			$where[] = "(" . join(" OR ", $ostatus) . ")";
		}

		// Countries:
		if (!empty($settings['countries'])) {
			$where[] = '`ui`.`virtuemart_country_id` IN (' . join(",", $settings['countries']) . ')';
		}

		// Shopper group:
		if ($settings['shopper_groups']) {
// 			$where[] =
		}

		// TODO: Handle vendorId
		if (VmConfig::get ('multix', 'none') != 'none') {
			if ($vendorId != 0) {
				$where[] = '`o`.`virtuemart_vendor_id` = "' . $vendorId . '" ';
			}
		}

		// TODO: Handle creation date!
		switch($settings['taxation_moment']) {
			case 'status':
				// TODO: Handle first status change:
				$tax_moment = "`o`.`created_on`";
				JFactory::getApplication()->enqueueMessage("Taxation moment 'First status change to any of the selected statuses' not yet implemented", 'warning');
				break;
			case 'payment':
				// TODO: Handle payment:
				$tax_moment = "`o`.`created_on`";
				// 				'o.virtuemart_paymentmethod_id'
				JFactory::getApplication()->enqueueMessage("Taxation moment 'When payment is made' not yet implemented", 'warning');
				break;
			case 'invoice':
				$tax_moment = "`inv`.`created_on`";
				$joins[] = "LEFT JOIN `#__virtuemart_invoices` AS `inv` ON ( `o`.`virtuemart_order_id` = `inv`.`virtuemart_order_id` )";
				break;
			case 'placement':
			default:
				$tax_moment = "`o`.`created_on`";
				break;
		}

		$where[] = ' DATE( ' . $tax_moment . ' ) BETWEEN "' . $this->from_date . '" AND "' . $this->until_date . '" ';

		$selectString = join(', ', $select) . ' FROM ' . $mainTable;
		$joinedTables = join('', $joins);
		$whereString = 'WHERE ' . join(' AND ', $where);
		$groupBy = "GROUP BY `vatid`";
		$orderBy = $this->_getOrdering ();

		return $this->exeSortSearchListQuery (1, $selectString, $joinedTables, $whereString, $groupBy, $orderBy);
	}


	public function renderMonthSelectList ($frequency, $selected='') {
		$vals = array();
		switch ($frequency) {
			case 1: // monthly
				$vals[1] = vmText::_('January');
				$vals[2] = vmText::_('February');
				$vals[3] = vmText::_('March');
				$vals[4] = vmText::_('April');
				$vals[5] = vmText::_('May');
				$vals[6] = vmText::_('June');
				$vals[7] = vmText::_('July');
				$vals[8] = vmText::_('August');
				$vals[9] = vmText::_('September');
				$vals[10] = vmText::_('October');
				$vals[11] = vmText::_('November');
				$vals[12] = vmText::_('December');
				break;

			case 3: // quartely
				$vals[1] = vmText::_('First Quarter');
				$vals[3] = vmText::_('Second Quarter');
				$vals[6] = vmText::_('Third Quarter');
				$vals[9] = vmText::_('Forth Quarter');
				break;

			case 12: // yearly
				$vals[1] = vmText::_('Year');
				break;
		}
		$options = array();
		foreach ($vals as $month=>$label) {
			$options[] = JHtml::_ ('select.option', $label, $month);
		}
		$listHTML = JHtml::_ ('select.genericlist', $options, 'month', 'size="7" class="inputbox" onchange="this.form.submit();" ', 'text', 'value', $selected);

		return $listHTML;
	}

	public function renderYearSelectList ($selected='') {
		$options = array();
		foreach (range(2010, 2020) as $year) {
			$options[] = JHtml::_ ('select.option', $year, $year);
		}
		$listHTML = JHtml::_ ('select.genericlist', $options, 'year', 'size="7" class="inputbox" onchange="this.form.submit();" ', 'text', 'value', $selected);
		return $listHTML;
	}

}
