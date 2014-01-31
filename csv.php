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
	
	$username = $_SESSION['username'];
		
	mysql_connect($DBhostname , $DBusername , $DBpassword ) 
		or die("Could not connect to MySQL server! ");

	mysql_select_db($DBdatabase ) or die( "Unable to select database");

	
	$select = "SELECT schedule.name as 'Schedule Name',
				user.username as 'Student ID', 
				user.first as 'Student First Name', 
				user.last as 'Student Last Name',
				user.email as 'Student Email',
				major.major as 'Student Major',
				datetime as 'Appointment Time', 
				reason as 'Appointment Reason'		
				  
					FROM 
						schedule join scheduleconfig on  schedule.id = scheduleconfig.scheduleid join
						owner on schedule.owner = owner.username join
						scheduleentry on schedule.id = scheduleentry.scheduleid join
						user on scheduleentry.userid = user.username join
						major on user.majorid = major.id

					WHERE	
						owner.username = '$username'";
								
	if(isset($_GET['date1']) && isset($_GET['date2']))
	{
		$date1 = $_GET['date1'];
		$date2 = $_GET['date2'];		
		$select = $select." and datetime BETWEEN '$date1' AND '$date2'";
	}

	$export = mysql_query ( $select ) or die ( "Sql error : " . mysql_error( ) );
	
	$fields = mysql_num_fields ( $export );
	
	for ( $i = 0; $i < $fields; $i++ )
	{
	    $header .= mysql_field_name( $export , $i ) . ",";
	}
	
	while( $row = mysql_fetch_row( $export ) )
	{
	    $line = '';
	    foreach( $row as $value )
	    {                                            
	        if ( ( !isset( $value ) ) || ( $value == "" ) )
	        {
	            $value = ",";
	        }
	        else
	        {
	            $value = str_replace( '"' , '""' , $value );
	            $value = '"' . $value . '"' . ",";
	        }
	        $line .= $value;
	    }
	    $data .= trim( $line ) . "\n";
	}
	$data = str_replace( "\r" , "" , $data );
	
	if ( $data == "" )
	{
	    $data = "\n(0) Records Found!\n";                        
	}
	
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=schedule.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	print "$header\n$data";
	
	mysql_close();
	
	
?>