<?php
include "../../db.php";
include "../users/auth.php";

if (!isset($_SESSION['user'])) {
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Acceso requerido</title>
        <link rel="stylesheet" href="../../css/estilos.css">
    </head>

    <body>
        <div class="alert alert-warning">
            <strong>Sesión requerida</strong><br>
            Debes iniciar sesión para ver tu carrito.
            <div style="margin-top:10px">
                <a href="../users/login.php" class="btn-primary">Iniciar sesión</a>
            </div>
        </div>
    </body>

    </html>
<?php
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE nombre = ?");
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$stmt->bind_result($user_id);

if (!$stmt->fetch()) {
    echo "<div class='alert alert-danger'>Usuario no encontrado</div>";
    exit;
}
$stmt->close();

$stmt = $conn->prepare("
    SELECT p.nombre, p.precio, p.codigo, c.cantidad
    FROM cart c
    INNER JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total = 0;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mi Carrito</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="dashboard-container">

        <div class="sidebar">
            <div class="profile">
                <i data-lucide="user"></i>
                <p><?= htmlspecialchars($_SESSION['user']) ?></p>
                <p class="role"><?= htmlspecialchars($_SESSION['nivel']) ?></p>
            </div>
            <nav>
                <a href="../../index.php#welcome" class="nav-link"><i data-lucide="home"></i> Inicio</a>
                <a href="../productos/lista.php" class="nav-link"><i data-lucide="list"></i> Productos</a>
                <a href="carrito.php" class="nav-link active"><i data-lucide="shopping-cart"></i> Mi Carrito</a>
                <?php if ($_SESSION['nivel'] === 'admin'): ?>
                    <a href="../productos/crear.php" class="nav-link"><i data-lucide="plus-square"></i> Crear Producto</a>
                <?php endif; ?>
                <a href="../users/logout.php" class="btn-logout"><i data-lucide="log-out"></i> Cerrar sesión</a>
            </nav>
        </div>

        <div class="main-content">
            <section class="card1">
                <h2><i data-lucide="shopping-cart"></i> Mi Carrito</h2>

                <?php if (isset($_SESSION['msg'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['msg'] ?></div>
                    <?php unset($_SESSION['msg']); ?>
                <?php endif; ?>

                <?php if ($result->num_rows > 0): ?>
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()):
                                $subtotal = $row['precio'] * $row['cantidad'];
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td>$<?= number_format($row['precio'], 2) ?></td>
                                    <td><?= $row['cantidad'] ?></td>
                                    <td>$<?= number_format($subtotal, 2) ?></td>
                                    <td>
                                        <a href="remover.php?codigo=<?= urlencode($row['codigo']) ?>" class="btn-remove" onclick="return confirm('¿Eliminar este producto?')">
                                            <i data-lucide="trash-2"></i>
                                        </a>
                                        <a href="añadir.php?codigo=<?= urlencode($row['codigo']) ?>" class="btn-add">
                                            <i data-lucide="shopping-cart"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <p class="cart-total"><strong>Total: $<?= number_format($total, 2) ?></strong></p>
                    <a href="vaciar.php" class="btn-logout" onclick="return confirm('¿Vaciar todo el carrito?')">Vaciar carrito</a>
                <?php else: ?>
                    <div class="alert alert-info">
                        <strong>Tu carrito está vacío</strong><br>
                        Explora nuestros productos y agrega los que te gusten.
                        <div style="margin-top:10px">
                            <a href="../productos/lista.php" class="btn-success">Ver productos</a>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>