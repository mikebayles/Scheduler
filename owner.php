<?php
	session_start(); 
	if(!isset($_SESSION['loggedin']))
	{
		header("Location: login.php");
	    die("To access this page, you need to <a href='login.php'>LOGIN</a>"); // Make sure they are logged in!
	}
	
	
	$DBhostname = "rei.cs.ndsu.nodak.edu";
	$DBusername = "mbayles";
	$DBpassword = "Zz8N9aYXt6";
	$DBdatabase = $DBusername;
 
	
?>

<!DOCTYPE html>
	<html >
	
	<head>
	
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<title>Welcome Schedule Owner</title>
		
		<link rel="stylesheet" type="text/css" href="owner.css" >
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css">
			
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
		<script src="password.js"></script>
		<script src="timepicker.js"></script>
		<script>
			var hash= window.location.hash;
			$("a[href='"+hash+"']").click();
			
			
			$(function() 
			{
				$( "#tabs" ).tabs({heightStyle: "content"});
				$("#accordion").accordion({collapsible: true, heightStyle: "content", header: 'h4'});
				$("#timepicker1, #timepicker2").datetimepicker({dateFormat: 'yy/mm/dd', timeFormat:'HH:mm:ss', showSeoncd: true});
				$("#newPass1").complexify({}, function (valid, complexity) 
				{
					if (!valid) 
					{
						$('#progress').css({'width':complexity + '%'}).removeClass('progressbarValid').addClass('progressbarInvalid');
					}
					else 
					{
						$('#progress').css({'width':complexity + '%'}).removeClass('progressbarInvalid').addClass('progressbarValid');
					}
					$('#complexity').html(Math.round(complexity) + '%');
				});	  	
				
				$("#newPass2, #newPass1").keyup(function() 
				{
					var newPass1 = $("#newPass1").val();
					var newPass2 = $("#newPass2").val();
					
					if(newPass1 != newPass2)
					{
						$("#response").html("<p style='color:red'>Passwords do not match</p>");
						$("#btnChangePass").attr('disabled','disabled');
					
					}
					else if(newPass2 != '')
					{
						$("#btnChangePass").removeAttr('disabled');
						$("#response").html("<p style='color:green'>Passwords match</p>");
					}
				
				});
				
				
				$(".colorInput").keyup(function ()
				{
				
					var closest = $(this).nextAll("div:first");
					var newColor = $(this).val();
					closest.css('background-color',newColor);
				
				});
				
				$("#review table form").submit(function() {
					var data = $(this).serializeArray();
					data.push({name : 'sub', value: 'editEntry'});
					
					$.post('controller.php',data,function(rep)
					{
						$("#review pre").html(rep);
					});
					
					return false;
				});
				
			
			});
			
			
			
			function checkPassword()
			{
				$.post('controller.php', $('#frmChangePass').serialize(), function(data)
				{					
					alert(data);
					if(data == "Your session has expired")
						window.location = "login.php";
					else if(data == "Current password not correct" || data == "Password changed!")
					{
						$("#frmChangePass")[0].reset();	
						$("#response").html('');
						$("#complexity").html('0%');
						$("#progress").css('width','0px');
						$("#currentPass").focus();
					}
				});
			
			}
			
			function enableSchedule()
			{
				if($("#frmEnableSchedule select").val() == 0)
				{
					alert("Please select a schedule");
					return false;
				}
				
				$.post('controller.php', $('#frmEnableSchedule').serialize(), function(data)
				{	
				
					$("#frmEnableSchedule")[0].reset();
					if(data == 'Your session has expired')
						window.location='login.php';
					
					alert(data);
				});
			}
			
			function saveLogin()
			{
				$.post('controller.php', $("#frmLoginManage").serialize(), function(data)
				{
					alert(data);
				});
			
			}
			
			function logout()
			{
				$.post('controller.php', {sub: 'logout'}, function(data)
				{
					window.location='login.php';
				});
			}
			
			function updateLink(value)
			{

				if(isNumber(value))
				{
					$("#scheduleLink").attr('href','schedule.php?scheduleID='+value);
					$("#scheduleLink").parent('span').show();
					$("#frmConfig input[type=button]").removeAttr('disabled');
				}
				else
				{
					$("#scheduleLink").parent('span').hide();
					$("#frmConfig input[type=button]").attr('disabled','disabled');
				}
			}
			function isNumber(n) 
			{
				return !isNaN(parseFloat(n)) && isFinite(n);
			}
			
			
			function submitConfig()
			{
				$.post('controller.php', $("#frmConfig").serialize(), function(data)
				{
					alert(data);
					window.location.reload();
				});

			
			}
			
			function deleteSchedule()
			{
				if(confirm("Do you really want to delete this schedule?"))
				{
					var id = $('#frmConfig select[name="scheduleid"]').val();
					$.post('controller.php',{sub: 'deleteSchedule', scheduleid : id}, function(data)
					{
						alert(data);
						window.location.reload();
					});
					
				}
			}
			
			
			function getCSV()
			{
				var url = 'csv.php';
				var date1 = $("#timepicker1").val();
				var date2 = $("#timepicker2").val()
				if(date1 && date2)
				{
					url += '?date1=' + date1 + '&date2=' + date2;
				}
				window.open(url,'_blank');		
			}
			
		</script>
		
		
	</head>
	
	<body>
		<p id="logout"><a href="#" onclick="logout()">Logout</a></p>	
		<h2 style="text-align:center">Welcome Schedule Owner</h2>
		<div id="tabs">
			<ul>
				<li><a href="#loginPref">Login Management</a></li>
				<li><a href="#changePass">Change Password</a></li>
				<li><a href="#enableSchedule">Enable/Disable Schedule</a></li>
				<li><a href="#configSchedule">Configure Schedule</a></li>
				<li><a href="#createTemplate">Create Weekly Template</a></li>
				<li><a href="#review">Review Schedule</a></li>
				<li><a href="#download">Download Schedule</a></li>
			</ul>
			<div id="loginPref">
				<form id="frmLoginManage" action="javascript:saveLogin()">
					<input type="hidden" name="sub" value="saveLogin">
					<table>
						<tr>
							<th>First Name</th>
							<td><input type="text" maxlength="30" name="first"></td>
						</tr>
						<tr>
							<th>Last Name</th>
							<td><input type="text" maxlength="30" name="last"></td>
						</tr>
						<tr>
							<th>Office</th>
							<td><input type="text" maxlength="30" name="office"></td>
						</tr>
						<tr>
							<th>Phone</th>
							<td><input type="text" maxlength="30" name="phone"></td>
						</tr>
						<tr>
							<th>Email Address</th>
							<td><input type="text" maxlength="60" name="email"></td>
						</tr>
						<tr>
							<th><input type="submit" id="btnLoginManage" value="Submit" ></th>
						</tr>

					</table>
				</form>
			</div>
			<div id="changePass">
				<form id="frmChangePass" action="javascript:checkPassword();">
					<input type="hidden" name="sub" value="checkPassword">
					<table>

						<tr>
							<th>Current Password</th>
							<td><input type="password" name="currentPass" required="required" id="currentPass"></td>
						</tr>
						<tr>
							<th>New Password</th>
							<td><input type="password" name="newPass1" required="required" id="newPass1"></td>
						</tr>
						<tr>
							<th>Confirm New Password</th>
							<td><input type="password" name="newPass2" required="required" id="newPass2"></td>
						</tr>
						<tr>
							<th><input  style="margin-top:40px" type="submit" id="btnChangePass" value="Change Password"></th>
						</tr>
					</table>
					
					<div id="strength">
						<div id="progressbar">
							<div id="progress"></div>
						</div>
						
						<div id="status">
							<div id="complexity">0%</div>
							<div id="complexityLabel">Complexity</div>
						</div>
						<div id="response"></div>
					</div>
					
				</form>
				<p style="clear:both"></p>
			</div>
			<div id="enableSchedule">
				<form id="frmEnableSchedule" action="javascript:enableSchedule()">
					<input type="hidden" name="sub" value="enableSchedule">
					
					<?php
						mysql_connect($DBhostname , $DBusername , $DBpassword ) 
							or die("Could not connect to MySQL server! ");
														  
						mysql_select_db($DBdatabase ) or die( "Unable to select database");
						
						$username = $_SESSION['username'];
						$query = "select * from schedule where owner = '$username'";
						
						
						$result = mysql_query($query);
				
						$num=mysql_numrows($result);
						
						if($num == 0)
						{
							echo "No schedules found, go to ";
							echo '<a href="#" onclick="$(\'#tabs\').tabs(\'option\',\'active\', 2); return false;">Configure Schedule</a>';
							echo " to create one";
						}
						else
						{
							echo "<select name=scheduleid>";
							echo "<option value=0>select a schedule</option>";
							$a=0;
							while($a < $num)
							{
								$scheduleid = mysql_result($result,$a,"id");
								$label = mysql_result($result,$a,"name");
								echo "<option value=$scheduleid>$label</option>";
								$a++;
							}
							echo "</select>";

					?>
					<table>
						<tr>
							<th style="height:150px">Enable/Disable</th>
							<td><input style="margin: 18px 0 8px 20px;" type="radio" checked="checked" name="scheduleEnable" value="1"> Enable  <input type="radio" name="scheduleEnable" value="0">Disable</td>	
						</tr>
						<tr>
							<th>Disabled Message Type</th>
							<td><input style="margin: 18px 0 8px 20px;" type="radio" name="disabledMessageType" value=0 checked="checked"> Default <input type="radio" name="disabledMessageType" value=1> Custom Message (below)</td>
						</tr>
						<tr>
							<th>Disabled Schedule Message</th>
							<td><textarea rows="14" cols="50" name="disabledMessage" id="disabledMessage" maxlength="1000" ></textarea></td>
						</tr>
						<tr>
							<th><input type="submit" id="btnEnable" value="Submit" ></th>
						</tr>

					</table>
				
				</form>
					<?php
					
						}
						mysql_close();
					?>
			</div>
			<div id="configSchedule">
				<form id="frmConfig" action="javascript:submitConfig()">
					<p>Use each section to make changes. Press submit at the bottom when you are done</p>
					<?php
						
							mysql_connect($DBhostname , $DBusername , $DBpassword ) 
								or die("Could not connect to MySQL server! ");
															  
							mysql_select_db($DBdatabase ) or die( "Unable to select database");
							
							$username = $_SESSION['username'];
							$query = "select * from schedule where owner = '$username'";
							
							
							$result = mysql_query($query);
					
							$num=mysql_numrows($result);
							
							echo "<select name=scheduleid onchange='updateLink(this.value);'>";
							echo "<option value=newSchedule>New Schedule</option>";
							$a=0;
							while($a < $num)
							{
								$scheduleid = mysql_result($result,$a,"id");
								$label = mysql_result($result,$a,"name");
								echo "<option value=$scheduleid>$label</option>";
								$a++;
							}
							echo "</select>";
		
							
						
						?>
				
					<span style="display:none;">View your schedule <a id="scheduleLink" href="#">here</a></span>
					<div id="accordion" style="margin-top:25px;">
						
						<h4>General Configuration</h4>
							
						<div>
							<table>
								<tr>
									<th>Schedule Name</th>
									<td><input type="text" name="name" maxlength="30" placeholder="My Schedule"></td>
								</tr>
								<tr>
									<th>Schedule Background Color</th>
									<td><input type="text" class="colorInput" name="bgpagecolor" maxlength="30" placeholder="#112233 or black"><div class="colorTest"> </div></td>
								</tr>
	
							</table>
						</div>
						<h4>Time Configuration</h4>
						<div>
							<table>
								<tr>
									<th>Time Between Appointments (Delta)</th>
									<td>
										<select name="delta">
											<option value="">Pick 1</option>
											<option value="15">15 min</option>
											<option value="30">30 min</option>
											<option value="45">45 min</option>
											<option value="60">60 min</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>Start Hour (24 hour)</th>
									<td><input type="number" name="startingtime" min="0" max="23"placeholder="eg 8"></td>
								</tr>
								<tr>
									<th>End Hour (24 hour)</th>
									<td><input type="number" name="endingtime" min="0" max="23"placeholder="eg 17"></td>
								</tr>
								<tr>
									<th>Blockout Lead Time</th>
									<td><input type="number" name="blockleadtime" min="0"></td>
								</tr>
								<tr>
									<th>Email Lead Time</th>
									<td><input type="number" name="emailleadtime" min="0"></td>
								</tr>

							</table>
	
							
						</div>
						
						<?php 
						
							for($i=1; $i <=8; $i++)
							{
						?>
						
								<h4>Line <?php echo $i ?> Configuration</h4>
								<div>
									<table>
										<tr>
											<th>Text</th>
											<td><input type="text" name="line<?php echo $i ?>" maxlength="30"></td>
										</tr>
										<tr>
											<th>Font</th>
											<td>
												<input type="text" name="line<?php echo $i ?>font" maxlength="30">
											</td>
										</tr>
										<tr>
											<th>Font Size</th>
											<td>
												<input type="number" min="8" max="70" name="line<?php echo $i ?>fontsize">
											</td>
										</tr>
										<tr>
											<th>Font Color</th>
											<td>
												<input type="text" class="colorInput" maxlength="30" placeholder="#112233 or black" name="line<?php echo $i ?>color"><div class="colorTest"> </div>
											</td>
										</tr>
									</table>					
								</div>
	
						
						<?php										
							}
						
						?>
						
		
						<h4>Filled Cells Configuration</h4>
						<div>
							<table>
								<tr>
									<th>Font</th>
									<td><input type="text" name="fontfilled" maxlength="30"></td>
								</tr>
								<tr>
									<th>Font Size</th>
									<td><input type="text" name="fontsizefilled" maxlength="30"></td>
								</tr>
								<tr>
									<th>Text Color</th>
									<td>
										<input type="text" class="colorInput" maxlength="30" placeholder="#112233 or black" name="fgfilledcolor"><div class="colorTest"></div>
									</td>
								</tr>
								<tr>
									<th>Background Color</th>
									<td>
										<input type="text" class="colorInput" maxlength="30" placeholder="#112233 or black" name="bgfilledcolor"><div class="colorTest"></div>
									</td>
								</tr>
								<tr>
									<th>Filled Cell Type</th>
									<td>
										<input style="margin: 18px 0 8px 20px;" type="radio" checked="checked" name="filledcelltype" value=0> Username  <input type="radio" name="filledcelltype" value=1>Custom Message (Below)
									</td>								
								</tr>
								<tr>
									<th>Filled Cell Message</th>
									<td><input type="text" name="filledcellline" maxlength="30"></td>
								</tr>
							</table>
						</div>
						<h4>Empty Cells Configuration</h4>
						<div>
							<table>
								<tr>
									<th>Font</th>
									<td><input type="text" name="fontempty" maxlength="30"></td>
								</tr>
								<tr>
									<th>Font Size</th>
									<td><input type="text" name="fontsizeempty" maxlength="30"></td>
								</tr>
								<tr>
									<th>Text Color</th>
									<td>
										<input type="text" class="colorInput" maxlength="30" placeholder="#112233 or black" name="fgemptycolor"><div class="colorTest"></div>
									</td>
								</tr>
								<tr>
									<th>Background Color</th>
									<td>
										<input type="text" class="colorInput" maxlength="30" placeholder="#112233 or black" name="bgemptycolor"><div class="colorTest"></div>
									</td>
								</tr>
							</table>
						</div>
						
						<h4>Custom Entry Fields</h4>
						<div>
							<table>
							<?php 
							
								for($i = 1; $i<=4; $i++)
								{
								
							?>
							
								<tr>
									<th colspan="2" style="text-align:center; border:1px black solid">Custom Field <?php echo $i; ?></th>
								</tr>
								<tr>
									<th style="text-align:center">Field Label</th>
									<th style="text-align:center">Field Enabled</th>
								</tr>
								<tr style="height: 100px;">
									<td>
										<input type="text" maxlength="30" name="udfield<?php echo $i; ?>label">
									</td>
									<td>
										<input style="margin: 18px 0 8px 20px;" type="radio"  name="udfield<?php echo $i; ?>enabled" value=1> Enable  <input type="radio" checked="checked" name="udfield<?php echo $i; ?>enabled" value=0>Disable
									</td>
								</tr>
								
								
							<?php
								
								}
								
							
							?>
							</table>
						</div>
					</div>
					
					<input type="hidden" name="sub" value="saveConfig">
					<br><br><br>
					<input style="float:left" type="submit" value="Save Settings">
					<input type="button" disabled="disabled" onclick="deleteSchedule();" value="Delete Schedule" style="background-color:rgba(255,0,0,.5)">
				</form>
			</div>
			<div id="createTemplate"></div>
			
			
			<div id="review">
				<a href="#" onclick="$('.optional').toggle(); $('#review table').toggleClass('normalsize tinyfont');">Show/Hide details</a>
				<br><br>
				<table border="1" class="normalsize">
					<tr>
						<th class="optional">Schedule ID</th>
						<th class="optional">Schedule Name</th>
						<th>Appointment Date and Time</th>
						<th>Student ID</th>
						<th>Student Name</th>						
						<th>Student Email</th>
						<th>Student Major</th>
						<th class="optional">Appointment Reason</th>
						<th class="optional">Custom Field 1</th>
						<th class="optional">Custom Field 2</th>
						<th class="optional">Custom Field 3</th>
						<th class="optional">Custom Field 4</th>
					</tr>
				
				<?php
						
					mysql_connect($DBhostname , $DBusername , $DBpassword ) 
						or die("Could not connect to MySQL server! ");
													  
					mysql_select_db($DBdatabase ) or die( "Unable to select database");
					
					$username = $_SESSION['username'];
					$query = "select
								scheduleentry.id as 'Entry ID', 
								schedule.id as 'Schedule ID', 
								schedule.name as 'Schedule Name',
								scheduleentry.datetime as 'Appointment Date and Time',
								user.username as 'Student ID',
								concat(user.first, ' ', user.last) as 'Student Name',
								user.email as 'Student Email',
								majorid,
								reason,
								udfield1 as 'Custom Field 1',
								udfield2 as 'Custom Field 2',
								udfield3 as 'Custom Field 3',
								udfield4 as 'Custom Field 4'
									from schedule, scheduleentry, user, major 
										where 
											schedule.id = scheduleentry.scheduleid and 
											majorid = major.id and 
											scheduleentry.userid = user.username and
											schedule.owner ='$username'";
					
					
					$result = mysql_query($query);
			
					$num=mysql_numrows($result);
					
					$query = "select * from major order by major";
					$majorResults = mysql_query($query);
									
					$i = 0;
					while($a < mysql_numrows($majorResults))
					{
						echo "<option value=".mysql_result($majorResults,$a,"id").">".mysql_result($majorResults,$a,"major")."</option>";
						$a++;
					}

					
					$a=0;
					while($a < $num)
					{
						$entryid 		= mysql_result($result,$a,"Entry ID");
						$scheduleid 	= mysql_result($result,$a,"Schedule ID");
						$schedulename 	= mysql_result($result,$a,"Schedule Name");
						$appt			= mysql_result($result,$a,"Appointment Date and Time");
						$studentid 		= mysql_result($result,$a,"Student ID");
						$studentname 	= mysql_result($result,$a,"Student Name");
						$studentemail 	= mysql_result($result,$a,"Student Email");
						$majorid		= mysql_result($result,$a,"majorid");
						$reason			= mysql_result($result,$a,"reason");
						$udfield1		= mysql_result($result,$a,"Custom Field 1");
						$udfield2		= mysql_result($result,$a,"Custom Field 2");
						$udfield3		= mysql_result($result,$a,"Custom Field 3");
						$udfield4		= mysql_result($result,$a,"Custom Field 4");
						
						echo "<tr>";
							echo "<form>";
							echo "<input type=hidden name='entryid' value='$entryid'>";
							echo "<td class='optional'>$scheduleid</td>";
							echo "<td class='optional'>$schedulename</td>";
							echo "<td><input type='text' name='datetime' value='$appt'></td>";
							echo "<td><input type='text' readonly name='studentid' value='$studentid'></td>";
							echo "<td>$studentname</td>";
							echo "<td><input name='studentemail' value='$studentemail'></td>";
							echo "<td>";
							echo	"<select name=majorid>";
										$i = 0;
										while($i < mysql_numrows($majorResults))
										{
											$major = mysql_result($majorResults,$i,"major");
											$id = mysql_result($majorResults,$i,"id");
											
											
											if($id != $majorid)
												echo "<option value=$id>$major</option>";
											else
												echo "<option value=$id selected='selected'>$major</option>";
											$i++;
										}
							echo 	"</select>";
							echo "<td class='optional'><input type='text' name='reason' value='$reason'></td>";
							echo "<td class='optional'><input type='text' name='udfield1' value='$udfield1'></td>";
							echo "<td class='optional'><input type='text' name='udfield2' value='$udfield2'></td>";
							echo "<td class='optional'><input type='text' name='udfield3' value='$udfield3'></td>";
							echo "<td class='optional'><input type='text' name='udfield4' value='$udfield4'></td>";
							echo "<td>Unschedule? <input type='checkbox' name='unschedule'></td>";
							echo "<td>Hide? <input type='checkbox' name='hide'></td>";
							echo "<td><input type=submit value='Save Settings'></td>";
							echo "</form>";
						
						echo "</tr>";
					
						
						$a++;
					}
					
	
					
				
				?>
				</table>
				<pre style="color:red"></pre>
			</div>
			
			
			
			<div id="download">
				<p>Pick the dates you'd like to find your appointments between (or leave blank for all)</p>
				<table>
					<tr>
						<th>Starting Date</th>
						<td>
							<input type="text" id="timepicker1">	
						</td>
					</tr>
					<tr>
						<th>Ending Date</th>
						<td>
							<input type="text" id="timepicker2">	
						</td>
					</tr>

				</table>
				<br><br><br>
				<button style="clear:both" onclick="getCSV();">Press me for a CSV</button>
							
			</div>
		</div>
		
		<footer>Email commments and/or questions to <a href="mailto:michael.bayles@ndsu.edu">Mike Bayles</a></footer>
	</body>

</html>
