<?php
include 'db.php';
session_start();

try {
    if (!isset($_SESSION['username'])) {
        throw new Exception("Usuário não autenticado.");
    }

    $eq_user = $_SESSION['username'];
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['cv'])) { // Trata boat_id como cv aqui
        throw new Exception("O campo 'cv' é obrigatório.");
    }

    $cv = $data['cv'];

    $stmt = $conn->prepare("SELECT DISTINCT id 
FROM respostas 
WHERE cv = ? AND eq_user = ? AND caixa = '' AND (tipo = '' OR tipo IS NULL)");
    $stmt->bind_param("ss", $cv, $eq_user);
    $stmt->execute();
    $result = $stmt->get_result();

    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($ids);

    $result->free();
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    file_put_contents('debug.log', "[" . date('Y-m-d H:i:s') . "] Erro: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
