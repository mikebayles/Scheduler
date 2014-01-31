<!DOCTYPE html>
<html>
	<head>
		<title>Schedule Manager</title>
		
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>
		
		<link href='http://fonts.googleapis.com/css?family=Just+Me+Again+Down+Here' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="login.css" type="text/css" >
		
		<script>
			function ajaxLogin()
			{
				if($("#role").val() == 'blank' || !$("#username").val() || !$("#password").val())
				{
					$("#response").html("<pre>Please fill out the form completely</pre>");
					return false;
				}
				$.post('controller.php', $('#login').serialize(), function(data){
					
					if(data == "true")
					{
						var role = $("#role").val();
						window.location = role + '.php';
					}
					else
						$("#response").html("<pre>Invalid Username/Password</pre>");
				
				});
			}
			
		</script>
		
	</head>
	
	<body>
		<div id="wrapper">
			<form id="login" action="javascript:ajaxLogin();" method="post">
				<input type="hidden" name="sub" value="login" >
				<table>
				
					<tr>
						<th>Role :</th>
						<td>
							<select id="role" name="role">
								<option value="blank"></option>
								<option value="admin">Admin</option>
								<option value="owner">Owner</option>
							</select>
						</td>
					</tr>
					
					<tr>
						<th>Username :</th>
						<td>
							<input type="text" id="username" name="username" required="required" maxlength="30">
						</td>
					</tr>
					
					<tr>
						<th>Password :</th>
						<td>
							<input type="password" id="password" name="password" required="required" maxlength="30">
						</td>
					</tr>
					
					<tr>
						<th>
							<input type="submit" id="submit" value="login">
						</th>
					</tr>
					
				</table>
				
				<div id="response"></div>
			</form>
		</div>
	</body>
</html>