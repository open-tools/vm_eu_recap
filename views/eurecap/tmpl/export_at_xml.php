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

$from_month=date("Y-m", $this->from);
$until_month=date("Y-m", $this->until);
$fastnr = preg_replace('/[^0-9]/', '', $this->settings['taxnr']);

header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=\"EU_Zusammenfassende_Meldung_${from_month}_${until_month}.AT.xml\"");

?><?xml version="1.0" encoding="iso-8859-1"?>
<!-- Zusammenfassende Meldung - Stand: 10.01.2013 -->
<ERKLAERUNGS_UEBERMITTLUNG>
	<INFO_DATEN>
		<ART_IDENTIFIKATIONSBEGRIFF>FASTNR</ART_IDENTIFIKATIONSBEGRIFF>
		<IDENTIFIKATIONSBEGRIFF><?php echo $fastnr; ?></IDENTIFIKATIONSBEGRIFF>
		<PAKET_NR><?php echo rand(1, 999999999);?></PAKET_NR>
		<DATUM_ERSTELLUNG type="datum"><?php echo date("Y-m-d"); ?></DATUM_ERSTELLUNG>
		<UHRZEIT_ERSTELLUNG type="uhrzeit"><?php echo date("H:i:s"); ?></UHRZEIT_ERSTELLUNG>
		<ANZAHL_ERKLAERUNGEN>1</ANZAHL_ERKLAERUNGEN>
	</INFO_DATEN>
	<ERKLAERUNG art="U13">
		<SATZNR><?php echo rand(1, 999999999);?></SATZNR>
		<ALLGEMEINE_DATEN>
			<ANBRINGEN>U13</ANBRINGEN>
			<ZRVON type="jahrmonat"><?php echo $from_month; ?></ZRVON>
			<ZRBIS type="jahrmonat"><?php echo $until_month; ?></ZRBIS>
			<FASTNR><?php echo $fastnr; ?></FASTNR>
			<KUNDENINFO></KUNDENINFO>
		</ALLGEMEINE_DATEN>
<?php
foreach ($this->report as $r) { 
	$vatid = preg_replace('/[^A-Z0-9]/', '', strtoupper(trim($r['vatid'])));
?>
		<ZM>
			<UID_MS><?php echo $vatid; ?></UID_MS>
			<SUM_BGL type="kz"><?php echo $r['sum_order_total']; ?></SUM_BGL>
<?php //			<DREIECK>0</DREIECK> ?>
			<SOLEI>1</SOLEI>
		</ZM>
<?php 
} 
?>
<?php /*
		<GESAMTRUECKZIEHUNG>
			<GESAMTRUECK>J</GESAMTRUECK>
		</GESAMTRUECKZIEHUNG>
*/ ?>
	</ERKLAERUNG>
</ERKLAERUNGS_UEBERMITTLUNG>
