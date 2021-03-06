<?php
session_start();
if (!isset($_SESSION['token']) || strlen($_SESSION['token']) !== 10) {
header('Location: http://www.bownhs.org/');
exit();
}
$request = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `id`, `grade`, `subjects`, `free` FROM `tutor_req` WHERE `id` = :id');
	$stmt->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$request = $row;
		break;
	}
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the tutee viewer: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Form for tutor signups">
	<meta name="author" content="Dylan Wheeler">

	<title>National Honor Society</title>

	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../../images/logo144.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../../images/logo114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../../images/logo72.png">
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="../../images/logo57.png">
	<link rel="shortcut icon" href="../images/favicon.png">

	<!-- Bootstrap Core CSS -->
	<link href="../../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="../../css/sb-admin-2.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="../../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<body>

	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="login-panel panel panel-green">
					<div class="panel-heading">
						<h3 class="panel-title">Tutoring Signup Form</h3>
					</div>
					<div class="panel-body">
						<form role="form" action="../receipt.php" method="POST">
							<fieldset>
								<div class="form-group">
									<label>Grade Level: <?php echo $request['grade']; ?></label>
								</div>
								<div class="form-group">
									<label>Subjects Needing Help: <?php echo $request['subjects']; ?></label>
								</div>
								<div class="form-group">
									<label>Availability: <?php echo $request['free']; ?></label>
								</div>
								<div class="form-group">
									<input name="agree" type="checkbox" required /> By checking this box, I agree to make contact with my tutor and handle his/her request. If I run into any difficulties, I will talk to the right people to make sure this request goes back online for someone else to take. <i>Contact information will be available on the following page.</i>
								</div>
								<input name="id" type="text" value="<?php echo $request['id']; ?>" hidden />
								<button type="submit" class="btn btn-lg btn-success btn-block">Sign up!</button>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- jQuery -->
	<script src="../../bower_components/jquery/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="../../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- Custom Theme JavaScript -->
	<script src="../../js/sb-admin-2.js"></script>

</body>

</html>
