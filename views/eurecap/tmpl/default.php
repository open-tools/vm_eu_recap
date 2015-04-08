<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

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

AdminUIHelper::startAdminArea($this);

JHtml::_('behavior.framework', true);
if (!class_exists('CurrencyDisplay'))
    require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
$myCurrencyDisplay = CurrencyDisplay::getInstance();


?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php echo $this->addStandardHiddenToForm(); ?>

    <div id="header">
        <h2><?php echo vmText::sprintf('VMEXT_EU_RECAP_VIEW_TITLE_DATE', vmJsApi::date( $this->from_period, 'LC',true) , vmJsApi::date( $this->until_period, 'LC',true) ); ?></h2>
        <p><?php echo vmText::_('VMEXT_EU_RECAP_VIEW_EXPLANATION'); ?> </p>
        <div id="filterbox">
            <table width="100%">
                <tr width="100%">
                    <td align="left">
						<div style="float: left">
						<?php 
						echo vmText::_('VMEXT_EU_RECAP_LIST_PERIOD');
						if ($this->frequency<12) {
							echo $this->period_lists['month_list']; 
						} 
						echo $this->period_lists['year_list'];

                        if(VmConfig::get('multix','none')!='none'){
                            $vendorId = vRequest::getInt('virtuemart_vendor_id',1);
                            echo ShopFunctions::renderVendorList($vendorId,false);
                        } ?><br>
                        <label><input type="checkbox" <?php if ($this->include_taxed_orders) { ?>checked <?php } ?> name="include_taxed_orders" value="true" style="vertical-align: top; position: relative; bottom: 1px;">&nbsp;<?php echo vmText::_('VMEXT_EU_RECAP_INCLUDE_TAXED'); ?></label>
                        </div>
                        <span><button class="btn btn-small" name="Go" onclick="this.form.task.value=''; this.form.submit();"><?php echo vmText::_('COM_VIRTUEMART_GO'); ?></button></span>
                    </td>
                    <td align="right" style="vertical-align: top">
						<div style="float: right"><?php echo $this->export_format_list; ?>
                        <button class="btn btn-small" name="format" value="raw" onclick="this.form.task.value='export'; this.form.submit();" style="vertical-align: top;"><?php echo vmText::_('VMEXT_EU_RECAP_EXPORT'); ?>
                        </button></div>
                    </td>
                </tr>
            </table>
        </div>
        <div id="resultscounter">
            <?php if ($this->pagination) echo $this->pagination->getResultsCounter();?>
        </div>
    </div>

    <div id="editcell">
	    <table class="adminlist table table-striped" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th>
                        <?php echo $this->sort('`vatid`', 'VMEXT_EU_RECAP_VATID'); ?>
                    </th>
                    <th>
                        <?php echo $this->sort('`countrycode`', 'VMEXT_EU_RECAP_COUNTRYCODE') ; ?>
                    </th>
                    <th>
                        <?php echo $this->sort('`company`', 'VMEXT_EU_RECAP_COMPANY') ; ?>
                    </th>
                    <th>
                        <?php echo $this->sort('`last_name`', 'VMEXT_EU_RECAP_NAME') ; ?>
                    </th>
                    <th>
                        <?php echo $this->sort('`order_ids`', 'VMEXT_EU_RECAP_ORDERS') ; ?>
                    </th>
                    <th>
                        <?php echo $this->sort('`sum_order_total`', 'VMEXT_EU_RECAP_ORDERTOTALS') ; ?>
                    </th>
<?php if ($this->include_taxed_orders) { ?>
                    <th>
                        <?php echo $this->sort('`sum_order_tax`', 'VMEXT_EU_RECAP_ORDERTAXES') ; ?>
                    </th>
<?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
        $i = 0;
        foreach ($this->report as $r) {
            $userlink = JROUTE::_ ('index.php?option=com_virtuemart&view=user&task=edit&virtuemart_user_id[]=' . $r['virtuemart_user_id'], FALSE);
        ?>
                <tr class="row<?php echo $i;?>">
                    <td align="center">
                        <?php echo $r['vatid']; ?>
                    </td>
                    <?php 
                        $vatidcc = substr(trim($r['vatid']), 0, 2);
                        if ($vatidcc=="EL") $vatidcc="GR";
                        if ($vatidcc=="UK") $vatidcc="GB";
                        $countrymatch = ($vatidcc == $r['countrycode']);
                    ?>
                    <td align="center" <?php if (!$countrymatch) { echo "style=\"background: #FFBFBF;\""; }; ?>>
                        <?php echo $r['countrycode'];?>
                    </td>
                    <td align="left">
                        <?php 
                            if ($r['company']) { 
                                echo JHtml::_ ('link', JRoute::_ ($userlink, FALSE), $r['company'], array('title' => $r['company'])); 
                            } 
                        ?>
                    </td>
                    <td align="left">
                        <?php
                            $fullname = join(" ", array($r['first_name'], $r['last_name']));
                            if ($fullname) {
                                echo JHtml::_ ('link', JRoute::_ ($userlink, FALSE), $fullname, array('title' => $fullname));
                            } 
                        ?>
                    </td>
                    <td align="left">
                    <?php
                        $oids = explode(',', $r['order_ids']);
                        $onrs = explode(',', $r['order_numbers']);
                        $orders = array_combine($oids, $onrs);
                        $links = array();
                        foreach ($orders as $oid=>$onr) {
                            $orderlink = JROUTE::_ ('index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id=' . $oid, FALSE);
                            $links[] = JHtml::_ ('link', JRoute::_ ($orderlink, FALSE), $onr, array('title' => $onr));
                        }
                        echo join(", ", $links); 
                    ?>
                    </td>
                    <td align="right">
		                <?php echo $myCurrencyDisplay->priceDisplay($r['sum_order_total']); ?>
                    </td>
<?php if ($this->include_taxed_orders) { ?>
                    <td align="right">
		                <?php echo $myCurrencyDisplay->priceDisplay($r['sum_order_tax']); ?>
                    </td>
<?php } ?>
                </tr>
                <?php
	    	$i = 1-$i;
	    }
	    ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10">
                        <?php if ($this->pagination) echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</form>

<?php AdminUIHelper::endAdminArea(); ?>

