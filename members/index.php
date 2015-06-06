<?php
session_start();
if (isset($_SESSION['token'])) require('admin.html'); else require('login.html');
?>