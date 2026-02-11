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
    $stmt = $conn->prepare("SELECT nombre, precio, descripcion FROM products WHERE codigo = :codigo");
    $stmt->execute(['codigo' => $codigo]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Producto no encontrado.");
    }

    $nombre = $producto['nombre'];
    $precio = $producto['precio'];
    $descripcion = $producto['descripcion'];
} catch (PDOException $e) {
    die("Error al obtener producto: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $precio = floatval($_POST["precio"]);
    $descripcion = trim($_POST["descripcion"]);

    if ($nombre && $precio > 0) {
        $stmt = $conn->prepare("
            UPDATE products 
            SET nombre = :nombre, precio = :precio, descripcion = :descripcion
            WHERE codigo = :codigo
        ");
        $ejecutado = $stmt->execute([
            'nombre' => $nombre,
            'precio' => $precio,
            'descripcion' => $descripcion,
            'codigo' => $codigo
        ]);

        if ($ejecutado) {
            header("Location: lista.php");
            exit;
        } else {
            $mensaje = "Error al actualizar el producto.";
        }
    } else {
        $mensaje = "Datos inválidos. Verifica los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
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
                <h2><i data-lucide="edit-3"></i> Editar Producto</h2>
                <?php if ($mensaje): ?>
                    <p class="msg error"><?= $mensaje ?></p>
                <?php endif; ?>

                <form method="POST" class="form-modern">
                    <div class="input-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Precio</label>
                        <input type="number" name="precio" step="0.01" value="<?= $precio ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Descripción</label>
                        <textarea name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>
                    </div>

                    <button class="btn-primary"><i data-lucide="save"></i> Guardar Cambios</button>
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