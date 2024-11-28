$(document).ready(function () {
    $("#simular").click(function () {
        $.getJSON("api/simulador.php", function (data) {
            console.log(data);

            // Criar a tabela HTML com as colunas desejadas
            let html = "<h3>Resultados da Simulação</h3><table class='table table-bordered'>";
            html += `
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Chegada</th>
                        <th>Atendimento Concluído</th>
                    </tr>
                </thead>
                <tbody>
            `;

            // Adicionar uma linha por cliente na tabela
            data.fila.forEach((item, index) => {
                html += `
                    <tr>
                        <td>#${index + 1}</td>
                        <td>${item}</td>
                        <td>${data.atendimentos[index]}</td>
                    </tr>
                `;
            });

            html += "</tbody></table>";

            // Inserir o HTML gerado no elemento com o id "resultados"
            $("#resultados").html(html);
        });
    });
});
