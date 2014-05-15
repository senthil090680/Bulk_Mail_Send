<?php
include ("includes/config.php");
include ("includes/functions.php");
include("includes/header.php");
//set_time_limit(0);
ini_set("memory_limit","200M");

ini_set('max_execution_time',18000);
//echo ini_get('max_execution_time');

?>
<div class="loadpos"><img src="images/ajax_loader.gif" /></div>
<div class="bgcolor"></div>
<?php

$con = mysql_connect(SERVER_NAME,DATABASE_USERNAME,DATABASE_PASSWORD) or die(mysql_error());
mysql_select_db(DATABASE_NAME,$con) or die(mysql_error());

if(isset($_POST['mailsubmit']) && $_POST['mailsubmit'] == 'Submit') {
	//common::prestyle($_POST);

	//echo common::prestyle($_POST);
	//echo common::prestyle($_FILES);
	extract($_POST);
	
	$sleeptime			=	0;
	//print_r(pathinfo($_FILES[mailfile][tmp_name]));
	//exit;
	
	$error				=	'';

	$select_query		=	'SELECT id,mailtemp,mailfromid,mailfromname,description FROM '. TABLE_MAILTMP.' WHERE id = '.$mailtemp;
	$select_res			=	mysql_query($select_query) or die(mysql_error());
	$select_rows			=	mysql_num_rows($select_res);
	$select_row			=	mysql_fetch_array($select_res);
	
	if($select_rows > 0) {
		$mailtempname		=	$select_row['mailtemp'];
		$mailfromid			=	$select_row['mailfromid'];
		$mailfromname		=	$select_row['mailfromname'];
		$description		=	($select_row['description']);

		$headers = 'From: '.$mailfromname.' <'.$mailfromid.'>'. "\r\n".
		"Return-Path: IIBF<".$mailfromid.">". "\r\n".
		'Content-type: text/html; charset=iso-8859-1' . "\r\n".
		"Reply-To: ".$mailfromid. "\r\n".
		'MIME-Version: 1.0' . "\r\n".
		'X-Mailer: PHP/' . phpversion();

		//echo $description;
		//exit;

		/*echo $mailtempname."<br/>";
		echo $mailfromid."<br/>";
		echo $mailfromname."<br/>";
		echo $description."<br/>";*/
	}

	$pathArr		=	explode(".",$_FILES[mailfile][name]);
	$full_path		=	UPPATH."/".$pathArr[0]."_".time().".xls";

	if(move_uploaded_file($_FILES[mailfile][tmp_name],$full_path)) {
		//echo "moved";
	} else {
		$error	=	"Unable to upload CSV File, Check File Permission or the Folder Exists";
		//exit;
	}
	
	$logfile		=	LOGFILE."/".time().".log";
	$fp = fopen($logfile, "ab");
	if(!$fp){
		$error		=	"Unable to write log file please check folder permission.";
		//exit;
	}

	$file_open			=	fopen($full_path,"r");
	$k					=	0;
	
	if($error				==	'') {
		while(!feof($file_open)) { ?>
			<script>
				$(".bgclass").css({"display":"block"});
				$(".loadpos").css({"display":"block"});
			</script>
			<?php

			//exit;
			$fileContent		=	fgetcsv($file_open);

			if($fileContent != '') {
				if($error	== '') {
					if($k == 0){
						$headingfileContent		=	$fileContent;
						foreach($headingfileContent AS $headingfileContentVal) {
							if(common::checkstring($description,$headingfileContentVal)) {
								$returnedVal[]	=	common::checkstring($description,$headingfileContentVal);
							} else {
								if($headingfileContentVal != 'EMAIL') {
									$error		=	"Template doesn't contain this placeholder ".$headingfileContentVal;
									?>
									<script>
										$(".bgclass").css({"display":"none"});
										$(".loadpos").css({"display":"none"});
									</script>
									<?php break;
								}
							}
						}
						//common::prestyle($returnedVal);
						//exit;
						$k++;
					} elseif($k != 0){
						$linefileContent		=	$fileContent;
						//common::prestyle($linefileContent);
						//echo $k."<br/>";
						//exit;
						$h						=	0;
						$changingStr			=	$description;

						foreach($linefileContent as $linefileContentVal) {
							
							$linefileContentVal	=	trim($linefileContentVal);
							//echo $k."<br/>";
							
							$changingStr	=	str_replace($returnedVal[$h],$linefileContentVal,$changingStr);
							//echo $returnedVal[$h].'<br/>';
							//echo $linefileContentVal.'<br/>';
							//echo $changingStr.'<br/>';
							//echo $h.'<br/>';
							$h++;
						}
						//echo $changingStr.'<br/>';
						//exit;
						//echo common::prestyle($linefileContent);
						
						$MembershipNo		=	reset($linefileContent);
						$MembershipName		=	next($linefileContent);
						$MemberEmail		=	end($linefileContent);
						
						if($changingStr	!= '') {
							if(mail($MemberEmail,$mailtempname,$changingStr,$headers,'-f'.$mailfromid)){		
								$txt=$MemberEmail.','.$MembershipName.','.$MembershipNo.','.date('d-m-Y h:i:s');
								$sleeptime++;
								$fp = fopen($logfile, "a+");
								fwrite($fp,$txt."\r\n");
								fclose($fp);
								$success = "Successfully sent emails to Candidates";
								$changingStr	=	$description;
							}
						}
						if($sleeptime	==	NOOFMAILS) { ?>
							<script>
								$(".bgclass").css({"display":"block"});
								$(".loadpos").css({"display":"block"});
							</script>
							<?php
							sleep(SLEEPDURATION);
							$sleeptime	=	0;
						}
						$k++;
					} 
				}
			} else {
				//$success = "Successfully sent emails to Candidates";
			}
		}
	}
	?>
	<script>
		$(".bgclass").css({"display":"none"});
		$(".loadpos").css({"display":"none"});
	</script>
	<?php
	//exit;
}
//echo $error;
?>
<style type="text/css">
#errorid {
	display:none;
	background-color:#C1C1C1;
	color:#FF0000;
	font-size:11px;
	font-family:Arial;
	width:78%;
	height:25px;
	border-radius:10px;
	text-align:center;
}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		
		$("input[name='mailcancel']").click(function() {							
			window.location = "index.php";
		});
		
		$("input[name='mailsubmit']").click(function() {
			var mailtemp		=	$('#mailtemp').val();
			var mailfile		=	$('#mailfile').val();

			console.log(mailfile);

			if(mailtemp == '') {
				$("#errorid").css("display","block");
				$(".erroridh3").html('ERR : Select Mail Template ');
				setTimeout(function() {
					//$("#errorid").hide();
				},5000);
				$("#mailtemp").focus();
				return false;
			} else if(mailfile == '') {
				$("#errorid").css("display","block");
				$(".erroridh3").html('ERR : Select Mail File ');
				setTimeout(function() {
					$("#errorid").hide();
				},5000);
				$("#mailfromid").focus();
				return false;
			} else if(mailfile != '') {
				 var ext = mailfile.split('.');
				 var fileext	=	ext[1].toLowerCase();
				 console.log(fileext);
				//return false;
				 if(fileext != 'csv') {
						$("#errorid").css("display","block");
						$(".erroridh3").html('ERR : Only .csv or .CSV File');
						setTimeout(function() {
							$("#errorid").hide();
						},5000);
						$("#mailfromid").focus();
						return false;
				 }
			} 			
			//return false;				 
		});
		$(".closebtn").click(function() {
			$("#errorid").css("display","none");
		});
		
	});		
