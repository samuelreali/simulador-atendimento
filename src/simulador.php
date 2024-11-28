<?php
// Função para carregar dados de um arquivo
function carregarDados($arquivo) {
    $dados = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    array_shift($dados); // Remove o cabeçalho "x"
    return array_map('intval', $dados);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $temposServicoArquivo = 'ts.txt';
    $temposChegadaArquivo = 'tc.txt';

    if (file_exists($temposServicoArquivo) && file_exists($temposChegadaArquivo)) {
        $temposDeServico = carregarDados($temposServicoArquivo);
        $temposDeChegada = carregarDados($temposChegadaArquivo);
        echo json_encode([
            'temposServico' => $temposDeServico,
            'temposChegada' => $temposDeChegada
        ]);
    } else {
        echo json_encode(['error' => 'Arquivo não encontrado.']);
    }
}
?>
