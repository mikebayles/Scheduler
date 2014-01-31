<?php
		
	//db config
	$DBhostname = "rei.cs.ndsu.nodak.edu";
	$DBusername = "mbayles";
	$DBpassword = "Zz8N9aYXt6";
	$DBdatabase = $DBusername;
 
 
	mysql_connect($DBhostname , $DBusername , $DBpassword ) 
		or die("Could not connect to MySQL server! ");
	
	mysql_select_db($DBdatabase ) or die( "Unable to select database");
	
	
	$query = "select * from schedule";
	
	
	$result = mysql_query($query);
	$num=mysql_numrows($result);
	
?>




<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<title>Schedulizer</title>
		
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="main.css">
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
		<script src="masonry.js"></script>
		
		
		<script>
		
		$(function()
		{
			$('#container').masonry({
				// options
				itemSelector : '.item',
				columnWidth : 200
			});
			
			
			$(".item").click(function() {
			
				var url = $(this).children("iframe:first").attr("src");
				window.location = "http://latimer-vm1.cs.ndsu.nodak.edu/~mbayles/schedule/" + url;
			});
			
			
			var arr = [];
			<?php
			
				$a = 0;
				while($a < $num)
				{
				
				$id = mysql_result($result,$a,"id");
				$name = mysql_result($result,$a,"name");
				$owner = mysql_result($result,$a,"owner");
				
				echo "arr.push({value : 'schedule.php?scheduleID=$id', label: '$owner : $name', desc: '$owner : $name'});";
				
				$a++;
				}
			
			?>
			
			$("#tags").autocomplete({source : arr, select: function(event, ui){window.location=ui.item.value;}});
		});
				
				
			
		</script>
		
	</head>
	
	<body>
	
		<div id="wrapper">
			
			<div class="ui-widget">
				<label for="tags">Find a Schedule:</label>
				<input id="tags">
			</div>
			<br><br>
			<div id="container">
				
				<?php
					
			
					
					
					$a = 0;
					while($a < $num)
					{
					
						$id = mysql_result($result,$a,"id");
						$name = mysql_result($result,$a,"name");
						$owner = mysql_result($result,$a,"owner");
				?>
					
					<div class="item">
						
						<h4><?php echo $owner.":<br>".$name; ?></h4>
						<iframe class="frame" src="schedule.php?scheduleID=<?php echo $id; ?>"></iframe>
					
					</div>
					
				<?php
				
					$a++;
					}
				
				?>

							
			</div>
		
		</div>
		
	</body>
</html>