<?php
session_start();
$account = false;
require_once('../../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `role` FROM `members` WHERE `token` = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$account = $row;
		break;
	}
	if ($account === false || $account['role'] !== 'Administrator') {
		unset($stmt);
		unset($dbh);
		$_SESSION['status'] = 'error';
		header('Location: http://www.bownhs.org/members/');
		return;
	}
	$stmt = $dbh->prepare('UPDATE `vars` SET `value` = :value WHERE `key` = :key');
	$stmt->bindParam(':key', $_GET['key'], PDO::PARAM_STR);
	$stmt->bindParam(':value', $_POST['value'], PDO::PARAM_STR);
	$stmt->execute();
	if ($_GET['key'] == 'announcement') {
		$stmt = $dbh->prepare('SELECT * FROM `notification_email` WHERE `newAnnouncement` = 1');
		$stmt->execute();
		$count = 0;
		while ($row = $stmt->fetch()) {
			mail($row['recipient'], 'NHS Alerts', 'New announcement: ' . $_POST['value']);
			$count++;
		}
		$stmt = $dbh->prepare('SELECT * FROM `notification_phone` WHERE `newAnnouncement` = 1');
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			mail($row['recipient'], '', 'New announcement: ' . $_POST['value']);
			$count++;
		}
		file_put_contents('../../stats/emails_sent.txt', file_get_contents('../../stats/emails_sent.txt') + $count);
	}
	$_SESSION['status'] = 'success';
	header('Location: http://www.bownhs.org/members/');
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the variable changer: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>