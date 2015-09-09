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
	$stmt = $dbh->prepare('SELECT studentname,community,tutoring FROM members WHERE role != "Administrator" ORDER BY studentname ASC');
	$stmt->execute();
	echo '<p>You can use CTRL+A to select all of the data and paste it into an Excel document for further analysis and formatting.</p>';
	switch ($_GET['filter']) {
		case '1':
			echo '<table style="border: 1px solid black;" padding="1px"><tr><th>Student Name</th><th>Needed CS</th><th>Needed Tutoring</th><th>Needed Total</th></tr>';
			while ($row = $stmt->fetch()) {
				$needed_cs = $row['community'] <= 10 ? 10 - $row['community'] : 0;
				$needed_tut = $row['tutoring'] <= 5 ? 5 - $row['tutoring'] : 0;
				$needed_tot = max(array(20 - $row['community'] - $row['tutoring'], $needed_cs, $needed_tut));
				if ($needed_tot == 0) continue;
				echo '<tr><td>' . $row['studentname'] . '</td><td align="right">' . $needed_cs . '</td><td align="right">' . $needed_tut . '</td><td align="right">' . $needed_tot . '</td></tr>';
			}
			break;
		default:
			echo '<table style="border: 1px solid black;" padding="1px"><tr><th>Student Name</th><th>Community Service</th><th>Tutoring</th></tr>';
			while ($row = $stmt->fetch()) {
				echo '<tr><td>' . $row['studentname'] . '</td><td align="right">' . $row['community'] . '</td><td align="right">' . $row['tutoring'] . '</td></tr>';
			}
			break;
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
