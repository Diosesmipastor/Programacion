<?php
$conn = new mysqli("localhost", "root", "", "compras");

if ($conn->connect_error) {
    die();
}