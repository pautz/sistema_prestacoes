<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Níveis de Óleo</title>
    <style>
        .btn-inicio {
            background-color: #FF9800;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-family: Arial, sans-serif;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
            display: block;
            margin: 0 auto;
        }

        .btn-inicio:hover {
            background-color: #F57C00;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-container, .table-container {
            width: 90%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .highlight-red {
            background-color: red;
            color: white;
            font-weight: bold;
        }

        .highlight-blue {
            background-color: blue;
            color: white;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Adicionar Registro</h2>
        <button class="btn-inicio" onclick="location.href='../site/welcome.php'">Ínicio</button><br>
        
        <label for="cv">Selecionar Contrato (boat_id):</label>
        <select id="cv" required>
            <option value="" disabled selected>Selecione...</option>
        </select>

        <label for="id">Selecionar Prestação:</label>
        <select id="id" required>
            <option value="" disabled selected>Selecione um ID...</option>
        </select>
    <input type="number" id="nv-oleo" placeholder="Nível de Óleo" required>

        <input type="number" id="oil-level" placeholder="Valor da Prestação" required>
        <input type="date" id="next-change" required>
        <input type="number" step="0.01" id="next-change-value" placeholder="Valor de Troca" required>
        <input type="text" id="whatsapp-number" placeholder="Número WhatsApp do Pagador" required>
        <select id="paymentstatus">
            <option value="Não Pago" selected>Não Pago</option>
            <option value="Pago">Pago</option>
        </select>
        <button onclick="addOilLevel()">Adicionar</button>
    </div>

    <div class="table-container">
        <h2>Registros</h2>
        <table>
            <thead>
                <tr>
                    <th>Prestação</th>
                    <th>Contrato</th>
                    <th>Nível de Óleo</th>
                    <th>Valor da Prestação</th>
                    <th>Data da Prestação</th>
                    <th>Valor de Troca</th>
                    <th>WhatsApp</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="oil-levels-table"></tbody>
        </table>
    </div>

    <script>
        async function loadSelectOptions() {
            try {
                const response = await fetch('get_respostas.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error("Erro ao carregar dados do servidor.");
                }

                const respostas = await response.json();
                const cvSelect = document.getElementById('cv');
                cvSelect.innerHTML = '<option value="" disabled selected>Selecione...</option>';
                const uniqueCVs = [...new Set(respostas.map(resposta => resposta.cv))];

                uniqueCVs.forEach(cv => {
                    const cvOption = document.createElement('option');
                    cvOption.value = cv;
                    cvOption.textContent = cv;
                    cvSelect.appendChild(cvOption);
                });

                cvSelect.addEventListener('change', async function () {
                    const cvValue = this.value;
                    const idSelect = document.getElementById('id');

                    if (!cvValue) {
                        idSelect.innerHTML = '<option value="" disabled selected>Selecione um ID...</option>';
                        return;
                    }

                    const response = await fetch('get_ids_by_cv.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ cv: cvValue })
                    });

                    const ids = await response.json();
                    idSelect.innerHTML = '<option value="" disabled selected>Selecione um ID...</option>';
                    const uniqueIDs = [...new Set(ids)];

                    uniqueIDs.forEach(id => {
                        const idOption = document.createElement('option');
                        idOption.value = id;
                        idOption.textContent = id;
                        idSelect.appendChild(idOption);
                    });
                });
            } catch (error) {
                alert("Erro ao carregar opções: " + error.message);
                console.error(error);
            }
        }

        async function addOilLevel() {
    try {
        // Coleta os valores do formulário
        const boat_id = document.getElementById('id').value;
        const cv = document.getElementById('cv').value;
        const oilLevel = document.getElementById('oil-level').value;
        const nvOleo = document.getElementById('nv-oleo').value; // Novo campo adicionado
        const nextChange = document.getElementById('next-change').value;
        const nextChangeValue = document.getElementById('next-change-value').value;
        const whatsapp_number = document.getElementById('whatsapp-number').value;
        const paymentstatus = document.getElementById('paymentstatus').value;

        // Verifica se todos os campos foram preenchidos
        if (!boat_id || !cv || !oilLevel || !nvOleo || !nextChange || !nextChangeValue || !whatsapp_number || !paymentstatus) {
            alert("Por favor, preencha todos os campos.");
            return;
        }

        // Envia os dados ao backend
        const response = await fetch('add_oil_level.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                boat_id,
                cv,
                oilLevel,
                nvOleo, // Inclui nv_oleo no envio
                nextChange,
                nextChangeValue,
                whatsapp_number,
                paymentstatus,
            }),
        });

        // Verifica o status da resposta do backend
        if (!response.ok) {
            throw new Error("Erro ao adicionar registro.");
        }

        const result = await response.json();
        alert(result.message);

        // Atualiza os registros na tabela
        loadOilLevels();
    } catch (error) {
        console.error("Erro ao adicionar registro:", error);
        alert("Erro ao adicionar registro: " + error.message);
    }
}


 async function loadOilLevels() {
    try {
        // Faz a requisição ao backend para obter os registros
        const response = await fetch('get_oil_levels.php');
        if (!response.ok) {
            throw new Error("Erro ao carregar os registros.");
        }

        const levels = await response.json();
        const tableBody = document.getElementById('oil-levels-table');
        tableBody.innerHTML = ''; // Limpa os registros anteriores

        // Verifica se há registros
        if (!levels || levels.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = '<td colspan="9">Nenhum registro encontrado.</td>'; // Adapta para 9 colunas
            tableBody.appendChild(row);
            return;
        }

        // Preenche a tabela com os registros organizados
        const rows = levels.map(level => {
            const row = document.createElement('tr');

            const now = new Date();
            const nextChangeDate = new Date(level.next_change);

            // Remove estilos antigos e aplica novos estilos
            row.className = ""; // Limpa quaisquer classes antigas
            if (level.paymentstatus === "Pago") {
                row.classList.add('highlight-blue'); // Linha azul para status pago
            } else if (parseFloat(level.nv_oleo) >= parseFloat(level.next_change_value)) {
                row.classList.add('highlight-red'); // Linha vermelha para nível crítico
            } else if (nextChangeDate <= now && level.paymentstatus === "Não Pago") {
                row.classList.add('highlight-red'); // Linha vermelha para atrasos
            }

            // Preenche as células da linha com todas as informações necessárias
            row.innerHTML = `
                <td>${level.boat_id}</td>
                <td>${level.cv}</td>
                <td>
                    <input type="number" value="${level.nv_oleo}" 
                           data-field="nv_oleo" 
                           onchange="updateFieldAndColors(this, 'nv_oleo', '${level.boat_id}', '${level.next_change_value}')" />
                </td>
                <td>
                    <input type="number" step="0.01" value="${level.oil_level}" 
                           data-field="oil_level" 
                           onchange="updateField(this, 'oil_level', '${level.boat_id}')" />
                </td>
                <td>
                    <input type="date" value="${level.next_change}" 
                           data-field="next_change" 
                           onchange="updateFieldAndColors(this, 'next_change', '${level.boat_id}', '${level.next_change_value}')" />
                </td>
                <td>
                    <input type="number" step="0.01" value="${level.next_change_value}" 
                           data-field="next_change_value" 
                           onchange="updateField(this, 'next_change_value', '${level.boat_id}')" />
                </td>
                <td>
                    <input type="text" value="${level.whatsapp_number}" 
                           data-field="whatsapp_number" 
                           onchange="updateField(this, 'whatsapp_number', '${level.boat_id}')" />
                </td>
                <td>
                    <select data-field="paymentstatus" 
                            onchange="updateFieldAndColors(this, 'paymentstatus', '${level.boat_id}', '${level.next_change_value}')">
                        <option value="Não Pago" ${level.paymentstatus === "Não Pago" ? "selected" : ""}>Não Pago</option>
                        <option value="Pago" ${level.paymentstatus === "Pago" ? "selected" : ""}>Pago</option>
                    </select>
                </td>
                <td>
                    <button onclick="sendToWhatsApp('${level.cv}', '${level.boat_id}', '${level.oil_level}', '${level.next_change}', '${level.next_change_value}', '${level.whatsapp_number}')">
                        Enviar para WhatsApp
                    </button>
                    <button onclick="removeOilLevel('${level.boat_id}')">Remover</button>
                </td>
            `;
            return row;
        });

        // Ordena as linhas: vermelhas > azuis > brancas
        rows.sort((rowA, rowB) => {
            const isRedA = rowA.classList.contains('highlight-red');
            const isBlueA = rowA.classList.contains('highlight-blue');
            const isRedB = rowB.classList.contains('highlight-red');
            const isBlueB = rowB.classList.contains('highlight-blue');

            // Prioridade: vermelhas no topo, azuis logo abaixo, brancas no final
            if (isRedA && !isRedB) return -1;
            if (!isRedA && isRedB) return 1;
            if (isBlueA && !isBlueB) return -1;
            if (!isBlueA && isBlueB) return 1;
            return 0; // Mantém a ordem original entre brancas
        });

        // Reanexa as linhas ordenadas ao corpo da tabela
        rows.forEach(row => tableBody.appendChild(row));

    } catch (error) {
        console.error("Erro ao carregar registros:", error);
        alert("Erro ao carregar registros: " + error.message);
    }
}








     async function updateField(element, field, boat_id) {
    try {
        if (!element) {
            throw new Error("Elemento não encontrado.");
        }

        const newValue = element.value?.trim(); // Verifique se o valor existe
        if (!newValue) {
            alert("Valor inválido ou vazio.");
            return;
        }

        const oldValue = element.getAttribute('data-old-value') || "";
        if (newValue === oldValue) {
            console.log("Nenhuma alteração detectada.");
            return;
        }

        // Continua com a lógica de atualização no backend
        const response = await fetch('update_oil_level.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ boat_id, field, value: newValue }),
        });

        if (!response.ok) throw new Error("Erro ao atualizar no backend.");

        const result = await response.json();
        if (result.status === "success") {
            element.setAttribute('data-old-value', newValue);
            alert("Campo atualizado com sucesso!");
        } else {
            alert(`Erro ao atualizar: ${result.message}`);
        }
    } catch (error) {
        console.error("Erro ao atualizar campo:", error);
        alert("Erro ao atualizar campo: " + error.message);
    }
}


