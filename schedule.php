<!DOCTYPE html>
	<html >
	
	<head>
	
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<title>Schedulizer</title>
		<link rel="stylesheet" type="text/css" href="schedule.css" >
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>

		<script type="text/javascript">
		
			$(document).ready(function(){
			
				$('input:radio[name=appt]').change(function(){
					
					$("#entry").show();
					$(".schedule").css('float','right');
				
				})
			
			});
			
			function ajaxSubmit()
			{
				if(!$('#first').val() || !$('#last').val() || !$('#idNumber').val() || !$('#email').val() || !$('#major').val() || !$('#reason').val())
				{
					alert("Please fill out all required fields (denoted by *)");
					return false;
				}
				$.post('controller.php', $('#entry, #scheduleForm').serialize(), function(data){
					
					alert(data);
					window.location.reload();
				});
			}
			
			function autoFill()
			{
			
				$.post('controller.php',{sub : 'autoFill', username : $('#idNumber').val()}, function(data){
					
					if(data != "false")
					{
						var decoded = jQuery.parseJSON(data);
						$('#first').val(decoded.first);
						$('#last').val(decoded.last);
						$('#email').val(decoded.email);
						$('#major').val(decoded.major);
					}
				
				});
			
			}
	
		</script>
		
	</head>
	
	<body>
		
		<?php
				
			$DBhostname = "rei.cs.ndsu.nodak.edu";
			$DBusername = "mbayles";
			$DBpassword = "Zz8N9aYXt6";
			$DBdatabase = $DBusername;
			
			$scheduleID = $_GET['scheduleID'];
			//$scheduleID = $argv[1];
			
			mysql_connect($DBhostname , $DBusername , $DBpassword ) 
				or die("Could not connect to MySQL server! ");
										  
			mysql_select_db($DBdatabase ) or die( "Unable to select database");
			
			$query = "select * from scheduleconfig, schedule, owner ". 
				"where scheduleconfig.scheduleid = $scheduleID and ".
				"scheduleconfig.scheduleid = schedule.id and ".
				"schedule.owner = owner.username";
							
				
			$result = mysql_query($query);
			
			$num=mysql_numrows($result);
			mysql_close();
			
			if($num != 1)
			{
				echo $query;
				echo "Invalid Schedule Selected";
				die();
			}
			
			$firstname = mysql_result($result,0,"first");
			$lastname = mysql_result($result,0,"last");
			$office = mysql_result($result,0,"office");
			$phone = mysql_result($result,0,"phone");
			$email = mysql_result($result,0,"email");
			
			
			$line1 = is_null(mysql_result($result,0,"line1")) ? "Advising Schedule for $firstname $lastname" : mysql_result($result,0,"line1");
			$line2 = is_null(mysql_result($result,0,"line2")) ? "$phone" : mysql_result($result,0,"line2");
			$line3 = is_null(mysql_result($result,0,"line3")) ? "$office" : mysql_result($result,0,"line3");
			$line4 = is_null(mysql_result($result,0,"line4")) ? "<a href=mailto:$email>$email</a>" : mysql_result($result,0,"line4");
			
			$line5 = mysql_result($result,0,"line5");
			$line6 = mysql_result($result,0,"line6");
			$line7 = mysql_result($result,0,"line7");
			$line8 = mysql_result($result,0,"line8");
			
			$bgpagecolor = mysql_result($result,0,"bgpagecolor");
			
			$fgemptycolor = mysql_result($result,0,"fgemptycolor");
			$bgemptycolor = mysql_result($result,0,"bgemptycolor");
			$fontempty = mysql_result($result,0,"fontempty");
			$fontsizeempty = mysql_result($result,0,"fontsizeempty")."px";
			
			$fgfilledcolor = mysql_result($result,0,"fgfilledcolor");
			$bgfilledcolor = mysql_result($result,0,"bgfilledcolor");
			$fontfilled = mysql_result($result,0,"fontfilled");
			$fontsizefilled = mysql_result($result,0,"fontsizefilled")."px";
			
			$current = "";
			
			if(isset($_POST['current']))
			{
				$current = $_POST['current'];
				
			}
			else
			{
				$monday = strtotime('last monday', strtotime('tomorrow'));
				$friday = strtotime("this friday",$monday);
				$mondayDate = date("m/d/y",$monday);
				$fridayDate = date("m/d/y",$friday);
			
										
				$current =  "$mondayDate - $fridayDate";

			}
					
			$enabled = mysql_result($result,0,"enabled");
			if(ord($enabled) == 1)
			{

		?>
			
			
			<p id="line1" 
				style='
					font-family:<?php echo mysql_result($result,0,"line1font");?>;
					font-size: <?php echo mysql_result($result,0,"line1fontsize"); ?>px;
					color: <?php echo mysql_result($result,0,"line1color"); ?>;'>
					
						<?php echo $line1; ?>
			</p>
			
			<p id="line2" 
				style='
					font-family:<?php echo mysql_result($result,0,"line2font");?>;
					font-size: <?php echo mysql_result($result,0,"line2fontsize");?>px;
					color: <?php echo mysql_result($result,0,"line2color"); ?>;'>
					
						<?php echo $line2; ?>
			</p>

			<p id="line3" 
				style='
					font-family:<?php echo mysql_result($result,0,"line3font");?>;
					font-size: <?php echo mysql_result($result,0,"line3fontsize");?>px;
					color: <?php echo mysql_result($result,0,"line3color"); ?>;'>
					
						<?php echo $line3; ?>
			</p>
			
			<p id="line4" 
				style='
					font-family:<?php echo mysql_result($result,0,"line4font");?>;
					font-size: <?php echo mysql_result($result,0,"line4fontsize");?>px;
					color: <?php echo mysql_result($result,0,"line4color"); ?>;'>
					
						<?php echo $line4; ?>
			</p>

			<p id="line5" 
				style='
					font-family:<?php echo mysql_result($result,0,"line5font");?>;
					font-size: <?php echo mysql_result($result,0,"line5fontsize");?>px;
					color: <?php echo mysql_result($result,0,"line5color"); ?>;'>
					
						<?php echo $line5; ?>
			</p>
			
			<p id="line6" 
				style='
					font-family:<?php echo mysql_result($result,0,"line6font");?>;
					font-size: <?php echo mysql_result($result,0,"line6fontsize");?>px;
					color: <?php echo mysql_result($result,0,"line6color"); ?>;'>
					
						<?php echo $line6; ?>
			</p>
			

			<p id="line7" 
				style='
					font-family:<?php echo mysql_result($result,0,"line7font");?>;
					font-size: <?php echo mysql_result($result,0,"line7fontsize");?>px;
					color: <?php echo mysql_result($result,0,"line7color"); ?>;'>
					
						<?php echo $line7; ?>
			</p>
			
			<p id="line8" 
				style='
					font-family:<?php echo mysql_result($result,0,"line8font");?>;
					font-size: <?php echo mysql_result($result,0,"line8fontsize");?>px;
					color: <?php echo mysql_result($result,0,"line8color"); ?>;'>
					
						<?php echo $line8; ?>
			</p>


			<div id="formWrapper">			
				<div id="dates">
					<form style="display:inline" action="schedule.php?scheduleID=<?php echo $scheduleID; ?>" method="post">
						 <?php
						 
							$datesPrev = explode(" - ",$current);
															
							$mondayPrev = strtotime("-1 week",strtotime($datesPrev [0]));
							$fridayPrev = strtotime("-1 week",strtotime($datesPrev [1]));
							$mondayPrevDate = date("m/d/y",$mondayPrev );
							$fridayPrevDate = date("m/d/y",$fridayPrev );
							
							$newWeekBack = "$mondayPrevDate - $fridayPrevDate ";
	
						 
						 ?>
						<input type="submit" id="subtractWeek" value=&lt;>
						<input type="hidden" name="current" value="<?php echo $newWeekBack;?>" >
					</form>
					<span id="week"><?php echo $current; ?></span>
					<form style="display:inline" action="schedule.php?scheduleID=<?php echo $scheduleID; ?>" method="post">
						 <?php
						 
							$datesForward = explode(" - ",$current);
															
							$mondayForward = strtotime("+1 week",strtotime($datesForward [0]));
							$fridayForward = strtotime("+1 week",strtotime($datesForward [1]));
							$mondayDateForward = date("m/d/y",$mondayForward );
							$fridayDateForward = date("m/d/y",$fridayForward );
							
							$newWeekForward = "$mondayDateForward - $fridayDateForward";
	
						 
						 ?>
						<input type="submit" id="addWeek" value=&gt;>
						<input type="hidden" name="current" value="<?php echo $newWeekForward;?>" >
					</form>
				</div>
				
				<form id="scheduleForm">
								
					
					<table class="schedule" id="schedule<?php echo $scheduleID; ?>" border="1">
					<?php
							$datesCurrent = explode(" - ",$current);
							
							$mondayCurrent = strtotime($datesCurrent [0]);
							$fridayCurrent = strtotime($datesCurrent [1]);
							$mondayDateCurrent = date("m/d/y G:i:s",$mondayCurrent );
							$fridayDateCurrent = date("m/d/y G:i:s",$fridayCurrent );
	
							$currentAppt = $mondayCurrent;
							
							$endingtime = mysql_result($result,0,"endingtime");
							$startingtime = mysql_result($result,0,"startingtime");
							$delta = mysql_result($result,0,"delta");


							$timeBetweenStartEnd = (24-$endingtime) + $startingtime;
							$rows = ($endingtime-$startingtime)*(60/$delta);
							
							$mondayCurrent = strtotime("+$startingtime hours", $mondayCurrent);
							
							echo "<tr>";
							echo "<th>Time</th>";
							for($x =0; $x<5; $x++)
							{
								
								echo "<th>".date("D m/d/y",strtotime("+ $x days",$mondayCurrent))."</th>";
								
							}						
							echo "</tr>";
							
							mysql_connect($DBhostname , $DBusername , $DBpassword ) 
								or die("Could not connect to MySQL server! ");
											  
							mysql_select_db($DBdatabase ) or die( "Unable to select database");
	
							
							for($i=0; $i<=$rows; $i++)
							{
								echo "<tr>";
								echo "<td>".date("h:i",$mondayCurrent),"</td>";
								for($j=0; $j<5; $j++)
								{											
									$temp = strtotime("+$j days",$mondayCurrent);
									$value = date("m/d/y H:i",$temp);
									$mysqlValue = date("Y-m-d H:i:s",$temp);
									
									$query = "select * from scheduleentry where scheduleid = $scheduleID and datetime = '$mysqlValue '";
									
									$apptResult = mysql_query($query);
									
									$num=mysql_numrows($apptResult);
			
			
									
									if($num == 0)
										echo "<td class='empty'><input type='radio' name='appt' value='$mysqlValue'  ></td>";
									else
									{
											$bit = ord(mysql_result($result,0,"filledcelltype"));
											
										 if( $bit== 0)
										 	echo "<td class='filled'>".mysql_result($apptResult,0,"userid")."</td>";
										 	
										else
											echo "<td class='filled'>".mysql_result($result,0,"filledcellline")."</td>";
									}
									
									
																	//echo date("m/d/y H:i",$temp)."     |     ";
								}	
								$mondayCurrent = strtotime("+$delta minutes", $mondayCurrent);
								echo "</tr>";
							}
							
							mysql_close();
							
					?>
						
					</table>
				</form>
				
				<form id="entry" action="javascript:ajaxSubmit();">
					<input type="hidden" name="scheduleid" value="<?php echo $scheduleID; ?>">
					<input type="hidden" name="sub" value="createEntry" >
					<table>
						<tr>
							<th>ID Number*</th>
							<td><input type="text" name="idNumber" id="idNumber" maxlength="30" onchange="autoFill();"></td>
						</tr>
						<tr>
							<th>First Name*</th>
							<td><input type="text" name="first" id="first" maxlength="30"></td>						
						</tr>
						<tr>
							<th>Last Name*</th>
							<td><input type="text" name="last" id="last" maxlength="30"></td>						
						</tr>
						
						<tr>
							<th>Email*</th>
							<td><input type="text" name="email" id="email" maxlength="60"></td>
						</tr>

						<tr>
							<th>Major*</th>
							<td>
								<select name="major" id="major">
									<option value="blank"></option>
									<?php
									
										mysql_connect($DBhostname , $DBusername , $DBpassword ) 
											or die("Could not connect to MySQL server! ");
											  
										mysql_select_db($DBdatabase ) or die( "Unable to select database");
									
										$query = "select * from major order by major";
										$majorResults = mysql_query($query);
									
										$a = 0;
										while($a < mysql_numrows($majorResults))
										{
											echo "<option value=".mysql_result($majorResults,$a,"id").">".mysql_result($majorResults,$a,"major")."</option>";
											$a++;
										}
										
										mysql_close();								
									?>
								</select>							
							</td>
						</tr>
						<tr>
							<th>Reason*</th>
							<td><textarea id="reason" name="reason" rows="5" maxlength="1000"></textarea></td>
						</tr>
						
						<?php
														
														
							
							for($i=1; $i<=4; $i++)
							{
								$fieldname = "udfield".$i;
								$label = mysql_result($result,0,$fieldname."label");
								$bitvalue = mysql_result($result,0,$fieldname."enabled");
								
								if($bitvalue == 1)
								{
									echo "<tr>";
									echo "<th>$label</th>";
									$name = "udfield".(string)($i);
									echo "<td><input maxlength='30' type='text' name='$name' id='$name'></td>"; 
									echo "</tr>";
								}
							}							
						?>
								
								
						
				</table>
					
					<br><br>
					<input type="submit" value="Create Appointment">
				</form>
			</div>
			
			<?php 
				
				}
				else
				{
					$messageType = mysql_result($result,0,"disabledscheduledisplaytype");
					$message = ord($messageType) == 1 ? mysql_result($result,0,"disabledscheduledisplayline") : "Schedule Currently Not Available";
					echo "<h1>$message</h1>";
				}
			
			?>
			
			<script type="text/javascript">
				//use jquery to create css rules given by the owner via php
				$('body').css('background-color','<?php echo $bgpagecolor; ?>');
				
				$('.empty').css({
					'color' : '<?php echo $fgemptycolor; ?>',
					'background-color' : '<?php echo $bgemptycolor; ?>',
					'font-family' : '<?php echo $fontempty; ?>',	
					'font-size' : '<?php echo $fontsizeempty; ?>'
				});
				
				$('.filled').css({
					'color' : '<?php echo $fgfilledcolor; ?>',
					'background-color' : '<?php echo $bgfilledcolor; ?>',
					'font-family' : '<?php echo $fontfilled; ?>',	
					'font-size' : '<?php echo $fontsizefilled; ?>'
				});

			</script>

	</body>

</html>
