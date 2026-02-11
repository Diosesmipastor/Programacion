<?php

$host = getenv("DB_HOST");
$port = getenv("DB_PORT") ?: 5432;
$db   = getenv("DB_NAME");
$user = getenv("DB_USER");
$pass = getenv("DB_PASSWORD");

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            nivel VARCHAR(20) NOT NULL DEFAULT 'usuario'
        )
    ");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS products (
            id SERIAL PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            codigo VARCHAR(50) UNIQUE NOT NULL,
            precio NUMERIC(10,2) NOT NULL,
            descripcion TEXT
        )
    ");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS cart (
            id SERIAL PRIMARY KEY,
            user_id INT REFERENCES users(id),
            product_id INT REFERENCES products(id),
            cantidad INT NOT NULL
        )
    ");
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
