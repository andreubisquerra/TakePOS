<?php
/* Copyright (C) 2018	Andreu Bisquerra	<jove@bisquerra.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

define('NOCSRFCHECK',1);	// This is main home and login page. We must be able to go on it from another web site.

$res=@include("../main.inc.php");
if (! $res) $res=@include("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
$langs->load("bills");
$langs->load("cashdesk");
$id = GETPOST('id');
$action = GETPOST('action');
$idproduct = GETPOST('idproduct');
$place = GETPOST('place');
$number = GETPOST('number');
$idline = GETPOST('idline');
$desc = GETPOST('desc');

$sql="SELECT rowid FROM ".MAIN_DB_PREFIX."facture where facnumber='ProvPOS-$place'";
$resql = $db->query($sql);
$row = $db->fetch_array ($resql);
$placeid=$row[0];
if (! $placeid) $placeid=0; // not necesary
else{
	$invoice = new Facture($db);
	$invoice->fetch($placeid);
}

/*
 * Actions
 */

if ($action == 'valid' && $user->rights->facture->creer){
	$invoice = new Facture($db);
	$invoice->fetch($placeid);
	$invoice->validate($user);
}

if ($action=="addline" and $placeid==0)
{
	if ($placeid==0) {
	$invoice = new Facture($db);
	$invoice->socid=1;
	$invoice->date=mktime();
	$invoice->ref="asdf";
	$placeid=$invoice->create($user);
	$sql="UPDATE ".MAIN_DB_PREFIX."facture set facnumber='ProvPOS-$place' where rowid=$placeid";
	$db->query($sql);
	}
}

if ($action=="addline"){
	$prod = new Product($db);
	$prod->fetch($idproduct);
	$invoice->addline($prod->description, $prod->price, 1, 21, $prod->localtax1_tx, $prod->localtax2_tx, $idproduct, $prod->remise_percent, '', 0, 0, 0, '', $prod->price_base_type, $prod->price_ttc, $prod->type, - 1, 0, '', 0, 0, null, 0, '', 0, 100, '', null, 0);
	$invoice->fetch($placeid);
}

if ($action=="freezone"){
	$invoice->addline($desc, $number, 1, 21, 0, 0, 0, 0, '', 0, 0, 0, '', 'TTC', $number, 0, - 1, 0, '', 0, 0, null, 0, '', 0, 100, '', null, 0);
	$invoice->fetch($placeid);
}

if ($action=="deleteline"){
	$invoice->deleteline($idline);
	$invoice->fetch($placeid);
}

if ($action=="updateqty"){
	$invoice->updateline($idline,'','',$number);
	$invoice->fetch($placeid);
}

?>
<style>
.selected {
	color: red;
}
</style>
<script language="javascript">
var selectedline=0;
var selectedtext="";
$(document).ready(function(){
    $('table tbody tr').click(function(){
		$('table tbody tr').removeClass("selected");
        $(this).addClass("selected");
		if (selectedline==this.id) return; // If is already selected
        else selectedline=this.id;
        selectedtext=$('#'+selectedline).find("td:first").html();
    });
});

function Print(id){
	$.colorbox({href:"receipt.php?facid="+id, width:"80%", height:"90%", transition:"none", iframe:"true", title:"<?php echo $langs->trans("PrintTicket");?>"});
}
</script>
<?php
print '<div class="div-table-responsive-no-min">';
print '<table id="tablelines" class="noborder noshadow" width="100%">';
print '<tr class="liste_titre nodrag nodrop">';
print '<td class="linecoldescription">'.$langs->trans('Description').'</td>';
print '<td class="linecolqty" align="right">'.$langs->trans('Qty').'</td>';
print '<td class="linecolht" align="right">'.$langs->trans('TotalHTShort').'</td>';
print "</tr>\n";
if ($placeid>0) foreach ($invoice->lines as $line)
{
	print '<tr class="drag drop oddeven" id="'.$line->rowid.'">';
	print '<td>'.$line->product_label.$line->desc.'</td>';
	print '<td align="right">'.$line->qty.'</td>';
	print '<td align="right">'.price($line->total_ttc).'</td>';
	print '</tr>';
}
print '</table>';
print '<p style="font-size:120%;" align="right"><b>'.$langs->trans('TotalTTC').': '.price($invoice->total_ttc, 1, '', 1, - 1, - 1, $conf->currency).'&nbsp;</b></p>';
if ($action=="valid"){
	print '<p style="font-size:120%;" align="center"><b>'.$invoice->facnumber." ".$langs->trans('BillShortStatusValidated').'</b></p>';
	print '<center><button type="button" onclick="Print('.$placeid.');">'.$langs->trans('PrintTicket').'</button><center>';
}
if ($action=="search"){
	print '<center>
	<input type="text" id="search" onkeydown="Search2();" name="search" style="width:80%;font-size: 150%;" placeholder='.$langs->trans('Search').'
	</center>';
}
print '</div>';