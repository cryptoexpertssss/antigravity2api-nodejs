<?php
/**
 * Admin Logout
 */

require_once dirname(__DIR__) . '/app/helpers/AuthHelper.php';

AuthHelper::logout();
header('Location: /admin/login.php');
exit();
