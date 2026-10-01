<?php
require_once __DIR__ . '/config.php';

// Login & Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged']);
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (hash('sha256', trim($_POST['password'] ?? '')) === ADMIN_AUTH_HASH) {
        $_SESSION['admin_logged'] = true;
        header('Location: index.php');
        exit;
    } else {
        $loginError = 'Senha incorreta.';
    }
}

$isLogged = !empty($_SESSION['admin_logged']);

// Atualização de Status
if ($isLogged && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $targetProtocol = trim($_POST['protocol'] ?? '');
    $newStatus = trim($_POST['status'] ?? '');
    
    $pedidos = loadPedidos();
    foreach ($pedidos as &$p) {
        if ($p['protocol'] === $targetProtocol) {
            $p['status'] = $newStatus;
            break;
        }
    }
    savePedidos($pedidos);
    header('Location: index.php');
    exit;
}

$pedidos = $isLogged ? loadPedidos() : [];

// Métricas
$total = count($pedidos);
$emAnalise = 0;
$retidos = 0;
$estornados = 0;

foreach ($pedidos as $p) {
    if ($p['status'] === 'analise') $emAnalise++;
    elseif ($p['status'] === 'retido') $retidos++;
    elseif ($p['status'] === 'estornado') $estornados++;
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel de Gestão de Solicitações — Caminhos da Fé</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body {
    background-color: #050B17;
    color: #E2E8F0;
    font-family: 'Montserrat', sans-serif;
  }
  .font-cinzel { font-family: 'Cinzel', serif; }
  .gold-border { border-color: rgba(212, 175, 55, 0.3); }
  .gold-btn {
    background: linear-gradient(135deg, #D4AF37 0%, #AA820A 100%);
    color: #050B17;
    font-weight: 700;
  }
</style>
</head>
<body class="min-h-screen py-8 px-4">

<?php if (!$isLogged): ?>
<!-- TELA DE LOGIN -->
<div class="max-w-sm mx-auto my-auto mt-20 bg-[#0B152E] border gold-border rounded-3xl p-7 shadow-2xl space-y-5 text-center">
  <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-300 flex items-center justify-center mx-auto text-xl border border-amber-400/40 font-bold">
    🔒
  </div>

  <div class="space-y-1">
    <h1 class="text-xl font-cinzel font-bold text-white">Acesso Restrito</h1>
    <p class="text-xs text-slate-400">Painel de Atendimento e Reversão</p>
  </div>

  <?php if (!empty($loginError)): ?>
    <div class="text-xs text-red-400 bg-red-950/60 p-2 rounded-xl border border-red-500/40">
      <?= htmlspecialchars($loginError) ?>
    </div>
  <?php endif; ?>

  <form method="POST" class="space-y-3">
    <input type="password" name="password" required placeholder="Digite a senha mestra" class="w-full bg-[#070D1E] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-400 text-center font-mono text-sm tracking-widest">
    <button type="submit" class="w-full py-2.5 gold-btn rounded-xl text-xs font-bold transition">
      Aceder ao Painel
    </button>
  </form>
</div>

<?php else: ?>
<!-- PAINEL ADMINISTRATIVO -->
<div class="max-w-6xl mx-auto space-y-6">

  <!-- Topo -->
  <div class="flex flex-col sm:flex-row items-center justify-between pb-4 border-b border-slate-800 gap-3">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-xl font-cinzel font-bold text-amber-300">Gestão Pastoral de Solicitações</h1>
        <span class="text-[10px] bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full font-bold">Tempo Real</span>
      </div>
      <p class="text-xs text-slate-400">Acompanhamento de desistências, reversão no 1 a 1 e proteção de gateway</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="solicitar.php" target="_blank" class="px-3 py-1.5 rounded-xl border border-slate-700 hover:border-amber-400 text-xs text-slate-300 transition flex items-center gap-1">
        <span>🔗 Ver Formulário Público</span>
      </a>
      <a href="?logout=1" class="px-3 py-1.5 rounded-xl bg-red-950/60 border border-red-500/40 text-xs text-red-300 hover:bg-red-900 transition">
        Sair
      </a>
    </div>
  </div>

  <!-- Métricas Rápidas -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
    <div class="bg-[#0B152E] border border-slate-800 p-4 rounded-2xl space-y-1">
      <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Total Recebido</span>
      <h3 class="text-2xl font-black text-white"><?= $total ?></h3>
    </div>

    <div class="bg-[#0B152E] border border-amber-500/30 p-4 rounded-2xl space-y-1">
      <span class="text-[10px] text-amber-300 uppercase tracking-wider block font-bold">Em Análise (Cozinhando)</span>
      <h3 class="text-2xl font-black text-amber-300"><?= $emAnalise ?></h3>
    </div>

    <div class="bg-[#0B152E] border border-emerald-500/30 p-4 rounded-2xl space-y-1">
      <span class="text-[10px] text-emerald-400 uppercase tracking-wider block font-bold">Retidos com Sucesso</span>
      <h3 class="text-2xl font-black text-emerald-400"><?= $retidos ?></h3>
    </div>

    <div class="bg-[#0B152E] border border-red-500/30 p-4 rounded-2xl space-y-1">
      <span class="text-[10px] text-red-400 uppercase tracking-wider block font-bold">Reembolsados no Portal</span>
      <h3 class="text-2xl font-black text-red-400"><?= $estornados ?></h3>
    </div>
  </div>

  <!-- Tabela de Solicitações -->
  <div class="bg-[#0B152E] border gold-border rounded-3xl overflow-hidden shadow-2xl">
    <div class="p-4 px-6 border-b border-slate-800 flex items-center justify-between">
      <h3 class="font-cinzel font-bold text-white text-sm">Leads que Solicitaram Restituição</h3>
      <span class="text-xs text-slate-400">Atualizado dinamicamente</span>
    </div>

    <?php if (empty($pedidos)): ?>
      <div class="p-10 text-center text-xs text-slate-500">
        Nenhuma solicitação de reembolso registrada ainda. O sistema está ativo e monitorando.
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
          <thead class="bg-slate-950/80 text-[10px] text-slate-400 uppercase tracking-wider border-b border-slate-800">
            <tr>
              <th class="py-3 px-4">Protocolo / Data</th>
              <th class="py-3 px-4">Devoto(a)</th>
              <th class="py-3 px-4">Contato & Preferência</th>
              <th class="py-3 px-4">Contribuição / Motivo</th>
              <th class="py-3 px-4">Status Atual</th>
              <th class="py-3 px-4 text-right">Ação Direta</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/80">
            <?php foreach ($pedidos as $p): ?>
              <?php
                $cleanPhone = preg_replace('/[^0-9]/', '', $p['whatsapp']);
                $waMsg = "A paz de Cristo, " . $p['nome'] . "! Aqui é da Comunidade Caminhos da Fé sobre o seu protocolo pastoral " . $p['protocol'] . " enviado ao Padre Elias. Podemos conversar rapidamente?";
                $waUrl = "https://wa.me/" . $cleanPhone . "?text=" . rawurlencode($waMsg);
              ?>
              <tr class="hover:bg-slate-900/50 transition">
                <td class="py-3.5 px-4">
                  <a href="acompanhar.php?p=<?= urlencode($p['protocol']) ?>" target="_blank" class="font-mono font-bold text-amber-300 hover:underline">
                    <?= htmlspecialchars($p['protocol']) ?>
                  </a>
                  <span class="text-[10px] text-slate-500 block"><?= date('d/m H:i', $p['createdAt']) ?></span>
                </td>

                <td class="py-3.5 px-4">
                  <strong class="text-white block"><?= htmlspecialchars($p['nome']) ?></strong>
                  <span class="text-[11px] text-slate-400"><?= htmlspecialchars($p['email']) ?></span>
                </td>

                <td class="py-3.5 px-4">
                  <span class="font-mono text-slate-200 block"><?= htmlspecialchars($p['whatsapp']) ?></span>
                  <?php if ($p['canal'] === 'whatsapp'): ?>
                    <span class="text-[9px] font-bold text-emerald-400 bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-500/30">🟢 Prefere WhatsApp</span>
                  <?php else: ?>
                    <span class="text-[9px] font-bold text-slate-400 bg-slate-900 px-2 py-0.5 rounded-full border border-slate-700">✉️ Prefere E-mail</span>
                  <?php endif; ?>
                </td>

                <td class="py-3.5 px-4 max-w-xs">
                  <span class="text-amber-300 font-semibold block text-[11px]"><?= htmlspecialchars($p['produto']) ?></span>
                  <span class="text-[10px] text-slate-400 italic block leading-snug">"<?= htmlspecialchars($p['motivo']) ?>"</span>
                  <?php if (!empty($p['mensagem'])): ?>
                    <span class="text-[10px] text-slate-500 block truncate mt-0.5">Obs: <?= htmlspecialchars($p['mensagem']) ?></span>
                  <?php endif; ?>
                </td>

                <td class="py-3.5 px-4">
                  <form method="POST" class="inline">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" name="protocol" value="<?= htmlspecialchars($p['protocol']) ?>">
                    <select name="status" onchange="this.form.submit()" class="bg-[#070D1E] border border-slate-700 rounded-lg px-2 py-1 text-[11px] text-white focus:outline-none focus:border-amber-400 cursor-pointer">
                      <option value="analise" <?= $p['status'] === 'analise' ? 'selected' : '' ?>>⏳ Em Análise</option>
                      <option value="retido" <?= $p['status'] === 'retido' ? 'selected' : '' ?>>✅ Retido (Desistiu)</option>
                      <option value="estornado" <?= $p['status'] === 'estornado' ? 'selected' : '' ?>>💸 Estornado</option>
                      <option value="cancelado" <?= $p['status'] === 'cancelado' ? 'selected' : '' ?>>❌ Cancelado</option>
                    </select>
                  </form>
                </td>

                <td class="py-3.5 px-4 text-right">
                  <?php if (!empty($cleanPhone)): ?>
                    <a href="<?= $waUrl ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow transition">
                      <span>🟢</span>
                      <span>Chamar WhatsApp</span>
                    </a>
                  <?php else: ?>
                    <a href="mailto:<?= htmlspecialchars($p['email']) ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 text-xs">
                      <span>✉️ E-mail</span>
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</div>
<?php endif; ?>

</body>
</html>
