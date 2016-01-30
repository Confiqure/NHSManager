<?php
session_start();
$current = $_POST['current'];
$password1 = $_POST['password'];
$password2 = $_POST['password2'];
if ($password1 !== $password2) {
	header('Location: http://www.bownhs.org/members/changepassword/?mismatch=true');
	return;
}
$success = false;
require_once('../../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT * FROM `members` WHERE `token` = :token AND `password` = :password');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->bindParam(':password', $current, PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$success = true;
		break;
	}
	if ($success) {
		$stmt = $dbh->prepare('UPDATE `members` SET `password` = :password WHERE `token` = :token');
		$stmt->bindParam(':password', $password1, PDO::PARAM_STR);
		$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
		$stmt->execute();
		$_SESSION['status'] = 'pass_changed';
		header('Location: http://www.bownhs.org/members/');
	} else {
		header('Location: http://www.bownhs.org/members/changepassword/?failure=true');
	}
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the password changer: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>