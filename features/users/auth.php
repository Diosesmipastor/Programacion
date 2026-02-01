<?php
session_start();

if (!isset($_SESSION["token"])) {
    header("Location: /Carrito/features/users/login.php");
    exit;
}