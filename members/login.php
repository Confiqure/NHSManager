<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Log in form for NHS members">
	<meta name="author" content="Dylan Wheeler">

	<title>National Honor Society</title>

	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../images/logo144.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../images/logo114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../images/logo72.png">
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="../images/logo57.png">
	<link rel="shortcut icon" href="../images/favicon.png">

	<!-- Bootstrap Core CSS -->
	<link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="../css/sb-admin-2.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<body>

	<div class="container">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="login-panel panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Please Sign In<?php if ($_GET['username'] != false) echo '&nbsp;<small style="color:red">(try again)</small>';?></h3>
					</div>
					<div class="panel-body">
						<form role="form" action="scripts/do_login.php" method="POST">
							<fieldset>
								<div class="form-group">
									<input class="form-control" placeholder="Username" name="username" type="text" maxlength=15 <?php if ($_GET['username'] != false) echo 'value="' . $_GET['username'] . '"'; else echo 'autofocus';?>  required />
								</div>
								<div class="form-group">
									<input class="form-control" placeholder="Password" name="password" type="password" maxlength=15 value="" <?php if ($_GET['username'] != false) echo 'autofocus';?> required />
								</div>
								<!-- Change this to a button or input when using this as a form -->
								<button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- jQuery -->
	<script src="../bower_components/jquery/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- Custom Theme JavaScript -->
	<script src="../js/sb-admin-2.js"></script>

</body>

</html>
