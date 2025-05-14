<?php
include 'db.php'; // Conexão com o banco de dados
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
    // Verifica se o usuário está autenticado
    if (!isset($_SESSION['username'])) {
        throw new Exception("Usuário não autenticado.");
    }

    // Captura o nome do usuário logado
    $eq_user = $_SESSION['username'];

    // Captura os dados enviados pelo frontend
    $data = json_decode(file_get_contents("php://input"), true);

    // Valida se o ID foi fornecido
    if (!isset($data['id'])) {
        throw new Exception("ID não fornecido.");
    }

    $id = $data['id'];

    // Atualiza o campo boat_id na tabela oil_levels com base no eq_user
    $stmt = $conn->prepare("
        UPDATE oil_levels
        SET boat_id = ?
        WHERE eq_user = ?
    ");
    if (!$stmt) {
        throw new Exception("Erro ao preparar consulta SQL: " . $conn->error);
    }

    // Vincula os valores aos parâmetros
    $stmt->bind_param("ss", $id, $eq_user);

    // Executa a consulta
    if (!$stmt->execute()) {
        throw new Exception("Erro ao executar a consulta: " . $stmt->error);
    }

    // Retorna uma mensagem de sucesso
    echo json_encode(["status" => "success", "message" => "boat_id atualizado com sucesso!"]);

    // Fecha o statement e a conexão
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Retorna o erro como resposta
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
