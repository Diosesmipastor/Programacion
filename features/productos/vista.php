<?php
include "../../db.php";
include "../users/auth.php";

$codigo = $_GET['codigo'] ?? '';

$stmt = $conn->prepare("SELECT * FROM products WHERE codigo = :codigo");
$stmt->execute(['codigo' => $codigo]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
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
            <h2><i data-lucide="package"></i> <?= htmlspecialchars($product['nombre']) ?></h2>
            <p><strong>Código:</strong> <?= htmlspecialchars($product['codigo']) ?></p>
            <p><strong>Precio:</strong> $<?= number_format($product['precio'], 2) ?></p>
            <p><strong>Descripción:</strong> <?= htmlspecialchars($product['descripcion']) ?></p>
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