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
	$date = $_POST['date'];
	$date = str_replace(',', '/[c]/', $date);
	$absent = $_POST['absent'];
	$absent = str_replace(',', '/[c]/', $absent);
	$absent = str_replace(';', '/[s]/', $absent);
	$absent = str_replace('"', '/[q]/', $absent);
	$stmt = $dbh->prepare("INSERT INTO minutes VALUES(\"$date\",\"" . $_POST['link'] . "\",\"$absent\")");
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
	echo "Feature currently unavailable. Please try again later.";
	die();
}
?>
