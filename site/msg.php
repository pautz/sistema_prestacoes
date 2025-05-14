<?php
// Inicializar a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Configuração do banco de dados
$servername = "127.0.0.1";
$username = "";
$password = "";
$dbname = "";

// Habilitar exibição de erros
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Definição da variável $id com tratamento para valores indefinidos
$id = isset($_GET['id']) && !empty($_GET['id']) ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8') : 'Contrato não informado';

// Alternar ativação do campo "tipo"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["toggleTipo"])) {
    $_SESSION['tipo_enabled'] = !isset($_SESSION['tipo_enabled']) || !$_SESSION['tipo_enabled'];
}

// Lógica para inserção de registros

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["quantidade"])) {
    $quantidade = intval($_POST['quantidade']);
    $tipo = isset($_POST['tipo']) && $_SESSION['tipo_enabled'] ? htmlspecialchars($_POST['tipo'], ENT_QUOTES, 'UTF-8') : null;
    $qrcode = htmlspecialchars($_POST['qrcode'], ENT_QUOTES, 'UTF-8');
    $nome_recebedor = htmlspecialchars($_POST['nome_recebedor'], ENT_QUOTES, 'UTF-8');
    $cidade_recebedor = htmlspecialchars($_POST['cidade_recebedor'], ENT_QUOTES, 'UTF-8');
    $descricao = htmlspecialchars($_POST['descricao'], ENT_QUOTES, 'UTF-8');
    $eq_user = $_SESSION["username"];
    $metamask = htmlspecialchars($_POST['metamask'], ENT_QUOTES, 'UTF-8');
if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $metamask)) {
    die("Erro: Endereço Metamask inválido.");
}


    // Verificar duplicata somente se o campo "tipo" estiver preenchido
    $isDuplicate = false;
    if (!empty($tipo)) {
      $stmt = $conn->prepare("SELECT COUNT(*) FROM respostas WHERE tipo = ? AND cv = ? AND eq_user = ?");
$stmt->bind_param("sss", $tipo, $id, $eq_user); // "sss" indica três strings
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();


        if ($count > 0) {
            $isDuplicate = true;
            echo "<p>Erro: O valor 'tipo' já existe no banco de dados.</p>";
        }
    }

    // Inserir os registros se não houver duplicata ou se "tipo" for nulo
    if (!$isDuplicate) {
        $stmt = $conn->prepare("INSERT INTO respostas (cv, tipo, qrcode, eq_user, nome_recebedor, cidade_recebedor, descricao, metamask) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        for ($i = 0; $i < $quantidade; $i++) {
            $stmt->bind_param("ssssssss", $id, $tipo, $qrcode, $eq_user, $nome_recebedor, $cidade_recebedor, $descricao, $metamask);
            try {
                $stmt->execute();
                echo "<p>Registro " . ($i + 1) . " criado com sucesso!</p>";
            } catch (Exception $e) {
                echo "<p>Erro ao criar o registro " . ($i + 1) . ": " . $e->getMessage() . "</p>";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Dados</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style>
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <center><a href="http://carlitoslocacoes.com/site2" class="btn btn-primary">Início</a></center>
    <div class="page-header text-center">
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION["username"], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    </div>
    <div class="container">
        <form method="POST" action="">
            <p><b>Cadastrar no Contrato:</b> <?php echo $id; ?></p>
            <input type="hidden" name="eq_user" value="<?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
                <label for="quantidade">Quantidade de prestações:</label>
                <input type="number" id="quantidade" name="quantidade" min="1" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tipo">Número Código de Barra:</label>
                <input type="text" id="tipo" name="tipo" class="form-control" <?php echo isset($_SESSION['tipo_enabled']) && $_SESSION['tipo_enabled'] ? '' : 'disabled'; ?>>
            </div>
            <div class="form-group">
                <button type="button" id="toggleTipo" class="btn btn-warning">Desativado Prestação/Ativado Produto</button>
            </div>
            <div class="form-group">
                <label for="qrcode">Número WhatsApp:</label>
                <input type="text" id="qrcode" name="qrcode" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="nome_recebedor">Nome do Recebedor:</label>
                <input type="text" id="nome_recebedor" name="nome_recebedor" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="cidade_recebedor">Cidade do Recebedor:</label>
                <input type="text" id="cidade_recebedor" name="cidade_recebedor" class="form-control" required>
            </div>
            <div class="form-group">
   
    <label for="metamask">Endereço Metamask:</label>
    <input type="text" id="metamask" name="metamask" class="form-control" required>
</div>

            <div class="form-group">
                <label for="descricao">Preço Unidade:</label>
                <input type="number" id="descricao" name="descricao" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Cadastrar</button>
        </form>
    </div>
    <script>
        document.getElementById("toggleTipo").addEventListener("click", () => {
            const form = document.createElement("form");
            form.method = "POST";
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "toggleTipo";
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</body>
</html>
