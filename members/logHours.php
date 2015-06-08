<?php
session_start();
$date = explode('-', $_POST['date']);
if (!checkdate($date[1], $date[2], $date[0]) || !is_numeric($_POST['hours'])) {
	$_SESSION['status'] = 'invalid';
	header('Location: http://confiqure.uphero.com/nhs/members/');
	return;
}
$account = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	try {
		$stmt = $dbh->prepare('SELECT * FROM nhs_members WHERE token = :token');
		$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$account = $row;
			break;
		}
		if ($account === false) {
			unset($stmt);
			$_SESSION['status'] = 'error';
			header('Location: http://confiqure.uphero.com/nhs/members/');
			return;
		}
		$desc = $_POST['description'];
		$desc = str_replace(',', '/[c]/', $desc);
		$desc = str_replace(';', '/[s]/', $desc);
		$desc = str_replace('"', '/[q]/', $desc);
		$new = $date[1] . '/' . $date[2] . '/' . $date[0] . ',' . ($_POST['service'] === 'Tutoring' ? 'tutoring' : 'community') . ',' . $_POST['hours'] . ',' . $desc . ',' . $_POST['contact'] . ';';
		$stmt = $dbh->prepare('UPDATE nhs_members SET waiting = "' .  $new . $account['waiting'] . '" WHERE username = "' . $account['username'] . '"');
		$stmt->execute();
		unset($stmt);
		$_SESSION['status'] = 'success';
		header('Location: http://confiqure.uphero.com/nhs/members/');
	} catch (Exception $f) {
		echo "Exception: " . $f->getMessage() . "<br />";
	}
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS service log page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	echo "Feature currently unavailable. Please try again later.";
	die();
}
?>
