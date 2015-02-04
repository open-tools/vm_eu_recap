<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/**
*
* @version $Id$
* @package VirtueMart
* @subpackage EU Recapitulative Statement
* @copyright Copyright (C) 2015 Open Tools, Reinhold Kainhofer
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://www.open-tools.net/
*/

AdminUIHelper::startAdminArea($this);

JHtml::_('behavior.framework', true);

?>
<form name="adminForm" id="adminForm" method="post" action="">
	<?php AdminUIHelper::imitateTabs('start'); ?>
    <fieldset>
	<legend><?php echo vmText::_('VMEXT_EU_RECAP_SETTINGS_TITLE'); ?></legend>
<?php if (isset($this->config_form)) {
	// VM 3 uses JForm and the formrenderer.php
	$form = $this->config_form;
	include (VMPATH_ADMIN.DS.'fields'.DS.'formrenderer.php');
} elseif ($this->config_params) {
	// VM 2 uses vmParameters
	echo $this->config_params->render();
} else { ?>
	<p><?php echo vmText::_('VMEXT_EU_RECAP_FORM_NOTFOUND'); ?></p>
<?php 
}
?>
    </fieldset>
	<?php echo $this->addStandardHiddenToForm(); ?>
	<?php AdminUIHelper::imitateTabs('end'); ?>
</form>


<?php AdminUIHelper::endAdminArea(); ?>

