<?php
$tut = $cs = $events = $queue = $logins = $lastlog = $members = $noties = 0;
session_start();
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `role`, `tutoring`, `community`, `processed`, `pending`, `logins`, `lastlogin` FROM `members` ORDER BY `lastlogin` DESC');
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		if ($lastlog == 0) $lastlog = strtotime($row['lastlogin']);
		$tut += $row['tutoring'];
		$cs += $row['community'];
		if (strpos($row['processed'], ';') !== false) $events += sizeof(explode(';', $row['processed'])) - 1;
		if (strpos($row['pending'], ';') !== false) $queue += sizeof(explode(';', $row['pending'])) - 1;
		$logins += $row['logins'];
		$members++;
	}
	$stmt = $dbh->prepare('SELECT * FROM `notification_email`');
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$noties++;
	}
	$stmt = $dbh->prepare('SELECT * FROM `notification_phone`');
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$noties++;
	}
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the stats page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
$lastlog = floor((time() - $lastlog - 25200) / 360) / 10; //adjust for SQL time zone
echo "<center><h1>Tutors Matched: " . file_get_contents('tutor_req.txt') . "<br />Tutoring Hours Logged: $tut</h1><h1>Community Service Hours Logged: $cs<br />Service Events Logged: $events<br />Service Events Queued: $queue</h1><h1>Logins Processed: $logins<br />Last Login: $lastlog hours ago</h1><h1>Members: $members</h1><h1>Notification Opted-In: $noties<br />Notifications Sent: " . file_get_contents('emails_sent.txt') . "</h1></center>";
?>