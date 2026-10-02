<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNOSTICO DO SERVIDOR ===\n";
echo "Dir atual: " . __DIR__ . "\n";
echo "Doc root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";

// Checar se existem logs em pastas superiores
$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
$parent = dirname($docRoot);
echo "Parent do DocRoot: $parent\n";

if (is_dir($parent)) {
    $files = scandir($parent);
    echo "Arquivos no parent:\n";
    print_r($files);
}

// Checar pasta de logs do Hostinger
$possibleLogs = [
    $parent . '/logs',
    $parent . '/.logs',
    $docRoot . '/../logs',
    '/var/log/apache2',
    '/var/log/httpd'
];

foreach ($possibleLogs as $p) {
    if (is_dir($p)) {
        echo "Pasta de log encontrada: $p\n";
        print_r(scandir($p));
    }
}

// Checar git status ou git reflog no servidor
echo "\n=== GIT LOG NO SERVIDOR ===\n";
@exec('git status 2>&1', $gitStatus);
echo implode("\n", (array)$gitStatus) . "\n";

@exec('git log -n 3 --oneline 2>&1', $gitLog);
echo implode("\n", (array)$gitLog) . "\n";

@exec('git reflog -n 5 2>&1', $gitReflog);
echo implode("\n", (array)$gitReflog) . "\n";

@exec('git stash list 2>&1', $gitStash);
echo implode("\n", (array)$gitStash) . "\n";

