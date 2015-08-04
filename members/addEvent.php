<?php
session_start();
$date = explode('-', $_POST['date']);
if (!checkdate($date[1], $date[2], $date[0])) {
	$_SESSION['status'] = 'invalid';
	header('Location: http://nhs.comxa.com/members/');
	return;
}
$account = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT * FROM members WHERE token = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$account = $row;
		break;
	}
	if ($account === false || $account['role'] === 'Member') {
		unset($stmt);
		unset($dbh);
		$_SESSION['status'] = 'error';
		header('Location: http://nhs.comxa.com/members/');
		return;
	}
	$title = $_POST['title'];
	$title = str_replace(',', '/[c]/', $title);
	$title = str_replace(';', '/[s]/', $title);
	$title = str_replace('"', '/[q]/', $title);
	$desc = $_POST['description'];
	$desc = str_replace(',', '/[c]/', $desc);
	$desc = str_replace(';', '/[s]/', $desc);
	$desc = str_replace('"', '/[q]/', $desc);
	$color = '';
	switch ($_POST['color']) {
		case 'Yellow':
			$color = 'warning';
			break;
		case 'Red':
			$color = 'danger';
			break;
		case 'Blue':
			$color = 'info';
			break;
		case 'Green':
			$color = 'success';
			break;
	}
	$icon = strtolower($_POST['icon']);
	if ($icon == '' || strpos($icon, '-') == false) $icon = 'fa fa-check';
	else $icon = substr($icon, 0, strpos($icon, '-')) . ' ' . $icon;
	$stmt = $dbh->prepare("INSERT INTO events VALUES(\"$title\",\"" . $date[1] . '/' . $date[2] . '/' . $date[0] . "\",\"$color\",\"$icon\",\"$desc\")");
	$stmt->execute();
	unset($stmt);
	unset($dbh);
	$_SESSION['status'] = 'success';
	header('Location: http://nhs.comxa.com/members/');
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS service log page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	echo "Feature currently unavailable. Please try again later.";
	die();
}
?>
