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

Documentação do Sistema de Gerenciamento de Níveis de Óleo e Solicitação de Pagamentos
1. Introdução
Este sistema foi desenvolvido para monitorar registros de níveis de óleo e gerenciar solicitações e confirmações de pagamento, garantindo que transações via MetaMask sejam verificadas na blockchain antes de serem registradas no banco de dados. Ele permite funcionalidades como registro, edição, exclusão e visualização de dados, além de integração com MetaMask para envio de pagamentos e comunicação via WhatsApp.

Tecnologias Utilizadas
Backend: PHP e MySQL

Frontend: JavaScript, HTML, CSS

Autenticação: Sessões PHP

Solicitações e Confirmação de Pagamento: MetaMask (Binance Smart Chain)

Monitoramento: Gerenciamento de nível de óleo

2. Estrutura do Projeto
O projeto está organizado da seguinte maneira:

/site/
│── db.php
│── index.php
│── add_oil_level.php
│── delete_oil_level.php
│── get_oil_levels.php
│── get_respostas.php
│── get_ids_by_cv.php
│── registrar_edicao.php
│── update_boat_id.php
│── update_oil_level.php
│── page.php  → (contém a função valida_transacao)
│── meusequipamentos.php
│── msg.php
│── styles.css
│── scripts.js
3. Banco de Dados
O sistema utiliza um banco de dados MySQL com tabelas organizadas para garantir a integridade dos dados e otimizar buscas.

3.1. Tabelas
oil_levels – Registra informações de nível de óleo e status da solicitação de pagamento.

respostas – Contém informações sobre prestações e contratos.

paymentsprestacao – Armazena pagamentos confirmados.

3.2. Estrutura da tabela paymentsprestacao
sql
CREATE TABLE paymentsprestacao (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hashTransacao VARCHAR(66) NOT NULL UNIQUE,
    cv VARCHAR(50) NOT NULL,
    eq_user VARCHAR(50) NOT NULL,
    payment TINYINT(1) DEFAULT 0, 
    valorpayment DECIMAL(10,6),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
4. Backend
Os arquivos PHP manipulam registros e garantem que apenas usuários autenticados possam interagir com o sistema.

4.1. db.php
Responsável por conectar ao banco de dados.

4.2. add_oil_level.php
Recebe dados via JSON e insere registros na tabela oil_levels.

4.3. delete_oil_level.php
Exclui um registro com base no boat_id.

4.4. get_oil_levels.php
Obtém os registros de nível de óleo do usuário autenticado.

4.5. get_respostas.php
Retorna IDs de prestações disponíveis para um usuário.

4.6. get_ids_by_cv.php
Filtra IDs das prestações vinculadas a um contrato (cv).

4.7. registrar_edicao.php
Guarda edições feitas nos registros para auditoria.

4.8. update_boat_id.php
Atualiza boat_id em oil_levels.

4.9. update_oil_level.php
Atualiza qualquer campo do registro e registra logs de edição.

4.10. valida_transacao (dentro de page.php)
Consulta automática à blockchain para verificar se a transação foi confirmada.

Verifica receipt.status para garantir que a transação foi finalizada antes de registrar.

Registra hash no banco de dados e altera paymentstatus após confirmação.

5. Frontend
Os arquivos responsáveis pela interface do usuário e interação com o sistema.

5.1. index.php
Formulário para adicionar registros e tabela para visualizar os existentes.

Exibição do status de pagamento atualizado automaticamente após confirmação na blockchain.

5.2. scripts.js
Carrega selects dinamicamente.

Adiciona, edita e remove registros.

Realiza pagamentos via MetaMask e verifica transações confirmadas.

5.3. styles.css
Define estilos visuais para melhor apresentação e responsividade.

6. Segurança
O sistema implementa boas práticas de segurança para proteger dados e evitar ataques:

Sessões protegidas – Apenas usuários autenticados podem interagir com registros.

Prepared Statements – Evita SQL Injection.

Validação de entrada – Previne valores incorretos.

Registro de edições – Mantém um histórico de alterações no banco de dados.

7. Pagamento via MetaMask e Confirmação na Blockchain
O sistema não apenas solicita pagamentos via MetaMask, mas também confirma a transação na blockchain antes de registrar no banco de dados.

7.1. Como Funciona
O usuário inicia um pagamento via MetaMask.

A transação é enviada para a Binance Smart Chain.

O sistema verifica a hash da transação na blockchain usando validarTransacao().

Se confirmada, o banco de dados registra o pagamento e atualiza o status.

O usuário recebe a mensagem de confirmação.

7.2. Validação da Transação na Blockchain
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
7.3. Registro da Transação Após Confirmação
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
}
