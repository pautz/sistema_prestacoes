<?php
// Inicializar a sessão
session_start();

// Conexão segura ao banco de dados
$cx = new mysqli("127.0.0.1", "username", "password", "dbname");
if ($cx->connect_error) {
    die("Erro de conexão: " . $cx->connect_error);
}

// Capturar parâmetros da URL com validação
$cv = isset($_GET['cv']) ? htmlspecialchars($_GET['cv']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Construir a consulta dinâmica com base nos filtros fornecidos e incluir oil_levels, filtrando por boat_id=id
if (!empty($cv) && !empty($id)) {
    $query = "SELECT r.*, o.oil_level FROM respostas r LEFT JOIN oil_levels o ON o.boat_id = r.id WHERE r.cv = ? AND r.id = ? AND (r.tipo IS NULL OR r.tipo = '') ORDER BY r.id DESC";
    $stmt = $cx->prepare($query);
    $stmt->bind_param("si", $cv, $id);
} elseif (!empty($cv)) {
    $query = "SELECT r.*, o.oil_level FROM respostas r LEFT JOIN oil_levels o ON o.boat_id = r.id WHERE r.cv = ? AND (r.tipo IS NULL OR r.tipo = '') ORDER BY r.id DESC";
    $stmt = $cx->prepare($query);
    $stmt->bind_param("s", $cv);
} elseif (!empty($id)) {
    $query = "SELECT r.*, o.oil_level FROM respostas r LEFT JOIN oil_levels o ON o.boat_id = r.id WHERE r.id = ? AND (r.tipo IS NULL OR r.tipo = '') ORDER BY r.id DESC";
    $stmt = $cx->prepare($query);
    $stmt->bind_param("i", $id);
} else {
    $query = "SELECT r.*, o.oil_level FROM respostas r LEFT JOIN oil_levels o ON o.boat_id = r.id WHERE (r.tipo IS NULL OR r.tipo = '') ORDER BY r.id DESC";
    $stmt = $cx->prepare($query);
}

// Executar a consulta
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificados</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
  <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: rgba(240, 240, 240, 0.8); /* Fundo claro */
        color: #333;
        margin: 0;
        padding: 0;
        text-align: center;
    }

    .search-container {
        display: flex; /* Campos e botão lado a lado */
        align-items: center; /* Centraliza verticalmente */
        justify-content: center; /* Centraliza horizontalmente */
        gap: 15px; /* Espaçamento entre os campos e o botão */
        margin: 20px 0;
        padding: 15px;
        background-color: rgba(255, 255, 255, 0.9); /* Fundo leve */
        border-radius: 10px; /* Bordas arredondadas */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra suave */
        flex-wrap: wrap; /* Adapta em telas menores */
    }

    .search-container label {
        font-size: 14px;
        font-weight: bold;
        margin-right: 5px; /* Espaçamento entre texto e input */
    }

    .search-container input {
        width: 250px;
        padding: 10px 15px;
        font-size: 14px;
        border: 2px solid rgba(204, 204, 204, 0.8); /* Contorno suave */
        border-radius: 25px; /* Bordas arredondadas */
        background-color: #f9f9f9; /* Fundo claro do campo */
        transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
    }

    .search-container input:hover {
        background-color: rgba(240, 240, 240, 1); /* Leve destaque no hover */
    }

    .search-container input:focus {
        outline: none; /* Remove contorno padrão */
        border-color: rgba(0, 123, 255, 0.8); /* Azul no foco */
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5); /* Efeito brilhante */
        background-color: rgba(255, 255, 255, 1); /* Fundo mais claro no foco */
    }

    .search-container button {
        padding: 12px 20px;
        border: none;
        border-radius: 25px; /* Botão arredondado */
        font-size: 16px;
        font-weight: bold;
        color: white;
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.8), rgba(0, 86, 179, 0.8)); /* Gradiente moderno */
        cursor: pointer;
        transition: transform 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra leve */
    }

    .search-container button:hover {
        transform: scale(1.1); /* Zoom leve no hover */
        background: linear-gradient(135deg, rgba(0, 86, 179, 0.9), rgba(0, 50, 150, 0.9)); /* Gradiente mais intenso */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Sombra mais pronunciada */
    }

    .search-container button:active {
        transform: scale(0.95); /* Leve redução no clique */
        box-shadow: none; /* Remove sombra no clique */
    }

    .container {
        display: flex; /* Alinha os divs lado a lado */
        flex-wrap: wrap; /* Permite quebra de linha */
        gap: 20px; /* Espaçamento entre os elementos */
        justify-content: center; /* Centraliza os elementos */
    }

    .product-details {
        flex: 1; /* Ocupa espaço proporcional */
        min-width: 200px; /* Define tamanho mínimo */
        max-width: 300px; /* Limita a largura */
        background-color: rgba(255, 255, 255, 1); /* Fundo branco */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); /* Sombra para destaque */
        transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out; /* Transição suave */
    }

    .product-details:hover {
        background-color: rgba(255, 200, 150, 1); /* Fundo laranja ao passar o mouse */
        transform: scale(1.05); /* Leve aumento no hover */
    }

    /* Responsividade para telas menores */
    @media (max-width: 768px) {
        .search-container {
            flex-wrap: wrap; /* Permite quebra em dispositivos menores */
        }

        .container {
            flex-wrap: wrap; /* Organiza os divs verticalmente */
            justify-content: center; /* Centraliza os elementos */
        }

        .product-details {
            max-width: 100%; /* Cada div ocupa toda a largura */
        }
    }
