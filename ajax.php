<?php
/* Copyright (C) 2001-2004	Andreu Bisquerra	<jove@bisquerra.com>
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

/**
 *	\file       htdocs/index.php
 *	\brief      Dolibarr home page
 */

define('NOCSRFCHECK',1);	// This is main home and login page. We must be able to go on it from another web site.

$res=@include("../main.inc.php");
if (! $res) $res=@include("../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

$category = GETPOST('category');
$action = GETPOST('action');
$term = GETPOST('term');

if ($action=="getProducts"){
	$object = new Categorie($db);
	$result=$object->fetch($category);
	$prods = $object->getObjectsInCateg("product");
	echo json_encode($prods);
}

if ($action=="search"){
	$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'product';
	$sql.= ' WHERE entity IN ('.getEntity('product').')';
    $sql .= ' AND tosell = 1 AND ( ';
    $sql .= natural_search('label', $term, 0, 1);
	$sql .= " or barcode='".$term."'";
    $sql .= ' )';
	$resql = $db->query($sql);
	$rows = array();
	while($row = $db->fetch_array ($resql)){
		$rows[] = $row;
	}
	echo json_encode($rows);
}
