<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/**
*
* @version $Id$
* @package VirtueMart
* @subpackage Report
* @copyright Copyright (C) VirtueMart Team - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.org
*/

AdminUIHelper::startAdminArea($this);

JHtml::_('behavior.framework', true);
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div id="header">
        <h2><?php echo vmText::sprintf('VMEXT_EU_RECAP_VIEW_TITLE_DATE', vmJsApi::date( $this->from_period, 'LC',true) , vmJsApi::date( $this->until_period, 'LC',true) ); ?></h2>
        <div id="filterbox">
            <table>
                <tr>
                    <td align="left" width="100%">
						<?php if ($this->frequency<12) {
							echo vmText::_('VMEXT_EU_RECAP_LIST_MONTH') . $this->lists['month_list']; 
						} ?>
                        <?php echo vmText::_('VMEXT_EU_RECAP_LIST_YEAR') . $this->lists['year_list'];

                        if(VmConfig::get('multix','none')!='none'){
                            $vendorId = vRequest::getInt('virtuemart_vendor_id',1);
                            echo ShopFunctions::renderVendorList($vendorId,false);
                        } ?>
                        <button class="btn btn-small" onclick="this.form.submit();"><?php echo vmText::_('COM_VIRTUEMART_GO'); ?>
                        </button>
                    </td>
                </tr>
            </table>
        </div>
        <div id="resultscounter">
            <?php echo $this->pagination->getResultsCounter();?>
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
                    <th>
                        <?php echo $this->sort('`sum_order_tax`', 'VMEXT_EU_RECAP_ORDERTAXES') ; ?>
                    </th>
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
                    <td align="center">
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
		                <?php echo $r['sum_order_total'];?>
                    </td>
                    <td align="right">
		                <?php echo $r['sum_order_tax'];?>
                    </td>
                </tr>
                <?php
	    	$i = 1-$i;
	    }
	    ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

	<?php echo $this->addStandardHiddenToForm(); ?>
</form>

<?php AdminUIHelper::endAdminArea(); ?>

