<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNOSTICO SEGURO ===\n";
$dataFile = __DIR__ . '/data/pedidos.json';
echo "Data file: $dataFile\n";
echo "Exists: " . (file_exists($dataFile) ? 'SIM' : 'NAO') . "\n";
if (file_exists($dataFile)) {
    echo "Tamanho: " . filesize($dataFile) . " bytes\n";
    echo "Conteudo:\n" . file_get_contents($dataFile) . "\n";
}

echo "\nArquivos em data/:\n";
$dataDir = __DIR__ . '/data';
if (is_dir($dataDir)) {
    print_r(scandir($dataDir));
}

echo "\nArquivos em refund/:\n";
print_r(scandir(__DIR__));
