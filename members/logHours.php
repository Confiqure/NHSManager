<?php
session_start();
$date = explode('-', $_POST['date']);
if (!checkdate($date[1], $date[2], $date[0]) || !is_numeric($_POST['hours'])) {
	$_SESSION['status'] = 'invalid';
	header('Location: http://www.bownhs.org/members/');
	return;
}
$account = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `username`, `pending` FROM `members` WHERE `token` = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$account = $row;
		break;
	}
	if ($account === false) {
		unset($stmt);
		unset($dbh);
		$_SESSION['status'] = 'error';
		header('Location: http://www.bownhs.org/members/');
		return;
	}
	$desc = $_POST['description'];
	$desc = str_replace(',', '/[c]/', $desc);
	$desc = str_replace(';', '/[s]/', $desc);
	$desc = str_replace('"', '/[q]/', $desc);
	$contact = $_POST['contact'];
	$contact = str_replace(',', '/[c]/', $contact);
	$contact = str_replace(';', '/[s]/', $contact);
	$contact = str_replace('"', '/[q]/', $contact);
	$new = $date[1] . '/' . $date[2] . '/' . $date[0] . ',' . ($_POST['service'] === 'Tutoring' ? 'tutoring' : 'community') . ',' . $_POST['hours'] . ',' . $desc . ',' . $contact . ';';
	$stmt = $dbh->prepare('UPDATE `members` SET `pending` = "' .  $new . $account['pending'] . '" WHERE `username` = "' . $account['username'] . '"');
	$stmt->execute();
	unset($stmt);
	unset($dbh);
	$_SESSION['status'] = 'success';
	header('Location: http://www.bownhs.org/members/');
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the hour logger: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>
