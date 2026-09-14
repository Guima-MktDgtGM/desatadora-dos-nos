# 📖 MANUAL COMPLETO DA OFERTA — NOVENA DE NOSSA SENHORA DESATADORA DOS NÓS
> **Documento Mestre de Contexto e Arquitetura**  
> *Idealizado para consulta rápida da equipe e pronto para ser carregado no Claude, ChatGPT ou em qualquer ferramenta de IA para manutenção, geração de criativos, suporte e otimização contínua.*

---

## 1. 📌 VISÃO GERAL & POSICIONAMENTO DA OFERTA

- **Nome da Oferta:** Novena de Nossa Senhora Desatadora dos Nós (*"A Novena das 9 Palavras"*)
- **Nicho:** Espiritualidade Cristã / Catolicismo / Desenvolvimento Pessoal & Fé
- **Subnicho:** Desamarração de Trava Financeira, Dívidas e Cura Familiar
- **Moeda de Faturamento:** Dólar Americano ($ USD) — rodando para tráfego de Portugal e Europa
- **Geolocalização / Avatar Principal:**
  - **Público Primário:** Portugal (PT-PT) e fiéis católicos na Europa (rodando tráfego direto para a Europa com faturamento em Dólar $ USD).
  - **Faixa Etária:** Mulheres e homens de 35 a 65+ anos.
  - **Perfil do Comprador:** Pessoas devotas ou com raízes católicas profundas que estão passando por um momento de grande aperto financeiro, dívidas acumuladas, desavenças na família, sensação de "vida amarrada" ou portas fechadas.
- **Grande Ideia / Gancho (Big Idea):**
  - O Padre Jakob Rem (Baviera, 1615) descobriu a revelação das **"9 Palavras Sagradas"** que acionam a intercessão imediata da Virgem Maria como Desatadora dos Nós.
  - Não é apenas uma oração comum: existe uma ordem litúrgica milenar que primeiro pacifica o coração, desata os nós espirituais e abre as comportas da providência financeira antes do amanhecer.
- **Tom de Voz da Marca (Brand Voice):**
  - Solene, acolhedor, respeitoso, esperançoso e litúrgico.
  - Evita promessas milagrosas rasas de "enriquecimento fácil"; utiliza linguagem bíblica e pastoral de *Providência Divina, Quebra de Maldições Hereditárias, Justiça Divina, Blindagem do Lar e Desamarração*.

---

## 2. 🌐 INFRAESTRUTURA TÉCNICA & HOSPEDAGEM

- **Domínio Oficial:** `https://caminhosdafe.online`
- **Hospedagem:** Hostinger Web Hosting
- **Repositório GitHub:** `https://github.com/Guima-MktDgtGM/desatadora-dos-nos.git`
- **Branch de Produção:** `main`
- **Deploy Contínuo:** Hostinger Git Auto-Deploy ativado. Qualquer `git push origin main` atualiza o site ao vivo em instantes.
- **E-mail Oficial de Suporte / Pastoral:** `gabriel.luz@noticiasdafe.com.br`
- **Stack Tecnológico:**
  - Front-end: HTML5 semântico, Tailwind CSS / CSS puro ultraleve, Lucide Icons, Vanilla JavaScript.
  - PWA: `manifest.json`, `sw.js` (Service Worker com cache inteligente offline).
  - Áudio: Web Audio API nativo do navegador para sintetização de ondas binaurais puras (432Hz e 528Hz) com sleep timer em tempo real.
  - Backend / Roteamento: Apache / PHP com mod_rewrite e regras de segurança.

---

## 3. 🗺️ MAPEAMENTO COMPLETO DO FUNIL & ROTEAMENTO DE URLS

### 🔄 Fluxo do Tráfego:
```text
[Anúncio Meta / Ads]
         │
         ▼
[Cloaker PHP (index.php)]
   ├─► Bot / Revisor / Curioso sem UTM ──► [White TSL Clean: clean.html]
   └─► Lead Real com Parâmetro / Ads ──► [VSL Black: vendas.html]
                                                │
                                                ▼ (Delay ~22:30)
                                     [Checkout Front (KashPay)]
                                                │
                                                ▼
                                    [Upsell 1: /u1/?src=fs2026]
                                      "15 Dias com Maria" ($36.90 USD)
                                                │
                                    (Aceitando ou Recusando)
                                                ▼
                                    [Upsell 2: /u2/?src=fs2026]
                                      "Áudios do Terço" ($46.90 USD)
                                                │
                                    (Aceitando ou Recusando)
                                                ▼
                                    [Página Obrigado: /obrigado/]
                                                │
                                                ▼
                                     [Web App PWA: /app/]
                                       "Caminhos da Fé"
```

---

### 📍 Tabela Detalhada de Páginas e Endereços

