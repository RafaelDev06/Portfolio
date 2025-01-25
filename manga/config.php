<?php
$servername = "localhost";
$username = "root";
$password = ""; // Deixe em branco se estiver usando o XAMPP com a configuração padrão
$dbname = "manga";

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
