<?php
session_start();
$username = $_POST['username'];
$password = $_POST['password'];
$success = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `logins` FROM `members` WHERE `username` = :username AND `password` = :password');
	$stmt->bindParam(':username', $username, PDO::PARAM_STR);
	$stmt->bindParam(':password', $password, PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$success = true;
		break;
	}
	if ($success) {
		$charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$count = strlen($charset) - 1;
		$length = 10;
		while ($length--) $token .= $charset[mt_rand(0, $count)];
		$stmt = $dbh->prepare('UPDATE `members` SET `token` = "' .  $token . '", `logins` = ' . ++$row['logins'] . ' WHERE `username` = :username');
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->execute();
		$_SESSION['token'] = $token;
		header('Location: http://www.bownhs.org/members/');
	} else {
		header("Location: http://www.bownhs.org/members/?username=$username");
	}
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the login page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>