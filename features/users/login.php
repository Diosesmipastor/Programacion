<?php
session_start();
include "../../db.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["token"] = bin2hex(random_bytes(16));
            $_SESSION["user"] = $user["nombre"];
            $_SESSION["nivel"] = $user["nivel"];
            header("Location: /index.php");
            exit();
        }
    }

    $mensaje = "Credenciales incorrectas";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="card">
        <i data-lucide="log-in"></i>
        <form method="POST" onsubmit="return validarFormulario()">
            <h2>Iniciar Sesión</h2>
            <div class="input-group">
                <i data-lucide="mail"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i data-lucide="lock"></i>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit">Ingresar</button>
            <div id="msg" class="msg <?= $mensaje ? 'error' : '' ?>"><?= $mensaje ?></div>
            <a href="registro.php">Crear cuenta</a>
        </form>
    </div>

    <script>
        lucide.createIcons();

        function validarFormulario() {
            const email = document.querySelector("input[name='email']");
            const password = document.querySelector("input[name='password']");
            const msg = document.getElementById("msg");
            if (!email.value.trim() || !password.value.trim()) {
                msg.textContent = "Completa todos los campos";
                msg.className = "msg error";
                return false;
            }
            msg.textContent = "Verificando credenciales...";
            msg.className = "msg success";
            return true;
        }

        const msg = document.getElementById("msg");
        if (msg.textContent.trim() !== "") {
            msg.style.opacity = 0;
            setTimeout(() => {
                msg.style.transition = "opacity 0.5s";
                msg.style.opacity = 1;
            }, 100);
        }
    </script>
</body>

</html>