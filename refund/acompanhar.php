<?php
require_once __DIR__ . '/config.php';

$protocol = trim($_GET['p'] ?? '');
$pedido = null;

if (!empty($protocol)) {
    $pedido = findPedido($protocol);
}

// Cálculo dinâmico das etapas e percalços com base no tempo decorrido
$diasDecorridos = 0;
$progresso = 25;
$faseTitulo = "Acolhimento Pastoral e Entrada na Tesouraria";
$faseDescricao = "O seu pedido de restituição foi acolhido pelo Padre Elias e registrado na nossa tesouraria. A equipe iniciou o procedimento de desvinculação da oferta. Prazo padrão bancário previsto: 3 a 7 dias úteis.";

if ($pedido) {
    $diasDecorridos = floor((time() - $pedido['createdAt']) / 86400);

    if ($diasDecorridos <= 2) {
        $progresso = 25;
        $faseTitulo = "Acolhimento Pastoral e Entrada na Tesouraria";
        $faseDescricao = "O seu pedido de restituição foi acolhido pelo Padre Elias e registrado na nossa tesouraria. A equipe iniciou o procedimento de desvinculação da oferta. Prazo padrão bancário previsto: 3 a 7 dias úteis.";
    } elseif ($diasDecorridos >= 3 && $diasDecorridos <= 6) {
        $progresso = 50;
        $faseTitulo = "Aguardando Confirmação do Adquirente Internacional";
        $faseDescricao = "A tesouraria paroquial enviou a ordem de cancelamento da transação ao processador bancário. O adquirente internacional está a realizar a conferência do lote para validar a reversão do valor da doação.";
    } elseif ($diasDecorridos >= 7 && $diasDecorridos <= 13) {
        $progresso = 70;
        $faseTitulo = "Esclarecimento de Destinação Social Solicitado pelo Banco";
        $faseDescricao = "O emissor bancário solicitou documentação comprobatória sobre a desvinculação dos fundos já alocados na compra de materiais da capela e alimentos. A nossa assessoria já respondeu aos esclarecimentos e aguarda a resposta final da operadora.";
    } else {
        $progresso = 85;
        $faseTitulo = "Fase Final de Liquidação Contábil";
        $faseDescricao = "A solicitação foi aceita no sistema bancário e encontra-se na fila de compensação final da bandeira do cartão. O valor será creditado no mesmo meio de pagamento assim que a compensação interbancária for liberada.";
    }

    if ($pedido['status'] === 'estornado') {
        $progresso = 100;
        $faseTitulo = "Restituição Concluída com Sucesso";
        $faseDescricao = "O valor da sua oferta foi estornado pela nossa paróquia junto à operadora do cartão. Conforme a política do seu banco, o crédito aparecerá na sua fatura.";
    } elseif ($pedido['status'] === 'retido') {
        $progresso = 100;
        $faseTitulo = "Oferta Mantida sob as Bênçãos do Altar";
        $faseDescricao = "Após atendimento pastoral individual, o devoto optou por manter o seu amparo às famílias e à construção da capela. As bênçãos continuam ativas.";
    }
}

