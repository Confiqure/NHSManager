<?php
session_start();
$date = explode('-', $_POST['date']);
if (!checkdate($date[1], $date[2], $date[0])) {
	$_SESSION['status'] = 'invalid';
	header('Location: http://www.bownhs.org/members/');
	return;
}
$account = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT role FROM members WHERE token = :token');
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
		header('Location: http://www.bownhs.org/members/');
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
		case 'Red':
			$color = 'danger';
			break;
		case 'Green':
			$color = 'success';
			break;
		case 'Blue':
			$color = 'info';
			break;
		case 'Yellow':
			$color = 'warning';
			break;
	}
	$icon = strtolower($_POST['icon']);
	if ($icon == '' || strpos($icon, '-') == false) $icon = 'fa fa-check';
	else $icon = substr($icon, 0, strpos($icon, '-')) . ' ' . $icon;
	$charset = 'abcdefghijklmnopqrstuvwxyz';
	$count = strlen($charset) - 1;
	$length = 4;
	while ($length--) $id .= $charset[mt_rand(0, $count)];
	$stmt = $dbh->prepare("INSERT INTO events VALUES(\"$id\",\"$title\",\"" . $_POST['date'] . "\",\"$color\",\"$icon\",\"$desc\",\"\")");
	$stmt->execute();
	unset($stmt);
	unset($dbh);
	$_SESSION['status'] = 'success';
	header('Location: http://www.bownhs.org/members/');
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS add event page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die('<META HTTP-EQUIV="refresh" CONTENT="1" />Feature currently unavailable. This page will refresh in a moment.');
}
?>
