<?php
$username = $_POST['username'];
$password = $_POST['password'];
$success = false;
require_once('../dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	try {
		$stmt = $dbh->prepare('SELECT * FROM nhs_members WHERE username = :username AND password = :password');
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->bindParam(':password', $password, PDO::PARAM_STR);
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$success = true;
			break;
		}
		if ($success) {
			$charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			$count = strlen($charset) - 1;
			$length = 10;
			while ($length--) $token .= $charset[mt_rand(0, $count)];
			$stmt = $dbh->prepare('UPDATE nhs_members SET token = "' .  $token . '" WHERE username = :username');
			$stmt->bindParam(':username', $username, PDO::PARAM_INT);
			$stmt->execute();
			setcookie('token', $code, 0);
			header('Location: http://confiqure.uphero.com/nhs/members/');
		} else {
			header("Location: http://confiqure.uphero.com/nhs/members/?username=$username");
		}
		unset($stmt);
	} catch (Exception $f) {
		echo "Exception: " . $f->getMessage() . "<br />";
	}
} catch (Exception $e) {
	$recipient = "dwheelerw@gmail.com";
	$subject = "ERROR - SQL Connection";
	$mail_body = "An exception occurred on the NHS log-in page: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
	echo "Feature currently unavailable. Please try again later.";
	die();
}
?>