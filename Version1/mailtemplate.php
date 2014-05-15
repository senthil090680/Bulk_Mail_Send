<?php
include ("includes/config.php");
include ("includes/functions.php");
include("includes/header.php");

if(isset($_POST['mailsubmit']) && $_POST['mailsubmit'] == 'Submit') {
	//common::prestyle($_POST);

	extract($_POST);

	$con = mysql_connect(SERVER_NAME,DATABASE_USERNAME,DATABASE_PASSWORD) or die(mysql_error());
	mysql_select_db(DATABASE_NAME,$con) or die(mysql_error());

	$query_temp					=	'SELECT mailtemp FROM '. TABLE_MAILTMP. ' WHERE mailtemp = "'.$mailtemp.'"';
	//echo $query_temp;
	//exit;
	$res_temp					=	mysql_query($query_temp) or die(mysql_error());
	$nor_temp					=	mysql_num_rows($res_temp);

	if($nor_temp > 0) {
		$query_tempup			=	'UPDATE '. TABLE_MAILTMP. ' SET mailfromid = "'.$mailfromid.'", mailfromname = "'.$mailfromname.'", description = "'.mysql_real_escape_string($description).'", updated_date = NOW() WHERE mailtemp = "'.$mailtemp.'"';
		//echo $query_tempup;
		$res_tempup				=	mysql_query($query_tempup) or die(mysql_error());
		if($res_tempup) { ?>
			<form id="formsubmit" method="post" action="index.php">
				<input type="hidden" name="updated" value="done" />
			</form>
			<?php
			//echo "Updated successfully";
		} else { ?>
			<form id="formsubmit" method="post" action="index.php">
				<input type="hidden" name="updated" value="notdone" />
			</form>
	<?php }
	} else {		
		if(isset($_POST['edit_id']) && $_POST['edit_id'] != '') {
			$query_tempup			=	'UPDATE '. TABLE_MAILTMP. ' SET mailtemp = "'.$mailtemp.'", mailfromid = "'.$mailfromid.'", mailfromname = "'.$mailfromname.'", description = "'.mysql_real_escape_string($description).'", updated_date = NOW() WHERE id = "'.$edit_id.'"';
			//echo $query_tempup;
			$res_tempup				=	mysql_query($query_tempup) or die(mysql_error());
			if($res_tempup) { ?>
				<form id="formsubmit" method="post" action="index.php">
					<input type="hidden" name="updated" value="done" />
				</form>
				<?php
				//echo "Updated successfully";
			} else { ?>
				<form id="formsubmit" method="post" action="index.php">
					<input type="hidden" name="updated" value="notdone" />
				</form>
			<?php }
		} else {
			$query_tempin		=	'INSERT INTO '. TABLE_MAILTMP. ' (mailtemp,mailfromid,mailfromname,description,created_date) VALUES ("'.$mailtemp.'", "'.$mailfromid.'", "'.$mailfromname.'", "'.mysql_real_escape_string($description).'",NOW())';
			//echo $query_tempin;
			//exit;
			$res_tempin				=	mysql_query($query_tempin) or die(mysql_error());
			if($res_tempin) { ?>
				<form id="formsubmit" method="post" action="index.php">
					<input type="hidden" name="inserted" value="done" />
				</form>
				<?php
				//echo "Inserted successfully";
			} else { ?>
				<form id="formsubmit" method="post" action="index.php">
					<input type="hidden" name="inserted" value="notdone" />
				</form>
			<?php }
		}
	} ?>
	<script>
		
		$("#formsubmit").submit();
	</script>
	<?php
}
if(isset($_GET['id']) && $_GET['id'] != '') {
	extract($_GET);
	$con = mysql_connect(SERVER_NAME,DATABASE_USERNAME,DATABASE_PASSWORD) or die(mysql_error());
	mysql_select_db(DATABASE_NAME,$con) or die(mysql_error());

	$select_query		=	'SELECT id,mailtemp,mailfromid,mailfromname,description FROM '. TABLE_MAILTMP.' WHERE id = '.$id;
	$select_res			=	mysql_query($select_query) or die(mysql_error());
	$select_nor			=	mysql_num_rows($select_res);
	$select_row			=	mysql_fetch_array($select_res);
	//echo addslashes($select_row['description']); 
	//exit;	
} 
?>
<script type="text/javascript">
	$(document).ready(function() {
		
		$("input[name='mailcancel']").click(function() {							
			window.location = "index.php";
		});
		

		$("input[name='mailsubmit']").click(function() {
			var mailtemp		=	$('#mailtemp').val();
			var mailfromid		=	$('#mailfromid').val();
			var mailfromname	=	$('#mailfromname').val();
			var description		=	tinyMCE.get('description').getContent();

			console.log(description);

			if(mailtemp == '') {
				$("#errorid").css("display","block");
				$(".erroridh3").html('ERR : Enter Mail Template ');
				setTimeout(function() {
					$("#errorid").hide();
				},5000);
				$("#mailtemp").focus();
				return false;
			} else if(mailfromid == '') {
				$("#errorid").css("display","block");
				$(".erroridh3").html('ERR : Enter From Mail Id ');
				setTimeout(function() {
					$("#errorid").hide();
				},5000);
				$("#mailfromid").focus();
				return false;
			} else if(mailfromid != '') {
				 var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				 if(!regex.test(mailfromid)) {
						$("#errorid").css("display","block");
						$(".erroridh3").html('ERR : Enter Valid E-mail');
						setTimeout(function() {
							$("#errorid").hide();
						},5000);
						$("#mailfromid").focus();
						return false;
				 }
			} 
			
			if(mailfromname == '') {
				$("#errorid").css("display","block");
				$(".erroridh3").html('ERR : Enter From Mail Name');
				setTimeout(function() {
					$("#errorid").hide();
				},5000);
				$("#mailfromname").focus();
				return false;
			} else if(description == '') {
				$("#errorid").css("display","block");
				$(".erroridh3").html('ERR : Enter Mail Content');
				setTimeout(function() {
					$("#errorid").hide();
				},5000);
				$("#description").focus();
				return false;
			}
			//return false;				 
		});
		$(".closebtn").click(function() {
			$("#errorid").css("display","none");
		});
		
	});		
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "table",

		// Theme options


		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect, table, row_props, cell_props, delete_col, delete_row, col_after, col_before, row_before, split_cells, merge_cells",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});	
