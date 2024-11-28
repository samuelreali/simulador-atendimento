<?php

// Função para ler um arquivo e retornar os dados como array
function lerArquivo($arquivo) {
    return array_map('intval', file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}

// Ler os arquivos de entrada
$tc = lerArquivo('../data/tc.txt'); // Tempos de chegada
$ts = lerArquivo('../data/ts.txt'); // Tempos de serviço

// Simulação da fila
$tempo_atual = 0;
$fila = [];
$atendimentos = [];

foreach ($tc as $index => $tempo_chegada) {
    $tempo_servico = isset($ts[$index]) ? $ts[$index] : 0;

    if ($tempo_chegada > $tempo_atual) {
        $tempo_atual = $tempo_chegada;  // Ajusta o tempo atual se o cliente chegar depois
    }

    // O tempo de atendimento é o tempo atual + o tempo de serviço
    $tempo_atendimento = $tempo_atual + $tempo_servico;

    // Armazenando os tempos de chegada e atendimento
    $fila[] = $tempo_chegada;
    $atendimentos[] = $tempo_atendimento;

    // Atualizando o tempo atual com o tempo de atendimento
    $tempo_atual = $tempo_atendimento;
}

// Retorna os resultados como JSON para o frontend
echo json_encode(['fila' => $fila, 'atendimentos' => $atendimentos]);

?>
