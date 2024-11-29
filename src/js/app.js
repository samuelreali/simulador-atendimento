$(document).ready(function () {
    $("#simular").click(function () {
        $.getJSON("api/simulador.php", function (data) {
            console.log(data.espera);

            // Criar a tabela HTML com as colunas desejadas
            let html = "<h3>Resultados da Simulação</h3><table class='table table-bordered'>";
            html += `
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Chegada</th>
                        <th>Atendimento Concluído</th>
                        <th>TC (bruto)</th>
                        <th>TS (bruto)</th>
                    </tr>
                </thead>
                <tbody>
            `;

            // Adicionar uma linha por cliente na tabela
            data.fila.forEach((item, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item}</td>
                        <td>${data.atendimentos[index]}</td>
                        <td>${data.chegada_bruta[index]}</td> <!-- Exibe chegada bruta (intervalo) -->
                        <td>${data.servico_bruto[index]}</td> <!-- Exibe tempo de serviço bruto -->
                    </tr>
                `;
            });

            html += "</tbody></table>";

            // Inserir o HTML gerado no elemento com o id "resultados"
            $("#resultados").html(html);
        });
    });
});
