<?php
include "features/users/auth.php";
include "db.php";

$productos = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 5");
$carrito = $_SESSION['cart'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Principal</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="dashboard-container">

        <div class="sidebar" id="sidebar">
            <div class="profile">
                <i data-lucide="user" class="profile-icon"></i>
                <p class="username"><?= $_SESSION["user"] ?></p>
                <p class="role"><?= ucfirst($_SESSION["nivel"]) ?></p>
            </div>
            <nav>
                <a href="#welcome" class="nav-link"><i data-lucide="home"></i> Inicio</a>
                <a href="/features/productos/lista.php" class="nav-link"><i data-lucide="list"></i> Productos</a>
                <a href="/features/cart/carrito.php" class="nav-link"><i data-lucide="shopping-cart"></i> Carrito</a>
                <?php if ($_SESSION["nivel"] === "admin"): ?>
                    <a href="/features/productos/crear.php" class="nav-link"><i data-lucide="plus-square"></i> Crear Producto</a>
                <?php endif; ?>
                <a href="features/users/logout.php" class="btn-logout"><i data-lucide="log-out"></i> Cerrar sesión</a>
            </nav>
        </div>

        <div class="main-content">
            <form class="search" action="features/productos/lista.php" method="GET">
                <i data-lucide="search"></i>
                <input type="text" name="q" placeholder="Buscar productos..." autocomplete="off" style="flex:1; border:none; outline:none; background:transparent; font-size:14px; color:#111827;">
            </form>

            <section id="welcome" class="card1 centered-card">
                <i data-lucide="smile" class="section-icon"></i>
                <h2>Bienvenid@, <?= $_SESSION["user"] ?></h2>
            </section>

            <section id="productos" class="card1">
                <h2><i data-lucide="package"></i> Productos Recientes</h2>

                <?php if ($productos->num_rows > 0): ?>
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Código</th>
                                <th>Precio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = $productos->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $p['nombre'] ?></td>
                                    <td><a href="features/productos/vista.php?codigo=<?= $p['codigo'] ?>"><?= $p['codigo'] ?></a></td>
                                    <td>$<?= number_format($p['precio'], 2) ?></td>
                                    <td class="actions-cell">
                                        <?php if ($_SESSION["nivel"] === "admin"): ?>
                                            <a href="features/productos/crear.php" class="btn-add"><i data-lucide="plus-circle"></i></a>
                                            <a href="features/productos/editar.php?codigo=<?= $p['codigo'] ?>" class="btn-edit"><i data-lucide="edit-3"></i></a>
                                            <a href="features/productos/eliminar.php?codigo=<?= $p['codigo'] ?>" class="btn-remove"><i data-lucide="trash-2"></i></a>
                                        <?php else: ?>
                                            <a href="features/cart/añadir.php?codigo=<?= $p['codigo'] ?>" class="btn-add">Añadir <i data-lucide="shopping-cart"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay productos disponibles</p>
                <?php endif; ?>
            </section>

            <section id="carrito" class="card1">
                <h2><i data-lucide="shopping-cart"></i> Mi Carrito</h2>

                <?php
                if (!isset($_SESSION['user'])) {
                    echo "<p>Debes iniciar sesión para ver el carrito.</p>";
                } else {
                    $stmt = $conn->prepare("SELECT id FROM users WHERE nombre = ?");
                    $stmt->bind_param("s", $_SESSION['user']);
                    $stmt->execute();
                    $stmt->bind_result($user_id);

                    if (!$stmt->fetch()) {
                        echo "<p>Usuario no encontrado.</p>";
                    } else {
                        $stmt->close();

                        $stmt = $conn->prepare("
                        SELECT p.nombre, p.codigo, p.precio, c.cantidad
                        FROM cart c
                        JOIN products p ON c.product_id = p.id
                        WHERE c.user_id = ?
                    ");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $total = 0;
                            echo '<ul class="cart-list">';
                            while ($item = $result->fetch_assoc()) {
                                $subtotal = $item['precio'] * $item['cantidad'];
                                $total += $subtotal;
                                echo '<li>';
                                echo htmlspecialchars($item['nombre']) . ' - $' . number_format($item['precio'], 2);
                                echo ' x ' . $item['cantidad'] . ' = $' . number_format($subtotal, 2);
                                echo ' <a href="features/cart/remover.php?codigo=' . urlencode($item['codigo']) . '" class="btn-remove"><i data-lucide="trash-2"></i></a>';
                                echo '</li>';
                            }
                            echo '</ul>';
                            echo '<p class="cart-total"><strong>Total: $' . number_format($total, 2) . '</strong></p>';
                            echo '<a href="features/cart/vaciar.php" class="btn-logout" style="margin-top:10px; display:inline-block;">Vaciar Carrito</a>';
                        } else {
                            echo "<p>El carrito está vacío</p>";
                        }
                    }
                }
                ?>
            </section>

        </div>
    </div>

    <script>
        lucide.createIcons();

        const sections = document.querySelectorAll('section');
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
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

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                if (pageYOffset >= sectionTop) current = section.getAttribute('id');
            });
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) link.classList.add('active');
            });
        });

        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('#productosTable tr');
        if (searchInput) {
            searchInput.addEventListener('keyup', () => {
                const value = searchInput.value.toLowerCase();
                rows.forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
                });
            });
        }
    </script>

</body>

</html>