$(document).ready(function () {
    $("#iniciar-simulacao").click(function () {
        $.get("api/simulador.php", function (data) {
            $("#resultado").html(data);
        }).fail(function () {
            alert("Erro ao processar a simulação.");
        });
    });
});
