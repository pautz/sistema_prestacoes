<?php
include 'db.php'; // Inclui a conexão com o banco de dados
session_start();

try {
    // Verifica se o usuário está autenticado
    if (!isset($_SESSION['username'])) {
        throw new Exception("Usuário não autenticado.");
    }

    // Captura o usuário logado
    $eq_user = $_SESSION['username'];

    // Consulta SQL para buscar os registros, incluindo nv_oleo
    $stmt = $conn->prepare("
        SELECT boat_id, cv, oil_level, nv_oleo, next_change, next_change_value, whatsapp_number, paymentstatus 
        FROM oil_levels 
        WHERE eq_user = ?
    ");
    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta: " . $conn->error);
    }

    // Vincula o parâmetro eq_user à consulta
    $stmt->bind_param("s", $eq_user);

    // Executa a consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepara os dados para retornar
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row; // Adiciona cada registro ao array $data
    }

    // Retorna os registros em formato JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);

    // Fecha o statement e a conexão com o banco de dados
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Retorna erro como resposta JSON
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
