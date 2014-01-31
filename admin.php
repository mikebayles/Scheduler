<?php
	session_start(); 
	if(!isset($_SESSION['loggedin']) || $_SESSION['isadmin'] == false)
	{
		header("Location: login.php");
	    die("To access this page, you need to <a href='login.php'>LOGIN</a>"); // Make sure they are logged in!
	}
	
	
	//db config
	$DBhostname = "rei.cs.ndsu.nodak.edu";
	$DBusername = "mbayles";
	$DBpassword = "Zz8N9aYXt6";
	$DBdatabase = $DBusername;
 
	
?>


<!DOCTYPE html>
	<html >
	
	<head>
	
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<link rel="stylesheet" type="text/css" href="owner.css" >
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css">
			
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
		<script src="password.js"></script>

		<title>Welcome Admin</title>
		
		<style>
			.ui-tabs .ui-tabs-nav li a 
			{
				font-size:15pt !important;
			}

		</style>
		
		<script>
			var hash= window.location.hash;
			$("a[href='"+hash+"']").click();
		
		
			$(document).ready(function ()
			{
				//create the tab view
				$( "#tabs" ).tabs({heightStyle: "content"});
				
				//only show the selected user in the Modify User screen
				$("#selUsername").change(function (){
				
					var btn = $("#frmModifyUser input[type=button]");
					var user = $(this).val();
					
					if(user == 'blank')
						btn.attr('disabled','disabled');
					else
						btn.removeAttr('disabled');
					
					$("#modifyUser div").hide();														
					document.getElementById(user).style.display = 'block';					
				});
				
				//ajax to controller.php to check password strength
				$("input[name=password]").keyup(function (){
				
					var textbox = $(this);
					var password = textbox.val();
					$.post('controller.php', {sub : 'passStrength', password : password}, function(data){
					
						var a = $(textbox).parent().next();
						a.html(data);
					
					});
				
				});
				
				//check if the username is taken by ajax to controller.php
				$("#frmAddUser input[name=username]").keyup(function (){
				
					var textbox = $(this);
					var username = textbox.val();
					$.post('controller.php', {sub : 'userExists', username : username}, function(data){
					
						var results = $(textbox).parent().next();
						var valid = (data == 'false');
						var btn = $("#frmAddUser input[type=submit]");
						if(valid)
						{
							results .html('<span style="color:red;">Username taken</span>');
							btn.attr('disabled','disabled');
						}
						else
						{
							results .html('<span style="color:green;">Username available</span>');
							btn.removeAttr('disabled');
						}
						
						
					
					});
				
				
				});
			
			});
			
			

			
			function logout()
			{
				$.post('controller.php', {sub: 'logout'}, function(data)
				{
					window.location='login.php';
				});
			}
			
			function addUser()
			{
				
				$.post('controller.php', $("#frmAddUser").serialize(), function(data)
				{
					alert(data);
					window.location="admin.php#addUser";
					window.location.reload();				
				});
			
			}

			
			
			function modifyUser()
			{
				$("#frmModifyUser input[type=hidden]").val("modifyUser");
				
				$.post('controller.php', $("#frmModifyUser").serialize(), function(data)
				{
					alert(data);
					window.location="admin.php#modifyUser";
					window.location.reload();									
				});
			
			}
			
			function deleteUser()
			{
				$("#frmModifyUser input[type=hidden]").val("deleteUser");
				
				$.post('controller.php', $("#frmModifyUser").serialize(), function(data)
				{
					alert(data);
					window.location="admin.php#modifyUser";
					window.location.reload();
									
				});
			
			}


		</script>
		
	</head>
	
	<body>
	
		<p id="logout"><a href="#" onclick="logout()">Logout</a></p>	
		<h2 style="text-align:center">Welcome Administrator</h2>

		<div id="tabs">
			<ul>
				<li><a href="#addUser">Add User</a></li>
				<li><a href="#modifyUser">Modify User</a></li>
				
			</ul>
			
			<div id="addUser">
				<form id="frmAddUser" action="javascript:addUser();">
					<input type="hidden" name="sub" value="addUser">
					<table>
						<tr>
							<th>Username</th>
							<td><input type="text" name="username" required="required"> </td><td></td>
						</tr>
						<tr>
							<th>Password</th>
							<td id="no"><input type="text" name="password" maxlength="30"></td><td id="yes"></td>
						</tr>
						<tr>
							<th>First Name</th>
							<td><input required="required" type="text" name="first" maxlength="30"></td>
						</tr>
						<tr>
							<th>Last Name</th>
							<td><input required="required" type="text" name="last" maxlength="30"></td>
						</tr>
						<tr>
							<th>Office</th>
							<td><input type="text" name="office" maxlength="30"></td>
						</tr>
						<tr>
							<th>Phone Number</th>
							<td><input type="text" name="phone" maxlength="30"></td>
						</tr>
						<tr>
							<th>Email</th>
							<td><input type="text" name="email" maxlength="60"></td>
						</tr>
						<tr>
							<th>Allow Admin Access?</th>
							<td><input type="checkbox" name="isadmin"></td>
						</tr>
						
					</table>
					<input type="submit" value="Save Settings" disabled="disabled" id="btnAddUser">
				</form>
			
			</div>
			<div id="modifyUser">
				<form id="frmModifyUser">
				<input type="hidden" name="sub" value="modifyUser">
					<?php 
						
						mysql_connect($DBhostname , $DBusername , $DBpassword ) 
									or die("Could not connect to MySQL server! ");
																  
						mysql_select_db($DBdatabase ) or die( "Unable to select database");
											
						$query = "select * from owner";
						
						$result = mysql_query($query);
						
						$num=mysql_numrows($result);
						
						echo "<select id='selUsername' name=username>";
						echo "<option value=blank>Select a user to modify</option>";
						$a=0;
						while($a < $num)
						{
							$username = mysql_result($result,$a,"username");
							$label = mysql_result($result,$a,"first");
							$label = $label. " ".mysql_result($result,$a,"last");
							$label = $label."&nbsp;($username)";
							
							echo "<option value=$username>$label</option>";
							$a++;
						}
						echo "</select>";
	
						$a=0;
						while($a < $num)
						{
							$username = mysql_result($result,$a,"username");
							$password = mysql_result($result,$a,"password");
							$first = mysql_result($result,$a,"first");
							$last = mysql_result($result,$a,"last");
							$office = mysql_result($result,$a,"office");
							$phone = mysql_result($result,$a,"phone");
							$email = mysql_result($result,$a,"email");
							$isadmin = mysql_result($result,$a,"isadmin");												
							$isadminbool = ($isadmin == 1) ?  'checked ' : '';
							
						?>
							<div id="<?php echo $username;?>" style="display:none">
								<table>
									<tr>
										<th>Password</th>
										<td><input type="text" required name="<?php echo $username?>password" maxlength="30" value="<?php echo $password?>"></td><td></td>
									</tr>
									<tr>
										<th>First Name</th>
										<td><input type="text" name="<?php echo $username?>first" value="<?php echo $first;?>" ></td>
									</tr>
									<tr>
										<th>Last Name</th>
										<td><input type="text" name="<?php echo $username?>last" value="<?php echo $last ;?>"></td>
									</tr>
									<tr>
										<th>Office</th>
										<td><input type="text" name="<?php echo $username?>office" value="<?php echo $office;?>"></td>
									</tr>
									<tr>
										<th>Phone Number</th>
										<td><input type="text" name="<?php echo $username?>phone" value="<?php echo $phone;?>"></td>
									</tr>
									<tr>
										<th>Email</th>
										<td><input type="text" name="<?php echo $username?>email" value="<?php echo $email;?>"></td>
									</tr>
									<tr>
										<th>Allow Admin Access?</th>
										<td><input type="checkbox" <?php echo $isadminbool;?> name="<?php echo $username?>isadmin"></td>
									</tr>
	
	
	
								</table>
							
							</div>
						
						<?php
							$a++;
						}
						
						mysql_close();
	
					?>
					<input type="button" onclick ="modifyUser()" value="Save Settings" disabled="disabled" id="btnModifyUser">
					<input type="button" onclick ="deleteUser()" value="Delete Selected" disabled="disabled" id="btnDeleteUser">				
				</form>
			</div>
		</div>
	
	</body>

</html>
