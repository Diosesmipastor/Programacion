<?php
include "../../db.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $nivel = $_POST["nivel"] ?? "usuario";

    if ($nombre && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6 && in_array($nivel, ['usuario', 'admin'])) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $userExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userExists) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nombre, email, password, nivel) VALUES (:nombre, :email, :password, :nivel)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hash);
            $stmt->bindParam(':nivel', $nivel);
            $mensaje = $stmt->execute() ? "Registro exitoso" : "Error al registrar";
        } else {
            $mensaje = "El email ya está registrado";
        }
    } else {
        $mensaje = "Datos inválidos. Verifica los campos y que la contraseña tenga al menos 6 caracteres.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <div class="card">
        <form method="POST" onsubmit="return validarFormulario()">
            <h2>Registro</h2>

            <div class="input-group">
                <i data-lucide="user"></i>
                <input type="text" name="nombre" placeholder="Nombre" required>
            </div>

            <div class="input-group">
                <i data-lucide="mail"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <i data-lucide="lock"></i>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>

            <div class="input-group">
                <label>Nivel</label>
                <select name="nivel" required>
                    <option value="usuario" selected>Usuario</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button>Registrar</button>

            <div id="msg" class="msg"><?= $mensaje ?></div>

            <a href="login.php">Iniciar sesión</a>
        </form>
    </div>

    <script>
        lucide.createIcons();

        function validarFormulario() {
            const inputs = document.querySelectorAll("input, select");
            const msg = document.getElementById("msg");
            for (let input of inputs) {
                if (!input.value.trim()) {
                    msg.textContent = "Completa todos los campos";
                    msg.className = "msg error";
                    return false;
                }
            }
            msg.textContent = "Registrando usuario...";
            msg.className = "msg success";
            return true;
        }
    </script>
</body>

</html>