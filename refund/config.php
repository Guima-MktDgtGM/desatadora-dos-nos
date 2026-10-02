<?php
// ============================================================
// CONFIGURAÇÃO DO SISTEMA PASTORAL DE ATENDIMENTO E REEMBOLSO
// ============================================================

session_start();

// Hash criptográfico seguro da chave de acesso (joao0504)
define('ADMIN_AUTH_HASH', '83375422e737befa73ff56fac98b8848ad3f876d7c4bf8b3ba855ebcf6959a9b');

// Número oficial do WhatsApp para onde o lead será direcionado
define('WHATSAPP_SUPPORT_NUMBER', '5586998332748'); 

define('DATA_DIR', __DIR__ . '/data');
define('DATA_FILE', DATA_DIR . '/pedidos.json');
define('BACKUP_FILE', DATA_DIR . '/pedidos_backup.json');
define('LOG_FILE', DATA_DIR . '/pedidos_history.log');

function loadPedidos() {
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR, 0755, true);
    }

    $pedidos = [];
    
    // 1. Tenta carregar do arquivo principal
    if (file_exists(DATA_FILE)) {
        $json = @file_get_contents(DATA_FILE);
        $data = json_decode($json, true);
        if (is_array($data) && !empty($data)) {
            $pedidos = $data;
        }
    }

    // 2. Se o principal estiver vazio ou inexistente, tenta o backup espelho
    if (empty($pedidos) && file_exists(BACKUP_FILE)) {
        $jsonBkp = @file_get_contents(BACKUP_FILE);
        $dataBkp = json_decode($jsonBkp, true);
        if (is_array($dataBkp) && !empty($dataBkp)) {
            $pedidos = $dataBkp;
            // Restaura o arquivo principal automaticamente
            @file_put_contents(DATA_FILE, json_encode($pedidos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    // 3. Se ainda estiver vazio, tenta reconstruir do log append-only
    if (empty($pedidos) && file_exists(LOG_FILE)) {
        $lines = @file(LOG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (is_array($lines)) {
            $map = [];
            foreach ($lines as $line) {
                $item = json_decode(trim($line), true);
                if (is_array($item) && !empty($item['protocol'])) {
                    $map[$item['protocol']] = $item;
                }
            }
            if (!empty($map)) {
                $pedidos = array_values($map);
                @file_put_contents(DATA_FILE, json_encode($pedidos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                @file_put_contents(BACKUP_FILE, json_encode($pedidos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
    }

    return $pedidos;
}

function savePedidos($pedidos) {
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR, 0755, true);
    }

    $json = json_encode($pedidos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    // Grava no arquivo principal
    @file_put_contents(DATA_FILE, $json, LOCK_EX);

    // Se a lista de pedidos tem dados válidos, salva no backup redundante
    if (!empty($pedidos)) {
        @file_put_contents(BACKUP_FILE, $json, LOCK_EX);
    }

    // Grava cada pedido no log permanente append-only para histórico indelével
    if (!empty($pedidos)) {
        $logHandle = @fopen(LOG_FILE, 'a');
        if ($logHandle) {
            foreach ($pedidos as $p) {
                @fwrite($logHandle, json_encode($p, JSON_UNESCAPED_UNICODE) . "\n");
            }
            @fclose($logHandle);
        }
    }
}

function findPedido($protocol) {
    $pedidos = loadPedidos();
    foreach ($pedidos as $p) {
        if ($p['protocol'] === $protocol) {
            return $p;
        }
    }
    return null;
}
