<?php
session_start();
if (isset($_SESSION['token'])) require('admin.php'); else require('login.php');
?>