<?php
require_once dirname(__DIR__) . '/config.php';
session_start();
session_unset();
session_destroy();
header("Location: " . BASE_URL . "pages/login.php?msg=logout");
exit();

?>