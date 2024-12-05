<?php
function carregarDados($arquivo) {
    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_map('intval', $linhas);
}

$temposChegada = carregarDados('../data/tc.txt');
$temposServico = carregarDados('../data/ts.txt');

// Inicializando variáveis
$statusServidor = "Livre";
$fila = [];
$resultado = [];
$chamadoAtual = null;
$tempoServidorLivre = 0;
$tempoTotal = 0;

// Preenchendo os chamados com tempos acumulados de chegada
$chamados = [];
$tempoAtual = 0;
foreach ($temposChegada as $indice => $tempoChegada) {
    $tempoAtual += $tempoChegada; // Tempo acumulado de chegada
    $chamados[] = [
        'tempo_chegada' => $tempoAtual,
        'tempo_servico' => $temposServico[$indice],
        'id' => $indice
    ];
}

foreach ($chamados as $chamado) {
    // Tempo em que o chamado começa a ser atendido:
    $tempoInicio = max($chamado['tempo_chegada'], $tempoServidorLivre);
    
    // Calcula o tempo de término para este chamado
    $tempoFinal = $tempoInicio + $chamado['tempo_servico'];
    
    // Atualiza o tempo em que o servidor estará livre
    $tempoServidorLivre = $tempoFinal;

    // O tempo total é o maior tempo de término de todos os chamados
    $tempoTotal = max($tempoTotal, $tempoFinal);
}

// Loop principal usando range de tempo
foreach (range(1, $tempoTotal) as $tempoSimulador) {
    // Verificar novos chamados e adicioná-los à fila
    foreach ($chamados as $indice => $chamado) {
        if ($chamado['tempo_chegada'] == $tempoSimulador) {
            $fila[] = $chamado; // Adicionar à fila
            unset($chamados[$indice]); // Remover da lista de chamados
        }
    }

    // Atualizar status do servidor
    if ($chamadoAtual) {
        if ($chamadoAtual['tempo_final'] == $tempoSimulador) {
            // Registrar estado "Livre" quando o chamado atual é concluído
            $resultado[] = [
                'tempo_simulador' => $tempoSimulador,
                'status_servidor' => "Chamado concluído!",
                'fila' => implode(", ", array_map(function ($item) {
                    $itemId = $item['id'] + 1;
                    return "Chamado #{$itemId}";
                }, $fila)),
            ];
            $chamadoAtual = null; // Concluir chamado atual
            $statusServidor = "Livre";
        }
    }

    // Processar próximo chamado na fila
    if (!$chamadoAtual && !empty($fila)) {
        $chamadoAtual = array_shift($fila); // Retirar o próximo da fila
        $chamadoAtual['tempo_inicio'] = max($tempoSimulador, $chamadoAtual['tempo_chegada']);
        $chamadoAtual['tempo_final'] = $chamadoAtual['tempo_inicio'] + $chamadoAtual['tempo_servico'];
        $chamadoId = $chamadoAtual['id'] + 1;
        $statusServidor = "Ocupado - Chamado #{$chamadoId}";
    }

    // Registrar estado atual
    $resultado[] = [
        'tempo_simulador' => $tempoSimulador,
        'status_servidor' => $statusServidor,
        'fila' => implode(", ", array_map(function ($item) {
            $itemId = $item['id'] + 1;
            return "Chamado #{$itemId}";
        }, $fila)),
    ];
}

// Gerar tabela HTML
echo "<tbody>";
foreach ($resultado as $linha) {
    echo "<tr>";
    echo "<td>{$linha['tempo_simulador']}</td>";
    echo "<td>{$linha['status_servidor']}</td>";
    echo "<td>" . ($linha['fila'] ?: "Vazia") . "</td>";
    echo "</tr>";
}
echo "</tbody>";
?>
