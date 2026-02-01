<?php
include "../../db.php";
include "../users/auth.php";

if ($_SESSION["nivel"] !== "admin") {
    header("Location: ../../index.php");
    exit;
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $codigo = trim($_POST["codigo"]);
    $precio = floatval($_POST["precio"]);
    $descripcion = trim($_POST["descripcion"]);

    if ($nombre && $codigo && $precio > 0) {
        $check = $conn->prepare("SELECT id FROM products WHERE codigo = ?");
        $check->bind_param("s", $codigo);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $stmt = $conn->prepare(
                "INSERT INTO products (nombre, codigo, precio, descripcion) VALUES (?,?,?,?)"
            );
            $stmt->bind_param("ssds", $nombre, $codigo, $precio, $descripcion);
            $mensaje = $stmt->execute()
                ? "Producto creado con éxito"
                : "Error al crear producto";
        } else {
            $mensaje = "El código ya existe";
        }
    } else {
        $mensaje = "Datos inválidos, el precio debe ser mayor a 0";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="dashboard-container">

        <aside class="sidebar">
            <div class="profile">
                <i data-lucide="user" class="profile-icon"></i>
                <p class="username"><?= $_SESSION["user"] ?></p>
                <p class="role"><?= $_SESSION["nivel"] ?></p>
            </div>

            <nav>
                <a href="../../index.php#welcome" class="nav-link">
                    <i data-lucide="home"></i> Bienvenida
                </a>

                <a href="../../index.php#productos" class="nav-link">
                    <i data-lucide="list"></i> Productos
                </a>

                <a href="../../index.php#carrito" class="nav-link">
                    <i data-lucide="shopping-cart"></i> Carrito
                </a>

                <a href="crear.php" class="nav-link active">
                    <i data-lucide="plus-square"></i> Crear Producto
                </a>

                <a href="../users/logout.php" class="btn-logout">
                    <i data-lucide="log-out"></i> Cerrar sesión
                </a>
            </nav>
        </aside>
        <main class="main-content centered-main">

            <section class="card2">
                <h2><i data-lucide="plus-circle"></i> Crear Producto</h2>

                <form method="POST" onsubmit="return validarFormulario()">

                    <div class="input-group">
                        <i data-lucide="tag"></i>
                        <input type="text" name="nombre" placeholder="Nombre del producto" required>
                    </div>

                    <div class="input-group">
                        <i data-lucide="hash"></i>
                        <input type="text" name="codigo" placeholder="Código único" required>
                    </div>

                    <div class="input-group">
                        <i data-lucide="dollar-sign"></i>
                        <input type="number" name="precio" step="0.01" placeholder="Precio" required>
                    </div>

                    <div class="input-group">
                        <i data-lucide="file-text"></i>
                        <input type="text" name="descripcion" placeholder="Descripción (opcional)">
                    </div>

                    <button class="btn-primary">
                        <i data-lucide="save"></i> Crear Producto
                    </button>

                    <?php if ($mensaje): ?>
                        <div id="msg" class="msg success"><?= $mensaje ?></div>
                    <?php endif; ?>

                    <a href="lista.php" class="link-muted">
                        <i data-lucide="arrow-left"></i> Ver todos los productos
                    </a>

                </form>
            </section>

        </main>
    </div>

    <script>
        lucide.createIcons();

        function validarFormulario() {
            const nombre = document.querySelector("input[name='nombre']").value.trim();
            const codigo = document.querySelector("input[name='codigo']").value.trim();
            const precio = parseFloat(document.querySelector("input[name='precio']").value);
            const msg = document.getElementById("msg");

            if (!nombre || !codigo || precio <= 0) {
                if (msg) {
                    msg.textContent = "Completa todos los campos correctamente (precio > 0)";
                    msg.className = "msg error";
                }
                return false;
            }

            return true;
        }
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', e => {
                const href = link.getAttribute('href');
                if (href.startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    </script>

</body>

</html>