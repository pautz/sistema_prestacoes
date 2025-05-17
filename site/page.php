<?php
session_start();
include 'phpqrcode/qrlib.php';

// Conexão ao banco de dados
$cx = new mysqli("127.0.0.1", "u839226731_cztuap", "Meu6595869Trator", "u839226731_meutrator");
if ($cx->connect_error) {
    die("Erro de conexão: " . $cx->connect_error);
}

// Capturar parâmetros da URL
$eqUser = isset($_SESSION["username"]) ? $_SESSION["username"] : "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($id)) {
    echo "<p>Erro: ID não especificado.</p>";
    exit;
}

$queryDados = "SELECT r.qrcode, r.nome_recebedor, r.cidade_recebedor, r.cv, r.tipo, r.status_pagamento, r.data_pagamento, r.eq_user, o.oil_level, o.whatsapp_number 
               FROM respostas r
               LEFT JOIN oil_levels o ON o.boat_id = r.id
               WHERE r.id = ?";
$stmtDados = $cx->prepare($queryDados);
$stmtDados->bind_param("i", $id);
$stmtDados->execute();
$resultDados = $stmtDados->get_result();

if ($resultDados->num_rows === 0) {
    echo "<p>Erro: Dados não encontrados para o ID especificado.</p>";
    exit;
}

$dadosCertificado = $resultDados->fetch_assoc();
$stmtDados->close();

function getBnbToBrlRate() {
    $url = "https://api.coingecko.com/api/v3/simple/price?ids=binancecoin&vs_currencies=brl";
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    return $data["binancecoin"]["brl"] ?? null;
}

$bnbToBrlRate = getBnbToBrlRate();
$bnbValue = $dadosCertificado['oil_level'] ?? 0;
$equivalenteEmBrl = number_format($bnbValue * $bnbToBrlRate, 2, ',', '.');


 
// Consulta para obter o endereço da wallet sem usar eq_user
$queryWallet = "SELECT metamask FROM respostas WHERE eq_user = ? AND id = ?";
$stmtWallet = $cx->prepare($queryWallet);
$stmtWallet->bind_param("si", $dadosCertificado['eq_user'], $id);
$stmtWallet->execute();
$resultWallet = $stmtWallet->get_result();


if ($resultWallet->num_rows === 0) {
    echo "<p>Erro: Endereço da wallet não encontrado.</p>";
    exit;
}

$dadosWallet = $resultWallet->fetch_assoc();
$enderecoDestino = htmlspecialchars($dadosWallet['metamask'] ?? '');
$stmtWallet->close();


// Preparar dados para QR Code
$telefonepagador = htmlspecialchars($dadosCertificado['whatsapp_number'] ?? '');
$valorPrestacao = number_format($dadosCertificado['oil_level'] ?? 0, 2, '.', '');
$dataPagamento = htmlspecialchars(!empty($dadosCertificado['data_pagamento']) ? (new DateTime($dadosCertificado['data_pagamento']))->format('d/m/Y') : 'Não disponível');
$usuario = htmlspecialchars($dadosCertificado['eq_user'] ?? 'Não disponível');
$cv = htmlspecialchars($dadosCertificado['cv'] ?? 'Não disponível');
$mensagem = "Olá! Gostaria de solicitar o pagamento da prestação. Usuário: $usuario ID Prestação: $id CV: $cv Valor: R$ " . number_format($bnbValue * $bnbToBrlRate, 6, ',', '.') . " Data: $dataPagamento";
$dadosWallet = $resultWallet->fetch_assoc();

// Inclua a mensagem no link do WhatsApp
$linkWhatsApp = "https://api.whatsapp.com/send?phone=$telefonepagador&text=" . urlencode($mensagem);

// Gerar QR Code
$filePath = 'temp_qrcode.png';
QRcode::png($linkWhatsApp, $filePath, QR_ECLEVEL_L, 10);

// Registrar pagamento
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hashTransacao'])) {
    $hashTransacao = $_POST['hashTransacao'];
    $payment = 1; // Definir como pago
    $valorPayment = $dadosCertificado['oil_level'] ?? 0; // Valor do oil_level

    // Verificar se já existe um registro com o mesmo hash
    $queryCheck = "SELECT COUNT(*) FROM paymentsprestacao WHERE hashTransacao = ?";
    $stmtCheck = $cx->prepare($queryCheck);
    $stmtCheck->bind_param("s", $hashTransacao);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
        echo "Erro: Pagamento já registrado.";
        exit;
    }

    // Registrar pagamento no banco de dados
    $queryInsert = "INSERT INTO paymentsprestacao (id, hashTransacao, cv, eq_user, payment, valorpayment) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = $cx->prepare($queryInsert);
    $stmtInsert->bind_param("isssid", $id, $hashTransacao, $cv, $usuario, $payment, $valorPayment);

    if ($stmtInsert->execute()) {
        echo "Pagamento registrado com sucesso!";
    } else {
        echo "Erro ao registrar pagamento: " . $stmtInsert->error;
    }
    $stmtInsert->close();
    // Atualizar status de pagamento na tabela respostas