| Etapa | Nome da Página | URL do Funil | URL de Teste / Bypass | Função Principal |
|---|---|---|---|---|
| **Gate / Cloaker** | Filtro Inteligente | `https://caminhosdafe.online/` | `?bypass=gl2026` | Analisa User-Agent, fbclid e referrers para entregar Black ou White. |
| **Front VSL (Black)** | Transmissão Sagrada | `https://caminhosdafe.online/vendas.html` | Direto | Página de alta conversão com player VTurb e delay de pitch. |
| **Front TSL (White)** | Devoção Histórica | `https://caminhosdafe.online/clean.html` | Direto | Página institucional e artigo litúrgico aprovado para aprovação de anúncios. |
| **Upsell 1 (U1)** | Guia 15 Dias com Maria | `https://caminhosdafe.online/u1/?src=fs2026` | `https://caminhosdafe.online/u1/?bypass=gl2026` | Proteção e blindagem do lar contra forças contrárias ($36.90 USD). |
| **Upsell 2 (U2)** | Áudios Frequenciais Terço | `https://caminhosdafe.online/u2/?src=fs2026` | `https://caminhosdafe.online/u2/?bypass=gl2026` | Áudios do Terço com frequências de 432Hz/528Hz para dormir ($46.90 USD). |
| **Obrigado** | Agradecimento & Bênção | `https://caminhosdafe.online/obrigado/` | Direto | Imagem de Maria, gratidão pelo ato de caridade/construção e dados de envio. |
| **Web App** | Santuário Caminhos da Fé | `https://caminhosdafe.online/app/` | Direto | Web App PWA completo com novena, vela, orações, áudios e comunidade. |

---

## 4. 💳 CHECKOUTS KASHPAY & REGRAS DE 1-CLICK UPSELL

### Gateway de Pagamento: **KashPay**
- **Processador One-Click:** `https://checkout.kashpay.com.br/scripts/upsell-processor.js`
- **Integração:** Injetado nos cabeçalhos de `/u1/` e `/u2/`.

### Detalhes de Produtos & Checkouts:
1. **Front-End (Novena das 9 Palavras):**
   - **Preço Oficial:** $26.90 USD
   - Configurado no player VTurb / Checkout principal.
   - Entrega: E-mail com boas-vindas e acesso irrestrito ao Web App `/app/`.
2. **Upsell 1 (U1) — Guia 15 Dias com Maria:**
   - **Checkout KashPay:** `https://checkout.kashpay.com.br/u/b99e60b100fb3abe`
   - **Chamada no Botão:** `onclick="acceptUpsell('https://checkout.kashpay.com.br/u/b99e60b100fb3abe')"`
   - **Preço Oficial:** $36.90 USD
3. **Upsell 2 (U2) — Áudios Frequenciais do Terço de Desamarração:**
   - **Checkout KashPay:** `https://checkout.kashpay.com.br/u/a18aafbc3a9f66a8`
   - **Chamada no Botão:** `onclick="acceptUpsell('https://checkout.kashpay.com.br/u/a18aafbc3a9f66a8')"`
   - **Preço Oficial:** $46.90 USD

---

## 5. 📊 TRACKING, META PIXEL & REGRAS DE CONVERSÃO

- **ID Principal do Pixel:** `2782586508793387`
- **Disparo no Site:**
  - Todas as páginas (`vendas.html`, `clean.html`, `u1/`, `u2/`, `obrigado/`, `app/`) disparam exclusivamente o evento padrão:
    ```javascript
    fbq('init', '2782586508793387');
    fbq('track', 'PageView');
    ```
- **Suporte a Múltiplos Pixels Dinâmicos:**
  - Caso seja passado `?pixel=SEU_ID` na URL, os scripts capturam e inicializam automaticamente o novo Pixel.
- **Rastreamento de Conversões (InitiateCheckout & Purchase):**
  - Os eventos de compra e início de finalização são configurados **dentro da plataforma KashPay** (integração nativa de Pixel e CAPI - Conversion API) para evitar disparos duplicados ou discrepâncias com adblockers.
- **Preservação de UTMs:**
  - Todo o funil mantém intactos os parâmetros `utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term` e `src=fs2026` entre as páginas.

---

## 6. 🛡️ CLOAKER & SISTEMA DE BLINDAGEM

### Arquivo `index.php`:
1. **Bypass da Equipe / Análise:** Acesso com `?bypass=gl2026` libera a página de vendas Black imediatamente e salva cookie válido por 7 dias.
2. **Lista Negra de Bots:** Mais de 40 agentes e bots bloqueados (Meta Crawler, Googlebot, Semrush, Ahrefs, Puppeteer, Lighthouse, etc.). Bots são imediatamente redirecionados para a TSL Clean.
3. **Verificação de Tráfego:** Requer `fbclid`, `src=fs2026` ou referrer orgânico legítimo. Acessos diretos avulsos caem na TSL Clean.

---

## 7. 📱 O WEB APP PWA — "CAMINHOS DA FÉ" (`/app/`)

O aplicativo foi projetado com alta retenção, gamificação espiritual e blindagem jurídica contra chargebacks.

