<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>C.D.U — Sua Cidade, Seu Cuidado</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

  <div class="container">
    <header>
      <nav>
        <div class="logo">
          <img
            src="{{ Vite::asset('resources/images/logo_invert.svg') }}"
            alt="Logo" class="logo-img">
        </div>
        <div class="nav-links">
          <a href="#inicio">Início</a>
          <a href="#como-funciona">Como Funciona</a>
          <a href="#sobre">Sobre</a>
        </div>
        <a href="{{ route('register') }}" class="btn-cadastrar">Cadastrar</a>
      </nav>
    </header>

    <!-- HERO -->
    <section class="hero" id="inicio">
      <div class="hero-grid">
        <div>
          <h1>Sua Cidade,<br><span class="accent">Seu Cuidado.</span></h1>
          <p>Ajude a tornar sua cidade melhor. Registre problemas urbanos do seu bairro e acompanhe a resolução.</p>
          <div class="hero-buttons">
            <a href="{{ route('login') }}" class="btn-primary">Denunciar</a>
            <a href="#como-funciona" class="link-secondary">Saiba Mais</a>
          </div>
        </div>

        <div class="map-card">
          <img
            src="{{ Vite::asset('resources/images/map-homepage.png') }}"
            alt="Mapa de ocorrências" class="map-img">
        </div>
      </div>

      <div class="stats">
        <div class="stat">
          <div class="num">1393</div>
          <div class="label">Denúncias</div>
        </div>
        <div class="stat">
          <div class="num">89%</div>
          <div class="label">Resolvidas</div>
        </div>
        <div class="stat">
          <div class="num">42</div>
          <div class="label">Bairros</div>
        </div>
      </div>
    </section>

    <!-- COMO FUNCIONA -->
    <section id="como-funciona">
      <div class="section-head">
        <h2>Como funciona</h2>
        <p>Simples, rápido e transparente.</p>
      </div>

      <div class="steps">
        <div class="step">
          <div class="icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M4 8h3l2-2h6l2 2h3v11H4z" />
              <circle cx="12" cy="13" r="3.5" />
            </svg>
          </div>
          <h3>1. Registre</h3>
          <p>Tire uma foto e descreva o problema encontrado.</p>
        </div>
        <div class="step">
          <div class="icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <circle cx="10.5" cy="10.5" r="6.5" />
              <line x1="20" y1="20" x2="15.2" y2="15.2" />
            </svg>
          </div>
          <h3>2. Análise</h3>
          <p>A equipe avalia e encaminha pro órgão responsável.</p>
        </div>
        <div class="step">
          <div class="icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M12 21s7-6.3 7-11.5A7 7 0 105 9.5C5 14.7 12 21 12 21z" />
              <circle cx="12" cy="9.5" r="2.3" />
            </svg>
          </div>
          <h3>3. Acompanhe</h3>
          <p>Veja o status atualizado em tempo real.</p>
        </div>
        <div class="step">
          <div class="icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
              stroke-linejoin="round">
              <polyline points="4,12.5 9,17.5 20,6.5" />
            </svg>
          </div>
          <h3>4. Resolva</h3>
          <p>Receba um aviso assim que for resolvido.</p>
        </div>
      </div>

      <hr class="divider">

      <div class="section-head">
        <h2>O que dizem os moradores</h2>
        <p>Quem já usou pra melhorar o próprio bairro.</p>
      </div>

      <div class="testimonials">
        <div class="t-card">
          <span class="quote-mark">"</span>
          <p class="quote">Consegui registrar um buraco na rua em menos de um minuto e acompanhei toda a resolução pelo
            próprio site.</p>
          <div class="t-person">
            <div class="avatar">MS</div>
            <div>
              <div class="name">Marina Silva</div>
              <div class="place">Jardim das Flores</div>
            </div>
          </div>
        </div>

        <div class="t-card">
          <span class="quote-mark">"</span>
          <p class="quote">A poda da árvore da minha rua foi resolvida em uma semana depois que registrei por aqui.</p>
          <div class="t-person">
            <div class="avatar">CA</div>
            <div>
              <div class="name">Carlos Andrade</div>
              <div class="place">Vila Nova</div>
            </div>
          </div>
        </div>

        <div class="t-card">
          <span class="quote-mark">"</span>
          <p class="quote">Finalmente um jeito fácil de cobrar melhorias no bairro sem precisar de burocracia.</p>
          <div class="t-person">
            <div class="avatar">RC</div>
            <div>
              <div class="name">Renata Costa</div>
              <div class="place">Síndica, Cond. Bela Vista</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

</body>

</html>