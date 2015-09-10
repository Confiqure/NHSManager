<?php
session_start();
$name = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT studentname FROM members WHERE token = :token');
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
		header('Location: http://nhs.comxa.com/members/');
		return;
	}
	$stmt = $dbh->prepare('SELECT going FROM events WHERE title = :title');
	$stmt->bindParam(':title', $_GET['title'], PDO::PARAM_STR);
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
		header('Location: http://nhs.comxa.com/members/');
		return;
	}
	$stmt = $dbh->prepare('UPDATE events SET going = :going WHERE title = :title');
	$stmt->bindParam(':going', $going = $_GET['going'] == '1' ? $going . $name . ', ' : str_replace($name . ', ', '', $going), PDO::PARAM_STR);
	$stmt->bindParam(':title', $_GET['title'], PDO::PARAM_STR);
	$stmt->execute();
	unset($stmt);
	unset($dbh);
	$_SESSION['status'] = $_GET['going'] == '1' ? 'going' : 'not_going';
	header('Location: http://nhs.comxa.com/members/');
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS event updater page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	echo "Feature currently unavailable. Please try again later.";
	die();
}
?>
