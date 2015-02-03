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
    <fieldset>
	<legend><?php echo vmText::_('VMEXT_EU_RECAP_SETTINGS_TITLE'); ?></legend>
<?php if ($this->config_form) {
	$form = $this->config_form;
	include(VMPATH_ADMIN.DS.'fields'.DS.'formrenderer.php');
} else { ?>
	<p><?php echo vmText::_('VMEXT_EU_RECAP_FORM_NOTFOUND'); ?></p>
<?php 
}
?>
    </fieldset>
	<?php echo $this->addStandardHiddenToForm(); ?>
</form>


<?php AdminUIHelper::endAdminArea(); ?>

