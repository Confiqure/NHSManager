<?php
session_start();
if (!isset($_SESSION['token']) || strlen($_SESSION['token']) !== 10) {
header('Location: http://www.bownhs.org/');
exit();
}
$user = $eSettings = $pSettings = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `username` FROM `members` WHERE `token` = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$user = $row['username'];
		break;
	}
	if ($user === false) {
		unset($stmt);
		unset($dbh);
		$_SESSION['status'] = 'error';
		header('Location: http://www.bownhs.org/members/');
	}
	$stmt = $dbh->prepare('SELECT * FROM `notification_email` WHERE `username` = :username');
	$stmt->bindParam(':username', $user, PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$eSettings = $row;
		break;
	}
	$stmt = $dbh->prepare('SELECT * FROM `notification_phone` WHERE `username` = :username');
	$stmt->bindParam(':username', $user, PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$pSettings = $row;
		break;
	}
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the notification page: " . $e->getMessage();
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
	<meta name="description" content="Form for notification subscriptions">
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
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-primary" style="margin-top:10%">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-bell fa fw"></i> Notification Settings</h3>
					</div>
					<div class="panel-body">
						<form role="form" action="changenotis.php" method="POST">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs">
								<li class="active"><a href="#email" data-toggle="tab">Email Notifications</a></li>
								<li><a href="#phone" data-toggle="tab">Text Notifications</a></li>
							</ul>
							<!-- Tab panes -->
							<div class="tab-content">
								<div class="tab-pane fade in active" id="email">
									<h3>Settings</h3>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<input id="enableEmail" name="enableEmail" type="checkbox"<?php if ($eSettings !== false) echo ' checked'; ?> />
												<label>Enable email notifications on your account</label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label>Email Address</label>
												<input class="form-control" type="email" id="eRecipient" name="eRecipient" maxlength="128" placeholder="Enter a valid email address to send notifications to" value="<?php if ($eSettings !== false) echo $eSettings['recipient'] . '"'; else echo '" disabled'; ?> />
											</div>
										</div>
										<div class="col-sm-12">
											<div class="form-group">
												<label>Manage Subscriptions</label>
												<br />
												<span>
													<input id="eEventReminder" name="eEventReminder" type="checkbox"<?php if ($eSettings !== false) { if ($eSettings['eventReminder'] == true) echo ' checked'; } else echo ' checked disabled'; ?> />
													Reminder 1 day before the start of an event you signed up for
												</span>
												<br />
												<span>
													<input id="eNewAnnouncement" name="eNewAnnouncement" type="checkbox"<?php if ($eSettings !== false) { if ($eSettings['newAnnouncement'] == true) echo ' checked'; } else echo ' checked disabled'; ?> />
													When a new announcement is posted
												</span>
												<br />
												<span>
													<input id="eNewTutoring" name="eNewTutoring" type="checkbox"<?php if ($eSettings !== false) { if ($eSettings['newTutoring'] == true) echo ' checked'; } else echo ' checked disabled'; ?> />
													When new tutoring requests become available
												</span>
												<br />
												<span>
													<input id="eNewEvents" name="eNewEvents" type="checkbox"<?php if ($eSettings !== false) { if ($eSettings['newEvents'] == true) echo ' checked'; } else echo ' disabled'; ?> />
													When new events are posted
												</span>
												<br />
												<span>
													<input id="eNewMinutes" name="eNewMinutes" type="checkbox"<?php if ($eSettings !== false) { if ($eSettings['newMinutes'] == true) echo ' checked'; } else echo 'disabled'; ?> />
													When new meeting minutes are posted
												</span>
												<br />
												<span>
													<input id="eNewApproval" name="eNewApproval" type="checkbox"<?php if ($eSettings !== false) { if ($eSettings['newApproval'] == true) echo ' checked'; } else echo 'disabled'; ?> />
													When your hours are approved
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="phone">
									<h3>Settings</h3>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<input id="enablePhone" name="enablePhone" type="checkbox"<?php if ($pSettings !== false) echo ' checked'; ?> />
												<label>Enable text notifications on your account</label>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<div class="form-group">
												<label>Phone Number</label>
												<input class="form-control" type="tel" id="pRecipient" name="pRecipient" maxlength="16" placeholder="Enter a valid phone number to send notifications to" value="<?php if ($pSettings !== false) echo $pSettings['recipient'] . '"'; else echo '" disabled'; ?> />
											</div>
										</div>
										<div class="col-sm-12">
											<div class="form-group">
												<label>Manage Subscriptions</label>
												<br />
												<span>
													<input id="pEventReminder" name="pEventReminder" type="checkbox"<?php if ($pSettings !== false) { if ($pSettings['eventReminder'] == true) echo ' checked'; } else echo ' checked disabled'; ?> />
													Reminder 1 day before the start of an event you signed up for
												</span>
												<br />
												<span>
													<input id="pNewAnnouncement" name="pNewAnnouncement" type="checkbox"<?php if ($pSettings !== false) { if ($pSettings['newAnnouncement'] == true) echo ' checked'; } else echo ' checked disabled'; ?> />
													When a new announcement is posted
												</span>
												<br />
												<span>
													<input id="pNewTutoring" name="pNewTutoring" type="checkbox"<?php if ($pSettings !== false) { if ($pSettings['newTutoring'] == true) echo ' checked'; } else echo ' checked disabled'; ?> />
													When new tutoring requests become available
												</span>
												<br />
												<span>
													<input id="pNewEvents" name="pNewEvents" type="checkbox"<?php if ($pSettings !== false) { if ($pSettings['newEvents'] == true) echo ' checked'; } else echo 'disabled'; ?> />
													When new events are posted
												</span>
												<br />
												<span>
													<input id="pNewMinutes" name="pNewMinutes" type="checkbox"<?php if ($pSettings !== false) { if ($pSettings['newMinutes'] == true) echo ' checked'; } else echo 'disabled'; ?> />
													When new meeting minutes are posted
												</span>
												<br />
												<span>
													<input id="pNewApproval" name="pNewApproval" type="checkbox"<?php if ($pSettings !== false) { if ($pSettings['newApproval'] == true) echo ' checked'; } else echo 'disabled'; ?> />
													When your hours are approved
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<button type="submit" class="btn btn-lg btn-success btn-block">Save Changes</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- jQuery -->
	<script src="../bower_components/jquery/jquery.min.js"></script>

	<script type="text/javascript">
		$('#enableEmail').change(function() {
			 if ($(this).is(':checked')) {
			 	$('#eRecipient').removeAttr('disabled');
			 	$('#eEventReminder').removeAttr('disabled');
			 	$('#eNewAnnouncement').removeAttr('disabled');
			 	$('#eNewTutoring').removeAttr('disabled');
			 	$('#eNewEvents').removeAttr('disabled');
			 	$('#eNewMinutes').removeAttr('disabled');
			 	$('#eNewApproval').removeAttr('disabled');
			 } else {
			 	$('#eRecipient').attr('disabled', 'disabled');
			 	$('#eEventReminder').attr('disabled', 'disabled');
			 	$('#eNewAnnouncement').attr('disabled', 'disabled');
			 	$('#eNewTutoring').attr('disabled', 'disabled');
			 	$('#eNewEvents').attr('disabled', 'disabled');
			 	$('#eNewMinutes').attr('disabled', 'disabled');
			 	$('#eNewApproval').attr('disabled', 'disabled');
			 }
		});
		$('#enablePhone').change(function() {
			 if ($(this).is(':checked')) {
			 	$('#pRecipient').removeAttr('disabled');
			 	$('#pEventReminder').removeAttr('disabled');
			 	$('#pNewAnnouncement').removeAttr('disabled');
			 	$('#pNewTutoring').removeAttr('disabled');
			 	$('#pNewEvents').removeAttr('disabled');
			 	$('#pNewMinutes').removeAttr('disabled');
			 	$('#pNewApproval').removeAttr('disabled');
			 } else {
			 	$('#pRecipient').attr('disabled', 'disabled');
			 	$('#pEventReminder').attr('disabled', 'disabled');
			 	$('#pNewAnnouncement').attr('disabled', 'disabled');
			 	$('#pNewTutoring').attr('disabled', 'disabled');
			 	$('#pNewEvents').attr('disabled', 'disabled');
			 	$('#pNewMinutes').attr('disabled', 'disabled');
			 	$('#pNewApproval').attr('disabled', 'disabled');
			 }
		});
	</script>

	<!-- Bootstrap Core JavaScript -->
	<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- Custom Theme JavaScript -->
	<script src="../js/sb-admin-2.js"></script>

</body>

</html>