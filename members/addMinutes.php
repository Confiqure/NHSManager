<?php
session_start();
$account = false;
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
	if ($account === false || ($account['role'] !== 'Administrator' && $account['role'] !== 'Secretary')) {
		unset($stmt);
		unset($dbh);
		$_SESSION['status'] = 'error';
		header('Location: http://nhs.comxa.com/members/');
		return;
	}
	$stmt = $dbh->prepare('INSERT INTO minutes VALUES(:date,:link,:absent)');
	$stmt->bindParam(':date', $_POST['date'], PDO::PARAM_STR);
	$stmt->bindParam(':link', $_POST['link'], PDO::PARAM_STR);
	$stmt->bindParam(':absent', $_POST['absent'], PDO::PARAM_STR);
	$stmt->execute();
	unset($stmt);
	unset($dbh);
	$_SESSION['status'] = 'success';
	header('Location: http://nhs.comxa.com/members/');
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS service log page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die('<META HTTP-EQUIV="refresh" CONTENT="1" />Feature currently unavailable. This page will refresh in a moment.');
}
?>
