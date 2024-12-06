<?php
function carregarDados($arquivo) {
    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_map('intval', $linhas);
}

$temposChegada = carregarDados('../data/tc.txt');
$temposServico = carregarDados('../data/ts.txt');

// Inicializando variáveis
$statusServidores = ["Livre", "Livre"];
$fila = [];
$resultado = [];
$servidores = [null, null]; // Representa os dois servidores
$tempoServidoresLivres = [0, 0]; // Tempo em que cada servidor estará livre
$mensagensConcluidos = [null, null]; // Mensagens temporárias para servidores
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

$tempoEspera = 0;
foreach ($chamados as $chamado) {
    $tempoInicio = max($chamado['tempo_chegada'], min($tempoServidoresLivres));
    $tempoFinal = $tempoInicio + $chamado['tempo_servico'];
    $servidorLivre = array_search(min($tempoServidoresLivres), $tempoServidoresLivres);
    $tempoServidoresLivres[$servidorLivre] = $tempoFinal;
    $tempoTotal = max($tempoTotal, $tempoFinal);
    $tempoEspera += $tempoInicio - $chamado['tempo_chegada'];
}

$mediaTempoEspera = $tempoEspera / count($chamados);
$somaFila = 0;

foreach (range(1, $tempoTotal) as $tempoSimulador) {
    foreach ($chamados as $indice => $chamado) {
        if ($chamado['tempo_chegada'] == $tempoSimulador) {
            $fila[] = $chamado;
            unset($chamados[$indice]);
        }
    }

    $somaFila += count($fila) > 5 ? 1 : 0;

    foreach ($servidores as $index => &$servidor) {
        // Exibe mensagem de "Chamado concluído!" se aplicável
        /* if ($mensagensConcluidos[$index]) {
            $resultado[] = [
                'tempo_simulador' => $tempoSimulador,
                'status_servidor' => $mensagensConcluidos[$index],
                'fila' => implode(", ", array_map(fn($item) => "Chamado #".($item['id']+1), $fila)),
            ];
            $mensagensConcluidos[$index] = null;
        } */

        // Finaliza chamado se o tempo for igual ao término
        if ($servidor && $servidor['tempo_final'] == $tempoSimulador) {
            $mensagensConcluidos[$index] = "Servidor #".($index+1).": Chamado concluído!";
            $servidor = null;
            $statusServidores[$index] = "Livre";
        }

        // Processa novo chamado se o servidor estiver livre
        if (!$servidor && !empty($fila)) {
            $servidor = array_shift($fila);
            $servidor['tempo_inicio'] = max($tempoSimulador, $servidor['tempo_chegada']);
            $servidor['tempo_final'] = $servidor['tempo_inicio'] + $servidor['tempo_servico'];
            $statusServidores[$index] = "Ocupado - Chamado #".($servidor['id']+1);
        }
    }

    $resultado[] = [
        'tempo_simulador' => $tempoSimulador,
        'status_servidores' => implode(", ", $statusServidores),
        'fila' => implode(", ", array_map(fn($item) => "Chamado #".($item['id']+1), $fila)),
    ];
}

$mediaFila = round(($somaFila / $tempoTotal) * 100)."%";

echo "<tbody>";
foreach ($resultado as $linha) {
    echo "<tr>";
    echo "<td>{$linha['tempo_simulador']}</td>";
    echo  "<td>{$linha['status_servidores']}</td>";
    echo "<td style='max-width: 250px'>" . ($linha['fila'] ?: "Vazia") . "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "<span id='tempo-total'>{$tempoTotal}</span>";
echo "<span id='media-espera'>{$mediaTempoEspera}</span>";
echo "<span id='chamado-fila'>{$mediaFila}</span>";
?>
