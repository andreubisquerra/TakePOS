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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';

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
<link rel="stylesheet" href="css/pos.css?a=b"> 
<script type="text/javascript" src="js/takepos.js" ></script>
<script language="javascript">
<?php
$categorie = new Categorie($db);
$categories = $categorie->get_full_arbo('product');
?>
var categories = JSON.parse( '<?php echo json_encode($categories);?>' );
$("#catdesc0").html("asdf");
function PrintCategories(first){
	for (i = 0; i < 13; i++) {
		if (typeof variable == 'undefined') break;
		$("#catdesc"+i).text(categories[parseInt(i)+parseInt(first)]['label']);
	}
}
PrintCategories(0);
</script>       

<body>
<div class="row">
   <div id="poslines">
   </div>
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
   <div>
   <?php
	$count=0;
	while ($count<16)
	{
	?>
	<div class='wrapper' <?php if ($count==15) echo 'onclick="prevcategories();"'; else if ($count==16) echo 'onclick="nextcategories();"'; else echo 'onclick="loadproducts(categories[(14*pagecat)-14+'.$count.'][0], 1, '.$count.');"';?> id='catdiv<?php echo $count;?>'>
		<img class='imgwrapper' <?php if ($count==15) echo 'src="img/arrow-prev-top.png"'; if ($count==16) echo 'src="img/arrow-next-top.png"';?> width="100%" id='catimg<?php echo $count;?>'/>
		<div class='description'>
			<div class='description_content' id='catdesc<?php echo $count;?>'>1<?php if ($count==16) echo $langs->trans("following");?></div>
		</div>
	</div>
	<?php
	$count++;
	}
	?>
   </div>
   <div>img</div>
   <div>txt</div>
</div>
</body>
<?php

llxFooter();

$db->close();



