<?php
function registrarEdicao($conn, $tabela, $id_registro, $coluna, $valor_antigo, $valor_novo, $usuario) {
    try {
        // Prepara a consulta para inserir o registro interno
        $stmt = $conn->prepare("
            INSERT INTO registrointerno (tabela_editada, id_registro_editado, coluna_editada, valor_antigo, valor_novo, usuario_que_editou, data_hora_edicao)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        if (!$stmt) {
            throw new Exception("Erro ao preparar a consulta SQL: " . $conn->error);
        }

        // Vincula os parâmetros à consulta SQL
        $stmt->bind_param("sissss", $tabela, $id_registro, $coluna, $valor_antigo, $valor_novo, $usuario);

        // Executa a consulta
        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a consulta: " . $stmt->error);
        }

        // Fecha o statement
        $stmt->close();
    } catch (Exception $e) {
        // Retorna o erro
        echo "Erro ao registrar edição: " . $e->getMessage();
    }
}
?>
