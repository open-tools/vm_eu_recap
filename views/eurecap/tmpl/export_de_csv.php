<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/**
*
* @version $Id$
* @package VirtueMart
* @subpackage Report
* @copyright Copyright (C) Open Tools, Reinhold Kainhofer
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* http://www.open-tools.net/
*/

$from_month=date("Y-m", $this->from);
$until_month=date("Y-m", $this->until);


header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"EU_Zusammenfassende_Meldung_${from_month}_${until_month}.DE.csv\"");
?>Laenderkennzeichen,USt-IdNr.,Betrag(EUR),Art der Leistung
<?php 
foreach ($this->report as $r) {
	$vatid = preg_replace('/[^A-Z0-9]/', '', strtoupper(trim($r['vatid'])));
    $vatidcc = substr($vatid, 0, 2);
    // TODO: Check whether $vatidcc is a valid country code. If not, use $r['countrycode'] and interpret $vatid as the rest
    echo $vatidcc . ',' . substr($vatid, 2) . ',' . $r['sum_order_total'] . ',S
';
}
