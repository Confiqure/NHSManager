<?php
session_start();
$event = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT role FROM members WHERE token = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		if ($row['role'] === 'Member') {
			unset($stmt);
			unset($dbh);
			$_SESSION['status'] = 'error';
			header('Location: http://nhs.comxa.com/members/');
			return;
		}
		break;
	}
	$stmt = $dbh->prepare('SELECT * FROM events WHERE id = :id');
	$stmt->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$event = $row;
		break;
	}
	echo '<center><h1>' . $event['title'] . '</h1><h2>' . $event['date'] . '</h2><h3>Attendees:</h3><h4>' . ($event['going'] == '' ? 'No one' : substr($event['going'], 0, strlen($event['going']) - 2)) . '</h4></center>';
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS event attendees page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die('<META HTTP-EQUIV="refresh" CONTENT="1" />Feature currently unavailable. This page will refresh in a moment.');
}
?>
