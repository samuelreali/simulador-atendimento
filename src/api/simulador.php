<?php

// Função para ler um arquivo e retornar os dados como array
function lerArquivo($arquivo) {
    return array_map('intval', file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}

// Ler os arquivos de entrada
$tc = lerArquivo('../data/tc.txt'); // Intervalos de tempo entre as chegadas
$ts = lerArquivo('../data/ts.txt'); // Tempos de serviço

// Inicializa variáveis
$tempo_atual = 0; // Tempo atual no sistema (o tempo inicial será 0)
$fila = [];        // Armazenará os tempos de chegada dos clientes
$atendimentos = []; // Armazenará os tempos de atendimento
$espera = [];       // Armazenará o tempo de espera de cada cliente

foreach ($tc as $index => $intervalo_chegada) {
    // O primeiro cliente chega no tempo 0, ou podemos usar outro valor inicial
    $tempo_chegada = ($index == 0) ? $intervalo_chegada : $tempo_chegada + $intervalo_chegada;
    $tempo_servico = isset($ts[$index]) ? $ts[$index] : 0;

    // Se o cliente chega após o tempo atual, o sistema "espera" até a chegada
    if ($tempo_chegada > $tempo_atual) {
        $tempo_atual = $tempo_chegada;
    }

    // O cliente começa o atendimento após o tempo atual
    $inicio_atendimento = $tempo_atual;

    // O tempo de atendimento é o tempo atual + o tempo de serviço
    $tempo_atendimento = $inicio_atendimento + $tempo_servico;

    // O tempo de espera é a diferença entre o tempo de atendimento e o tempo de chegada
    $tempo_espera = $inicio_atendimento - $tempo_chegada;

    // Armazenando os tempos
    $fila[] = $tempo_chegada;
    $atendimentos[] = $tempo_atendimento;
    $espera[] = $tempo_espera;

    // Atualizando o tempo atual com o tempo de atendimento (próximo cliente só começa após a conclusão do anterior)
    $tempo_atual = $tempo_atendimento;
}

// Retorna os resultados como JSON para o frontend
echo json_encode([
    'fila' => $fila, 
    'atendimentos' => $atendimentos, 
    'espera' => $espera
]);

?>
