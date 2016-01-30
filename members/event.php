<?php
session_start();
if (!isset($_SESSION['token']) || strlen($_SESSION['token']) !== 10) {
header('Location: http://www.bownhs.org/');
exit();
}
$event = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT * FROM `events` WHERE `id` = :id');
	$stmt->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$event = $row;
		break;
	}
	unset($stmt);
	unset($dbh);
	if ($event === false) {
		$_SESSION['status'] = 'error';
		header('Location: http://www.bownhs.org/members/');
		return;
	}
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the event viewer: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
switch ($event['color']) {
	case 'danger':
		$event['color'] = 'red';
		break;
	case 'success':
		$event['color'] = 'green';
		break;
	case 'info':
		$event['color'] = 'primary';
		break;
	case 'warning':
		$event['color'] = 'yellow';
		break;
}
$event['going'] = explode(', ', $event['going'] == '' ? 'No one' : substr($event['going'], 0, strlen($event['going']) - 2));
?>
<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Page for viewing event attendees">
	<meta name="author" content="Dylan Wheeler">

	<title>National Honor Society</title>

	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../../images/logo144.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../../images/logo114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../../images/logo72.png">
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="../../images/logo57.png">
	<link rel="shortcut icon" href="../../images/favicon.png">

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
				<div class="login-panel panel panel-<?php echo $event['color']; ?>">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $event['title'] . ' on ' . date('n/j/Y', strtotime($event['date'])); ?></h3>
					</div>
					<div class="panel-body">
						<h4>These members have indicated they are attending this event:</h4>
						<div class="row">
							<div class="col-md-6">
								<ul>
									<?php
										for ($i = 0; $i < sizeof($event['going']); $i += 2) {
											echo '
									<li>' . $event['going'][$i] . '</li>';
										}
									?>
								</ul>
							</div>
							<div class="col-md-6">
								<ul>
									<?php
										for ($i = 1; $i < sizeof($event['going']); $i += 2) {
											echo '
									<li>' . $event['going'][$i] . '</li>';
										}
									?>
								</ul>
							</div>
						</div>
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
