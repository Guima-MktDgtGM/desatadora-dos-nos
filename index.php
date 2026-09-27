<?php
// ============================================================
//  CLOAKER BLINDADO — Novena de Nossa Senhora Desatadora dos Nós
//  Bloqueia bots, spiders e curiosos. Serve White TSL ou VSL.
// ============================================================

define('SALES_PAGE',  'vendas.html');   // Página VSL (Black Page)
define('CLEAN_PAGE',  'clean.html');    // White TSL de alta conversão
define('SECRET_BYPASS', 'gl2026');     // ?bypass=gl2026 libera sempre

// --- 1. REGRA SUPREMA: BYPASS MANUAL DA EQUIPE E TESTE DE EVENTOS META ---
$is_fb_test = isset($_GET['test_event_code']) || isset($_GET['fb_test_events']);
if ((isset($_GET['bypass']) && $_GET['bypass'] === SECRET_BYPASS) || $is_fb_test) {
    setcookie('_gl_ok', '1', time() + 86400 * 7, '/');
    serve_sales();
    exit;
}

// --- 2. VERIFICAÇÃO RIGOROSA DE BOTS / CRAWLERS / REVISORES ---
$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
$bot_agents = [
    'facebookexternalhit', 'facebot', 'facebookplatform', 'meta-externalagent',
    'googlebot', 'adsbot-google', 'mediapartners-google', 'apis-google', 'feedfetcher',
    'google-inspectiontool', 'storebot-google', 'google-adwords',
    'bingbot', 'bingpreview', 'msnbot', 'slurp', 'duckduckbot', 'baiduspider',
    'yandexbot', 'applebot', 'semrushbot', 'ahrefsbot', 'mj12bot', 'dotbot',
    'petalbot', 'screaming frog', 'rogerbot', 'exabot', 'ia_archiver',
    'archive.org_bot', 'wget', 'curl', 'python', 'requests', 'urllib',
    'go-http-client', 'java/', 'libwww', 'scrapy', 'httpclient', 'guzzle',
    'okhttp', 'apache', 'adbeat', 'brand-checker', 'pingdom', 'uptimerobot',
    'headlesschrome', 'phantomjs', 'selenium', 'webdriver', 'puppeteer',
    'lighthouse', 'crawler', 'spider'
];

foreach ($bot_agents as $b) {
    if (strpos($ua, $b) !== false) {
        serve_clean();
        exit;
    }
}

// UA vazio ou curto demais = automação
if (empty($ua) || strlen($ua) < 20) {
    serve_clean();
    exit;
}

// --- 3. COOKIE ATIVO (Usuário já validado anteriormente) ---
if (!empty($_COOKIE['_gl_ok'])) {
    serve_sales();
    exit;
}

// --- 4. FILTRO: SÓ CLIQUE REAL DE ANÚNCIO ---
// O referer NÃO vale como prova de origem: a Biblioteca de Anúncios, os grupos e
// qualquer link partilhado no Facebook chegam com referer facebook.com e abririam
// a VSL para quem não clicou em anúncio nenhum. Só passa quem traz ID de clique.
$is_meta_click   = !empty($_GET['fbclid']) && strlen($_GET['fbclid']) > 15;
$is_tiktok_click = !empty($_GET['ttclid']) && $_GET['ttclid'] !== '__CLICKID__' && strlen($_GET['ttclid']) > 10;
$has_src = (isset($_GET['src']) && $_GET['src'] === 'fs2026');

if (!$is_meta_click && !$is_tiktok_click && !$has_src) {
    serve_clean();
    exit;
}

// Visitante legítimo: salva cookie e serve a VSL
setcookie('_gl_ok', '1', time() + 86400 * 7, '/');
serve_sales();
exit;

function serve_sales() {
    if (file_exists(__DIR__ . '/' . SALES_PAGE)) {
        include __DIR__ . '/' . SALES_PAGE;
    } else {
        echo "Sales page not found.";
    }
}

function serve_clean() {
    if (file_exists(__DIR__ . '/' . CLEAN_PAGE)) {
        include __DIR__ . '/' . CLEAN_PAGE;
    } else {
        echo "Clean page not found.";
    }
}
