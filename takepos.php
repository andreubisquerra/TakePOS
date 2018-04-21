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
<link rel="stylesheet" href="css/pos.css?a=xxx"> 
<script type="text/javascript" src="js/takepos.js" ></script>
<link rel="stylesheet" href="css/colorbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
<script language="javascript">
<?php
$categorie = new Categorie($db);
$categories = $categorie->get_full_arbo('product');
?>
var categories = JSON.parse( '<?php echo json_encode($categories);?>' );
var currentcat;
var pageproducts=0;
var pagecategories=0;
var place="0";
function PrintCategories(first){
	for (i = 0; i < 14; i++) {
		if (typeof (categories[parseInt(i)+parseInt(first)]) == "undefined") break;
		$("#catdesc"+i).text(categories[parseInt(i)+parseInt(first)]['label']);
        $("#catimg"+i).attr("src","genimg/?query=cat&w=55&h=50&id="+categories[parseInt(i)+parseInt(first)]['rowid']);
        $("#catdiv"+i).data("rowid",categories[parseInt(i)+parseInt(first)]['rowid']);
	}
}

function MoreCategories(moreorless){
	if (moreorless=="more"){
		$('#catimg15').animate({opacity: '0.5'}, 100);
		$('#catimg15').animate({opacity: '1'}, 100);
		pagecategories=pagecategories+1;
	}
	if (moreorless=="less"){
		$('#catimg14').animate({opacity: '0.5'}, 100);
		$('#catimg14').animate({opacity: '1'}, 100);
		if (pagecategories==0) return; //Return if no less pages
		pagecategories=pagecategories-1;
	}
	if (typeof (categories[14*pagecategories] && moreorless=="more") == "undefined"){ // Return if no more pages
		pagecategories=pagecategories-1;
		return;
	}
	for (i = 0; i < 14; i++) {
		if (typeof (categories[i+(14*pagecategories)]) == "undefined"){
				$("#catdesc"+i).text("");
				$("#catimg"+i).attr("src","");
				continue;
			}
		$("#catdesc"+i).text(categories[i+(14*pagecategories)]['label']);
        $("#catimg"+i).attr("src","genimg/?query=cat&w=55&h=50&id="+categories[i+(14*pagecategories)]['rowid']);
        $("#catdiv"+i).data("rowid",categories[i+(14*pagecategories)]['rowid']);
	}	
}

function LoadProducts(position){
    $('#catimg'+position).animate({opacity: '0.5'}, 100);
	$('#catimg'+position).animate({opacity: '1'}, 100);
	currentcat=$('#catdiv'+position).data('rowid');
	pageproducts=0;
	$.getJSON('./ajax.php?action=getProducts&category='+currentcat, function(data) {
		for (i = 0; i < 30; i++) {
			if (typeof (data[i]) == "undefined"){
				$("#prodesc"+i).text("");
				$("#proimg"+i).attr("src","");
				continue;
			}
			$("#prodesc"+i).text(data[parseInt(i)]['label']);
			$("#proimg"+i).attr("src","genimg/?query=pro&w=55&h=50&id="+data[i]['id']);
			$("#prodiv"+i).data("rowid",data[i]['id']);
		}
	});
}

function MoreProducts(moreorless){
	if (moreorless=="more"){
		$('#proimg31').animate({opacity: '0.5'}, 100);
		$('#proimg31').animate({opacity: '1'}, 100);
		pageproducts=pageproducts+1;
	}
	if (moreorless=="less"){
		$('#proimg30').animate({opacity: '0.5'}, 100);
		$('#proimg30').animate({opacity: '1'}, 100);
		if (pageproducts==0) return; //Return if no less pages
		pageproducts=pageproducts-1;
	}
	$.getJSON('./ajax.php?action=getProducts&category='+currentcat, function(data) {
		if (typeof (data[(30*pageproducts)]) == "undefined" && moreorless=="more"){ // Return if no more pages
			pageproducts=pageproducts-1;
			return;
		}
		for (i = 0; i < 30; i++) {
			if (typeof (data[i+(30*pageproducts)]) == "undefined"){
				$("#prodesc"+i).text("");
				$("#proimg"+i).attr("src","");
				continue;
			}
			$("#prodesc"+i).text(data[parseInt(i+(30*pageproducts))]['label']);
			$("#proimg"+i).attr("src","genimg/?query=pro&w=55&h=50&id="+data[i+(30*pageproducts)]['id']);
			$("#prodiv"+i).data("rowid",data[i+(30*pageproducts)]['id']);
		}
	});
}

function ClickProduct(position){
    $('#proimg'+position).animate({opacity: '0.5'}, 100);
	$('#proimg'+position).animate({opacity: '1'}, 100);
	idproduct=$('#prodiv'+position).data('rowid');
	$("#poslines").load("invoice.php?action=addline&place="+place+"&idproduct="+idproduct);
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

<?php
$menus = array();
$r=0;
$menus[$r++]=array('title'=>$langs->trans("CloseBill"),
                   'action'=>'');	
?>
<div style="position:absolute; top:1%; left:65.5%; height:37%; width:32.5%;">
<?php
foreach($menus as $menu) {
    echo '<button type="button" class="actionbutton" onclick="changer(7);">'.$menu['title'].'</button>';
}
?>
</div>
				
<div style="position:absolute; top:39%; left:0.3%; height:59%; width:32%;">
	<?php
	$count=0;
	while ($count<16)
	{
	?>
	<div class='wrapper' <?php if ($count==14) echo 'onclick="MoreCategories(\'less\');"'; else if ($count==15) echo 'onclick="MoreCategories(\'more\');"'; else echo 'onclick="LoadProducts('.$count.');"';?> id='catdiv<?php echo $count;?>'>
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
	?>
	<div class='wrapper2' id='prodiv<?php echo $count;?>' <?php if ($count==30) {?> onclick="MoreProducts('less');" <?php } if ($count==31) {?> onclick="MoreProducts('more');" <?php } else echo 'onclick="ClickProduct('.$count.');"';?>>
		<img class='imgwrapper' <?php if ($count==30) echo 'src="img/arrow-prev-top.png"'; if ($count==31) echo 'src="img/arrow-next-top.png"';?> width="95%" id='proimg<?php echo $count;?>'/>
		<div class='description'>
			<div class='description_content' id='prodesc<?php echo $count;?>'></div>
		</div>
	</div>
	<?php
	$count++;
	}
?>
</div>

</body>
<?php

llxFooter();

$db->close();



