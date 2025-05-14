<?php
include 'db.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_SESSION["username"])) {
        throw new Exception("Usuário não autenticado.");
    }

    $eq_user = $_SESSION["username"];
    file_put_contents('debug.log', "[" . date('Y-m-d H:i:s') . "] Usuário autenticado: $eq_user\n", FILE_APPEND);

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['boat_id']) || empty($data['boat_id'])) {
        throw new Exception("Dados incompletos. O campo 'boat_id' é obrigatório.");
    }

    $boat_id = intval($data['boat_id']);
    file_put_contents('debug.log', "[" . date('Y-m-d H:i:s') . "] Boat ID recebido: $boat_id\n", FILE_APPEND);

    $stmt = $conn->prepare("DELETE FROM oil_levels WHERE boat_id = ? AND eq_user = ?");
    if (!$stmt) {
        throw new Exception("Erro ao preparar consulta SQL: " . $conn->error);
    }

    $stmt->bind_param("is", $boat_id, $eq_user);
    $stmt->execute();
    file_put_contents('debug.log', "[" . date('Y-m-d H:i:s') . "] Consulta executada. Linhas afetadas: " . $stmt->affected_rows . "\n", FILE_APPEND);

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Registro removido com sucesso!", "deleted_boat_id" => $boat_id]);
    } else {
        throw new Exception("Nenhum registro encontrado com o 'boat_id' fornecido ou você não tem permissão.");
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    file_put_contents('debug.log', "[" . date('Y-m-d H:i:s') . "] Erro: " . $e->getMessage() . "\n", FILE_APPEND);

    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
