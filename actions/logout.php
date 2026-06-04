<?php
session_start();
session_unset();
session_destroy();
header("Location: ../pages/login.php?msg=logout");
exit();

?>