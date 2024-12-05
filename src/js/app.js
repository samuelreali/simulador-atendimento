$(document).ready(function () {
    $("#iniciar-simulacao").click(function () {
        $.get("api/simulador.php", function (data) {
            // Transformar o retorno em HTML processável
            const tabela = $("<div>").html(data).find("tbody tr"); // Obter linhas do corpo da tabela
            let index = 0;

            // Função para inserir linhas na tabela com intervalo de 1 segundo
            const intervalo = setInterval(function () {
                if (index < tabela.length) {
                    $("#tabela-simulacao tbody").append(tabela[index]); // Adicionar linha ao corpo da tabela
                    index++;
                } else {
                    clearInterval(intervalo); // Interromper quando todas as linhas forem exibidas
                }
            }, 1000); // 1 segundo
        }).fail(function () {
            alert("Erro ao processar a simulação.");
        });
    });
});