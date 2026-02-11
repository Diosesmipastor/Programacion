<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["token"]) || empty($_SESSION["token"])) {
    header("Location: /features/users/login.php");
    exit();
}
?>
