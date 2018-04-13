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
$id = GETPOST('id');
$action = GETPOST('action');
$idproduct = GETPOST('idproduct');
$place = GETPOST('place');
$number = GETPOST('number');

$sql="SELECT rowid FROM ".MAIN_DB_PREFIX."facture where facnumber='ProvPOS-$place'";
$resql = $db->query($sql);
$row = $db->fetch_array ($resql);
$placeid=$row[0];
if (! $placeid) $placeid=0;
else{
	$invoice = new Facture($db);
	$invoice->fetch($placeid);
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
}

echo "articulo".$idproduct;
echo "<br>Place ID ".$placeid;