</script>
</head>
<body>
<form method="post">
	<table align='center' cellpadding="10" cellspacing="1">
		<tr>
			<th colspan="3" align="center">Mail Template</th>
		</tr>		
		<tr>
			<td>Mail Subject</td><td>:</td><td><input type="text" name="mailtemp" id="mailtemp" size="50" value="<?php echo $select_row['mailtemp']; ?>" /></td>
		</tr>
		<tr>
			<td>From Email Id </td><td>:</td><td><input type="text" name="mailfromid" id="mailfromid" size="50" value="<?php echo $select_row['mailfromid']; ?>" /></td>
		</tr>
		<tr>
			<td>From Name </td><td>:</td><td><input type="text" name="mailfromname" id="mailfromname" size="50" value="<?php echo $select_row['mailfromname']; ?>" /></td>
		</tr>
		<tr>
		<?php //echo $select_row[description]; ?>
			<td>Mail Content </td><td>:</td><td><textarea rows="10" cols="73" name="description" id="description" lang="false"><?php echo addslashes($select_row['description']); ?></textarea><input type="hidden" name="edit_id" id="edit_id" size="50" value="<?php echo $select_row['id']; ?>" /></td>
		</tr>		
		<tr>
			<td align="center"></td><td></td><td align="center"><input type="submit" name="mailsubmit" value="Submit"/>&nbsp;&nbsp;&nbsp;<input type="button" name="mailcancel" value="Cancel"/></td>
		</tr>
		<tr>
			<td colspan="3" align="center"><div id="errorid" style="display:none;"><h3 class="erroridh3"></h3><span class="closebtn" style="float:right;margin-top:-48px;" ><img src="images/close_pop.png" /></span></div></td>
		</tr>
	</table>
</form>
</body>
<?php include("includes/footer.php"); ?>