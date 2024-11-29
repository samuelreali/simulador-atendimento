<?php

// Função para ler um arquivo e retornar os dados como array
function lerArquivo($arquivo) {
    return array_map('intval', file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}

// Função para converter minutos em formato HH:MM
function minutosParaHoras($minutos) {
    $horas = floor($minutos / 60); // Calcula as horas
    $minutosRestantes = $minutos % 60; // Calcula os minutos restantes
    return sprintf("%02d:%02d", $horas, $minutosRestantes); // Retorna no formato HH:MM
}

// Definindo o início do tempo como 09:00 (540 minutos após a meia-noite)
$inicio_tempo = 540; // 09:00 em minutos

// Ler os arquivos de entrada
$tc = lerArquivo('../data/tc.txt'); // Intervalos de tempo entre as chegadas (em minutos)
$ts = lerArquivo('../data/ts.txt'); // Tempos de serviço (em minutos)

// Inicializa variáveis
$tempo_atual = $inicio_tempo; // Tempo atual no sistema (inicia em 540 minutos = 09:00)
$fila = [];        // Armazenará os tempos de chegada dos clientes (formatados)
$atendimentos = []; // Armazenará os tempos de atendimento concluído (formatados)
$espera = [];       // Armazenará o tempo de espera de cada cliente
$chegada_bruta = []; // Armazenará os tempos de chegada brutos (intervalos)
$servico_bruto = []; // Armazenará os tempos de serviço brutos

foreach ($tc as $index => $intervalo_chegada) {
    // A chegada de cada cliente é o tempo acumulado dos intervalos
    $tempo_chegada = $index == 0 ? $tempo_atual + $tc[$index] : $tempo_chegada + $intervalo_chegada;
    $tempo_chegada_att = $tempo_chegada;
    $tempo_servico = isset($ts[$index]) ? $ts[$index] : 0;

    // O cliente começa o atendimento após o tempo de chegada
    $inicio_atendimento = $tempo_chegada;

    // O tempo de atendimento é o tempo de chegada + o tempo de serviço
    $tempo_atendimento = $inicio_atendimento + $tempo_servico;

    // O tempo de espera é a diferença entre o tempo de atendimento e o tempo de chegada
    $tempo_espera = $inicio_atendimento - $tempo_chegada;

    // Armazenando os tempos formatados e brutos
    $fila[] = minutosParaHoras($tempo_chegada); // Converte para HH:MM
    $atendimentos[] = minutosParaHoras($tempo_atendimento); // Converte para HH:MM
    $espera[] = $tempo_espera;
    $chegada_bruta[] = $intervalo_chegada; // Dados brutos de chegada
    $servico_bruto[] = $tempo_servico; // Dados brutos de tempo de serviço

    // Atualizando o tempo atual com o tempo de atendimento (próximo cliente só começa após a conclusão do anterior)
    /* $tempo_atual = $tempo_atendimento; */
}

// Retorna os resultados como JSON para o frontend
echo json_encode([
    'fila' => $fila, 
    'atendimentos' => $atendimentos, 
    'espera' => $espera,
    'chegada_bruta' => $chegada_bruta,
    'servico_bruto' => $servico_bruto
]);

?>
