<?php
session_start();
if (isset($_SESSION['token'])) require('loadout.php'); else require('login.php');
?>