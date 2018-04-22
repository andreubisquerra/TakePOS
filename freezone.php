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
$langs->load("bills");
$langs->load("cashdesk");
$place = GETPOST('place');
top_htmlhead($head, $title, $disablejs, $disablehead, $arrayofjs, $arrayofcss);
?>
<script>
function Save(){
	$.get( "invoice.php", { action: "freezone", place: "<?php echo $place;?>", desc:$('#desc').val(), number:$('#price').val()} );
	parent.$.colorbox.close();
}
</script>
</head>
<body>
<br>
<center>
<input type="text" id="desc" name="desc" style="width:40%;font-size: 200%;" placeholder="<?php echo $langs->trans('Description');?>">
<input type="text" id="price" name="price" style="width:15%;font-size: 200%;" placeholder="<?php echo $langs->trans('Price');?>">
<input type="hidden" name="place" value="<?php echo $place;?>">
<input type="button" style="width:15%;font-size: 200%;" value="OK" onclick="Save();">
</center>

</body>
</html>