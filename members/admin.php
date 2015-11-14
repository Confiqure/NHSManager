<?php
session_start();
$account = false;
$tutors = $events = $minutes = $pending = array();
if (isset($_SESSION['token']) && strlen($_SESSION['token']) === 10) {
	require_once('../dbconfig.php');
	try {
		$dbh = new PDO($driver, $user, $pass, $attr);
		$stmt = $dbh->prepare('SELECT * FROM members WHERE token = :token');
		$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$account = $row;
			break;
		}
		$stmt = $dbh->prepare('SELECT id,subjects FROM tutor_req');
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$tutors[sizeof($tutors)] = $row;
		}
		$stmt = $dbh->prepare('SELECT * FROM events WHERE date >= "' . date('Y-m-d', time()) . '" ORDER BY date ASC');
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$events[sizeof($events)] = $row;
		}
		$stmt = $dbh->prepare('SELECT * FROM minutes');
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$minutes[sizeof($minutes)] = $row;
		}
		if ($account['role'] === "Administrator" || $account['role'] === "Parliamentarian") {
			$stmt = $dbh->prepare('SELECT username,studentname,pending FROM members WHERE pending != ""');
			$stmt->execute();
			while ($row = $stmt->fetch()) {
				$pending[sizeof($pending)] = $row;
			}
		}
		unset($stmt);
		unset($dbh);
	} catch (Exception $e) {
		$recipient = "dwheelerw@gmail.com";
		$subject = "ERROR - SQL Connection";
		$mail_body = "An exception occurred on the NHS admin fetcher: " . $e->getMessage();
		mail($recipient, $subject, $mail_body);
		die('<META HTTP-EQUIV="refresh" CONTENT="1" />Feature currently unavailable. This page will refresh in a moment.');
	}
}
if ($account === false) {
	session_unset();
	session_destroy();
	header('Location: http://www.bownhs.org/members/');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Section for NHS members to review their hours and responsibilities">
	<meta name="author" content="Dylan Wheeler">

	<title>National Honor Society</title>

	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../images/logo144.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../images/logo114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../images/logo72.png">
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="../images/logo57.png">
	<link rel="shortcut icon" href="../images/favicon.png">

	<!-- Bootstrap Core CSS -->
	<link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Timeline CSS -->
	<link href="../css/timeline.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="../css/sb-admin-2.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

	<?php
		if (isset($_SESSION['status'])) {
			echo '<script type="text/javascript">alert("';
			switch ($_SESSION['status']) {
				case 'success':
					echo 'The form has been submitted successfully!';
					break;
				case 'processed':
					echo 'The service has been processed!';
					break;
				case 'invalid':
					echo 'One or more of the inputs for your application was entered invalidly. Please try again.';
					break;
				case 'error':
					echo 'An unexpected error has occurred while trying to process your request. Please try again. NOTICE: you might not have the required permissions to perform that action.';
					break;
				case 'pass_changed':
					echo 'You have successfully changed your password! Use the new one to sign in from now forward.';
					break;
				case 'going':
					echo 'You are now going to the event!';
					break;
				case 'not_going':
					echo 'You are no longer signed up to go to the event.';
					break;
				case 'tutor_confirmed':
					echo 'You have successfully signed up to tutor! Please check your email for further information.';
					break;
			}
			echo '");</script>';
			unset($_SESSION['status']);
		}
	?>

</head>

<body>

	<!-- Navigation -->
	<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="../" class="navbar-brand">National Honor Society</a>
		</div>
		<!-- /.navbar-header -->
		 <ul class="nav navbar-top-links navbar-right">
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="fa fa-tasks fa-lg fa-fw"></i>  <i class="fa fa-caret-down fa-lg"></i>
				</a>
				<ul class="dropdown-menu dropdown-tasks">
					<?php
						$community_percent = round(($account['community'] > 10 ? 10 : $account['community']) * 10);
						$tutoring_percent = round(($account['tutoring'] > 5 ? 5 : $account['tutoring']) * 20);
						$total_percent = round((($account['tutoring'] > 5 ? 5 : $account['tutoring']) + ($account['community'] > 10 ? 10 : $account['community']) + ($account['tutoring'] + $account['community'] < 15 ? 0 : $account['tutoring'] + $account['community'] - 15)) * 5);
						if ($total_percent > 100) $total_percent = 100;
					?>
					<li>
						<a>
							<div>
								<p>
									<strong>Community Service</strong>
									<span class="pull-right text-muted"><?php echo $community_percent; ?>% Complete</span>
								</p>
								<div class="progress progress-striped active">
									<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $community_percent; ?>%">
										<span class="sr-only"><?php echo $community_percent; ?>% Complete</span>
									</div>
								</div>
							</div>
						</a>
					</li>
					<li class="divider"></li>
					<li>
						<a>
							<div>
								<p>
									<strong>Tutoring</strong>
									<span class="pull-right text-muted"><?php echo $tutoring_percent; ?>% Complete</span>
								</p>
								<div class="progress progress-striped active">
									<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $tutoring_percent; ?>%">
										<span class="sr-only"><?php echo $tutoring_percent; ?>% Complete</span>
									</div>
								</div>
							</div>
						</a>
					</li>
					<li class="divider"></li>
					<li>
						<a>
							<div>
								<p>
									<strong>Total Hours</strong>
									<span class="pull-right text-muted"><?php echo $total_percent; ?>% Complete</span>
								</p>
								<div class="progress progress-striped active">
									<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $total_percent; ?>%">
										<span class="sr-only"><?php echo $total_percent; ?>% Complete</span>
									</div>
								</div>
							</div>
						</a>
					</li>
				</ul>
				<!-- /.dropdown-tasks -->
			</li>
			<!-- /.dropdown -->
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="fa fa-user fa-lg fa-fw"></i>  <i class="fa fa-caret-down fa-lg"></i>
				</a>
				<ul class="dropdown-menu dropdown-user">
					<li><a href="changepassword.php"><i class="fa fa-gear fa-fw"></i> Change Password</a></li>
					<li class="divider"></li>
					<?php
					if ($account['role'] === 'Administrator' || $account['role'] === 'Parliamentarian') echo '
					<li><a href="getHours.php?filter=0"><i class="fa fa-print fa-fw"></i> Full Hours List</a></li>
					<li><a href="getHours.php?filter=1"><i class="fa fa-print fa-fw"></i> Needed Hours List</a></li>
					<li class="divider"></li>';
					?>
					<li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
				</ul>
				<!-- /.dropdown-user -->
			</li>
			<!-- /.dropdown -->
		</ul>
		<!-- /.navbar-top-links -->
	</nav>

	<div class="container">

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $account['studentname'] . ($account['role'] !== 'Member' ? ' (' . $account['role'] . ')' : ''); ?></h1>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->

		<div class="row">
			<div class="col-lg-6 col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Announcements
					</div>
					<div class="panel-footer">
						Stop by at least once a week for new updates! We know who is and isn't checking in during our virtual meetings.
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3">
				<div class="panel panel-yellow">
					<div class="panel-heading">
						<div class="row">
							<div class="col-xs-3">
								<i class="fa fa-heart fa-5x"></i>
							</div>
							<div class="col-xs-9 text-right">
								<div class="huge"><?php if ($account['role'] != "Administrator") echo floor($account['community']) == $account['community'] ? substr($account['community'], 0, strlen($account['community']) - 2) : $account['community']; else echo "NA"; ?></div>
								<div>Community Service</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3">
				<div class="panel panel-red">
					<div class="panel-heading">
						<div class="row">
							<div class="col-xs-3">
								<i class="fa fa-book fa-5x"></i>
							</div>
							<div class="col-xs-9 text-right">
								<div class="huge"><?php if ($account['role'] != "Administrator") echo floor($account['tutoring']) == $account['tutoring'] ? substr($account['tutoring'], 0, strlen($account['tutoring']) - 2) : $account['tutoring']; else echo "NA"; ?></div>
								<div>Tutoring</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /.row -->

		<div class="row">
			<div class="col-lg-9">
<?php if ($total_percent != 100 && $account['role'] !== 'Administrator') include('panel_record.php'); else include('panel_events.php');?>
			</div>
			<!-- /.col-lg-8 -->
			<div class="col-lg-3">
				<?php
					if (sizeof($tutors) != 0) {
						echo '
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-book fa-fw"></i> Tutoring Requests
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<div class="list-group">';
						for ($i = sizeof($tutors) - 1; $i >= 0; $i--) {
							if ($tutors[$i] == null) continue;
							echo '
							<a href="tutor.php?id=' . $tutors[$i]['id'] . '" class="list-group-item">
								' . (strlen($tutors[$i]['subjects']) > 30 ? substr($tutors[$i]['subjects'], 0, 30) . '...' : $tutors[$i]['subjects']) . '
							</a>' . "\n";
						}
						echo '
						</div>
						<!-- /.list-group -->
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->';
					}
				?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-clipboard fa-fw"></i> Meeting Minutes
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<div class="list-group">
							<?php
							for ($i = sizeof($minutes) - 1; $i >= 0; $i--) {
								if ($minutes[$i] == null) continue;
								if (strlen($minutes[$i]['absent']) > 0) {
									$minutes[$i]['absent'] = explode(',', $minutes[$i]['absent']);
									$is_absent = false;
									foreach ($minutes[$i]['absent'] as $name) {
										$name = trim($name);
										if ($name == '') continue;
										if (strpos($account['studentname'], $name) === 0) {
											$is_absent = true;
											break;
										}
									}
								}
								echo '
							<a href="' . $minutes[$i]['link'] . '" class="list-group-item" target="_blank">
								' . $minutes[$i]['date'] . ' <span class="pull-right text-muted small" style="color:' . ($is_absent ? 'red"><em>Absent' : 'green"><em>Present') . '</em></span>
							</a>' . "\n";
								}
							?>
						</div>
						<!-- /.list-group -->
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
			</div>
			<!-- /.col-lg-4 -->
		</div>
		<!-- /.row -->
		<?php if ($account['role'] !== 'Member') { echo'
		<div class="row">
			<div class="col-lg-12">'; include('panel_admin.php'); echo '</div>
		</div>
		<!-- /.row -->'; } ?>
		<div class="row">
			<div class="col-lg-12">
<?php if ($total_percent != 100 && $account['role'] !== 'Administrator') include('panel_events.php'); else include('panel_record.php'); ?>
			</div>
		</div>
	</div>
	<!-- /#page-wrapper -->

	<!-- jQuery -->
	<script src="../bower_components/jquery/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- Custom Theme JavaScript -->
	<script src="../js/sb-admin-2.js"></script>

</body>

</html>