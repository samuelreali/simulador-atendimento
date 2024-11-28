$(document).ready(function () {
    $("#simular").click(function () {
        /* alert() */
        $.getJSON("api/simulador.php", function (data) {
            console.log(data)
            let html = "<h3>Resultados da Simulação</h3><ul class='list-group'>";
            data.fila.forEach((item, index) => {
                html += `
                    <li class="list-group-item">
                        <strong>Chegada:</strong> ${item} | 
                        <strong>Atendimento Concluído:</strong> ${data.atendimentos[index]}
                    </li>
                `;
            });
            html += "</ul>";
            $("#resultados").html(html);
        });
    });
});