$queryUpdateStatus = "UPDATE respostas SET status_pagamento = 'Pago' WHERE id = ?";
$stmtUpdateStatus = $cx->prepare($queryUpdateStatus);
$stmtUpdateStatus->bind_param("i", $id);

if ($stmtUpdateStatus->execute()) {
    echo "Status de pagamento atualizado!";
} else {
    echo "Erro ao atualizar status: " . $stmtUpdateStatus->error;
}

$stmtUpdateStatus->close();

}

$cx->close();


?>
<?php
session_start();
$cx = new mysqli("127.0.0.1", "u839226731_cztuap", "Meu6595869Trator", "u839226731_meutrator");
if ($cx->connect_error) {
    die("Erro de conexão: " . $cx->connect_error);
}

// Definir `eq_user` da sessão com base no nome do usuário
$eqUser = isset($_SESSION["username"]) ? $_SESSION["username"] : "";

$idPrestacao = isset($_GET['id']) ? intval($_GET['id']) : 0;
$queryPrestacao = "SELECT id, eq_user, oil_level, status_pagamento, data_pagamento FROM respostas WHERE id = ?";
$stmtPrestacao = $cx->prepare($queryPrestacao);
$stmtPrestacao->bind_param("i", $idPrestacao);
$stmtPrestacao->execute();
$resultPrestacao = $stmtPrestacao->get_result();

if ($resultPrestacao->num_rows === 0) {
    die("Erro: Prestação não encontrada.");
}

$dadosPrestacao = $resultPrestacao->fetch_assoc();
$stmtPrestacao->close();
$owner = $dadosPrestacao['eq_user']; // Dono da prestação
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f4f9; /* Fundo claro */
        color: #333;
        margin: 0;
        padding: 20px;
        text-align: center;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .qrcode img {
        width: 100%; /* Imagem ajusta-se ao tamanho da tela */
        max-width: 180px; /* Tamanho máximo da imagem */
        height: auto;
        border-radius: 8px; /* Borda arredondada */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra para destacar a imagem */
    }

    button {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.8), rgba(0, 86, 179, 0.8)); /* Gradiente moderno */
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 25px; /* Botão arredondado */
        cursor: pointer;
        margin: 10px;
        font-size: 16px;
        font-weight: bold;
        transition: transform 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra leve */
    }

    button:hover {
        transform: scale(1.1); /* Zoom leve no hover */
        background: linear-gradient(135deg, rgba(0, 86, 179, 0.9), rgba(0, 50, 150, 0.9)); /* Gradiente mais intenso */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Sombra mais pronunciada */
    }

    button:active {
        transform: scale(0.95); /* Redução leve ao clicar */
        box-shadow: none; /* Remove sombra ao clicar */
    }

    .qrcode {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 20px 0;
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
        width: 250px; /* Largura fixa */
        padding: 10px 15px;
        font-size: 14px;
        border: 2px solid rgba(204, 204, 204, 0.8); /* Contorno leve */
        border-radius: 25px; /* Bordas arredondadas */
        background-color: #f9f9f9; /* Fundo claro */
        transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
    }

    .search-container input:hover {
        background-color: rgba(240, 240, 240, 1); /* Fundo destacado ao passar o mouse */
    }

    .search-container input:focus {
        outline: none; /* Remove contorno padrão */
        border-color: rgba(0, 123, 255, 0.8); /* Azul no foco */
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5); /* Efeito brilhante */
        background-color: rgba(255, 255, 255, 1); /* Fundo mais claro no foco */
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .container {
            padding: 10px; /* Ajusta o padding para telas menores */
        }

        .search-container {
            flex-wrap: wrap; /* Permite quebra dos elementos */
        }

        .qrcode img {
            max-width: 150px; /* Reduz o tamanho da imagem */
        }

        button {
            padding: 10px 15px; /* Ajusta o tamanho do botão */
            font-size: 14px; /* Fonte menor em telas menores */
        }
    }

    @media (max-width: 480px) {
        .search-container input {
            width: 100%; /* Campos ocupam toda a largura */
        }

        button {
            width: 100%; /* Botão ocupa toda a largura */
            margin: 5px 0; /* Ajusta o espaçamento */
        }
    }
