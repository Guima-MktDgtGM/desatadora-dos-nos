<?php
require_once __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $canal = trim($_POST['canal'] ?? 'whatsapp');
    $produto = trim($_POST['produto'] ?? 'Guia de Proteção Espiritual 15 Dias com Maria');
    $motivo = trim($_POST['motivo'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if (empty($nome) || empty($email) || empty($whatsapp)) {
        $error = 'Por favor, preencha o seu nome, e-mail e número de WhatsApp para localizarmos o seu registro.';
    } else {
        $pedidos = loadPedidos();
        $protocol = 'CP-' . rand(1000, 9999);
        
        $novoPedido = [
            'id' => uniqid('ref_'),
            'protocol' => $protocol,
            'createdAt' => time(),
            'nome' => $nome,
            'email' => $email,
            'whatsapp' => preg_replace('/[^0-9]/', '', $whatsapp),
            'canal' => $canal,
            'produto' => $produto,
            'motivo' => $motivo,
            'mensagem' => $mensagem,
            'status' => 'analise', // analise, retido, estornado, cancelado
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
<title>Atendimento Pastoral e Restituição — Comunidade Caminhos da Fé</title>
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
    transition: all 0.2s ease;
  }
  .gold-btn:hover {
    background: linear-gradient(135deg, #E6C35C 0%, #BF9417 100%);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(212, 175, 55, 0.25);
  }
</style>
</head>
<body class="min-h-screen py-8 px-4 flex flex-col justify-between">

<div class="max-w-xl mx-auto w-full space-y-6">
  
  <!-- Topo Solene -->
  <div class="text-center space-y-2">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-950/60 border gold-border text-amber-300 text-xs font-semibold">
      <span>🕊️ Atendimento Pastoral Fraterno</span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-wide">
      Solicitação de Restituição da Oferta
    </h1>
    <p class="text-xs sm:text-sm text-slate-300 max-w-md mx-auto leading-relaxed">
      Comunidade Caminhos da Fé & Obras Sociais de Nossa Senhora Desatadora dos Nós
    </p>
  </div>

  <!-- Carta do Padre Elias -->
  <div class="bg-[#0B152E]/90 border gold-border rounded-2xl p-5 sm:p-6 shadow-2xl space-y-4 text-xs sm:text-sm leading-relaxed text-slate-200">
    <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
      <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-amber-400/60 flex-shrink-0 shadow">
        <img src="https://caminhosdafe.online/images/padre-elias.jpg" alt="Padre Elias" class="w-full h-full object-cover">
      </div>
      <div>
        <h3 class="font-cinzel font-bold text-amber-300 text-sm">Padre Elias</h3>
        <span class="text-[11px] text-slate-400">Pároco e Guardião do Santuário</span>
      </div>
    </div>

    <p class="text-justify font-serif italic text-slate-300">
      "Irmão(ã) querido(a), a paz de Cristo esteja convosco. A sua contribuição foi acolhida com amor e direcionada à compra de materiais para a construção da nossa capela e socorro das famílias mais vulneráveis."
    </p>

    <p class="text-justify">
      <strong>É óbvio que nós devolveremos a sua oferta</strong> se este valor fizer falta na mesa da sua família ou se ocorreu qualquer engano. <em>Jamais iríamos querer ficar com algo que você não estivesse entregando de coração inteiramente aberto</em> para socorrer o próximo — mesmo sabendo que Deus multiplica a generosidade de quem ajuda.
    </p>

    <div class="bg-amber-950/30 border border-amber-500/30 rounded-xl p-3 text-[11px] text-amber-200 flex items-start gap-2">
      <span class="text-base">⏳</span>
      <span>
        <strong>Prazo previsto de liquidação:</strong> O processo de desvinculação da oferta e estorno bancário é estimado inicialmente de <strong>3 a 7 dias úteis</strong>, enquanto os lançamentos são regularizados pela nossa tesouraria.
      </span>
    </div>
  </div>

  <?php if ($error): ?>
  <div class="bg-red-950/80 border border-red-500 text-red-200 text-xs rounded-xl p-3.5 text-center font-medium">
    <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <!-- Formulário de Solicitação -->
  <form method="POST" class="bg-[#0B152E]/90 border gold-border rounded-2xl p-5 sm:p-6 shadow-2xl space-y-4">
    
    <div class="space-y-1">
      <label class="block text-xs font-bold text-amber-200">O seu Nome Completo:</label>
      <input type="text" name="nome" required placeholder="Como consta na sua contribuição" class="w-full bg-[#070D1E] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-400">
    </div>

    <div class="space-y-1">
      <label class="block text-xs font-bold text-amber-200">O seu E-mail da compra:</label>
      <input type="email" name="email" required placeholder="exemplo@email.com" class="w-full bg-[#070D1E] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-400">
    </div>

    <div class="space-y-1">
      <label class="block text-xs font-bold text-amber-200">Número de WhatsApp (com código do país/DDD):</label>
      <input type="text" name="whatsapp" required placeholder="Ex: +351 912 345 678 ou (11) 99999-9999" class="w-full bg-[#070D1E] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-400">
    </div>

    <!-- Canal de Preferência -->
    <div class="space-y-1.5 pt-2">
      <label class="block text-xs font-bold text-amber-200">Como prefere ser contactado(a) pela nossa equipe pastoral?</label>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-emerald-500/40 bg-emerald-950/20 cursor-pointer">
          <input type="radio" name="canal" value="whatsapp" checked class="text-emerald-500 focus:ring-0">
          <span class="text-xs text-emerald-300 font-semibold">🟢 Por WhatsApp (Mais rápido)</span>
        </label>
        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-700 bg-slate-900/40 cursor-pointer">
          <input type="radio" name="canal" value="email" class="text-amber-500 focus:ring-0">
          <span class="text-xs text-slate-300">✉️ Por E-mail</span>
        </label>
      </div>
    </div>

    <!-- Qual contribuição deseja restituir -->
    <div class="space-y-1.5 pt-2">
      <label class="block text-xs font-bold text-amber-200">Qual foi a contribuição realizada?</label>
      <select name="produto" class="w-full bg-[#070D1E] border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
        <option value="Guia de Proteção Espiritual 15 Dias com Maria (€36,90)">Guia de Proteção Espiritual 15 Dias com Maria (€36,90)</option>
        <option value="Áudios Frequenciais: Terço Milagroso Noturno (€46,90)">Áudios Frequenciais: Terço Milagroso Noturno (€46,90)</option>
        <option value="Novena das 9 Palavras Sagradas (€16,90 / €26,90)">Novena das 9 Palavras Sagradas (€16,90 / €26,90)</option>
        <option value="Todas as contribuições">Todas as contribuições realizadas</option>
      </select>
    </div>

    <!-- Motivo da Solicitação com Interceptação Inteligente -->
    <div class="space-y-2 pt-2">
      <label class="block text-xs font-bold text-amber-200">Qual é o motivo do seu pedido?</label>
      
      <div class="space-y-2 text-xs">
        <label class="flex items-start gap-2 p-2.5 rounded-xl border border-slate-800 bg-[#070D1E] hover:border-amber-400/50 cursor-pointer transition" onclick="checkInterception(this, 'app')">
          <input type="radio" name="motivo" value="app_access" class="mt-0.5 text-amber-400">
          <span class="text-slate-300 font-medium leading-relaxed">Não consegui aceder ao aplicativo / Não encontrei o conteúdo dos 15 Dias</span>
        </label>

        <label class="flex items-start gap-2 p-2.5 rounded-xl border border-slate-800 bg-[#070D1E] hover:border-amber-400/50 cursor-pointer transition" onclick="checkInterception(this, 'financial')">
          <input type="radio" name="motivo" value="Tive um aperto financeiro imprevisto na minha família" checked class="mt-0.5 text-amber-400">
          <span class="text-slate-300 font-medium leading-relaxed">Tive um aperto financeiro imprevisto na minha família</span>
        </label>

        <label class="flex items-start gap-2 p-2.5 rounded-xl border border-slate-800 bg-[#070D1E] hover:border-amber-400/50 cursor-pointer transition" onclick="checkInterception(this, 'mistake')">
          <input type="radio" name="motivo" value="Foi um engano de valor no momento da contribuição" class="mt-0.5 text-amber-400">
          <span class="text-slate-300 font-medium leading-relaxed">Foi um engano de valor no momento da contribuição</span>
        </label>

        <label class="flex items-start gap-2 p-2.5 rounded-xl border border-slate-800 bg-[#070D1E] hover:border-amber-400/50 cursor-pointer transition" onclick="checkInterception(this, 'faith')">
          <input type="radio" name="motivo" value="Não desejo mais manter o meu nome sob as orações do altar" class="mt-0.5 text-amber-400">
          <span class="text-slate-300 font-medium leading-relaxed">Não desejo mais manter o meu nome sob as orações do altar</span>
        </label>
      </div>
    </div>

    <!-- Mensagem Pessoal -->
    <div class="space-y-1 pt-1">
      <label class="block text-xs font-bold text-amber-200">Mensagem para o Padre Elias e equipe (Opcional):</label>
      <textarea name="mensagem" rows="2" placeholder="Conte-nos brevemente o que aconteceu..." class="w-full bg-[#070D1E] border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-400 resize-none"></textarea>
    </div>

    <!-- Botão de Fricção Máxima -->
    <div class="pt-3">
      <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-600 text-xs font-bold text-slate-200 hover:text-white transition leading-snug text-center">
        Quero retirar a minha doação da Capela e abrir mão da bênção consagrada
      </button>
      <p class="text-[10px] text-slate-500 text-center mt-2">
        Ao enviar, um protocolo pastoral será gerado para acompanhamento contínuo da sua solicitação.
      </p>
    </div>

  </form>

</div>

<!-- MODAL DE DESVIO IMEDIATO PARA QUEM NÃO CONSEGUIU ENTRAR NO APP -->
<div id="modal-app-bypass" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md hidden flex items-center justify-center p-4">
  <div class="bg-[#0B152E] border-2 border-amber-400/60 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 text-center animate-in zoom-in duration-200">
    <div class="w-14 h-14 rounded-2xl bg-amber-500/20 text-amber-300 flex items-center justify-center mx-auto text-2xl border border-amber-400/40">
      ✨
    </div>
    
    <div class="space-y-1">
      <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-400 bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-500/30">Acesso 100% Liberado</span>
      <h3 class="text-lg font-serif font-bold text-white">O seu Guia de 15 Dias já está ativo!</h3>
      <p class="text-xs text-slate-300 leading-relaxed">
        Irmão(ã), não precisa de passar por burocracias de estorno nem cancelar a sua bênção por causa de acesso! O seu aplicativo já está desbloqueado com todos os 15 roteiros prontos.
      </p>
    </div>

    <div class="pt-2 space-y-2">
      <a href="https://caminhosdafe.online/app/?guia=1" target="_blank" class="w-full py-3.5 gold-btn rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg block">
        <span>👉 Entrar no Meu Aplicativo Agora</span>
      </a>

      <button type="button" onclick="closeAppModal()" class="w-full py-2 text-[11px] text-slate-400 hover:text-white underline block">
        Ainda assim, quero prosseguir com a devolução da oferta
      </button>
    </div>
  </div>
</div>

<footer class="text-center text-[11px] text-slate-500 py-6">
  Comunidade Caminhos da Fé · Obras Sociais de Nossa Senhora Desatadora dos Nós<br>
  Apoio ao devoto: <a href="mailto:gabriel.luz@noticiasdafe.com.br" class="text-slate-400 underline">gabriel.luz@noticiasdafe.com.br</a>
</footer>

<script>
function checkInterception(el, type) {
  if (type === 'app') {
    document.getElementById('modal-app-bypass').classList.remove('hidden');
  }
}

function closeAppModal() {
  document.getElementById('modal-app-bypass').classList.add('hidden');
}
</script>

</body>
</html>