</style>
</head>
<body>
    <h1>Bem-vindo</h1>
    <div class="search-container">
    <form method="GET">
        <label for="cv">Filtrar por Contrato:</label>
        <input type="text" id="cv" name="cv" value="<?php echo htmlspecialchars($cv); ?>">
        <br>
        <label for="id">Filtrar por Prestação:</label>
        <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <button type="submit">Buscar</button>
    </form>
    </div>
<br>
    <div class="container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $oil_level = $row['oil_level'] ?? 0.00;
            $valor_formatado = number_format((float)$oil_level, 2, ',', '.');
            echo "<div class='product-details'>"; // Corrigido para garantir que cada item esteja dentro da classe correta
            echo "<p>Contrato: " . htmlspecialchars_decode($row['cv']) . "</p>";
            echo "<p>ID: " . htmlspecialchars_decode($row['id']) . "</p>";
            echo "<p>Status do pagamento: " . htmlspecialchars($row['status_pagamento']) . "</p>";
            echo "<p>Data do pagamento: " . htmlspecialchars($row['data_pagamento']) . "</p>";
            echo "<p><strong>Valor do Lance:</strong> R$ $valor_formatado</p>";
            echo "<p>Usuário: " . htmlspecialchars($row['eq_user']) . "</p>";
            echo "<button onclick='gerarPDF(\"" . $row['id'] . "\", \"" . htmlspecialchars($row['cv']) . "\", \"" . htmlspecialchars($row['tipo']) . "\", \"" . htmlspecialchars($row['status_pagamento']) . "\", \"" . htmlspecialchars($row['data_pagamento']) . "\", \"$valor_formatado\", \"" . htmlspecialchars($row['eq_user']) . "\")'>Gerar Certificado em PDF</button>";
            echo "</div>";
        }
    } else {
        echo "<p>Nenhum resultado encontrado.</p>";
    }
    ?>
</div>

    <script>
      async function gerarPDF(id, cv, tipo, statusPagamento, dataPagamento, oilLevel, eqUser) {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF();
    pdf.text(`Certificado do Trator`, 105, 10, { align: "center" });
    pdf.text(`ID: ${id}`, 10, 20);
    pdf.text(`Contrato: ${cv}`, 10, 30);
    pdf.text(`Tipo: ${tipo}`, 10, 40);
    pdf.text(`Status: ${statusPagamento}`, 10, 50);
    pdf.text(`Data do Pagamento: ${dataPagamento}`, 10, 60);
    pdf.text(`Nível de Óleo: R$ ${oilLevel}`, 10, 70);
    pdf.text(`Usuário: ${eqUser}`, 10, 80); // Adicionando o campo eq_user ao PDF
    pdf.save(`certificado_${id}.pdf`);
}

    </script>
</body>
</html>
