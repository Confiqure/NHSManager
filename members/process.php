<?php
session_start();
$auth = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT role FROM members WHERE token = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$auth = $row['role'] === 'Adminstrator' || $row['role'] === 'Parliamentarian';
		break;
	}
	if ($auth) {
		$stmt = $dbh->prepare('SELECT tutoring,community,processed,waiting,notifications FROM members WHERE username = :username');
		$stmt->bindParam(':username', $_GET['username'], PDO::PARAM_STR);
		$stmt->execute();
		$account = $new_waiting = false;
		while ($row = $stmt->fetch()) {
			$account = $row;
			break;
		}
		if ($account['waiting'] !== false) {
			$account['waiting'] = explode(';', $account['waiting']);
			for ($i = 0; $i < sizeof($account['waiting']); $i++) {
				if ($i == $_GET['count']) continue;
				$new_waiting .= $account['waiting'][$i] . ';';
			}
			$account['waiting'] = explode(',', $account['waiting'][$_GET['count']]);
			$account['processed'] =. $account['waiting'][0] . ',' . $account['waiting'][1] . ',' . $account['waiting'][2] . ',' . $account['waiting'][3] . ',' . ($_GET['state'] == true ? 'check' : 'times') . ';';
			if ($_GET['state'] == true) {
				if ($account['waiting'][1] === 'community') {
					$account['community'] += $account['waiting'][2];
					$account['notifications'] =. 'check;Community service approved' . time() . ';';
				} else {
					$account['tutoring'] += $account['waiting'][2];
					$account['notifications'] =. 'check;Tutoring approved' . time() . ';';
				}
			}
			$stmt = $dbh->prepare('UPDATE members SET tutoring = "' . $account['tutoring'] . '", community = "' . $account['community'] . '", processed = "' . $account['processed'] . '", waiting = "' . $new_waiting . '", notifications = "' . $account['notifications'] . '" WHERE username = :username');
			$stmt->bindParam(':username', $_GET['username'], PDO::PARAM_INT);
			$stmt->execute();
			$_SESSION['status'] = 'processed';
		}
		$_SESSION['status'] = 'error';
	} else {
		$_SESSION['status'] = 'error';
	}
	unset($stmt);
	unset($dbh);
	header("Location: http://confiqure.uphero.com/nhs/members/");
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS approval page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	echo "Feature currently unavailable. Please try again later.";
	die();
}
?>