<?php
session_start();
$account = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `role` FROM `members` WHERE `token` = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$account = $row;
		break;
	}
	if ($account === false || ($account['role'] !== 'Administrator' && $account['role'] !== 'Secretary')) {
		unset($stmt);
		unset($dbh);
		$_SESSION['status'] = 'error';
		header('Location: http://www.bownhs.org/members/');
		return;
	}
	$stmt = $dbh->prepare('INSERT INTO `minutes` VALUES(:date, :link, :absent)');
	$stmt->bindParam(':date', $_POST['date'], PDO::PARAM_STR);
	$stmt->bindParam(':link', $_POST['link'], PDO::PARAM_STR);
	$stmt->bindParam(':absent', $_POST['absent'], PDO::PARAM_STR);
	$stmt->execute();
	$stmt = $dbh->prepare('SELECT * FROM `notification_email` WHERE `newMinutes` = 1');
	$stmt->execute();
	$count = 0;
	while ($row = $stmt->fetch()) {
		mail($row['recipient'], 'NHS Alerts', 'The minutes for ' . $_POST['date'] . ' have been posted!');
		$count++;
	}
	file_put_contents('../stats/emails_sent.txt', file_get_contents('../stats/emails_sent.txt') + $count);
	unset($stmt);
	unset($dbh);
	$_SESSION['status'] = 'success';
	header('Location: http://www.bownhs.org/members/');
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the minute uploader: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>