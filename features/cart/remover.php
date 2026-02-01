<?php
include "../../db.php";
include "../users/auth.php";


if (!isset($_SESSION['user'])) {
    die("Debes iniciar sesión para eliminar productos del carrito.");
}


if (!isset($_GET['codigo'])) {
    die("Producto no especificado.");
}

$codigo = $_GET['codigo'];


$stmt = $conn->prepare("SELECT id FROM users WHERE nombre = ?");
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$stmt->bind_result($user_id);
if (!$stmt->fetch()) {
    die("Usuario no encontrado");
}
$stmt->close();

$stmt = $conn->prepare("SELECT id FROM products WHERE codigo = ?");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$stmt->bind_result($product_id);
if (!$stmt->fetch()) {
    die("Producto no encontrado");
}
$stmt->close();


$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->close();

header("Location: ../../index.php#carrito");
exit;
