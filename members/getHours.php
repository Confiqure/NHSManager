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
		if ($row['role'] !== 'Administrator' && $row['role'] !== 'Parliamentarian') {
			unset($stmt);
			unset($dbh);
			$_SESSION['status'] = 'error';
			header('Location: http://nhs.comxa.com/members/');
			return;
		}
		break;
	}
	$stmt = $dbh->prepare("SELECT studentname,community,tutoring FROM members ORDER BY studentname ASC");
	$stmt->execute();
	echo '<table style="border: 1px solid black;" padding="1px"><tr><th>Student Name</th><th>Community Service</th><th>Tutoring</th></tr>';
	while ($row = $stmt->fetch()) {
		echo '<tr><td>' . $row['studentname'] . '</td><td align="right">' . $row['community'] . '</td><td align="right">' . $row['tutoring'] . '</td></tr>';
	}
	echo '</table>';
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS service log page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	echo "Feature currently unavailable. Please try again later.";
	die();
}
?>
