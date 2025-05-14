<?php
include 'db.php'; // Conexão com o banco de dados
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
    // Verifica se o usuário está autenticado
    if (!isset($_SESSION['username'])) {
        throw new Exception("Usuário não autenticado.");
    }

    // Captura o usuário logado
    $eq_user = $_SESSION['username'];

    // Captura os dados enviados pelo frontend
    $data = json_decode(file_get_contents("php://input"), true);

    // Validação dos campos obrigatórios, incluindo nv_oleo
    if (!isset($data['boat_id'], $data['cv'], $data['oilLevel'], $data['nvOleo'], $data['nextChange'], $data['nextChangeValue'], $data['whatsapp_number'], $data['paymentstatus'])) {
        throw new Exception("Dados incompletos. Todos os campos são obrigatórios.");
    }

    $boat_id = $data['boat_id']; // Captura o valor do ID do barco
    $cv = $data['cv'];
    $oilLevel = $data['oilLevel'];
    $nvOleo = $data['nvOleo']; // Captura o valor de nv_oleo
    $nextChange = $data['nextChange'];
    $nextChangeValue = $data['nextChangeValue'];
    $whatsapp_number = $data['whatsapp_number'];
    $paymentstatus = $data['paymentstatus'];

    // Prepara a consulta SQL para inserir os dados
    $stmt = $conn->prepare("
        INSERT INTO oil_levels (boat_id, cv, oil_level, nv_oleo, next_change, next_change_value, whatsapp_number, eq_user, paymentstatus)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta SQL: " . $conn->error);
    }

    // Vincula os valores aos parâmetros da consulta
    $stmt->bind_param("sssssssss", $boat_id, $cv, $oilLevel, $nvOleo, $nextChange, $nextChangeValue, $whatsapp_number, $eq_user, $paymentstatus);

    // Executa a consulta
    if (!$stmt->execute()) {
        throw new Exception("Erro ao executar a consulta: " . $stmt->error);
    }

    // Retorna uma mensagem de sucesso
    echo json_encode(["status" => "success", "message" => "Registro adicionado com sucesso!"]);

    // Fecha o statement e a conexão
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Retorna o erro como resposta
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
