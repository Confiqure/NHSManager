<?php
session_start();
$auth = false;
require_once('../../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `role` FROM `members` WHERE `token` = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$auth = $row['role'] === 'Administrator' || $row['role'] === 'Parliamentarian';
		break;
	}
	if ($auth) {
		$stmt = $dbh->prepare('SELECT `tutoring`, `community`, `processed`, `pending` FROM `members` WHERE `username` = :username');
		$stmt->bindParam(':username', $_GET['username'], PDO::PARAM_STR);
		$stmt->execute();
		$account = false;
		while ($row = $stmt->fetch()) {
			$account = $row;
			break;
		}
		$account['pending'] = explode(';', $account['pending']);
		$new_pending = '';
		for ($i = 0; $i < sizeof($account['pending']); $i++) {
			if ($i == $_GET['count']) continue;
			$new_pending .= $account['pending'][$i] . ';';
		}
		$account['pending'] = explode(',', $account['pending'][$_GET['count']]);
		$account['processed'] = $account['pending'][0] . ',' . $account['pending'][1] . ',' . $account['pending'][2] . ',' . $account['pending'][3] . ',' . ($_GET['state'] === 'true' ? 'check' : 'times') . ';' . $account['processed'];
		if ($_GET['state'] === 'true') $account[$account['pending'][1]] += $account['pending'][2];
		$account['pending'] = strpos($new_pending, ',') == false ? '' : $new_pending;
		$stmt = $dbh->prepare('UPDATE `members` SET `tutoring` = "' . $account['tutoring'] . '", `community` = "' . $account['community'] . '", `processed` = "' . $account['processed'] . '", `pending` = "' . $account['pending'] . '" WHERE `username` = :username');
		$stmt->bindParam(':username', $_GET['username'], PDO::PARAM_INT);
		$stmt->execute();
		$stmt = $dbh->prepare('SELECT * FROM `notification_email` WHERE `username` = :username AND `newApproval` = 1');
		$stmt->bindParam(':username', $_GET['username'], PDO::PARAM_INT);
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			mail($row['recipient'], 'NHS Alerts', 'Your service hours have been checked by the Parliamentarian!');
			file_put_contents('../../stats/emails_sent.txt', file_get_contents('../../stats/emails_sent.txt') + 1);
			break;
		}
		$stmt = $dbh->prepare('SELECT * FROM `notification_phone` WHERE `username` = :username AND `newApproval` = 1');
		$stmt->bindParam(':username', $_GET['username'], PDO::PARAM_INT);
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			mail($row['recipient'], '', 'Your service hours have been checked by the Parliamentarian!');
			file_put_contents('../../stats/emails_sent.txt', file_get_contents('../../stats/emails_sent.txt') + 1);
			break;
		}
		$_SESSION['status'] = 'processed';
	} else {
		$_SESSION['status'] = 'error';
	}
	unset($stmt);
	unset($dbh);
	header("Location: http://www.bownhs.org/members/#admin");
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the hour processor: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>