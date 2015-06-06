<?php
	setcookie('token', '', time() - 10000);
	unset($_COOKIE['token']);
	header('Location: http://confiqure.uphero.com/nhs/');
?>