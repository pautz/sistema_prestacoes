meusequipamentos.php 
$cx = mysqli_connect("127.0.0.1", "username", "password");

// Verificar se a conexão foi bem-sucedida
if (!$cx) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Selecionar o banco de dados
$db = mysqli_select_db($cx, "dbname");