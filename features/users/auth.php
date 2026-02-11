<?php
session_start();

if (!isset($_SESSION["token"])) {
    header("Location: /features/users/login.php");
    exit;
}