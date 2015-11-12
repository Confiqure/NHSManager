<?php
session_start();
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
	echo '<center><p>You can use CTRL+A to select all of the data and paste it into an Excel document for further analysis and formatting.</p><table style="border: 1px solid black;" padding="1px" width="40%">';
	switch ($_GET['filter']) {
		case '1':
			echo '<tr><th>Student Name</th><th>Needed CS</th><th>Needed Tutoring</th><th>Needed Total</th></tr>';
			$names = $needed_cs = $needed_tut = $needed_tot = array();
			while ($row = $stmt->fetch()) {
				$names[sizeof($names)] = $row['studentname'];
				$needed_cs[sizeof($needed_cs)] = $row['community'] <= 10 ? 10 - $row['community'] : 0;
				$needed_tut[sizeof($needed_tut)] = $row['tutoring'] <= 5 ? 5 - $row['tutoring'] : 0;
				$needed_tot[sizeof($needed_tot)] = max(array(20 - $row['community'] - $row['tutoring'], $needed_cs[sizeof($needed_cs) - 1], $needed_tut[sizeof($needed_tut) - 1]));
			}
			array_multisort($needed_tot, SORT_NUMERIC, SORT_DESC, $names, $needed_cs, $needed_tut);
			for ($i = 0; $i < sizeof($names); $i++) {
				if ($needed_tot[$i] == 0) continue;
				echo '<tr><td>' . $names[$i] . '</td><td align="right">' . $needed_cs[$i] . '</td><td align="right">' . $needed_tut[$i] . '</td><td align="right">' . $needed_tot[$i] . '</td></tr>';
			}
			break;
		default:
			echo '<tr><th>Student Name</th><th>Community Service</th><th>Tutoring</th></tr>';
			while ($row = $stmt->fetch()) {
				echo '<tr><td>' . $row['studentname'] . '</td><td align="right">' . $row['community'] . '</td><td align="right">' . $row['tutoring'] . '</td></tr>';
			}
			break;
	}
	echo '</table></center>';
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS service log page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die('<META HTTP-EQUIV="refresh" CONTENT="1" />Feature currently unavailable. This page will refresh in a moment.');
}
?>
