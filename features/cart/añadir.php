<?php
include "../../db.php";
include "../users/auth.php";

if (!isset($_SESSION['user'])) {
    die("Debes iniciar sesión para agregar productos al carrito.");
}

if (!isset($_GET['codigo'])) {
    die("Producto no especificado.");
}

$codigo = $_GET['codigo'];

$stmt = $conn->prepare("SELECT id FROM users WHERE nombre = :nombre");
$stmt->execute(['nombre' => $_SESSION['user']]);
$user_id = $stmt->fetchColumn();
if (!$user_id) {
    die("Usuario no encontrado");
}

$stmt = $conn->prepare("SELECT id FROM products WHERE codigo = :codigo");
$stmt->execute(['codigo' => $codigo]);
$product_id = $stmt->fetchColumn();
if (!$product_id) {
    die("Producto no encontrado");
}

$stmt = $conn->prepare("SELECT id, cantidad FROM cart WHERE user_id = :user_id AND product_id = :product_id");
$stmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cart) {
    $stmt = $conn->prepare("UPDATE cart SET cantidad = cantidad + 1 WHERE id = :id");
    $stmt->execute(['id' => $cart['id']]);
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, cantidad) VALUES (:user_id, :product_id, 1)");
    $stmt->execute(['user_id' => $user_id, 'product_id' => $product_id]);
}

header("Location: ../../index.php#carrito");
exit;
