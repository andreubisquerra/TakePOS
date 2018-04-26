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
$floor=GETPOST('floor');
if ($floor=="") $floor=1;
$id = GETPOST('id');
$action = GETPOST('action');
$left = GETPOST('left');
$top = GETPOST('top');
$place = GETPOST('place');
$floor = GETPOST('floor');
$after = GETPOST('after');
$result=$user->fetch('','admin');
$user->getrights();


if ($action=="update")
{
if ($left>95) $left=95;
if ($top>95) $top=95;
if ($left>3 or $top>4)
{
$db->begin();
$db->query("update ".MAIN_DB_PREFIX."pos_places set left_pos=$left, top_pos=$top where name='$place'");
$db->commit();
}
else
{
$db->begin();
$db->query("delete from ".MAIN_DB_PREFIX."pos_places where name='$place'");
$db->commit();
}
}

if ($action=="updatename")
{
$db->begin();
$db->query("update ".MAIN_DB_PREFIX."pos_places set name='$after' where name='$place'");
$db->commit();
}

if ($action=="add")
{
$sql="SELECT name from ".MAIN_DB_PREFIX."pos_places";
$resql = $db->query($sql);
$data = array();
$i=0;
while ($row = $db->fetch_array ($resql)) {
    $data[$i++]= $row[0];
}
$data[$i++]= 0;
$nextplace=max(array_values($data));
$nextplace++;
$db->begin();
$db->query("insert into ".MAIN_DB_PREFIX."pos_places (name, left_pos, top_pos, zone) values ('$nextplace', '25', '25', $floor)");
$db->commit();
exit;
}

// Title
$title='TakePOS - Dolibarr '.DOL_VERSION;
if (! empty($conf->global->MAIN_APPLICATION_TITLE)) $title='TakePOS - '.$conf->global->MAIN_APPLICATION_TITLE;
top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss);
?>
<link rel="stylesheet" href="css/pos.css?a=xxx"> 
<style type="text/css">
div.tablediv{
background-image:url(img/table.gif);
-moz-background-size:100% 100%;
-webkit-background-size:100% 100%;
background-size:100% 100%;
height:10%;
width:10%;
text-align: center;
font-size:300%;
color:white;
}
html, body
{
height: 100%;
}
</style>

<script>
var DragDrop='<?php echo $langs->trans("DragDrop"); ?>';
	
function updateplace(idplace, left, top) {
	$.ajax({
		type: "POST",
		url: "floors.php",
		data: { action: "update", left: left, top: top, place: idplace }
		}).done(function( msg ) {
		window.location.reload()
		});
	}
	
function updatename(before) {
	var after=$("#"+before).text();
	$.ajax({
		type: "POST",
		url: "floors.php",
		data: { action: "updatename", place: before, after: after }
		}).done(function( msg ) {
		window.location.reload()
		});
	}
	
				//Get places
			$.getJSON('./floors.php?zone=<?php echo $floor; ?>', function(data) {
				$.each(data, function(key, val) {
				$('body').append('<div class="tablediv" contenteditable onblur="updatename('+val.place+');" style="position: absolute; left: '+val.left_pos+'%; top: '+val.top_pos+'%;" id="'+val.place+'">'+val.place+'</div>');
				$( "#"+val.place ).draggable(
				{
					start: function() {
					$("#add").attr("src","./img/delete.jpg");
					$("#addcaption").html(DragDrop);
					
					},
					stop: function() {
					var left=$(this).offset().left*100/$(window).width();
					var top=$(this).offset().top*100/$(window).height();
					updateplace($(this).attr('id'), left, top);
					}
					}
					);
					
					//simultaneous draggable and contenteditable
					$('#'+val.place).draggable().bind('click', function(){
					$(this).focus();
					})
					
					});
					});
	</script>
	</head>
	<body style="overflow: hidden">
	<div style="position: absolute; left: 0.1%; top: 0.8%; width:8%; height:11%;" onclick='
	$.ajax({
		type: "POST",
		url: "floors.php",
		data: { action: "add", zone: <?php echo $floor; ?> }
		}).done(function( msg ) {
		window.location.reload()
		});'>
	<div class='wrapper3' style="width:100%;height:100%;" id="setup">
		<img src='<?php echo DOL_URL_ROOT;?>/holiday/img/add.png' width="100%" height="100%" border="1" id='deleteimg'/>
		<div class='description2'>
		<div class='description_content' id="addcaption"><?php echo $langs->trans("AddTable"); ?></div>
		</div>
	</div>


	</div>
	
	<div style="position: absolute; left: 25%; bottom: 6%; width:50%; height:3%;">
	<center>
	<h1><img src="./img/arrow-prev.png" width="5%" onclick="location.href='floors.php?floor=<?php if ($floor>1) { $floor--; echo $floor; $floor++;} else echo "1"; ?>';"><?php echo $langs->trans("Floor")." ".$floor; ?><img src="./img/arrow-next.png" width="5%" onclick="location.href='floors.php?floor=<?php $floor++; echo $floor; ?>';"></h1>
	</center>
	</div>
	</body>
</html>