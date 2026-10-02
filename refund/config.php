<?php
// ============================================================
// CONFIGURAÇÃO DO SISTEMA PASTORAL DE ATENDIMENTO E REEMBOLSO
// ============================================================

session_start();

// Hash criptográfico seguro da chave de acesso (joao0504)
define('ADMIN_AUTH_HASH', '83375422e737befa73ff56fac98b8848ad3f876d7c4bf8b3ba855ebcf6959a9b');

// Número oficial do WhatsApp para onde o lead será direcionado
define('WHATSAPP_SUPPORT_NUMBER', '5586998332748'); 

define('DATA_FILE', __DIR__ . '/data/pedidos.json');

function loadPedidos() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $json = file_get_contents(DATA_FILE);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function savePedidos($pedidos) {
    file_put_contents(DATA_FILE, json_encode($pedidos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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
