<?php
include "../../db.php";
include "../users/auth.php";

if (!isset($_SESSION['user']) || $_SESSION['nivel'] !== 'admin') {
    die("Acceso no autorizado.");
}

if (!isset($_GET['codigo'])) {
    die("Producto no especificado.");
}

$codigo = $_GET['codigo'];
$mensaje = "";

try {
    $stmt = $conn->prepare("SELECT nombre FROM products WHERE codigo = :codigo");
    $stmt->execute(['codigo' => $codigo]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Producto no encontrado.");
    }

    $nombre = $producto['nombre'];
} catch (PDOException $e) {
    die("Error al obtener producto: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $stmt = $conn->prepare("DELETE FROM products WHERE codigo = :codigo");
        $eliminado = $stmt->execute(['codigo' => $codigo]);

        if ($eliminado) {
            header("Location: lista.php");
            exit;
        } else {
            $mensaje = "Error al eliminar el producto.";
        }
    } catch (PDOException $e) {
        $mensaje = "Error al eliminar el producto: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Eliminar Producto</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="profile">
                <i data-lucide="user"></i>
                <p><?= $_SESSION['user'] ?></p>
                <p class="role"><?= ucfirst($_SESSION['nivel']) ?></p>
            </div>
            <nav>
                <a href="../../index.php#welcome" class="nav-link"><i data-lucide="home"></i> Inicio</a>
                <a href="lista.php" class="nav-link active"><i data-lucide="list"></i> Productos</a>
                <a href="../cart/carrito.php" class="nav-link"><i data-lucide="shopping-cart"></i> Carrito</a>
                <a href="../users/logout.php" class="btn-logout"><i data-lucide="log-out"></i> Cerrar sesión</a>
            </nav>
        </aside>

        <main class="main-content centered-main">
            <section class="card centered-card">
                <h2><i data-lucide="trash-2"></i> Eliminar Producto</h2>
                <?php if ($mensaje): ?>
                    <p class="msg error"><?= $mensaje ?></p>
                <?php endif; ?>

                <p>¿Estás seguro que deseas eliminar el producto <strong><?= htmlspecialchars($nombre) ?></strong>?</p>

                <form method="POST" class="form-modern">
                    <button type="submit" class="btn-primary"><i data-lucide="trash-2"></i> Eliminar</button>
                    <a href="lista.php" class="btn-secondary">Cancelar</a>
                </form>
            </section>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>