async function updateFieldAndColors(element, field, boat_id, nextChangeValue) {
    try {
        const newValue = element.value?.trim();
        if (!newValue) {
            alert("Valor inválido ou vazio.");
            return;
        }

        // Faz a requisição para o backend
        const response = await fetch('update_oil_level.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ boat_id, field, value: newValue }),
        });

        if (!response.ok) throw new Error("Erro ao atualizar no servidor.");

        const result = await response.json();
        if (result.status === "success") {
            const row = element.closest('tr'); // Identifica a linha correspondente
            const now = new Date();

            // Obtém os valores necessários da linha
            const nvOleoField = row.querySelector('[data-field="nv_oleo"]') || row.querySelector('input[type="number"]');
            const nextChangeField = row.querySelector('[data-field="next_change"]') || row.querySelector('input[type="date"]');
            const paymentStatusField = row.querySelector('select');

            const nvOleoValue = nvOleoField ? parseFloat(nvOleoField.value) : null;
            const nextChangeDate = nextChangeField ? new Date(nextChangeField.value) : null;
            const paymentStatusValue = paymentStatusField ? paymentStatusField.value : "Não Pago";

            // Remove classes anteriores
            row.classList.remove('highlight-red', 'highlight-blue');

            // Aplica a lógica de cores
            if (paymentStatusValue === "Pago") {
                row.classList.add('highlight-blue'); // Linha azul
            } else if (
                nvOleoValue >= parseFloat(nextChangeValue) || 
                (nextChangeDate && nextChangeDate <= now && paymentStatusValue === "Não Pago")
            ) {
                row.classList.add('highlight-red'); // Linha vermelha
            }

            // Atualiza o atributo "data-old-value"
            element.setAttribute('data-old-value', newValue);

            // Reposiciona a linha na tabela
            moveRowToCorrectPosition(row);

            alert("Campo atualizado com sucesso!");
        } else {
            alert(`Erro ao atualizar: ${result.message}`);
        }
    } catch (error) {
        console.error("Erro ao atualizar campo:", error);
        alert("Erro ao atualizar campo: " + error.message);
    }
}


