<?php
session_start();
$account = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT role FROM members WHERE token = :token');
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
	$stmt = $dbh->prepare('INSERT INTO tutor_req VALUES("' . $id . '",:name,:grade,:contact,:subjects,:free)');
	$stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
	$stmt->bindParam(':grade', $_POST['grade'], PDO::PARAM_STR);
	$stmt->bindParam(':contact', $_POST['contact'], PDO::PARAM_STR);
	$stmt->bindParam(':subjects', $_POST['subjects'], PDO::PARAM_STR);
	$stmt->bindParam(':free', $_POST['free'], PDO::PARAM_STR);
	$stmt->execute();
	unset($stmt);
	unset($dbh);
	$_SESSION['status'] = 'success';
	header('Location: http://www.bownhs.org/members/');
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS add tutee page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die('<META HTTP-EQUIV="refresh" CONTENT="1" />Feature currently unavailable. This page will refresh in a moment.');
}
?>
