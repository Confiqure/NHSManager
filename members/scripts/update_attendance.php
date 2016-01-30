<?php
session_start();
$name = false;
require_once('../../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `studentname` FROM `members` WHERE `token` = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$name = $row['studentname'];
		break;
	}
	if ($name === false) {
		unset($stmt);
		unset($dbh);
		$_SESSION['status'] = 'error';
		header('Location: http://www.bownhs.org/members/');
		return;
	}
	$stmt = $dbh->prepare('SELECT `going` FROM `events` WHERE `id` = :id');
	$stmt->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
	$stmt->execute();
	$going = false;
	while ($row = $stmt->fetch()) {
		$going = $row['going'];
		break;
	}
	if ($going === false) {
		unset($stmt);
		unset($dbh);
		$_SESSION['status'] = 'error';
		header('Location: http://www.bownhs.org/members/');
		return;
	}
	$stmt = $dbh->prepare('UPDATE `events` SET `going` = :going WHERE `id` = :id');
	$stmt->bindParam(':going', $going = $_GET['going'] == '1' ? $going . $name . ', ' : str_replace($name . ', ', '', $going), PDO::PARAM_STR);
	$stmt->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
	$stmt->execute();
	unset($stmt);
	unset($dbh);
	$_SESSION['status'] = $_GET['going'] == '1' ? 'going' : 'not_going';
	header('Location: http://www.bownhs.org/members/');
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the event attendance updater: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>
