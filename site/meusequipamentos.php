<?php
setlocale(LC_TIME, 'pt_BR.utf8');

// Inicializar a sessão
session_start();

// Verificar se o usuário está logado, senão redireciona para a página de login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Conexão com o banco de dados
$cx = mysqli_connect("127.0.0.1", "username", "password");

// Verificar se a conexão foi bem-sucedida
if (!$cx) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Selecionar o banco de dados
$db = mysqli_select_db($cx, "dbname");
if (!$db) {
    die("Erro ao selecionar o banco de dados: " . mysqli_error($cx));
}

$eq_user = $_SESSION["username"];

// Inicializar variáveis de filtro
$cv_filter = '';
$id_filter = '';
$status_pagamento_filter = '';

// Verificar se os filtros foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_qrcode'])) {
        // Atualizar o campo qrcode no banco de dados
        $qrcode_id = mysqli_real_escape_string($cx, $_POST['qrcode_id']);
        $new_qrcode = mysqli_real_escape_string($cx, $_POST['new_qrcode']);
        $update_query = "UPDATE respostas SET qrcode = '$new_qrcode' WHERE id = '$qrcode_id' AND eq_user = '$eq_user'";
        mysqli_query($cx, $update_query) or die("Erro ao atualizar o QR Code: " . mysqli_error($cx));
    } elseif (isset($_POST['update_pagamento'])) {
        // Atualizar o status de pagamento no banco de dados
        $qrcode_id = mysqli_real_escape_string($cx, $_POST['qrcode_id']);
        $new_status_pagamento = mysqli_real_escape_string($cx, $_POST['new_status_pagamento']);
        $update_query = "UPDATE respostas SET status_pagamento = '$new_status_pagamento' WHERE id = '$qrcode_id' AND eq_user = '$eq_user'";
        mysqli_query($cx, $update_query) or die("Erro ao atualizar o status de pagamento: " . mysqli_error($cx));
    } elseif (isset($_POST['update_data_pagamento'])) {
        // Atualizar o campo data_pagamento no banco de dados
        $qrcode_id = mysqli_real_escape_string($cx, $_POST['qrcode_id']);
        $new_data_pagamento = mysqli_real_escape_string($cx, $_POST['new_data_pagamento']);
        $update_query = "UPDATE respostas SET data_pagamento = '$new_data_pagamento' WHERE id = '$qrcode_id' AND eq_user = '$eq_user'";
        mysqli_query($cx, $update_query) or die("Erro ao atualizar a data de pagamento: " . mysqli_error($cx));
    } else {
        // Aplicar filtros
        $cv_filter = !empty($_POST['cv']) ? mysqli_real_escape_string($cx, trim($_POST['cv'])) : '';
        $id_filter = !empty($_POST['id']) ? mysqli_real_escape_string($cx, trim($_POST['id'])) : '';
        $status_pagamento_filter = !empty($_POST['status_pagamento']) ? mysqli_real_escape_string($cx, trim($_POST['status_pagamento'])) : '';
    }
}

// Construir consulta SQL com lógica simplificada
$query = "SELECT * 
FROM respostas 
WHERE eq_user = '$eq_user' AND caixa = '' AND (tipo = '' OR tipo IS NULL)";

if (!empty($cv_filter)) {
    $query .= " AND cv = '$cv_filter'";
}

if (!empty($id_filter)) {
    $query .= " AND id = '$id_filter'";
}

if (!empty($status_pagamento_filter)) {
    $query .= " AND status_pagamento = '$status_pagamento_filter'";
}

$query .= " ORDER BY id DESC;";
$sql = mysqli_query($cx, $query);

if (!$sql) {
    die("Erro na consulta: " . mysqli_error($cx));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Minhas Prestações</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
   <style>
/* Reset de estilo para evitar inconsistências */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Estilo geral do corpo */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f5f5f5;
    color: #333;
    line-height: 1.6;
    text-align: center;
    margin: 0;
    padding: 0;
}

/* Container principal */
.container {
    width: 90%;
    max-width: 1200px;
    margin: 20px auto;
}

