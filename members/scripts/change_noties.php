<?php
session_start();
$user = false;
require_once('../../dbconfig.php');
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
	$stmt = $dbh->prepare('DELETE FROM `notification_email` WHERE `username` = :username');
	$stmt->bindParam(':username', $user, PDO::PARAM_STR);
	$stmt->execute();
	$stmt = $dbh->prepare('DELETE FROM `notification_phone` WHERE `username` = :username');
	$stmt->bindParam(':username', $user, PDO::PARAM_STR);
	$stmt->execute();
	if ($_POST['enableEmail'] == 'on') {
		$_POST['eNewAnnouncement'] = $_POST['eNewAnnouncement'] == 'on' ? 1 : 0;
		$_POST['eNewTutoring'] = $_POST['eNewTutoring'] == 'on' ? 1 : 0;
		$_POST['eNewEvents'] = $_POST['eNewEvents'] == 'on' ? 1 : 0;
		$_POST['eNewMinutes'] = $_POST['eNewMinutes'] == 'on' ? 1 : 0;
		$_POST['eNewApproval'] = $_POST['eNewApproval'] == 'on' ? 1 : 0;
		$stmt = $dbh->prepare('INSERT INTO `notification_email`(`username`, `recipient`, `newAnnouncement`, `newTutoring`, `newEvents`, `newMinutes`, `newApproval`) VALUES (:username, :recipient, :newAnnouncement, :newTutoring, :newEvents, :newMinutes, :newApproval)');
		$stmt->bindParam(':username', $user, PDO::PARAM_STR);
		$stmt->bindParam(':recipient', $_POST['eRecipient'], PDO::PARAM_STR);
		$stmt->bindParam(':newAnnouncement', $_POST['eNewAnnouncement'], PDO::PARAM_INT);
		$stmt->bindParam(':newTutoring', $_POST['eNewTutoring'], PDO::PARAM_INT);
		$stmt->bindParam(':newEvents', $_POST['eNewEvents'], PDO::PARAM_INT);
		$stmt->bindParam(':newMinutes', $_POST['eNewMinutes'], PDO::PARAM_INT);
		$stmt->bindParam(':newApproval', $_POST['eNewApproval'], PDO::PARAM_INT);
		$stmt->execute();
	}
	if ($_POST['enablePhone'] == 'on' && strlen($_POST['pRecipient']) === 10) {
		$recipient = $_POST['pRecipient'];
		switch ($_POST['pCarrier']) {
			case 'AT&T':
				$recipient .= '@txt.att.net';
				break;
			case 'Sprint':
				$recipient .= '@messaging.sprintpcs.com';
				break;
			case 'T-Mobile':
				$recipient .= '@tmomail.net';
				break;
			case 'US Cellular':
				$recipient .= '@email.uscc.net';
				break;
			case 'Verizon':
				$recipient .= '@vtext.com';
				break;
			case 'Virgin Mobile':
				$recipient .= '@vmobl.com';
				break;
		}
		$_POST['pNewAnnouncement'] = $_POST['pNewAnnouncement'] == 'on' ? 1 : 0;
		$_POST['pNewTutoring'] = $_POST['pNewTutoring'] == 'on' ? 1 : 0;
		$_POST['pNewEvents'] = $_POST['pNewEvents'] == 'on' ? 1 : 0;
		$_POST['pNewMinutes'] = $_POST['pNewMinutes'] == 'on' ? 1 : 0;
		$_POST['pNewApproval'] = $_POST['pNewApproval'] == 'on' ? 1 : 0;
		$stmt = $dbh->prepare('INSERT INTO `notification_phone`(`username`, `recipient`, `newAnnouncement`, `newTutoring`, `newEvents`, `newMinutes`, `newApproval`) VALUES (:username, :recipient, :newAnnouncement, :newTutoring, :newEvents, :newMinutes, :newApproval)');
		$stmt->bindParam(':username', $user, PDO::PARAM_STR);
		$stmt->bindParam(':recipient', $recipient, PDO::PARAM_STR);
		$stmt->bindParam(':newAnnouncement', $_POST['pNewAnnouncement'], PDO::PARAM_INT);
		$stmt->bindParam(':newTutoring', $_POST['pNewTutoring'], PDO::PARAM_INT);
		$stmt->bindParam(':newEvents', $_POST['pNewEvents'], PDO::PARAM_INT);
		$stmt->bindParam(':newMinutes', $_POST['pNewMinutes'], PDO::PARAM_INT);
		$stmt->bindParam(':newApproval', $_POST['pNewApproval'], PDO::PARAM_INT);
		$stmt->execute();
	}
	unset($stmt);
	unset($dbh);
	$_SESSION['status'] = 'success';
	header('Location: http://www.bownhs.org/members/');
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the notification settings changer: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>