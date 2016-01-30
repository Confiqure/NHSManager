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
	if ($account === false || $account['role'] === 'Member') {
		unset($stmt);
		unset($dbh);
		$_SESSION['status'] = 'error';
		header('Location: http://www.bownhs.org/members/');
		return;
	}
	$charset = 'abcdefghijklmnopqrstuvwxyz';
	$count = strlen($charset) - 1;
	$length = 4;
	while ($length--) $id .= $charset[mt_rand(0, $count)];
	$stmt = $dbh->prepare('INSERT INTO `tutor_req` VALUES("' . $id . '", :name, :grade, :contact, :subjects, :free)');
	$stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
	$stmt->bindParam(':grade', $_POST['grade'], PDO::PARAM_STR);
	$stmt->bindParam(':contact', $_POST['contact'], PDO::PARAM_STR);
	$stmt->bindParam(':subjects', $_POST['subjects'], PDO::PARAM_STR);
	$stmt->bindParam(':free', $_POST['free'], PDO::PARAM_STR);
	$stmt->execute();
	$stmt = $dbh->prepare('SELECT * FROM `notification_email` WHERE `newTutoring` = 1');
	$stmt->execute();
	$count = 0;
	while ($row = $stmt->fetch()) {
		mail($row['recipient'], 'NHS Alerts', 'Someone needs tutoring in ' . $_POST['subjects'] . '!');
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
	$mail_body = "An exception occurred on the tutor requester: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>