/* Estilo geral do formulário */
form {
    background-color: #ffffff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

/* Estilo dos campos de entrada */
form input[type="text"],
form input[type="date"],
form select {
    width: calc(100% - 20px);
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 14px;
    background-color: #f9f9f9;
    transition: border-color 0.3s ease;
}

/* Estilo ao focar nos campos de entrada */
form input[type="text"]:focus,
form input[type="date"]:focus,
form select:focus {
    border-color: #28a745;
    outline: none;
}

/* Estilo do botão de envio */
form button[type="submit"] {
    background-color: #28a745;
    color: #ffffff;
    font-size: 16px;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Hover no botão */
form button[type="submit"]:hover {
    background-color: #218838;
}

/* Estilo dos labels */
form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

/* Responsividade para telas menores */
@media (max-width: 768px) {
    form input[type="text"],
    form input[type="date"],
    form select {
        width: 100%;
    }

    form button[type="submit"] {
        width: 100%;
    }
}

/* Estilo das colunas */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.col {
    flex: 0 0 25%;
    max-width: 25%;
    padding: 15px;
    box-sizing: border-box;
}

/* Estilo de cada item */
.content {
    background-color: #ffffff;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Efeito de hover nos itens */
.content:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Ajustes para tablets */
@media (max-width: 992px) {
    .col {
        flex: 0 0 33.33%;
        max-width: 33.33%;
    }
}

/* Ajustes para celulares */
@media (max-width: 768px) {
    .col {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* Ajustes para telas muito pequenas */
@media (max-width: 576px) {
    .col {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

</head>
<body>
    <a href="https://carlitoslocacoes.com/site/welcome.php" class="btn btn-primary btn-xl">Início</a>
    <br>
    <br>

    <!-- Formulário de filtros -->
    <form method="POST" action="">
        <label for="cv">Filtrar por Prestação:</label>
        <input type="text" name="id" id="id" value="<?php echo htmlspecialchars($id_filter); ?>" />
        <label for="id">Filtrar por Contrato:</label>
        <input type="text" name="cv" id="cv" value="<?php echo htmlspecialchars($cv_filter); ?>" />
        <label for="status_pagamento">Filtrar por Pagamento:</label>
        <select name="status_pagamento" id="status_pagamento">
            <option value="">Todos</option>
            <option value="Pago" <?php if ($status_pagamento_filter == 'Pago') echo 'selected'; ?>>Pago</option>
            <option value="Não Pago" <?php if ($status_pagamento_filter == 'Não Pago') echo 'selected'; ?>>Não Pago</option>
        </select>
        <label for="data_pagamento">Selecionar Data de Pagamento:</label>
        <input type="date" name="data_pagamento" id="data_pagamento" value="<?php echo isset($_POST['data_pagamento']) ? htmlspecialchars($_POST['data_pagamento']) : ''; ?>" />
        <button type="submit" class="btn btn-success">Aplicar Filtros</button>
    </form>
    <br>

    <div class="row">
        <?php
        if (mysqli_num_rows($sql) > 0) {
            while ($aux = mysqli_fetch_assoc($sql)) {
                echo '<div class="col">';
                echo '<div class="content">';
                echo "<p><strong>Prestação:</strong> " . $aux['id'] . "</p>";
                echo "<p><strong>Contrato:</strong> " . $aux['cv'] . "</p>";
                echo "<p><strong>Tipo:</strong> " . $aux['tipo'] . "</p>";
                echo "<p><strong>QR Code:</strong> " . htmlspecialchars($aux['qrcode']) . "</p>";
                echo "<p><strong>Status de Pagamento:</strong> " . htmlspecialchars($aux['status_pagamento']) . "</p>";
                $date = new DateTime($aux['data_pagamento']);
                echo "<p><strong>Data de Pagamento:</strong> " . $date->format('d/m/Y') . "</p>";

                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="qrcode_id" value="' . $aux['id'] . '">';
                echo '<label for="new_qrcode">Número do Recebedor:</label>';
                echo '<input type="text" name="new_qrcode" value="' . htmlspecialchars($aux['qrcode']) . '">';
                echo '<button type="submit" name="update_qrcode" class="btn btn-warning">Atualizar QR Code</button>';
                echo '</form>';
                echo '<br>';

                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="qrcode_id" value="' . $aux['id'] . '">';
                echo '<label for="new_status_pagamento">Atualizar Pagamento:</label>';
                echo '<select name="new_status_pagamento">';
                echo '<option value="Pago" ' . ($aux['status_pagamento'] == 'Pago' ? 'selected' : '') . '>Pago</option>';
                echo '<option value="Não Pago" ' . ($aux['status_pagamento'] == 'Não Pago' ? 'selected' : '') . '>Não Pago</option>';
                                echo '</select>';
                echo '<br>';
                echo '<button type="submit" name="update_pagamento" class="btn btn-warning">Atualizar Pagamento</button>';
                echo '</form>';
                echo '<br>';

                // Formulário para editar a data de pagamento
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="qrcode_id" value="' . $aux['id'] . '">';
                echo '<label for="new_data_pagamento">Editar Data de Pagamento:</label>';
                echo '<input type="date" name="new_data_pagamento" value="' . htmlspecialchars($aux['data_pagamento']) . '">';
                echo '<button type="submit" name="update_data_pagamento" class="btn btn-warning">Atualizar Data de Pagamento</button>';
                echo '</form>';

                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>Nenhum resultado encontrado com os filtros aplicados.</p>';
        }
        ?>
    </div>
</body>
</html>