// Montagem do link para o WhatsApp do suporte
$waNumber = WHATSAPP_SUPPORT_NUMBER;
$waMsg = "A paz de Cristo, Padre Elias e equipe pastoral! Acabei de verificar o andamento do meu protocolo " . ($pedido ? $pedido['protocol'] : '') . " e gostaria de falar com vocês sobre a minha solicitação.";
$waLink = "https://wa.me/" . $waNumber . "?text=" . rawurlencode($waMsg);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acompanhamento de Protocolo — Comunidade Caminhos da Fé</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body {
    background-color: #070D1E;
    color: #E2E8F0;
    font-family: 'Montserrat', sans-serif;
  }
  .font-cinzel { font-family: 'Cinzel', serif; }
  .font-serif { font-family: 'Playfair Display', Georgia, serif; }
  .gold-border { border-color: rgba(212, 175, 55, 0.35); }
  .gold-btn {
    background: linear-gradient(135deg, #D4AF37 0%, #AA820A 100%);
    color: #070D1E;
    font-weight: 700;
  }
</style>
</head>
<body class="min-h-screen py-8 px-4 flex flex-col justify-between">

<div class="max-w-xl mx-auto w-full space-y-6">

  <!-- Cabeçalho -->
  <div class="text-center space-y-2">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-950/60 border gold-border text-amber-300 text-xs font-semibold">
      <span>⚖️ Painel de Transparência Pastoral</span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-wide">
      Acompanhamento de Solicitação
    </h1>
    <p class="text-xs text-slate-300">
      Verifique o status em tempo real da desvinculação da sua oferta
    </p>
  </div>

  <?php if (!$pedido): ?>
  <!-- Caso não tenha passado protocolo válido -->
  <div class="bg-[#0B152E] border gold-border rounded-2xl p-6 shadow-2xl space-y-4 text-center">
    <p class="text-xs text-slate-300">
      Por favor, informe o número do seu protocolo pastoral para consultar o andamento:
    </p>
    <form method="GET" class="flex gap-2 max-w-sm mx-auto">
      <input type="text" name="p" placeholder="Ex: CP-9482" class="flex-1 bg-[#070D1E] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
      <button type="submit" class="px-4 py-2.5 gold-btn rounded-xl text-xs font-bold">Consultar</button>
    </form>
    <div class="pt-2">
      <a href="solicitar.php" class="text-xs text-amber-300 hover:underline">Ainda não registrou a sua solicitação? Clique aqui</a>
    </div>
  </div>
  <?php else: ?>

  <!-- Card do Protocolo -->
  <div class="bg-[#0B152E]/95 border gold-border rounded-3xl p-5 sm:p-7 shadow-2xl space-y-5">
    
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
      <div>
        <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Número de Protocolo</span>
        <h2 class="text-xl font-cinzel font-black text-amber-300"><?= htmlspecialchars($pedido['protocol']) ?></h2>
      </div>
      <div class="text-right">
        <span class="text-[10px] text-slate-400 block font-medium">Registrado em</span>
        <span class="text-xs font-bold text-slate-200"><?= date('d/m/Y - H:i', $pedido['createdAt']) ?></span>
      </div>
    </div>

    <!-- Informações do Devoto -->
    <div class="grid grid-cols-2 gap-3 text-xs bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800/80">
      <div>
        <span class="text-[10px] text-slate-400 block">Devoto(a):</span>
        <strong class="text-white"><?= htmlspecialchars($pedido['nome']) ?></strong>
      </div>
      <div>
        <span class="text-[10px] text-slate-400 block">Contribuição:</span>
        <strong class="text-amber-300 text-[11px] truncate block"><?= htmlspecialchars($pedido['produto']) ?></strong>
      </div>
      <div>
        <span class="text-[10px] text-slate-400 block">Canal Preferencial:</span>
        <span class="font-bold <?= $pedido['canal'] === 'whatsapp' ? 'text-emerald-400' : 'text-slate-300' ?>">
          <?= $pedido['canal'] === 'whatsapp' ? '🟢 WhatsApp' : '✉️ E-mail' ?>
        </span>
      </div>
      <div>
        <span class="text-[10px] text-slate-400 block">Tempo Decorrido:</span>
        <span class="text-slate-300 font-bold"><?= $diasDecorridos ?> dia(s)</span>
      </div>
    </div>

    <!-- Barra de Progresso do Protocolo -->
    <div class="space-y-2 pt-2">
      <div class="flex justify-between items-center text-xs">
        <span class="font-bold text-amber-300 flex items-center gap-1.5 font-cinzel">
          <span>Status do Trâmite Bancário</span>
        </span>
        <span class="text-xs font-black text-amber-300"><?= $progresso ?>%</span>
      </div>

      <div class="w-full bg-slate-950 rounded-full h-3 p-0.5 border border-slate-800 overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-amber-300 h-full rounded-full transition-all duration-700" style="width: <?= $progresso ?>%"></div>
      </div>
    </div>

    <!-- Caixa da Etapa Atual (Com os percalços bancários detalhados) -->
    <div class="bg-gradient-to-r from-slate-900 to-[#0e1a38] border border-amber-500/30 rounded-2xl p-4 space-y-2">
      <div class="flex items-center gap-2 text-amber-300 text-xs font-bold font-cinzel">
        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
        <span><?= $faseTitulo ?></span>
      </div>
      <p class="text-xs text-slate-300 leading-relaxed text-justify">
        <?= $faseDescricao ?>
      </p>
    </div>

    <!-- Etapas do Processo -->
    <div class="space-y-3 pt-2 text-xs">
      <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Histórico de Movimentação</h4>

      <div class="space-y-2.5 border-l-2 border-slate-800 ml-2 pl-4">
        
        <div class="relative">
          <span class="absolute -left-[23px] top-1 w-3 h-3 rounded-full bg-emerald-500 border-2 border-[#0B152E]"></span>
          <p class="font-bold text-slate-200">1. Entrada no Protocolo Pastoral</p>
          <p class="text-[11px] text-slate-400">Solicitação registrada pelo devoto e encaminhada para a triagem.</p>
        </div>

        <div class="relative">
          <span class="absolute -left-[23px] top-1 w-3 h-3 rounded-full <?= $progresso >= 50 ? 'bg-emerald-500' : 'bg-slate-700' ?> border-2 border-[#0B152E]"></span>
          <p class="font-bold <?= $progresso >= 50 ? 'text-slate-200' : 'text-slate-500' ?>">2. Notificação e Envio ao Adquirente Bancário</p>
          <p class="text-[11px] text-slate-400">Ordem de estorno emitida para a processadora de pagamentos.</p>
        </div>

        <div class="relative">
          <span class="absolute -left-[23px] top-1 w-3 h-3 rounded-full <?= $progresso >= 70 ? 'bg-emerald-500' : 'bg-slate-700' ?> border-2 border-[#0B152E]"></span>
          <p class="font-bold <?= $progresso >= 70 ? 'text-slate-200' : 'text-slate-500' ?>">3. Parecer e Conciliação de Doação Social</p>
          <p class="text-[11px] text-slate-400">Verificação de alocação de recursos e retorno aos questionamentos da operadora.</p>
        </div>

        <div class="relative">
          <span class="absolute -left-[23px] top-1 w-3 h-3 rounded-full <?= $progresso >= 100 ? 'bg-emerald-500' : 'bg-slate-700' ?> border-2 border-[#0B152E]"></span>
          <p class="font-bold <?= $progresso >= 100 ? 'text-slate-200' : 'text-slate-500' ?>">4. Liquidação e Devolução em Conta</p>
          <p class="text-[11px] text-slate-400">Compensação bancária final da bandeira do cartão.</p>
        </div>

      </div>
    </div>

    
    <!-- ALERTA DE SEGURANÇA SOBRE NÃO ABRIR CONTESTAÇÃO BANCÁRIA -->
    <div class="bg-red-950/20 border border-red-500/30 rounded-2xl p-4 space-y-1.5 text-xs">
      <div class="flex items-center gap-2 text-red-300 font-bold">
        <span>⚠️</span>
        <span class="font-cinzel text-[11px] uppercase tracking-wider">Atenção com o seu banco:</span>
      </div>
      <p class="text-[11px] text-slate-300 leading-relaxed text-justify">
        Por favor, <strong>não abra uma contestação paralela no aplicativo do seu banco enquanto este protocolo estiver em curso</strong>. Como as doações são repassadas a projetos sociais, quando o banco intervém, o sistema oficial é bloqueado e o prazo salta para <strong>20 a 30 dias úteis de auditoria bancária</strong>. Aguarde a nossa liquidação direta ou fale connosco pelo botão abaixo.
      </p>
    </div>

    <!-- BOTÃO DIRETO DO WHATSAPP -->
    <div class="pt-3 space-y-2.5">
      <a href="<?= $waLink ?>" target="_blank" class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-lg transition">
        <span class="text-base">🟢</span>
        <span>Falar no WhatsApp sobre o meu Protocolo</span>
      </a>

      <p class="text-[10px] text-slate-400 text-center leading-relaxed">
        Guarde o link desta página para acompanhar as atualizações diárias do seu trâmite pastoral.
      </p>
    </div>

  </div>

  <?php endif; ?>

</div>

<footer class="text-center text-[11px] text-slate-500 py-6">
  Comunidade Caminhos da Fé · Obras Sociais de Nossa Senhora Desatadora dos Nós<br>
  Apoio ao devoto: <a href="mailto:gabriel.luz@noticiasdafe.com.br" class="text-slate-400 underline">gabriel.luz@noticiasdafe.com.br</a>
</footer>

</body>
</html>
