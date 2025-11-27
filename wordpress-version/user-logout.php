<?php
session_start();
// Only destroy user session, keep admin session if exists
unset($_SESSION['user_id']);
unset($_SESSION['user_username']);
unset($_SESSION['user_name']);
unset($_SESSION['user_role']);
header('Location: index.php');
exit;
?>