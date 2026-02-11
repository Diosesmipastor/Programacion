<?php
include "../../db.php";
include "../users/auth.php";

if (!isset($_SESSION['user'])) {
    die("Debes iniciar sesión para vaciar el carrito.");
}

$stmt = $conn->prepare("SELECT id FROM users WHERE nombre = :nombre");
$stmt->execute(['nombre' => $_SESSION['user']]);
$user_id = $stmt->fetchColumn();
if (!$user_id) {
    die("Usuario no encontrado");
}

$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);

header("Location: ../../index.php#carrito");
exit;
