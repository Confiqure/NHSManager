<?php
session_start();
$auth = false;
require_once('dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `role` FROM `members` WHERE `token` = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$auth = $row['role'] === 'Administrator';
		break;
	}
	if ($auth) {
		$people = array();
		$stmt = $dbh->prepare('SELECT `username`, `processed` FROM `members`');
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$people[sizeof($people)] = array($row['username'], $row['processed']);
		}
		foreach ($people as $person) {
			$entries = explode(';', $person[1]);
			$community = $tutoring = 0;
			foreach ($entries as $entry) {
				$parts = explode(',', $entry);
				if ($parts[4] !== 'check') continue;
				if ($parts[1] === 'community') $community += $parts[2];
				else if ($parts[1] === 'tutoring') $tutoring += $parts[2];
			}
			$stmt = $dbh->prepare('UPDATE `members` SET `community` = ' . $community . ', `tutoring` = ' . $tutoring . ' WHERE `username` = "' . $person[0] . '"');
			$stmt->execute();
		}
	}
	unset($stmt);
	unset($dbh);
	header("Location: http://www.bownhs.org/");
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the hour processor: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>