</script>
<form method="post" enctype='multipart/form-data'>
	<div <?php if(isset($error) && $error != '') { ?> class="errpos" <?php } elseif(isset($success) && $success != '') { ?> class="sucpos"  <?php } ?> ><?php if(isset($error) && $error != '') { echo $error; } elseif(isset($success) && $success != '') { echo $success; } ?></div>
	<div class="cenpos" >
	<table align='center' cellpadding="10" cellspacing="1" class="mstab">
		<tr>
			<th colspan="3" align="center">Mail Sending Form</th>
		</tr>		
		<tr>
			<td>Template</td><td>:</td>
				<td>
					<select name="mailtemp" id="mailtemp" >
					<option value="" >--Select--</option>						
					<?php $query_temp	=	"SELECT id,mailtemp FROM mailtemplate";
						  $result_temp	=	mysql_query($query_temp) or die(mysql_error());
						  $rows_temp	=	mysql_num_rows($result_temp);
						  if($rows_temp > 0) {
							while($row_temp	=	mysql_fetch_array($result_temp)) {
					?>
					<option value="<?php echo $row_temp['id']; ?>" ><?php echo ucwords($row_temp['mailtemp']); ?></option>
						<?php } 
						  } ?>
					</select>			
			</td>
		</tr>
		<tr>
			<td>File Upload </td><td>:</td><td><input type="file" name="mailfile" id="mailfile" size="50" /></td>
		</tr>
		<tr>
			<td align="center" colspan='3'><input type="submit" name="mailsubmit" value="Submit"/>&nbsp;&nbsp;&nbsp;<input type="button" name="mailcancel" value="Cancel"/></td>
		</tr>
		<tr>
			<td colspan="3" align="center"><div id="errorid" style="display:none;"><h3 class="erroridh3"></h3><span class="closebtn" style="float:right;margin-top:-48px;" ><img src="images/close_pop.png" /></span></div></td>
		</tr>
	</table>
	</div>
</form>
<?php include("includes/footer.php"); ?>