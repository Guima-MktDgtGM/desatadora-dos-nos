<?php
require_once __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $ddi = trim($_POST['ddi'] ?? '+351');
    $paisNome = trim($_POST['pais_nome'] ?? 'Portugal');
    $canal = trim($_POST['canal'] ?? 'email');
    $userTimezone = trim($_POST['user_timezone'] ?? '');
    $userLocalTime = trim($_POST['user_local_time'] ?? '');
    
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
        
        $cleanPhone = preg_replace('/[^0-9]/', '', $whatsapp);
        $cleanDdi = preg_replace('/[^0-9]/', '', $ddi);
        
        if (!empty($cleanPhone)) {
            // Se o devoto já digitou o DDI junto com o número, evita duplicar
            if (!empty($cleanDdi) && strpos($cleanPhone, $cleanDdi) === 0 && strlen($cleanPhone) > strlen($cleanDdi) + 6) {
                $formattedWhatsapp = '+' . $cleanPhone;
            } else {
                $formattedWhatsapp = ($cleanDdi ? ('+' . $cleanDdi) : '') . $cleanPhone;
            }
        } else {
            $formattedWhatsapp = '';
            $canal = 'email'; // Se não tiver WhatsApp, canal é e-mail automaticamente
        }

        // Horário oficial do Brasil (Brasília)
        try {
            $dtBr = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
            $horarioBrasil = $dtBr->format('d/m/Y H:i');
        } catch (Exception $e) {
            $horarioBrasil = date('d/m/Y H:i');
        }

        // Validação e cálculo do Horário Local do Devoto
        if (empty($userLocalTime) && !empty($userTimezone)) {
            try {
                $dtLead = new DateTime('now', new DateTimeZone($userTimezone));
                $userLocalTime = $dtLead->format('d/m/Y H:i');
            } catch (Exception $e) {
                $userLocalTime = $horarioBrasil;
            }
        } elseif (empty($userLocalTime)) {
            $tzGuess = ($ddi === '+351' || $paisNome === 'Portugal') ? 'Europe/Lisbon' : 'America/Sao_Paulo';
            try {
                $dtLead = new DateTime('now', new DateTimeZone($tzGuess));
                $userLocalTime = $dtLead->format('d/m/Y H:i');
                $userTimezone = $tzGuess;
            } catch (Exception $e) {
                $userLocalTime = $horarioBrasil;
            }
        }

        $novoPedido = [
            'id' => uniqid('ref_'),
            'protocol' => $protocol,
            'createdAt' => time(),
            'userTimezone' => $userTimezone,
            'userLocalTime' => $userLocalTime,
            'horarioBrasil' => $horarioBrasil,
            'nome' => $nome,
            'email' => $email,
            'whatsapp' => $formattedWhatsapp,
            'pais' => $paisNome,
            'canal' => $canal,
            'produto' => $produto,
            'motivo' => $motivo,
            'mensagem' => $mensagem,
            'status' => 'analise',
            'historico' => [
                [
                    'data' => time(),
                    'horaLocal' => $userLocalTime,
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
  /* Custom scrollbar para lista de países */
  .custom-scroll::-webkit-scrollbar {
    width: 6px;
  }
  .custom-scroll::-webkit-scrollbar-track {
    background: rgba(15, 23, 42, 0.6);
    border-radius: 8px;
  }
  .custom-scroll::-webkit-scrollbar-thumb {
    background: rgba(212, 175, 55, 0.4);
    border-radius: 8px;
  }
  .custom-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(212, 175, 55, 0.7);
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
  <form method="POST" id="form-refund" onsubmit="captureClientTime()" class="bg-slate-900/80 border gold-border rounded-3xl p-6 sm:p-8 card-shadow backdrop-blur-xl space-y-5">
    
    <!-- Captura de Fuso e Horário Local do Devoto -->
    <input type="hidden" name="user_timezone" id="input-user-timezone" value="">
    <input type="hidden" name="user_local_time" id="input-user-local-time" value="">

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

      <!-- WhatsApp Opcional com Seletor de País e Bandeiras Reais -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="text-xs font-semibold text-slate-300">WhatsApp / Telemóvel:</label>
          <span class="text-[10px] text-amber-300/80 bg-amber-400/10 px-2 py-0.5 rounded-full border border-amber-400/20 font-medium">Opcional</span>
        </div>

        <div class="relative">
          <div class="flex items-center">
            <!-- Botão Seletor com Bandeira Real e DDI -->
            <button type="button" id="btn-country-picker" onclick="toggleCountryDropdown()" class="flex-shrink-0 flex items-center gap-2 bg-slate-950 border border-slate-700/80 rounded-l-xl px-3 py-3 hover:border-amber-400 transition cursor-pointer select-none">
              <img id="selected-flag-img" src="https://flagcdn.com/w40/pt.png" alt="Portugal" class="w-5 h-3.5 object-cover rounded shadow-sm flex-shrink-0">
              <span id="selected-ddi-label" class="text-xs sm:text-sm font-semibold text-white font-mono">+351</span>
              <svg class="w-3.5 h-3.5 text-slate-400 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>

            <!-- Inputs Ocultos do País -->
            <input type="hidden" name="ddi" id="input-ddi" value="+351">
            <input type="hidden" name="pais_nome" id="input-pais" value="Portugal">

            <!-- Campo de Número -->
            <input type="tel" name="whatsapp" id="input-phone" placeholder="912 345 678" class="flex-1 bg-slate-950/90 border border-l-0 border-slate-700/80 rounded-r-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-amber-400 transition">
          </div>

          <!-- Dropdown de Países com Bandeiras Reais -->
          <div id="country-dropdown" class="hidden absolute top-full left-0 mt-2 w-full sm:w-80 bg-[#0B152E] border border-amber-400/40 rounded-2xl shadow-2xl p-3 z-50 backdrop-blur-xl">
            <div class="relative mb-2.5">
              <input type="text" id="country-search" oninput="filterCountries()" placeholder="🔍 Pesquisar país ou indicativo..." class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-amber-400 transition">
            </div>

            <div id="countries-list" class="max-h-60 overflow-y-auto divide-y divide-slate-800/60 custom-scroll pr-1">
              <!-- Preenchido dinamicamente via JS -->
            </div>
          </div>
        </div>

        <p class="text-[11px] text-slate-400 mt-1.5">Selecione o seu país para receber notificações do andamento direto no WhatsApp.</p>
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

    <!-- Botão de Envio com Ativação Visual Dinâmica -->
    <div class="pt-4 space-y-3">
      <button type="submit" id="btn-submit-refund" class="w-full py-4 px-5 rounded-2xl bg-slate-800/90 border border-slate-700 text-xs sm:text-sm font-bold text-slate-400 opacity-60 cursor-not-allowed transition-all duration-300 leading-snug shadow-xl flex items-center justify-center gap-2">
        <span id="btn-submit-icon">🔒</span>
        <span id="btn-submit-text">Quero retirar minha garantia e abrir mão da bênção consagrada</span>
      </button>

      <p id="form-validation-hint" class="text-[11px] text-amber-300/80 text-center leading-relaxed font-medium transition-colors">
        * Preencha o seu Nome Completo e E-mail acima para liberar a solicitação.
      </p>

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
  checkRequiredFields();
}

function captureClientTime() {
  try {
    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
    const tzInput = document.getElementById('input-user-timezone');
    if (tzInput) tzInput.value = tz;

    const now = new Date();
    const dateStr = now.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const timeStr = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    const localTimeInput = document.getElementById('input-user-local-time');
    if (localTimeInput) localTimeInput.value = `${dateStr} ${timeStr}`;
  } catch (e) {
    console.error(e);
  }
}

function checkRequiredFields() {
  const nomeInput = document.querySelector('input[name="nome"]');
  const emailInput = document.querySelector('input[name="email"]');
  const checkboxes = document.querySelectorAll('.product-checkbox');
  const btn = document.getElementById('btn-submit-refund');
  const icon = document.getElementById('btn-submit-icon');
  const hint = document.getElementById('form-validation-hint');

  if (!btn || !nomeInput || !emailInput) return;

  const nomeVal = (nomeInput.value || '').trim();
  const emailVal = (emailInput.value || '').trim();
  const hasProduct = Array.from(checkboxes).some(c => c.checked);

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const isNomeValid = nomeVal.length >= 2;
  const isEmailValid = emailRegex.test(emailVal);
  const isValid = isNomeValid && isEmailValid && hasProduct;

  if (isValid) {
    // BOTÃO VERDE ATIVADO COM DESTAQUE
    btn.classList.remove('bg-slate-800/90', 'border-slate-700', 'text-slate-400', 'opacity-60', 'cursor-not-allowed');
    btn.classList.add('bg-emerald-600', 'hover:bg-emerald-500', 'border-emerald-400', 'text-white', 'cursor-pointer', 'shadow-emerald-600/30', 'hover:shadow-emerald-500/50', 'transform', 'hover:-translate-y-0.5');
    if (icon) icon.textContent = '✓';
    if (hint) {
      hint.textContent = '✓ Dados preenchidos com sucesso. O botão acima está liberado.';
      hint.classList.remove('text-amber-300/80');
      hint.classList.add('text-emerald-400');
    }
  } else {
    // BOTÃO INATIVO
    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500', 'border-emerald-400', 'text-white', 'cursor-pointer', 'shadow-emerald-600/30', 'hover:shadow-emerald-500/50', 'transform', 'hover:-translate-y-0.5');
    btn.classList.add('bg-slate-800/90', 'border-slate-700', 'text-slate-400', 'opacity-60', 'cursor-not-allowed');
    if (icon) icon.textContent = '🔒';
    if (hint) {
      hint.textContent = '* Preencha o seu Nome Completo e E-mail acima para liberar a solicitação.';
      hint.classList.remove('text-emerald-400');
      hint.classList.add('text-amber-300/80');
    }
  }
}

// Lista completa de países com bandeiras reais via flagcdn
const countryList = [
  // Europa
  { name: "Portugal", code: "pt", ddi: "+351", placeholder: "912 345 678" },
  { name: "Espanha", code: "es", ddi: "+34", placeholder: "612 345 678" },
  { name: "França", code: "fr", ddi: "+33", placeholder: "6 12 34 56 78" },
  { name: "Suíça", code: "ch", ddi: "+41", placeholder: "79 123 45 67" },
  { name: "Reino Unido", code: "gb", ddi: "+44", placeholder: "7123 456789" },
  { name: "Alemanha", code: "de", ddi: "+49", placeholder: "151 23456789" },
  { name: "Itália", code: "it", ddi: "+39", placeholder: "312 345 6789" },
  { name: "Bélgica", code: "be", ddi: "+32", placeholder: "470 12 34 56" },
  { name: "Luxemburgo", code: "lu", ddi: "+352", placeholder: "621 123 456" },
  { name: "Holanda", code: "nl", ddi: "+31", placeholder: "6 12345678" },
  { name: "Irlanda", code: "ie", ddi: "+353", placeholder: "83 123 4567" },
  { name: "Áustria", code: "at", ddi: "+43", placeholder: "664 1234567" },
  { name: "Suécia", code: "se", ddi: "+46", placeholder: "70 123 45 67" },
  { name: "Noruega", code: "no", ddi: "+47", placeholder: "412 34 567" },
  { name: "Dinamarca", code: "dk", ddi: "+45", placeholder: "20 12 34 56" },
  { name: "Polônia", code: "pl", ddi: "+48", placeholder: "512 345 678" },

  // Américas
  { name: "Brasil", code: "br", ddi: "+55", placeholder: "(11) 98765-4321" },
  { name: "Estados Unidos", code: "us", ddi: "+1", placeholder: "(555) 123-4567" },
  { name: "Canadá", code: "ca", ddi: "+1", placeholder: "(555) 123-4567" },
  { name: "Argentina", code: "ar", ddi: "+54", placeholder: "11 1234-5678" },
  { name: "México", code: "mx", ddi: "+52", placeholder: "55 1234 5678" },
  { name: "Chile", code: "cl", ddi: "+56", placeholder: "9 1234 5678" },
  { name: "Colômbia", code: "co", ddi: "+57", placeholder: "300 123 4567" },
  { name: "Uruguai", code: "uy", ddi: "+598", placeholder: "99 123 456" },
  { name: "Paraguai", code: "py", ddi: "+595", placeholder: "981 123 456" },

  // Ásia, África & Oceania
  { name: "Japão", code: "jp", ddi: "+81", placeholder: "90 1234 5678" },
  { name: "Angola", code: "ao", ddi: "+244", placeholder: "923 123 456" },
  { name: "Moçambique", code: "mz", ddi: "+258", placeholder: "84 123 4567" },
  { name: "Cabo Verde", code: "cv", ddi: "+238", placeholder: "991 23 45" },
  { name: "Austrália", code: "au", ddi: "+61", placeholder: "412 345 678" },
  { name: "África do Sul", code: "za", ddi: "+27", placeholder: "71 123 4567" }
];

function renderCountries(filter = '') {
  const container = document.getElementById('countries-list');
  if (!container) return;
  const lowerFilter = filter.toLowerCase().trim();
  const filtered = countryList.filter(c => 
    c.name.toLowerCase().includes(lowerFilter) || 
    c.ddi.includes(lowerFilter) || 
    c.code.toLowerCase().includes(lowerFilter)
  );

  if (filtered.length === 0) {
    container.innerHTML = '<div class="text-center py-4 text-xs text-slate-500">Nenhum país encontrado</div>';
    return;
  }

  container.innerHTML = filtered.map(c => `
    <button type="button" onclick="selectCountry('${c.code}', '${c.ddi}', '${c.name.replace(/'/g, "\\'")}', '${c.placeholder}')" class="w-full flex items-center justify-between p-2 rounded-xl hover:bg-slate-800/80 transition text-left group">
      <div class="flex items-center gap-2.5">
        <img src="https://flagcdn.com/w40/${c.code}.png" alt="${c.name}" class="w-5 h-3.5 object-cover rounded shadow-sm flex-shrink-0">
        <span class="text-xs text-slate-200 group-hover:text-white font-medium">${c.name}</span>
      </div>
      <span class="text-xs font-mono font-bold text-amber-400">${c.ddi}</span>
    </button>
  `).join('');
}

function selectCountry(code, ddi, name, placeholder) {
  const flagImg = document.getElementById('selected-flag-img');
  const ddiLabel = document.getElementById('selected-ddi-label');
  const ddiInput = document.getElementById('input-ddi');
  const paisInput = document.getElementById('input-pais');
  const phoneInput = document.getElementById('input-phone');

  if (flagImg) {
    flagImg.src = `https://flagcdn.com/w40/${code}.png`;
    flagImg.alt = name;
  }
  if (ddiLabel) ddiLabel.textContent = ddi;
  if (ddiInput) ddiInput.value = ddi;
  if (paisInput) paisInput.value = name;
  if (phoneInput && placeholder) phoneInput.placeholder = placeholder;

  closeCountryDropdown();
}

function toggleCountryDropdown() {
  const dropdown = document.getElementById('country-dropdown');
  if (!dropdown) return;
  const isHidden = dropdown.classList.contains('hidden');
  if (isHidden) {
    dropdown.classList.remove('hidden');
    const searchInput = document.getElementById('country-search');
    if (searchInput) {
      searchInput.value = '';
      renderCountries();
      setTimeout(() => searchInput.focus(), 50);
    }
  } else {
    dropdown.classList.add('hidden');
  }
}

function closeCountryDropdown() {
  const dropdown = document.getElementById('country-dropdown');
  if (dropdown) dropdown.classList.add('hidden');
}

function filterCountries() {
  const searchInput = document.getElementById('country-search');
  if (searchInput) renderCountries(searchInput.value);
}

document.addEventListener('click', function(e) {
  const dropdown = document.getElementById('country-dropdown');
  const btn = document.getElementById('btn-country-picker');
  if (dropdown && !dropdown.contains(e.target) && btn && !btn.contains(e.target)) {
    closeCountryDropdown();
  }
});

// Inicialização inteligente com base no idioma do visitante
window.addEventListener('DOMContentLoaded', function() {
  captureClientTime();
  renderCountries();

  const lang = (navigator.language || navigator.userLanguage || '').toLowerCase();
  if (lang.includes('br')) {
    selectCountry('br', '+55', 'Brasil', '(11) 98765-4321');
  } else if (lang.includes('es')) {
    selectCountry('es', '+34', 'Espanha', '612 345 678');
  } else if (lang.includes('fr')) {
    selectCountry('fr', '+33', 'França', '6 12 34 56 78');
  } else if (lang.includes('us') || lang.includes('en-us')) {
    selectCountry('us', '+1', 'Estados Unidos', '(555) 123-4567');
  } else {
    selectCountry('pt', '+351', 'Portugal', '912 345 678');
  }

  // Monitorar campos obrigatórios para ativar o botão verde
  const nomeInput = document.querySelector('input[name="nome"]');
  const emailInput = document.querySelector('input[name="email"]');
  const checkboxes = document.querySelectorAll('.product-checkbox');

  if (nomeInput) nomeInput.addEventListener('input', checkRequiredFields);
  if (emailInput) emailInput.addEventListener('input', checkRequiredFields);
  checkboxes.forEach(c => c.addEventListener('change', checkRequiredFields));

  // Validação inicial (caso campos venham pré-preenchidos pelo navegador)
  setTimeout(checkRequiredFields, 120);
});
</script>

</body>
</html>
