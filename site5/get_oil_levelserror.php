<?php
// Inclui a conexão com o banco de dados
include 'db.php';

// Configura os cabeçalhos HTTP para a API
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

try {
    // Consulta SQL para recuperar os dados dos níveis de óleo
    $query = "SELECT boat_id, oil_level, next_change, next_change_value, registration_date, whatsapp_number FROM oil_levels";
    $result = $conn->query($query);

    // Verifica se a consulta SQL retornou resultados
    if (!$result) {
        throw new Exception("Erro ao buscar dados: " . $conn->error);
    }

    // Se não houver registros, retorna uma resposta vazia
    if ($result->num_rows === 0) {
        echo json_encode(["status" => "success", "data" => [], "message" => "Nenhum registro encontrado."]);
        exit;
    }

    // Processa os resultados e os armazena em um array
    $oilLevels = [];
    while ($row = $result->fetch_assoc()) {
        $oilLevels[] = $row;
    }

    // Retorna os dados no formato JSON
    echo json_encode(["status" => "success", "data" => $oilLevels]);

} catch (Exception $e) {
    // Retorna um erro com mensagem amigável ao cliente
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Erro interno no servidor. Por favor, tente novamente mais tarde."
    ]);
} finally {
    // Fecha a conexão com o banco de dados
    $conn->close();
}
?>
