<?php
function carregarDados($arquivo) {
    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_map('intval', array_slice($linhas, 1)); // Ignorar a linha de cabeçalho
}

$temposChegada = carregarDados('../data/tc.txt');
$temposServico = carregarDados('../data/ts.txt');

$tempoSimulador = 0;
$statusServidor = "Livre";
$fila = [];
$resultado = [];
$chamadoAtual = null;

// Preenchendo as chamadas com tempos acumulados de chegada
$chamadas = [];
$tempoAtual = 4; // Primeiro chamado chega no tempo 4
foreach ($temposChegada as $indice => $tempoRelativo) {
    $chamadas[] = [
        'tempo_chegada' => $tempoAtual,
        'tempo_servico' => $temposServico[$indice],
    ];
    $tempoAtual += $tempoRelativo; // Próxima chegada acumulada
}

// Simulação
while (!empty($chamadas) || $chamadoAtual || !empty($fila)) {
    // Verificar novos chamados e adicioná-los à fila
    foreach ($chamadas as $indice => $chamado) {
        if ($chamado['tempo_chegada'] == $tempoSimulador) {
            $fila[] = $chamado; // Adicionar à fila
            unset($chamadas[$indice]); // Remover da lista de chamados
        }
    }

    // Atualizar status do servidor
    if ($chamadoAtual) {
        if ($chamadoAtual['tempo_final'] == $tempoSimulador) {
            $chamadoAtual = null; // Concluir chamado atual
            $statusServidor = "Livre";
        }
    }

    // Processar próximo chamado na fila
    if (!$chamadoAtual && !empty($fila)) {
        $chamadoAtual = array_shift($fila); // Retirar o próximo da fila
        $chamadoAtual['tempo_inicio'] = $tempoSimulador;
        $chamadoAtual['tempo_final'] = $tempoSimulador + $chamadoAtual['tempo_servico'];
        $statusServidor = "Ocupado";
    }

    // Registrar estado atual
    $resultado[] = [
        'tempo_simulador' => $tempoSimulador,
        'status_servidor' => $statusServidor,
        'fila' => implode(", ", array_map(function ($item) {
            return "T{$item['tempo_chegada']}";
        }, $fila)),
    ];

    $tempoSimulador++;
}

// Gerar tabela HTML
echo "<table>";
echo "<tr><th>Tempo do Simulador</th><th>Status do Servidor</th><th>Fila</th></tr>";
foreach ($resultado as $linha) {
    echo "<tr>";
    echo "<td>{$linha['tempo_simulador']}</td>";
    echo "<td>{$linha['status_servidor']}</td>";
    echo "<td>" . ($linha['fila'] ?: "Vazia") . "</td>";
    echo "</tr>";
}
echo "</table>";
