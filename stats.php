<?php
$tut = $cs = $events = $logins = $members = 0;
session_start();
require_once('dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT role,tutoring,community,processed,logins FROM members');
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$tut += $row['tutoring'];
		$cs += $row['community'];
		if (strpos($row['processed'], ';') !== false) $events += sizeof(explode(';', $row['processed']));
		$logins += $row['logins'];
		$members++;
	}
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS admin fetcher: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
echo "<center><h1>Tutoring Hours Logged: $tut<br />Community Service Hours Logged: $cs<br />Service Events Logged: $events<br />Logins Processed: $logins<br />Members: $members</h1></center>";
?>