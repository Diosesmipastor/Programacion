<?php
include "../../db.php";
include "../users/auth.php";

$search = trim($_GET['q'] ?? '');

try {
    if ($search) {
        $stmt = $conn->prepare(
            "SELECT * FROM products 
             WHERE nombre ILIKE :search OR codigo ILIKE :search
             ORDER BY id DESC"
        );
        $stmt->execute(['search' => "%$search%"]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Error al obtener productos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Productos</title>
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
                <a href="../../index.php#welcome" class="nav-link"><i data-lucide="home"></i> Bienvenida</a>
                <a href="lista.php" class="nav-link active"><i data-lucide="list"></i> Productos</a>
                <a href="../cart/carrito.php" class="nav-link"><i data-lucide="shopping-cart"></i> Carrito</a>
                <?php if ($_SESSION["nivel"] === "admin"): ?>
                    <a href="crear.php" class="nav-link"><i data-lucide="plus-square"></i> Crear Producto</a>
                <?php endif; ?>
                <a href="../users/logout.php" class="btn-logout"><i data-lucide="log-out"></i> Cerrar sesión</a>
            </nav>
        </aside>

        <main class="main-content centered-main">
            <form method="GET" class="search">
                <i data-lucide="search"></i>
                <input type="text" name="q" placeholder="Buscar productos..." value="<?= htmlspecialchars($search) ?>" autocomplete="off">
            </form>

            <section class="card centered-card wide-card">
                <h2><i data-lucide="list"></i> Lista de Productos</h2>

                <?php if (count($result) > 0): ?>
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Código</th>
                                <th>Precio</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><a href="vista.php?codigo=<?= $row['codigo'] ?>" class="link-primary"><?= $row['codigo'] ?></a></td>
                                    <td>$<?= number_format($row['precio'], 2) ?></td>
                                    <td><?= $row['descripcion'] ?: '—' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty-state">No hay productos registrados</p>
                <?php endif; ?>

                <?php if ($_SESSION["nivel"] === "admin"): ?>
                    <a href="crear.php" class="btn-primary mt-20"><i data-lucide="plus-circle"></i> Crear Producto</a>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        lucide.createIcons();
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', e => {
                const href = link.getAttribute('href');
                if (href.startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>

</html>