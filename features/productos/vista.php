<?php
include "../../db.php";
include "../users/auth.php";

$codigo = $_GET['codigo'] ?? '';
$stmt = $conn->prepare("SELECT * FROM products WHERE codigo = ?");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="card">
        <?php if ($product): ?>
            <h2><i data-lucide="package"></i> <?= $product['nombre'] ?></h2>
            <p><strong>Código:</strong> <?= $product['codigo'] ?></p>
            <p><strong>Precio:</strong> $<?= number_format($product['precio'], 2) ?></p>
            <p><strong>Descripción:</strong> <?= $product['descripcion'] ?></p>
            <a href="lista.php" class="btn-logout">Volver a productos</a>
        <?php else: ?>
            <p>Producto no encontrado</p>
            <a href="lista.php" class="btn-logout">Volver a productos</a>
        <?php endif; ?>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>