<?php
session_start();
$check = $_POST['agree'];
if ($check !== 'on') {
	$_SESSION['status'] = 'invalid';
	header('Location: http://www.bownhs.org/members/');
	return;
}
$success = $request = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	$stmt = $dbh->prepare('SELECT `username` FROM `members` WHERE `token` = :token');
	$stmt->bindParam(':token', $_SESSION['token'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$success = true;
		break;
	}
	$stmt = $dbh->prepare('SELECT * FROM `tutor_req` WHERE `id` = :id');
	$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
	$stmt->execute();
	while ($row = $stmt->fetch()) {
		$request = $row;
		break;
	}
	if ($success && $request !== false) {
		$stmt = $dbh->prepare('DELETE FROM `tutor_req` WHERE `id` = :id');
		$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
		$stmt->execute();
		file_put_contents('../stats/tutor_req.txt', file_get_contents('../stats/tutor_req.txt') + 1);
		echo '<center><div width="60%"><h1>Congratulations!</h1><ul><li>You will be tutoring: ' . $request['name'] . '</li><li>Grade: ' . $request['grade'] . '</li><li>Subject area(s): ' . $request['subjects'] . '</li><li>Free times: ' . $request['free'] . '</li><li>Contact info: ' . $request['contact'] . '</li></ul><h2 style="color:red">Please take note of this information now, for when you leave the page it will be lost.</h2><a href="http://www.bownhs.org/members"><h5>Click here to return</h5></a></div></center>';
	} else {
		$_SESSION['status'] = 'error';
		header('Location: http://www.bownhs.org/members/');
	}
	unset($stmt);
	unset($dbh);
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the tutoring receipt page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	die("Feature currently unavailable. Please try again later.");
}
?>