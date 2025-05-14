# 💳 Sistema de Pagamentos na Blockchain
Este repositório contém um sistema de pagamentos descentralizado, permitindo transações rápidas e seguras utilizando blockchain, com integração ao MetaMask e WhatsApp.

🚀 Funcionalidades
Pagamentos via ID da Carteira: Em vez de memorizar longos endereços de carteira, utilize um ID único vinculado à carteira do destinatário.

Integração com MetaMask: Conecte sua carteira digital para autorizar pagamentos instantaneamente.

Envio de Pagamentos pelo WhatsApp: Gere um QR Code para compartilhar e confirmar transações.

Registro na Blockchain: Cada pagamento é registrado, garantindo segurança, transparência e rastreabilidade.

🛠 Como Usar
Conecte sua carteira MetaMask ao sistema.

Insira o ID da carteira do destinatário, evitando a necessidade de copiar endereços complexos.

Confirme a transação e aguarde a validação na blockchain.

Compartilhe o pagamento via WhatsApp para comunicação rápida.

Este sistema oferece uma solução moderna e eficiente para pagamentos digitais, tornando as transações mais simples, seguras e acessíveis.

instale https://github.com/t0k4rt/phpqrcode/tree/master em site
<br>
![image](https://github.com/user-attachments/assets/7e9ae3f7-0562-42c1-adb5-0b9bfac30b56)
<br>
![image](https://github.com/user-attachments/assets/95cb9a18-50b6-4193-b002-4d9d105d014a)
<br>
![image](https://github.com/user-attachments/assets/08a221c8-11b5-47d1-be18-8131d7ea6b65)
<br>
![image](https://github.com/user-attachments/assets/10458bc7-3436-4dfe-a2da-1d6ada5a235c)
<br>
![image](https://github.com/user-attachments/assets/7709bc31-d72c-4337-ab64-ba702937d570)
<br>
![image](https://github.com/user-attachments/assets/d305976c-3954-46e3-99de-1d3b715d22a2)

# Sistema de Gerenciamento de Níveis de Óleo e Solicitação de Pagamentos

## 📌 Introdução
Este sistema permite **monitorar registros de níveis de óleo** e **solicitar pagamentos via MetaMask**, garantindo que transações sejam verificadas na **blockchain** antes de serem registradas no banco de dados.

## 🚀 Tecnologias Utilizadas
- **Backend**: PHP e MySQL  
- **Frontend**: JavaScript, HTML, CSS  
- **Autenticação**: Sessões PHP  
- **Solicitações e Confirmação de Pagamento**: MetaMask (Binance Smart Chain)  
- **Monitoramento**: Gerenciamento de nível de óleo  

# 📌 Sistema de Gerenciamento de Níveis de Óleo e Solicitação de Pagamentos  

## ✨ Introdução  
Este sistema permite **monitorar registros de níveis de óleo** e **solicitar pagamentos via MetaMask**, garantindo que transações sejam verificadas na **blockchain** antes de serem registradas no banco de dados.  

---

## 🚀 Tecnologias Utilizadas  
- **Backend**: PHP e MySQL  
- **Frontend**: JavaScript, HTML, CSS  
- **Autenticação**: Sessões PHP  
- **Solicitações e Confirmação de Pagamento**: MetaMask (Binance Smart Chain)  
- **Monitoramento**: Gerenciamento de nível de óleo  

---

## 📂 Estrutura do Projeto  
/site/ │── db.php │── index.php │── add_oil_level.php │── delete_oil_level.php │── get_oil_levels.php │── get_respostas.php │── get_ids_by_cv.php │── registrar_edicao.php │── update_boat_id.php │── update_oil_level.php │── page.php → (contém a função valida_transacao) │── meusequipamentos.php │── msg.php │── styles.css │── scripts.js


---

## 🛢 Banco de Dados  
### 🗂 Estrutura da tabela `paymentsprestacao`  
```sql
CREATE TABLE paymentsprestacao (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hashTransacao VARCHAR(66) NOT NULL UNIQUE,
    cv VARCHAR(50) NOT NULL,
    eq_user VARCHAR(50) NOT NULL,
    payment TINYINT(1) DEFAULT 0, 
    valorpayment DECIMAL(10,6),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
🔧 Backend
✨ valida_transacao (dentro de page.php)
Consulta a blockchain para verificar se a transação foi confirmada.

Verifica receipt.status para garantir que a transação foi finalizada antes de registrar.

Registra a hash no banco de dados e altera paymentstatus após confirmação.

🎨 Frontend
Solicitação de pagamento via MetaMask

Consulta automática à blockchain

Geração de QR Code para WhatsApp

🔐 Segurança
Sessões protegidas

Prepared Statements contra SQL Injection

Registro de logs de transações

💰 Pagamento via MetaMask e Confirmação na Blockchain
✅ Fluxo do processo
O usuário inicia um pagamento via MetaMask.

A transação é enviada para a Binance Smart Chain.

O sistema verifica a hash da transação na blockchain usando validarTransacao().

Se confirmada, o banco de dados registra o pagamento e atualiza o status.

🔎 Validação da Transação na Blockchain
javascript
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
📝 Registro da Transação Após Confirmação
php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hashTransacao'])) {
    $hashTransacao = $_POST['hashTransacao'];
    $payment = 1; // Confirmado
    $valorPayment = $dadosCertificado['oil_level'] ?? 0;

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

    $queryInsert = "INSERT INTO paymentsprestacao (id, hashTransacao, cv, eq_user, payment, valorpayment) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = $cx->prepare($queryInsert);
    $stmtInsert->bind_param("isssid", $id, $hashTransacao, $cv, $usuario, $payment, $valorPayment);

    if ($stmtInsert->execute()) {
        echo "Pagamento confirmado e registrado!";
    } else {
        echo "Erro ao registrar pagamento.";
    }
    $stmtInsert->close();
