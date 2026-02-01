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

$stmt = $conn->prepare("SELECT id, cantidad FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->bind_result($cart_id, $cantidad);

if ($stmt->fetch()) {
    $stmt->close();
    $stmt = $conn->prepare("UPDATE cart SET cantidad = cantidad + 1 WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
} else {
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, cantidad) VALUES (?,?,1)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

header("Location: ../../index.php#carrito");
exit;