</style>

    <meta charset="UTF-8">
    <title>MetaMask/BNB</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/web3/1.7.5/web3.min.js"></script>
    <script>
    async function enviarPagamento() {
    if (typeof window.ethereum !== "undefined") {
        try {
            // Solicita ao usuário para conectar MetaMask, se necessário
            const contas = await window.ethereum.request({ method: "eth_requestAccounts" });

            if (contas.length === 0) {
                alert("Erro: Nenhuma conta MetaMask conectada.");
                return;
            }

            await window.ethereum.request({ method: "wallet_switchEthereumChain", params: [{ chainId: "0x38" }] });
            const web3 = new Web3(window.ethereum);

            // Recupera endereço de destino e valor corretamente
            const enderecoDestino = <?= json_encode($enderecoDestino) ?>;
            const totalGeral = <?= json_encode($dadosCertificado['oil_level'] ?? 0) ?>;

            if (!enderecoDestino || totalGeral <= 0) {
                alert("Erro: Endereço de destino ou valor inválido.");
                return;
            }

            const valorBNB = web3.utils.toWei(totalGeral.toString(), "ether");
            const contaOrigem = contas[0];

            // Envia a transação
            const tx = await web3.eth.sendTransaction({
                from: contaOrigem,
                to: enderecoDestino,
                value: valorBNB
            });

            const transactionHash = tx.transactionHash;
            alert("Pagamento enviado! Hash: " + transactionHash);

            // Valida a transação na blockchain
            const receipt = await validarTransacao(transactionHash);
            if (receipt && receipt.status) {
                alert("Pagamento confirmado na blockchain! Hash: " + transactionHash);
                registrarPagamento(transactionHash);
            } else {
                alert("Erro: Pagamento ainda não confirmado na blockchain.");
            }
        } catch (erro) {
            alert("Erro ao enviar pagamento: " + erro.message);
        }
    } else {
        alert("MetaMask não está instalado.");
    }
}

// Função para validar a transação na blockchain
async function validarTransacao(hash) {
    const web3 = new Web3(new Web3.providers.HttpProvider("https://bsc-dataseed.binance.org/"));
    try {
        const receipt = await web3.eth.getTransactionReceipt(hash);
        return receipt;
    } catch (erro) {
        console.error("Erro ao validar transação:", erro);
        return null;
    }
}

// Função para registrar o pagamento no banco de dados
function registrarPagamento(transactionHash) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        alert(xhr.responseText);
        location.reload();
    };
    xhr.send("hashTransacao=" + encodeURIComponent(transactionHash));
}

    function solicitarPagamentoWhatsApp() {
        window.open("<?= $linkWhatsApp ?>", "_blank");
    }
  

    </script>
    
</head>
<body>
    <div class="container">
        <h2>MetaMask/BNB</h2>
        <p><strong>ID:</strong> <?= htmlspecialchars($id) ?></p>
        <!--<p><strong>Tipo:</strong> <?= htmlspecialchars($dadosCertificado['tipo'] ?? 'Não definido') ?></p>-->
       <!-- <p><strong>Status do Pagamento:</strong> <?= htmlspecialchars($dadosCertificado['status_pagamento'] ?? 'Não disponível') ?></p>-->
        <p><strong>Valor do Lance:</strong> BNB <?= htmlspecialchars(number_format($dadosCertificado['oil_level'] ?? 0, 6, ',', '.')) ?></p>

    <?php if ($bnbToBrlRate !== null): ?>
    <p><strong>Equivalente em BRL:</strong> R$ <?= htmlspecialchars(number_format($bnbValue * $bnbToBrlRate, 2, ',', '.')) ?></p>
<?php else: ?>
    <p>Não foi possível obter a cotação do BNB.</p>
<?php endif; ?>


       <!-- <p><strong>Data do Pagamento:</strong> <?= htmlspecialchars($dataPagamento) ?></p>-->
        <p><strong>Usuário:</strong> <?= htmlspecialchars($usuario) ?></p>
        <p><strong>CV:</strong> <?= htmlspecialchars($cv) ?></p>
        <p><strong>Nome do Recebedor:</strong> <?= htmlspecialchars($dadosCertificado['nome_recebedor']) ?></p>
        <p><strong>Cidade do Recebedor:</strong> <?= htmlspecialchars($dadosCertificado['cidade_recebedor']) ?></p>
        
 <p><strong>Valor de BNB:</strong> 
    <input type="number" id="oil_level" step="0.000001" value="<?= htmlspecialchars(number_format($dadosPrestacao['oil_level'], 6, ',', '.')) ?>" <?= ($eqUser === $owner) ? '' : 'disabled' ?>>
</p>

<?php if ($eqUser === $owner): ?>
    <button onclick="atualizarOilLevel()">Atualizar Valor</button>
<?php else: ?>
    <p style="color:red;">Você pode visualizar esta prestação, mas não alterar o valor.</p>
<?php endif; ?>
<p><strong>Endereço MetaMask:</strong> <?= $enderecoDestino ?></p>



<script>
function atualizarOilLevel() {
    const oilLevel = document.getElementById("oil_level").value;
    const id = <?= json_encode($id) ?>; // Pegando ID da prestação

    fetch("atualizar_oil_level.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `oil_level=${encodeURIComponent(oilLevel)}&id=${encodeURIComponent(id)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            setTimeout(() => { window.location.reload(); }, 100); // Recarrega após 100ms
        }
    })
    .catch(error => console.error("Erro ao atualizar: ", error));
}
</script>






        <div class="qrcode">
            <h3>Escaneie para solicitar via WhatsApp:</h3>
            <img src="<?= $filePath ?>" alt="QR Code para WhatsApp">
        </div>

        <button onclick="enviarPagamento()">💰 Pagar via MetaMask/BNB</button>
        <button onclick="solicitarPagamentoWhatsApp()">📲 Solicitar Pagamento via WhatsApp</button>
    </div>
</body>
</html>
