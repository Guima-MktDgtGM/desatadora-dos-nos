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

// --- 3. COOKIE ATIVO (Usuário já validado anteriormente) ---
if (!empty($_COOKIE['_gl_ok'])) {
    serve_sales();
    exit;
}

// --- 4. FILTRO DE ORIGEM (FBCLID / ORGÂNICO / TOKEN) ---
$has_fbclid = isset($_GET['fbclid']) && !empty($_GET['fbclid']);
$ref = strtolower($_SERVER['HTTP_REFERER'] ?? '');
$is_organic = (
    strpos($ref, 'google.') !== false ||
    strpos($ref, 'bing.') !== false ||
    strpos($ref, 'facebook.com') !== false ||
    strpos($ref, 'instagram.com') !== false ||
    strpos($ref, 'youtube.com') !== false
);
$has_src = (isset($_GET['src']) && $_GET['src'] === 'fs2026');

// Se NÃO tiver fbclid, NEM orgânico, NEM token -> Serve a White TSL para os curiosos
if (!$has_fbclid && !$is_organic && !$has_src) {
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