function moveRowToCorrectPosition(row) {
    const tableBody = document.getElementById('oil-levels-table');
    const rows = Array.from(tableBody.querySelectorAll('tr'));

    // Remove a linha da tabela temporariamente
    tableBody.removeChild(row);

    // Reinsere a linha na posição correta
    const sortedRows = rows.filter(r => r !== row).concat(row).sort((rowA, rowB) => {
        const isRedA = rowA.classList.contains('highlight-red');
        const isBlueA = rowA.classList.contains('highlight-blue');
        const isRedB = rowB.classList.contains('highlight-red');
        const isBlueB = rowB.classList.contains('highlight-blue');

        // Prioridade: vermelhas, depois azuis, depois brancas
        if (isRedA && !isRedB) return -1;
        if (!isRedA && isRedB) return 1;
        if (isBlueA && !isBlueB) return -1;
        if (!isBlueA && isBlueB) return 1;
        return 0; // Mantém a ordem original entre brancas
    });

    // Reanexa as linhas ordenadas ao corpo da tabela
    sortedRows.forEach(row => tableBody.appendChild(row));
}






      async function removeOilLevel(boat_id) {
    console.log("Boat ID enviado para remoção:", boat_id); // Confirme que o boat_id correto está sendo enviado

    if (!confirm("Tem certeza de que deseja remover este registro?")) {
        return; // Cancela a exclusão caso o usuário desista
    }

    try {
        const response = await fetch('delete_oil_level.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ boat_id }), // Envia o boat_id ao backend
        });

        if (!response.ok) {
            throw new Error("Erro ao remover registro.");
        }

        const result = await response.json();
        alert(result.message);
        loadOilLevels(); // Atualiza a tabela após a exclusão
    } catch (error) {
        console.error("Erro ao remover registro:", error);
        alert("Erro ao remover registro: " + error.message);
    }
}


       function sendToWhatsApp(cv, boat_id, oilLevel, nextChange, nextChangeValue, whatsappNumber) {
    const message = `Contrato: ${cv}\nPrestação: ${boat_id}\nValor da Prestação: ${oilLevel}\nData da Prestação: ${nextChange}\nValor da Próxima Prestação: ${nextChangeValue}`;
    const url = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}

        // Carrega os selects e os registros ao carregar a página
        window.onload = function () {
            loadSelectOptions();
            loadOilLevels();
        };
    </script>
</body>
</html>
