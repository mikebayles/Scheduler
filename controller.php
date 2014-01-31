<?php
	session_start();

	$DBhostname = "rei.cs.ndsu.nodak.edu";
	$DBusername = "mbayles";
	$DBpassword = "Zz8N9aYXt6";
	$DBdatabase = $DBusername;



	$sub = $_POST['sub'];

	

	//use the switch to figure out what function to call
	switch($sub)
	{
		case "login":
			CheckLogin();
			break;
			
		case "createEntry":
			CreateEntry();
			break;
			
		case "saveLogin":
			SaveLogin();
			break;
			
		case "checkPassword":
			ChangePassword();
			break;
			
		case "enableSchedule":
			EnableSchedule();
			break;
			
		case "saveConfig":
			SaveConfig();
			break;
			
		case "deleteSchedule":
			DeleteSchedule();
			break;
			
		case "modifyUser":
			ModifyUser();
			break;
			
		case "deleteUser":
			DeleteUser();
			break;
			
		case "addUser":
			AddUser();
			break;
			
		case "autoFill":
			AutoFill();
			break;
			
		case "passStrength":
			CheckPasswordStrength();
			break;
			
		case "userExists":
			CheckUserExists();
			break;
			
		case "editEntry":
			EditEntry();
			break;
						
		case "logout":
			Logout();
			break;
	}



	//checks login credentials
	function CheckLogin()
	{
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");
		

		$username = $_POST['username'];
		$password = $_POST['password'];
		$isAdmin = $_POST['role'] == "admin" ? true : false ;
		

		$query = "select * from owner where username = '$username' and password = '$password'";
		
		//can only login to admin site if isadmin
		if($isAdmin == true)
			$query = $query. " and isadmin = 1";
		
		$result = mysql_query($query);

		$num=mysql_numrows($result);
		
		if($num == 0)
			echo "false";
		else
		{
			echo "true";
			$_SESSION['loggedin'] = "YES"; // Set it so the user is logged in!
   			$_SESSION['username'] = $username; // Make it so the username can be called by $_SESSION['name']
   			$_SESSION['isadmin'] = $isAdmin;
		}
			
			
		mysql_close( );
		
	}
	
	//creates an appointment entry into scheduleentry
	function CreateEntry()
	{
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");
		
		
		$scheduleid = $_POST['scheduleid'];
		$username = $_POST['idNumber'];
		$first = $_POST['first'];
		$last = $_POST['last'];
		$majorID = $_POST['major'];
		$reason = $_POST['reason'];
		$appt = $_POST['appt'];
		
		$ud1 = isset($_POST['udfield1']) ? $_POST['udfield1'] : NULL;
		$ud2 = isset($_POST['udfield2']) ? $_POST['udfield2'] : NULL;
		$ud3 = isset($_POST['udfield3']) ? $_POST['udfield3'] : NULL;
		$ud4 = isset($_POST['udfield4']) ? $_POST['udfield4'] : NULL;
		
		$query = "select * from user where username = '$username'";
		
		$result = mysql_query($query);

		$num=mysql_numrows($result);
		
		if($num == 0)
		{
		
			$query = "insert into user values('$username','$first','$last','$email',$majorID)";
			mysql_query($query);
		
		}
		
		$query = "insert into scheduleentry values('',$scheduleid,'$username','$appt','$reason','$ud1','$ud2','$ud3','$ud4')";
		$result = mysql_query($query);
		
		if($result)
			echo "Appointment created!";
		else
			echo "Appointment could not be created";	
			
		mysql_close( );

	}
	
	//allows a schedule owner to change his/her passowrd
	function ChangePassword()
	{
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");

		if(isset($_SESSION['username']))
			$username = $_SESSION['username'];
		else
		{
			session_destroy();
			mysql_close();
			die("Your session has expired");
		}		
		

		$currentPass = $_POST['currentPass'];
		$newPass = $_POST['newPass1'];

		$query = "select * from owner where username = '$username' and password = '$currentPass'";
		
		$result = mysql_query($query);

		$num=mysql_numrows($result);
		
		if($num == 0)
		{
			mysql_close();
			die("Current password not correct");
			//don't let anything else happen
		}
		
		$query = "update owner set password ='$newPass' where username='$username'";	
		mysql_query($query);	
		mysql_close();
		echo "Password changed!";
	}
	
	function SaveLogin()
	{
		if(isset($_SESSION['username']))
			$username = $_SESSION['username'];
		else
		{
			session_destroy();
			mysql_close();
			die("Your session has expired");
		}

	
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");
		
		$results = "Updated ";
		foreach($_POST as $key=>$value) 
		{
			if($key == "sub")
				continue;
				
			if(!empty($_POST[$key]))
			{
				$query = "update owner set $key = '$value' where username ='$username'";
				if(mysql_query($query))
					$results = $results."$key; ";
			}

		}
		
		
		echo $results;
		mysql_close();

	}
	
	function EnableSchedule()
	{
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");

	
	
		if(isset($_SESSION['username']))
			$username = $_SESSION['username'];
		else
		{
			session_destroy();
			mysql_close();
			die("Your session has expired");
		}
		
		$scheduleid = $_POST['scheduleid'];
		$enabled = $_POST['scheduleEnable'];
		$query = "update schedule set enabled = $enabled where id=$scheduleid";
		mysql_query($query);

		$type = $_POST['disabledMessageType'];
		$query = "update scheduleconfig set disabledscheduledisplaytype = $type where scheduleid=$scheduleid";
		mysql_query($query);

		if(strlen($_POST['disabledMessage']) > 0)
		{			
			$disabledMessage = $_POST['disabledMessage'];
			$query = "update scheduleconfig set disabledscheduledisplayline = '$disabledMessage' where scheduleid=$scheduleid"; 
			mysql_query($query);
		}
		
		echo "Settings saved!";
		mysql_close();
	}
	
	
	function SaveConfig()
	{
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");

	
	
		$scheduleID = $_POST['scheduleid'];
		$username = $_SESSION['username'];
		$schedulename = empty($_POST['name']) ? "My Schedule" : $_POST['name'];
		$delta = $_POST['delta'];
		
	
		$output = "";
		
		if($scheduleID == "newSchedule")
		{
			//create new entry in schedule table
			$query = "insert into schedule(id,owner,name,enabled) values('','$username','$schedulename',1)";
			
			mysql_query($query);
			
			//get the latest entry in schedule table
			$query = "select id from schedule order by id desc";
			
			$result = mysql_query($query);
			
			//store the newly created schedule id
			$scheduleID = mysql_result($result,0,"id");
			
			//insert a record into the schedule config table
			$query = "insert into scheduleconfig(scheduleid) values ($scheduleID)";
			
			
			mysql_query($query);
			
						
			
			$output=$output."Created a new schedule. ";
		}
		else
			$scheduleID = $_POST['scheduleid'];
			
		
		
		
		foreach($_POST as $key=>$value) 
		{
			if($key == "sub")
				continue;
				
			
			
				
			if(!empty($_POST[$key]))
			{
				if($key == "scheduleid")
					continue;
				elseif($key == "name")
					$query = "update schedule set name = '$value' where id = $scheduleID";	
				elseif($key =="delta")
					$query = "update schedule set delta = $value where id = $scheduleID";				
				elseif(is_string($value))
					$query = "update scheduleconfig set $key = '$value' where scheduleid = $scheduleID";
				elseif (is_numeric($value))
					$query = "update scheduleconfig set $key = $value where scheduleid = $scheduleID";
					
			
				if(!mysql_query($query))
					$output = $output ."$key:$value; ";
			}

		}

		
		
		mysql_close();
		
		if(strlen($output) < 1)
			echo "Success!";
		else
			echo $output." failed";
	}
	
	function DeleteSchedule()
	{
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");
		
		$scheduleid = $_POST['scheduleid'];
		$query="delete from schedule where id = $scheduleid";		
		mysql_query($query);
		
		
		mysql_close();

	}
	
	function AddUser()
	{
		
	
		$username 	= 	$_POST['username'];				
		$password 	= 	$_POST["password"];
		$first 		= 	$_POST["first"];
		$last 		= 	$_POST["last"];
		$office 	=	$_POST["office"];
		$phone 		= 	$_POST["phone"];
		$email 		= 	$_POST["email"];
		$isadmin 	=	$_POST["isadmin"];
		
		$isadmin = $isadmin == "on" ? 1 : 0;
		
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");

		$query = "insert into owner values('$username','$password','$first','$last','$office','$phone','$email',$isadmin)";
		
		if(mysql_query($query))
			echo $username." Added";
		else
			echo "Something failed";
		mysql_close();
		
	}

	
	
	
	function ModifyUser()
	{
		
	
		$modifyUser = $_POST['username'];
		
		$postKey = strpos($modifyUser , '.') !== FALSE ? str_replace(".","_",$modifyUser) : $modifyUser;
		
		$password 	= 	$_POST[$postKey."password"];
		$first 		= 	$_POST[$postKey."first"];
		$last 		= 	$_POST[$postKey."last"];
		$office 	=	$_POST[$postKey."office"];
		$phone 		= 	$_POST[$postKey."phone"];
		$email 		= 	$_POST[$postKey."email"];
		$isadmin 	=	$_POST[$postKey."isadmin"];
		
		$isadmin = $isadmin == "on" ? 1 : 0;
		
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");

		$query = "update owner set password = '$password', first = '$first', last = '$last', office = '$office', phone = '$phone', email = '$email', isadmin = $isadmin where username = '$modifyUser'";
		
		
		if(mysql_query($query))
			echo $modifyUser." updated";
		else
			echo "Something failed";
		mysql_close();
		
	}
	
	function DeleteUser()
	{
		
	
		$username = $_POST['username'];
		
				
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");

		$query = "delete from owner where username = '$username'";
		
		
		
		if(mysql_query($query))
			echo $modifyUser." deleted";
		else
			echo "Something failed";
		mysql_close();
		
	}

	
	
	
	function CheckPasswordStrength() 
	{
		$pwd = $_POST['password'];
		
		$strength = array("Blank","Very Weak","Weak","Medium","Strong","Very Strong");
		$score = 1;
	
		if (strlen($pwd) < 1)
		{
			$desc = $strength[0];
			echo "<span style='color:blue'>$desc</span>";
			return;
		}
		if (strlen($pwd) < 4 )
		{
			$desc =  $strength[1]; 
			echo "<span style='color:#E01B28'>$desc</span>";
			return;
		}
	
		if (strlen($pwd) >= 8)
		{
			$score++; 
		}
		if (strlen($pwd) >= 10)
		{
			$score++; 
		}
	
		if (preg_match("/[a-z]/", $pwd) && preg_match("/[A-Z]/", $pwd)) 
		{
			$score++; 
		}
		if (preg_match("/[0-9]/", $pwd)) 
		{
			$score++; 
		}
		if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,£,(,)]/", $pwd)) 
		{
			$score++; 
		}
	
		$desc = $strength[$score];
			
		if($score <= 3)
			echo "<span style='color:#E6811C'>$desc</span>";
		elseif($score == 4)
			echo "<span style='color:#E6C01C'>$desc</span>";
		elseif($score == 5)
			echo "<span style='color:#9BE61C'>$desc</span>";
		elseif($score == 6)
			echo "<span style='color:'green'>$desc</span>";

	}
	
	function CheckUserExists()
	{
	
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");
		

		$username = $_POST['username'];
				

		$query = "select * from owner where username = '$username'";
				
		$result = mysql_query($query);

		$num=mysql_numrows($result);
		
		if($num == 0)
			echo "true";
		else
		{
			echo "false";
		}
			
			
		mysql_close( );

	}
	
	function AutoFill()
	{
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");
		
				$username = $_POST['username'];
				

		$query = "select * from user,major where username = '$username' and majorid = id";
				
		$result = mysql_query($query);

		$num=mysql_numrows($result);
		mysql_close();
		
		if($num == 0)
		{
			echo "false";
			die();
			
			
		}
		else
		{
			$first 		= mysql_result($result,0,"first");
			$last 		= mysql_result($result,0,"last");
			$email		= mysql_result($result,0,"email");
			$majorid 	= mysql_result($result,0,"majorid");
			
			$arr = array('first' => $first, 'last' => $last, 'email' => $email, 'major' => $majorid);
			echo json_encode($arr);
		}
		

	}
	
	function EditEntry()
	{
		global $DBhostname, $DBusername, $DBpassword, $DBdatabase;
		
		mysql_connect($DBhostname , $DBusername , $DBpassword ) 
			or die("Could not connect to MySQL server! ");
										  
		mysql_select_db($DBdatabase ) or die( "Unable to select database");

	
	
		$id 		= $_POST['entryid'];
		$studentid 	= $_POST['studentid'];
		$datetime	= $_POST['datetime'];
		$email 	 	= $_POST['studentemail'];
		$majorid  	= $_POST['majorid'];
		$reason		= $_POST['reason'];
		$udfield1	= $_POST['udfield1'];
		$udfield2	= $_POST['udfield2'];
		$udfield3	= $_POST['udfield3'];
		$udfield4	= $_POST['udfield4'];		
		
		$query = "update scheduleentry set 
					datetime = '$datetime', 
					reason = '$reason', 
					udfield1 = '$udfield1', 
					udfield2 = '$udfield2',
					udfield3 = '$udfield3',
					udfield4 = '$udfield4'
						where id = $id";
					
		if(!mysql_query($query))
		{
			mysql_close();
			die("Update failed");
		}
		
		$query = "update user set
					email   = '$email',
					majorid =  $majorid
						where username = '$studentid'";
					
		if(!mysql_query($query))
		{
			mysql_close();
			die("Update failed");
		}
		
		if($_POST['unschedule'] == "on")
		{
			$query = "delete from scheduleentry where id = $id";
			if(!mysql_query($query))
			{
				mysql_close();
				die("Unschedule failed");
			}
		}
		
		echo "Settings Saved";

	}
	
	
	function Logout()
	{
		session_destroy();
	}

?>