<?php
session_start();
session_unset();
session_destroy();
header('Location: http://nhs.comxa.com/');
?>