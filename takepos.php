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
<link rel="stylesheet" href="css/pos.css?a=xx"> 
<script type="text/javascript" src="js/takepos.js" ></script>
<script language="javascript">
<?php
$categorie = new Categorie($db);
$categories = $categorie->get_full_arbo('product');
?>
var categories = JSON.parse( '<?php echo json_encode($categories);?>' );
function PrintCategories(first){
	for (i = 0; i < 13; i++) {
		if (typeof (categories[parseInt(i)+parseInt(first)]) == "undefined") break;
		$("#catdesc"+i).text(categories[parseInt(i)+parseInt(first)]['label']);
        $("#catimg"+i).attr("src","genimg/?query=cat&w=55&h=50&id="+categories[parseInt(i)+parseInt(first)]['rowid']);
        $("#catdiv"+i).data("rowid",categories[parseInt(i)+parseInt(first)]['rowid']);
	}
}

function LoadProducts(category, position){
	//$('#catimg'+position).addClass('gray'); 
    $('#catimg'+position).animate({opacity: '0.5'});
	//setTimeout(function(){$('#catimg'+position).removeClass('gray');},200);
		/*currentcat=category;
		pagepro=page;
		var cachedData = window.localStorage[category];
		if (cachedData) showproducts(JSON.parse(cachedData), page);
		else {
		$.getJSON('./ajax_pos.php?action=getProducts&category='+category, function(data) {
		window.localStorage[category] = JSON.stringify(data);
		showproducts(data, page);
		});
	}*/
}

$( document ).ready(function() {
    PrintCategories(0);
});
</script>       

<body style="overflow: hidden">

<div id="poslines" style="position:absolute; top:8%; left:0.5%; height:30%; width:40%; overflow: auto;">
</div>

<div style="position:absolute; top:1%; left:32.5%; height:37%; width:32.5%;">
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
    <button type="button" class="calcbutton" onclick="changer('c');">C</button>
    <button type="button" class="calcbutton2" id="notes"><?php echo $langs->trans("Notes"); ?></button>
</div>
				
<div style="position:absolute; top:39%; left:0.3%; height:59%; width:32%;">
	<?php
	$count=0;
	while ($count<16)
	{
	?>
	<div class='wrapper' <?php if ($count==14) echo 'onclick="prevcategories();"'; else if ($count==15) echo 'onclick="nextcategories();"'; else echo 'onclick="LoadProducts($(this).data(\'rowid\'),'.$count.');"';?> id='catdiv<?php echo $count;?>'>
		<img class='imgwrapper' <?php if ($count==14) echo 'src="img/arrow-prev-top.png"'; if ($count==15) echo 'src="img/arrow-next-top.png"';?> width="98%" id='catimg<?php echo $count;?>'/>
		<div class='description'>
			<div class='description_content' id='catdesc<?php echo $count;?>'></div>
		</div>
	</div>
	<?php
    $count++;
	}
	?>
</div>
	
<div style="position:absolute; top:39%; left:32%; height:58%; width:72%;">
<?php
$count=0;
while ($count<32)
	{
	$count++;
	?>
	<div class='wrapper2' id='prodiv<?php echo $count;?>' <?php if ($count==31) {?> onclick="if ($('#prodesc27').text()==previous) loadproducts(currentcat, pagepro-1);" <?php } if ($count==32) {?> onclick="if ($('#prodesc28').text()==following) loadproducts(currentcat, pagepro+1);" <?php } ?>>
		<img class='imgwrapper' <?php if ($count==31) echo 'src="img/arrow-prev-top.png"'; if ($count==32) echo 'src="img/arrow-next-top.png"';?> width="95%" id='proimg<?php echo $count;?>'/>
		<div class='description'>
			<div class='description_content' id='prodesc<?php echo $count;?>'></div>
		</div>
	</div>
	<?php
	}
?>
</div>

</body>
<?php

llxFooter();

$db->close();



