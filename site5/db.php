<?php
$servername = "localhost";
$username = "";
$password = "";
$dbname = "";

// Criando conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