### Funcionalidades Implementadas:
1. **Modal de Termos de Uso (1º Acesso Obrigatório):**
   - **Experiência do Usuário:** O botão *"Concordo e Aceito os Termos de Uso"* fica **livre e clicável a qualquer momento** no rodapé do modal (sem bloquear o usuário com exigência de rolagem forçada).
   - **Cláusula de Manutenção Periódica:** O texto legal contém a **Cláusula 3**, estipulando expressamente o direito de realizar cobrança periódica mensal destinada à manutenção de servidores dedicados de alta velocidade, blindagem cibernética contra censura e atualizações contínuas do aplicativo.
   - **Opção de Recusa & Prevenção de Chargeback:** Caso o lead clique em *"Não aceito os termos"*, a tela do app é bloqueada, exibindo uma mensagem acolhedora com o e-mail oficial (`gabriel.luz@noticiasdafe.com.br`) para devolução de 100% do valor sem perguntas nem burocracia.
   - **Persistência:** Uma vez aceito, salva `novena_terms_accepted = 'true'` no `localStorage`.
2. **Instalação do PWA (Home Screen):**
   - Modal tutorial customizado para iPhone (Safari: Compartilhar ➔ Adicionar à Tela de Início) e Android (Google Chrome: Adicionar ao Ecrã Principal).
3. **Guia Completo da Novena:**
   - Modal com passo a passo ilustrado: Como preparar o ambiente de oração, acender a vela virtual, ouvir a frequência harmônica e meditar nos nós pessoais.
4. **9 Dias de Orações Litúrgicas Densas:**
   - Cada dia possui oração completa com 4 a 6 minutos de leitura reflexiva, meditações do Padre Jakob Rem, invocação ao Espírito Santo e petição para quebra de nós específicos (Dívidas, Família, Discórdia, Inveja, Saúde, Prosperidade).
5. **Gerador de Frequências Sagradas (432Hz & 528Hz):**
   - Sintetizador puro em tempo real via Web Audio API.
   - Sleep Timer ajustável (15, 30, 45, 60 minutos ou contínuo) que desvanece o volume ao encerrar.
6. **Vela Virtual no Altar & Streak Diário:**
   - Contador de dias consecutivos em chamas. Se o usuário esquecer um dia, ele é incentivado a reiniciar o ciclo de 9 dias.
7. **Livro de Nós Pessoais:**
   - Cadastro interativo de intenções particulares com botão de desatar e animação de vitória.
8. **Comunidade de Fiéis & Bots Inteligentes:**
   - Feed dinâmico com relatos de fiéis de Portugal e do Brasil.
   - **Interação Automática Realista:** Quando o usuário posta qualquer comentário, oração ou desabafo no feed, a comunidade de bots responde realisticamente após 6 segundos com mensagens calorosas de acolhimento e bênção.
   - **Notificações Flutuantes Espaçadas:** Avisos no rodapé com testemunhos de graças alcançadas surgindo com intervalo orgânico entre 90 e 150 segundos.

---

## 8. ✉️ E-MAILS TRANSACIONAIS & COMUNICAÇÃO DE ACESSO

### E-mail 1: Boas-Vindas & Acesso Imediato (Pós-Compra do Front)
- **Assunto:** [Acesso Sagrado] A sua contribuição foi acolhida com bênçãos
- **Mensagem-chave:** Agradece calorosamente ao benfeitor. Explica que o valor investido não foi uma perda, mas sim uma contribuição sagrada que viabilizou a difusão da novena para milhares de irmãos em desespero e apoia a edificação da obra.
- **Link de Acesso Direto:** `https://caminhosdafe.online/app/`

### E-mail 2: Liberação de Acesso para Compradores Anteriores (Espera)
- **Assunto:** O seu acesso ao Santuário Sagrado foi restabelecido e blindado
- **Mensagem-chave:** Esclarece que os servidores sofreram tentativas de instabilidade e ataques promovidos por interesses contrários à mensagem de libertação financeira, mas a blindagem criptográfica foi concluída com sucesso. Libera o acesso prioritário imediato.
- **Link de Acesso:** `https://caminhosdafe.online/app/`

---

## 9. 🚀 COMANDOS ÚTEIS PARA DESENVOLVIMENTO & ATUALIZAÇÕES

- **Para clonar o projeto:**
  ```bash
  git clone https://github.com/Guima-MktDgtGM/desatadora-dos-nos.git
  ```
- **Para subir qualquer alteração para o ar (Deploy Automático):**
  ```bash
  git add .
  git commit -m "feat: sua atualizacao aqui"
  git push origin main
  ```
- **Para testar páginas ignorando o cloaker:**
  - VSL Front: `https://caminhosdafe.online/vendas.html`
  - Upsell 1: `https://caminhosdafe.online/u1/?bypass=gl2026`
  - Upsell 2: `https://caminhosdafe.online/u2/?bypass=gl2026`
  - App: `https://caminhosdafe.online/app/`

---
*Documento gerado e mantido pela equipe de engenharia e direct response Caminhos da Fé.*
