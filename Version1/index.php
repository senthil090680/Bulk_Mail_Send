<?php
include ("includes/config.php");
include ("includes/functions.php");
include("includes/header.php");

//echo common::prestyle($_POST);
//exit;

$con = mysql_connect(SERVER_NAME,DATABASE_USERNAME,DATABASE_PASSWORD) or die(mysql_error());
mysql_select_db(DATABASE_NAME,$con) or die(mysql_error());

if(isset($_GET[delid]) && $_GET[delid] != '') { 
	$del_query			=	'DELETE FROM '. TABLE_MAILTMP. ' WHERE id = '.$_GET[delid];
	$del_res			=	mysql_query($del_query) or die(mysql_error());	
}

$select_query		=	'SELECT id,mailtemp,mailfromid,mailfromname,description FROM '. TABLE_MAILTMP;
$select_res			=	mysql_query($select_query) or die(mysql_error());
$select_nor			=	mysql_num_rows($select_res);

if((isset($_POST[updated]) && $_POST[updated] == 'done') || (isset($_POST[inserted]) && $_POST[inserted] == 'done')) { ?>
<style>
	#errorid {
		display:block;
		background-color:#C1C1C1;
		color:blue;
		font-size:11px;
		font-family:Arial;
		width:40%;
		height:25px;
		border-radius:10px;
		text-align:center;
	}
</style>
<?php } elseif((isset($_POST[updated]) && $_POST[updated] == 'notdone') || (isset($_POST[inserted]) && $_POST[inserted] == 'notdone')) { ?>
<style>
	#errorid {
		display:block;
		background-color:#C1C1C1;
		color:#FF0000;
		font-size:11px;
		font-family:Arial;
		width:40%;
		height:25px;
		border-radius:10px;
		text-align:center;
	} 
</style>
<?php } elseif(isset($_GET[delid]) && $_GET[delid] != '') { ?>
<style>
	#errorid {
		display:block;
		background-color:#C1C1C1;
		color:#FF0000;
		font-size:11px;
		font-family:Arial;
		width:40%;
		height:25px;
		border-radius:10px;
		text-align:center;
	} 
</style>
<?php } ?>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.fixedheader.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.4.custom.css" id="link"/>
<link rel="stylesheet" type="text/css" href="css/base.css" />


<script type="text/javascript">
$(document).ready(function() {	
	$(".closebtn").click(function() {
			$("#errorid").css("display","none");
	});
	//alert(23);
	$('#tableid').fixheadertable({		
            caption : 'Mail Template ', 
			colratio : [300, 300, 300, 200, 235], 
			height : 200, 
			//width : 750, 
			//zebra : true, 
			//sortable : true,
			//sortedColId : 1, 
			//resizeCol : true,
			pager : true,
			rowsPerPage	 : 10,
			//sortType : ['integer', 'string', 'string', 'string', 'string', 'date'],
			//dateFormat : 'm/d/Y'
    });
});
function deltemp(id,name) {
	var delcon	=	confirm('Are You Sure You Want to Delete? '+name);
	if(delcon) {
		window.location = "index.php?delid="+id;
	}
}
</script>
<div><span class="fleft"><a class="nounderline" href="mailtemplate.php">Add Template<span class="addbut" ></span></a></span><span ><a class="nounderline fright" href="mailsend.php">Send Mail<span class="mailbut" ></span></a></span></div>
<div class="clrboth"></div>
<table border='1' id="tableid" style="margin:0;" align='center'>
	<thead>
	<tr>
		<th>Mail Subject</th>
		<th>From Mail ID</th>
		<th>From Mail Name</th>
		<!-- <th>Mail Content</th> -->
		<th>Edit</th>
		<th>Delete</th>
	</tr>
	</thead>
	<tbody>
<?php

if($select_nor > 0) { 
	while($select_row	= mysql_fetch_array($select_res)) { ?>
	<tr>
		<td><?php echo $select_row['mailtemp']; ?></td>
		<td><?php echo $select_row['mailfromid']; ?></td>
		<td><?php echo $select_row['mailfromname']; ?></td>
		<!-- <td><?php echo addslashes($select_row['description']); ?></td> -->
		<td align='center'><a href='mailtemplate.php?id=<?php echo $select_row['id']; ?>' ><img src='images/Edit_icon.png' /></a></td>
		<td align='center'><a href='javascript:void(0);' onClick="deltemp('<?php echo $select_row['id']; ?>','<?php echo ucwords($select_row['mailtemp']); ?>');" ><img src='images/del_icon.gif' /></a></td>
	</tr>
<?php
	}
} else { ?>
	<tr>
		<td colspan="6" align="center">No Records Found</td>
	</tr>
<?php
} ?>
</tbody>
</tr>
</table>
<div class="clrboth"></div>
<div class="pl475">
	<div id="errorid" ><h3 class="erroridh3">
	<?php if(isset($_POST[updated]) && $_POST[updated] == 'done') { 	
		echo "Data Updated Successfully"; ?>
		<script>
			setTimeout(function() {
				$("#errorid").hide();
			},5000);
		</script>
		<?php 
	}  elseif(isset($_POST[inserted]) && $_POST[inserted] == 'done') { 	
		echo "Data Inserted Successfully"; ?>
		<script>
			setTimeout(function() {
				$("#errorid").hide();
			},5000);
		</script>
		<?php 
		//unset($_POST);
	} elseif(isset($_GET[delid]) && $_GET[delid] != '') { 	
		echo "Data Deleted Successfully"; ?>
		<script>
			setTimeout(function() {
				$("#errorid").hide();
			},5000);
		</script>
		<?php //unset($_POST);
	} ?>
	</h3><span class="closebtn" style="float:right;margin-top:-35px;" ><img src="images/close_pop.png" /></span></div>
</div>
<?php
include("includes/footer.php"); ?>