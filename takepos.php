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

$res=@include("../../main.inc.php");
if (! $res) $res=@include("../../../main.inc.php");
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$langs->load("main");
$langs->load("bills");
$langs->load("orders");

/*
 * Actions
 */



/*
 * View
 */

if (! is_object($form)) $form=new Form($db);

// Title
$title='TakePOS - Dolibarr '.DOL_VERSION;
if (! empty($conf->global->MAIN_APPLICATION_TITLE)) $title='TakePOS - '.$conf->global->MAIN_APPLICATION_TITLE;
top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss);

?>
<link rel="stylesheet" href="css/pos.css"> 
<link rel="stylesheet" href="js/jtable/themes/metro/lightgray/jtable.min.css">
<script type="text/javascript" src="js/takepos.js" ></script>
<script language="javascript">
	$(function(){
		$('form.nice').jqTransform({imgPath:'img/'});
		
		});
		$(document).ready(function() {
			$('#poslines').jtable('load');
		});
</script>       

<body>
<div class="row">
   <div id="poslines"></div>
   <div>
        <button type="button" class="calcbutton" onclick="changer(7);">7</button>
        <button type="button" class="calcbutton" onclick="changer(8);">8</button>
        <button type="button" class="calcbutton" onclick="changer(9);">9</button>
        <button type="button" class="calcbutton2" onclick="changer('q');"><?php echo $langs->trans("Qty"); ?></button>
        <button type="button" class="calcbutton" onclick="changer(4);">4</button>
        <button type="button" class="calcbutton" onclick="changer(5);">5</button>
        <button type="button" class="calcbutton" onclick="changer(6);">6</button>
        <button type="button" class="calcbutton2" onclick="changer('p');"><?php echo $langs->trans("Price"); ?></button>
        <button type="button" class="calcbutton" onclick="changer(1);">1</button>
        <button type="button" class="calcbutton" onclick="changer(2);">2</button>
        <button type="button" class="calcbutton" onclick="changer(3);">3</button>
        <button type="button" class="calcbutton2" onclick="changer('d');"><?php echo $langs->trans("ReductionShort"); ?></button>
        <button type="button" class="calcbutton" onclick="changer(0);">0</button>
        <button type="button" class="calcbutton" onclick="changer('.');">.</button>
        <button type="button" class="calcbutton" onclick="changer('c');">C</button></div>
   <div>txt</div>
</div>

<div class="row">
   <div>text</div>
   <div>img</div>
   <div>txt</div>
</div>
</body>
<?php

llxFooter();

$db->close();



