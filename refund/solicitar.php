<?php
require_once __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $canal = trim($_POST['canal'] ?? 'email');
    
    // Múltipla escolha de produtos
    $produtosArray = $_POST['produtos'] ?? [];
    if (is_array($produtosArray) && !empty($produtosArray)) {
        $produto = implode(' + ', array_map('trim', $produtosArray));
    } else {
        $produto = trim($_POST['produto_outro'] ?? 'Todas as contribuições');
    }

    $motivo = trim($_POST['motivo'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    // Validação: apenas Nome e E-mail são obrigatórios! WhatsApp é opcional.
    if (empty($nome) || empty($email)) {
        $error = 'Por favor, preencha o seu nome e o seu e-mail para que possamos localizar a sua contribuição.';
    } else {
        $pedidos = loadPedidos();
        $protocol = 'CP-' . rand(1000, 9999);
        
        $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
        if (empty($cleanWhatsapp)) {
            $canal = 'email'; // Se não tiver WhatsApp, canal é e-mail automaticamente
        }

        $novoPedido = [
            'id' => uniqid('ref_'),
            'protocol' => $protocol,
            'createdAt' => time(),
            'nome' => $nome,
            'email' => $email,
            'whatsapp' => $cleanWhatsapp,
            'canal' => $canal,
            'produto' => $produto,
            'motivo' => $motivo,
            'mensagem' => $mensagem,
            'status' => 'analise',
            'historico' => [
                [
                    'data' => time(),
                    'evento' => 'Solicitação registrada no sistema pastoral'
                ]
            ]
        ];

        array_unshift($pedidos, $novoPedido);
        savePedidos($pedidos);

        header('Location: acompanhar.php?p=' . $protocol);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Atendimento Fraterno & Restituição Pastoral — Caminhos da Fé</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body {
    background: radial-gradient(circle at 50% 0%, #101c38 0%, #060b17 100%);
    color: #e2e8f0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    -webkit-font-smoothing: antialiased;
  }
  .font-cinzel { font-family: 'Cinzel', serif; }
  .font-serif { font-family: 'Playfair Display', Georgia, serif; }
  .gold-border { border-color: rgba(212, 175, 55, 0.3); }
  .gold-btn {
    background: linear-gradient(135deg, #d4af37 0%, #a87e11 100%);
    color: #060b17;
    font-weight: 700;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .gold-btn:hover {
    background: linear-gradient(135deg, #e6c555 0%, #be9117 100%);
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(212, 175, 55, 0.28);
  }
  .card-shadow {
    box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
  }
  /* Checkbox customizado */
  .custom-checkbox:checked {
    background-color: #d4af37;
    border-color: #d4af37;
  }
</style>
</head>
<body class="min-h-screen py-8 sm:py-12 px-4 flex flex-col justify-between">

<div class="max-w-xl mx-auto w-full space-y-6">

  <!-- Topo da Página -->
  <header class="text-center space-y-3">
    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-400/10 border border-amber-400/30 text-amber-300 text-[11px] font-semibold tracking-wide">
      <span>🕊️ Atendimento Pastoral Fraterno</span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-tight">
      Restituição de Oferta & Apoio
    </h1>
    <p class="text-xs sm:text-sm text-slate-300 max-w-md mx-auto leading-relaxed">
      Comunidade Caminhos da Fé · Paróquia de Nossa Senhora Desatadora dos Nós
    </p>
  </header>

  <!-- Mensagem Pastoral do Padre Elias -->
  <section class="bg-slate-900/70 border gold-border rounded-3xl p-6 sm:p-7 card-shadow backdrop-blur-xl space-y-4">
    <div class="flex items-center gap-3.5 pb-4 border-b border-slate-800/80">
      <div class="relative w-14 h-14 rounded-2xl overflow-hidden border-2 border-amber-400/70 shadow-lg flex-shrink-0 bg-slate-950">
        <img src="../images/padre-elias.jpg" alt="Padre Elias" class="w-full h-full object-cover" onerror="this.src='https://caminhosdafe.online/app/images/padre-elias.jpg'">
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h2 class="font-cinzel font-bold text-amber-300 text-sm sm:text-base">Padre Elias</h2>
          <span class="text-[9px] bg-amber-400/20 text-amber-300 font-bold px-2 py-0.5 rounded-full border border-amber-400/30">Pastoral</span>
        </div>
        <p class="text-xs text-slate-400">Pároco e Guardião do Santuário</p>
      </div>
    </div>

    <div class="space-y-3 text-xs sm:text-sm leading-relaxed text-slate-200">
      <p class="font-serif italic text-slate-300">
        "Irmão(ã) querido(a), a paz de Cristo esteja no seu coração. A sua generosa contribuição foi acolhida com profundo amor e direcionada à compra de materiais para erguer a nossa primeira capela e ao amparo de famílias que passam fome."
      </p>
      
      <p class="text-justify">
        <strong>É evidente que faremos a devolução da sua oferta</strong> se este valor fizer falta no sustento da sua casa ou se ocorreu qualquer engano. <em>Jamais iríamos querer reter algo que não tenha vindo de coração inteiramente aberto</em> para socorrer quem precisa — mesmo sabendo que Deus abençoa a generosidade de quem ajuda o próximo.
      </p>
    </div>
  </section>

  <!-- Comparativo de Prazos (Desestímulo ao Banco) -->
  <section class="bg-slate-900/70 border border-slate-700/70 rounded-3xl p-5 sm:p-6 card-shadow backdrop-blur-xl space-y-4">
    <div class="flex items-center gap-2 pb-2 border-b border-slate-800">
      <span class="text-lg">⚖️</span>
      <h3 class="font-cinzel font-bold text-amber-300 text-xs sm:text-sm">
        Aviso Importante sobre os Prazos de Restituição
      </h3>
    </div>

    <p class="text-xs text-slate-300 leading-relaxed text-justify">
      Como as ofertas recebidas são <strong>repassadas quase de imediato para a compra de materiais da obra e auxílio a famílias carenciadas</strong>, existe uma diferença fundamental de prazos:
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
      
      <!-- Se for pelo Banco -->
      <div class="bg-red-950/20 border border-red-500/30 rounded-2xl p-4 space-y-2">
        <div class="flex items-center justify-between">
          <span class="text-[10px] font-bold text-red-300 uppercase tracking-wider">Se for pelo seu Banco:</span>
          <span class="text-sm">🏛️</span>
        </div>
        <div class="text-base sm:text-lg font-black text-red-400 font-mono">20 a 30 dias úteis</div>
        <p class="text-[11px] text-slate-400 leading-relaxed text-justify">
          É um direito seu pleno recorrer à sua agência. Contudo, como os fundos foram alocados em obras sociais, os bancos internacionais instauram uma <strong>auditoria burocrática</strong> que retém o processo por até <strong>5 vezes mais tempo</strong>.
        </p>
      </div>

      <!-- Pelo Canal Oficial -->
      <div class="bg-emerald-950/20 border-2 border-emerald-500/50 rounded-2xl p-4 space-y-2 relative shadow-lg">
        <div class="flex items-center justify-between">
          <span class="text-[10px] font-black text-emerald-300 uppercase tracking-wider">Pelo Canal Oficial Direto:</span>
          <span class="text-sm">🕊️</span>
        </div>
        <div class="text-base sm:text-lg font-black text-emerald-400 font-mono">3 a 7 dias úteis</div>
        <p class="text-[11px] text-slate-200 leading-relaxed text-justify">
          Nós processamos diretamente na nossa tesouraria de <strong>bom grado e com prioridade</strong>, adiantando com recursos próprios da paróquia para que receba de volta o quanto antes e sem atritos.
        </p>
      </div>

    </div>

    <p class="text-[11px] text-amber-200/90 text-center font-medium">
      Ao preencher este formulário, o seu pedido entra na fila prioritária de 3 a 7 dias úteis.
    </p>
  </section>

  <?php if ($error): ?>
  <div class="bg-red-950/90 border border-red-500/70 text-red-200 text-xs rounded-2xl p-4 text-center font-semibold shadow-lg">
    ⚠️ <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <!-- Formulário de Solicitação -->
  <form method="POST" class="bg-slate-900/80 border gold-border rounded-3xl p-6 sm:p-8 card-shadow backdrop-blur-xl space-y-5">
    
    <div class="border-b border-slate-800 pb-3">
      <h3 class="text-sm font-bold text-white font-cinzel">1. Dados da Sua Contribuição</h3>
      <p class="text-xs text-slate-400">Preencha para localizarmos o seu registro no altar</p>
    </div>

    <!-- Nome e E-mail -->
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-300 mb-1.5">O seu Nome Completo: <span class="text-amber-400">*</span></label>
        <input type="text" name="nome" required placeholder="Como inseriu na sua contribuição" class="w-full bg-slate-950/90 border border-slate-700/80 rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-400 transition">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-300 mb-1.5">O seu E-mail da compra: <span class="text-amber-400">*</span></label>
        <input type="email" name="email" required placeholder="exemplo@email.com" class="w-full bg-slate-950/90 border border-slate-700/80 rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-400 transition">
      </div>

      <!-- WhatsApp Opcional -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="text-xs font-semibold text-slate-300">WhatsApp / Telemóvel:</label>
          <span class="text-[10px] text-amber-300/80 bg-amber-400/10 px-2 py-0.5 rounded-full border border-amber-400/20 font-medium">Opcional</span>
        </div>
        <input type="text" name="whatsapp" placeholder="Ex: +351 912 345 678 ou (86) 99833-2748" class="w-full bg-slate-950/90 border border-slate-700/80 rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-400 transition">
        <p class="text-[11px] text-slate-400 mt-1">Se preenchido, podemos enviar as notificações de andamento diretamente pelo WhatsApp.</p>
      </div>

      <!-- Canal Preferencial -->
      <div class="pt-1">
        <label class="block text-xs font-semibold text-slate-300 mb-2">Canal preferencial de atendimento:</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
          <label class="flex items-center gap-3 p-3 rounded-xl border border-emerald-500/40 bg-emerald-950/20 cursor-pointer hover:border-emerald-400 transition">
            <input type="radio" name="canal" value="whatsapp" checked class="text-emerald-500 focus:ring-0">
            <span class="text-xs text-emerald-300 font-semibold">🟢 WhatsApp (Prioritário)</span>
          </label>
          <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-800 bg-slate-950/40 cursor-pointer hover:border-slate-700 transition">
            <input type="radio" name="canal" value="email" class="text-amber-500 focus:ring-0">
            <span class="text-xs text-slate-300">✉️ Correio Eletrónico (E-mail)</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Múltipla Escolha de Produtos -->
    <div class="border-t border-slate-800 pt-4 space-y-3">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-bold text-white font-cinzel">2. Qual(is) contribuição(ões) deseja desvincular?</h3>
          <p class="text-[11px] text-slate-400">Pode selecionar uma ou mais opções abaixo:</p>
        </div>
        <button type="button" onclick="selectAllProducts()" class="text-[11px] text-amber-300 hover:underline font-semibold flex-shrink-0">
          ✓ Marcar Todas
        </button>
      </div>

      <div class="space-y-2">
        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-amber-400/40 cursor-pointer transition">
          <input type="checkbox" name="produtos[]" value="Guia 15 Dias com Maria (€36,90)" class="product-checkbox mt-0.5 rounded text-amber-500 focus:ring-0 w-4 h-4" checked>
          <div class="text-xs leading-snug">
            <strong class="text-white block">Guia de Proteção Espiritual 15 Dias com Maria</strong>
            <span class="text-slate-400 text-[11px]">Oferta de Blindagem e Acelerador do Desatador (€36,90)</span>
          </div>
        </label>

        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-amber-400/40 cursor-pointer transition">
          <input type="checkbox" name="produtos[]" value="Áudios Frequenciais Terço Noturno (€46,90)" class="product-checkbox mt-0.5 rounded text-amber-500 focus:ring-0 w-4 h-4">
          <div class="text-xs leading-snug">
            <strong class="text-white block">Áudios Frequenciais: Terço Noturno de Desamarração</strong>
            <span class="text-slate-400 text-[11px]">Frequências Sacras de 432Hz para Noites de Paz (€46,90)</span>
          </div>
        </label>

        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-amber-400/40 cursor-pointer transition">
          <input type="checkbox" name="produtos[]" value="Novena das 9 Palavras Sagradas (€16,90 / €26,90)" class="product-checkbox mt-0.5 rounded text-amber-500 focus:ring-0 w-4 h-4">
          <div class="text-xs leading-snug">
            <strong class="text-white block">Novena das 9 Palavras de Nossa Senhora</strong>
            <span class="text-slate-400 text-[11px]">Oferta Inicial da Novena e Manuscrito Sagrado (€16,90 / €26,90)</span>
          </div>
        </label>

        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-amber-400/40 cursor-pointer transition">
          <input type="checkbox" name="produtos[]" value="Sacramentais Consagrados: Amuleto e Óleo (€24,90)" class="product-checkbox mt-0.5 rounded text-amber-500 focus:ring-0 w-4 h-4">
          <div class="text-xs leading-snug">
            <strong class="text-white block">Envio Protegido — Sacramentais Consagrados</strong>
            <span class="text-slate-400 text-[11px]">Amuleto de Altar e Óleo Santo de Maria (€24,90)</span>
          </div>
        </label>
      </div>
    </div>

    <!-- Motivo com Interceptação -->
    <div class="border-t border-slate-800 pt-4 space-y-3">
      <h3 class="text-sm font-bold text-white font-cinzel">3. Motivo da Solicitação</h3>

      <div class="space-y-2 text-xs">
        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-amber-400/40 cursor-pointer transition" onclick="triggerAppBypass()">
          <input type="radio" name="motivo" value="app_access" class="mt-0.5 text-amber-400 focus:ring-0">
          <span class="text-slate-200 leading-relaxed font-medium">Não consegui aceder ao aplicativo / Não encontrei o conteúdo do Guia</span>
        </label>

        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-amber-400/40 cursor-pointer transition">
          <input type="radio" name="motivo" value="Tive um aperto financeiro imprevisto na minha família" checked class="mt-0.5 text-amber-400 focus:ring-0">
          <span class="text-slate-200 leading-relaxed font-medium">Tive um aperto financeiro imprevisto na minha família</span>
        </label>

        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-amber-400/40 cursor-pointer transition">
          <input type="radio" name="motivo" value="Foi um engano de valor no momento da contribuição" class="mt-0.5 text-amber-400 focus:ring-0">
          <span class="text-slate-200 leading-relaxed font-medium">Foi um engano de valor no momento da contribuição</span>
        </label>

        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-800 bg-slate-950/60 hover:border-amber-400/40 cursor-pointer transition">
          <input type="radio" name="motivo" value="Não desejo mais manter o meu nome sob as orações do altar" class="mt-0.5 text-amber-400 focus:ring-0">
          <span class="text-slate-200 leading-relaxed font-medium">Não desejo mais manter o meu nome sob as orações do altar</span>
        </label>
      </div>
    </div>

    <!-- Mensagem Opcional -->
    <div class="space-y-1.5">
      <label class="block text-xs font-semibold text-slate-300">Mensagem para o Padre Elias e conselho pastoral (Opcional):</label>
      <textarea name="mensagem" rows="2" placeholder="Se desejar, explique brevemente a sua situação..." class="w-full bg-slate-950/90 border border-slate-700/80 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-400 transition resize-none"></textarea>
    </div>

    <!-- Botão de Envio -->
    <div class="pt-4 space-y-3">
      <button type="submit" class="w-full py-4 px-5 rounded-2xl bg-slate-800 hover:bg-slate-700 border border-slate-600 text-xs sm:text-sm font-bold text-slate-200 hover:text-white transition leading-snug shadow-xl flex items-center justify-center gap-2">
        <span>Quero retirar a minha doação da Capela e abrir mão da bênção consagrada</span>
      </button>

      <p class="text-[11px] text-slate-400 text-center leading-relaxed">
        Ao confirmar, um protocolo individual será gerado para acompanhamento contínuo em tempo real.
      </p>
    </div>

  </form>

</div>

<!-- MODAL DE DESVIO DE ACESSO AO APP -->
<div id="modal-app-bypass" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
  <div class="bg-slate-900 border-2 border-amber-400/70 rounded-3xl p-6 sm:p-8 max-w-md w-full card-shadow space-y-4 text-center">
    <div class="w-14 h-14 rounded-2xl bg-amber-400/20 text-amber-300 flex items-center justify-center mx-auto text-2xl border border-amber-400/40 shadow-inner">
      ✨
    </div>

    <div class="space-y-1.5">
      <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-400 bg-emerald-950/80 px-2.5 py-0.5 rounded-full border border-emerald-500/30">
        Acesso 100% Liberado
      </span>
      <h3 class="text-lg font-serif font-bold text-white">O seu Guia de 15 Dias já está ativo!</h3>
      <p class="text-xs text-slate-300 leading-relaxed">
        Irmão(ã), não precisa de passar por burocracias nem cancelar a sua bênção por questões de acesso! O seu aplicativo já está completamente desbloqueado para o seu telemóvel.
      </p>
    </div>

    <div class="pt-2 space-y-2.5">
      <a href="https://caminhosdafe.online/app/?guia=1" target="_blank" class="w-full py-3.5 gold-btn rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg block font-bold">
        <span>👉 Entrar no Meu Aplicativo Agora</span>
      </a>

      <button type="button" onclick="closeAppModal()" class="w-full py-2 text-[11px] text-slate-400 hover:text-white underline block transition">
        Ainda assim, desejo continuar com o pedido de restituição
      </button>
    </div>
  </div>
</div>

<footer class="text-center text-[11px] text-slate-500 py-6 max-w-md mx-auto leading-relaxed">
  Comunidade Caminhos da Fé · Obras Sociais de Nossa Senhora Desatadora dos Nós<br>
  Apoio ao devoto: <a href="mailto:gabriel.luz@noticiasdafe.com.br" class="text-slate-400 underline">gabriel.luz@noticiasdafe.com.br</a>
</footer>

<script>
function triggerAppBypass() {
  document.getElementById('modal-app-bypass').classList.remove('hidden');
}

function closeAppModal() {
  document.getElementById('modal-app-bypass').classList.add('hidden');
}

function selectAllProducts() {
  const checkboxes = document.querySelectorAll('.product-checkbox');
  const allChecked = Array.from(checkboxes).every(c => c.checked);
  checkboxes.forEach(c => c.checked = !allChecked);
}
</script>

</body>
</html>
