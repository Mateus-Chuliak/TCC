<!doctype html>
<html lang="pt-br">
<head>
    <!-- Define codificação UTF-8 para compatibilidade com acentuação -->
    <meta charset="utf-8">

    <!-- Garante responsividade adequada em dispositivos móveis -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Título exibido na aba do navegador -->
    <title>ZéFish - Pesqueiro</title>

    <!-- Ícone exibido na aba do navegador -->
    <link rel="icon" href="assets/img/fav_icon.png">

    <!-- Estilos base do framework Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Biblioteca de ícones Font Awesome -->
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>

    <!-- Estilos customizados da aplicação -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<!-- Classe controla estilo visual de fundo -->
<body class="granulado">

    <!-- ======================== HEADER ======================== -->
    <!-- Cabeçalho fixo com navegação principal -->
    <header>
        <nav id="navbarHeader" class="navbar navbar-expand-xl fixed-top navbar-light navbar-transparente">
            <div class="container">

                <!-- Logotipo com redirecionamento para a home -->
                <a href="index.php" class="navbar-brand">
                    <img src="assets/img/logo.png" alt="Logotipo ZéFish" width="115" class="img-fluid">
                </a>

                <!-- Botão responsivo para menu colapsável -->
                <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-principal"
                        aria-controls="nav-principal" aria-expanded="false" aria-label="Alternar navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Menu principal de navegação -->
                <div class="collapse navbar-collapse" id="nav-principal">
                    <ul class="navbar-nav">

                        <!-- Dropdown contendo opções relacionadas à pescaria -->
                        <li class="nav-item dropdown">
                            <a href="/Zéfish/templates/views/Site/pescaria.php"
                               class="nav-link dropdown-toggle"
                               id="dropdownPescaria"
                               data-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false">
                                <i class="fa fa-anchor pr-4"></i>Pescaria
                            </a>

                            <!-- Links internos do submenu -->
                            <div class="dropdown-menu" aria-labelledby="dropdownPescaria">
                                <a class="dropdown-item" href="/Zéfish/templates/views/Site/reserva.php">Faça sua reserva</a>
                                <a class="dropdown-item" href="/Zéfish/templates/views/Site/pescaria.php">Tipos de pesca</a>
                                <a class="dropdown-item" href="/Zéfish/templates/views/Site/descricao.php">Nossos Peixes</a>
                            </div>
                        </li>

                        <!-- Acesso à página de cardápio -->
                        <li class="nav-item">
                            <a href="templates/views/Site/cardapio.php" class="nav-link">
                                <i class="fa fa-cutlery pr-4"></i>Gastronomia
                            </a>
                        </li>

                        <!-- Acesso à área de eventos -->
                        <li class="nav-item">
                            <a href="/Zéfish/templates/views/Site/eventos.php" class="nav-link">
                                <i class="fa fa-calendar pr-4"></i>Eventos
                            </a>
                        </li>

                        <!-- Link para área da comunidade -->
                        <li class="nav-item">
                            <a href="/Zéfish/templates/views/Site/comunidade.php" class="nav-link">
                                <i class="fa fa-users pr-4"></i>Comunidade
                            </a>
                        </li>

                        <!-- Página com regras do pesqueiro -->
                        <li class="nav-item">
                            <a href="templates/views/Site/regras.php" class="nav-link">
                                <i class="fa fa-exclamation-triangle pr-4"></i>Regras
                            </a>
                        </li>

                        <!-- Separador visual entre menu e usuário -->
                        <li class="divisor"></li>

                        <!-- Acesso ao painel de login -->
                        <li class="nav-item d-flex align-items-center white header-user">
                            <a href="templates/views/Admin/login.php" class="nav-link" title="Acessar conta">
                                <i class="fa fa-user-circle fa-2x"></i>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- ======================== HOME / CARROSSEL ======================== -->
    <main>
        <!-- Seção inicial com carrossel de destaque -->
        <section id="home" class="d-flex">
            <div class="container align-self-center">
                <div class="row">
                    <div class="col-md-12 text-center">

                        <!-- Carrossel principal de chamadas -->
                        <div id="carousel-entrada" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">

                                <!-- Slide inicial com chamada principal -->
                                <div class="carousel-item active">
                                    <h1>Venha nos conhecer</h1>

                                    <!-- CTA para reserva -->
                                    <a href="/Zéfish/templates/views/Site/reserva.php" class="btn btn-lg btn-custom btn-ambar">
                                        <i class="fa-solid fa-store"></i> Faça a sua reserva
                                    </a>

                                    <!-- Link externo para localização -->
                                    <a href="https://www.google.com/maps/embed?..." class="btn btn-lg btn-custom btn-verde">
                                        <i class="fa-solid fa-location-dot"></i> Onde ficamos
                                    </a>
                                </div>

                                <!-- Slide secundário com foco na gastronomia -->
                                <div class="carousel-item">
                                    <h1>Nossa gastronomia</h1>

                                    <a href="templates/views/Site/cardapio.php" class="btn btn-lg btn-custom btn-verde">
                                        <i class="fa fa-cutlery pr-4"></i> Cardápio
                                    </a>
                                </div>

                            </div>

                            <!-- Controle para slide anterior -->
                            <a href="#carousel-entrada" class="carousel-control-prev" data-slide="prev">
                                <i class="fas fa-angle-left fa-3x"></i>
                            </a>

                            <!-- Controle para próximo slide -->
                            <a href="#carousel-entrada" class="carousel-control-next" data-slide="next">
                                <i class="fas fa-angle-right fa-3x"></i>
                            </a>

                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- ======================== SEÇÃO SOBRE ======================== -->
        <!-- Conteúdo institucional e apresentação -->
        <section id="conteudo">
            <div class="container align-self-center">
                <div class="row">
                    <div class="col-md-12 text-center">

                        <!-- Logotipo em destaque -->
                        <div class="logo-container">
                            <img src="assets/img/logo.png" alt="Logo ZéFish">
                        </div>

                        <!-- Texto introdutório institucional -->
                        <p class="text-justify lead tab-paragrafo">
                            Imagine um lugar onde a tranquilidade das águas se encontra com a emoção de fisgar o peixe perfeito...
                        </p>

                        <p class="text-justify lead tab-paragrafo">
                            Venha viver momentos únicos, seja você um pescador experiente ou iniciante...
                        </p>

                        <p class="text-justify lead tab-paragrafo">
                            Garanta sua reserva e prepare-se para desfrutar de um ambiente que combina natureza e lazer.
                        </p>
                    </div>
                </div>

                <!-- Exibição imagética do tanque principal -->
                <div class="container-fluid p-5 position-relative">
                    <div class="row">
                        <div class="col-12 d-none d-lg-block">
                            <div class="img-tanque">
                                <img src="assets/img/tanque.jpeg" alt="Tanque" class="img-fluid w-100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ======================== RECURSOS ======================== -->
        <!-- Seção com atalhos para funcionalidades do site -->
        <section id="areas" class="caixa background-areas py-5">
            <div class="container">
                <div class="row">

                    <!-- Lista de recursos oferecidos -->
                    <div class="col-md-5">

                        <!-- Título da seção -->
                        <h1 class="mb-4 text-ciano">RECURSOS</h1>

                        <!-- Links rápidos de navegação -->
                        <div class="mb-4">
                            <a href="/Zéfish/templates/views/Site/cardapio.php">
                                <h2 class="text-ciano">Gastronomia</h2>
                            </a>
                            <p class="text-branco">Descubra nosso cardápio...</p>
                        </div>

                        <div class="mb-4">
                            <a href="/Zéfish/templates/views/Site/descricao.php">
                                <h2 class="text-ciano">Nossos Peixes</h2>
                            </a>
                            <p class="text-branco">Explore nosso tanque virtual...</p>
                        </div>

                        <div class="mb-4">
                            <a href="/Zéfish/templates/views/Site/eventos.php">
                                <h2 class="text-ciano">Eventos</h2>
                            </a>
                            <p class="text-branco">Fique por dentro dos shows...</p>
                        </div>

                        <div class="mb-4">
                            <a href="/Zéfish/templates/views/Site/comunidade.php">
                                <h2 class="text-ciano">Comunidade</h2>
                            </a>
                            <p class="text-branco">Compartilhe suas conquistas...</p>
                        </div>

                        <!-- Link externo para mapa -->
                        <div>
                            <a href="https://maps.app.goo.gl/yb9eS2yqpPZc8gx16">
                                <h2 class="text-ciano">Localização</h2>
                            </a>
                            <p class="text-branco">Encontre-nos facilmente.</p>
                        </div>

                    </div>

                    <!-- Conteúdo complementar e mapa -->
                    <div class="col-md-7 d-flex flex-column">

                        <!-- Card institucional -->
                        <div class="card p-4 mb-4"
                             style="background: rgba(255,255,255,0.12);
                                    border-radius: 18px;
                                    border:1px solid rgba(255,255,255,0.25);">

                            <h3 class="mb-3 text-ciano">
                                <i class="fa fa-fish mr-2"></i> Zéfish - Um local Inesquecível
                            </h3>

                            <p class="text-branco-suave">
                                Experimente técnicas modernas de pesca ou divirta-se pescando e levando seu peixe para casa.
                                Ambiente seguro, familiar e cheio de natureza.
                            </p>

                            <!-- CTA para reserva -->
                            <a href="/Zéfish/templates/views/Site/reserva.php" class="btn btn-success mt-3">
                                <i class="fa fa-calendar"></i> Fazer reserva
                            </a>
                        </div>

                        <!-- Mapa integrado via Google Maps -->
                        <div class="card" style="border-radius:18px; overflow:hidden;">
                            <iframe width="100%" 
                                height="330" 
                                frameborder="0"
                                style="border:0;"
                                allowfullscreen
                                src="https://www.google.com/maps/embed?pb=..."
                            ></iframe>
                        </div>

                    </div>

                </div>
            </div>
        </section>

    </main>

    <!-- ======================== RODAPÉ ======================== -->
    <!-- Rodapé institucional com informações complementares -->
    <footer id="footer">
        <div class="container">

            <div class="row align-items-center mb-3">

                <!-- Logo no rodapé -->
                <div class="col-md-4 text-center text-md-left mb-3">
                    <a href="index.php">
                        <img src="assets/img/logo.png" height="60" alt="ZéFish">
                    </a>
                </div>

                <!-- Texto institucional -->
                <div class="col-md-4 text-center">
                    <p>O lugar ideal para relaxar e viver momentos inesquecíveis.</p>
                </div>

                <!-- Ícones de redes sociais -->
                <div class="col-md-4 d-flex justify-content-center justify-content-md-end">
                    <ul class="socials d-flex list-unstyled m-0 p-0">
                        <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/instagram.png" height="28"></a></li>
                        <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/facebook.png" height="28"></a></li>
                        <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/twitter.png" height="28"></a></li>
                    </ul>
                </div>

            </div>

            <!-- Links auxiliares -->
            <div class="row">
                <div class="col text-center">
                    <a href="index.php">Início</a> |
                    <a href="/Zéfish/templates/views/Site/regras.php">Regras</a> |
                    <a href="/Zéfish/templates/views/Site/cardapio.php">Gastronomia</a> |
                    <a href="index.php">Localização</a>
                </div>
            </div>

            <!-- Direitos autorais -->
            <div class="row mt-4">
                <div class="col text-center">
                    <small>&copy; 2025 ZéFish. Todos os direitos reservados.</small>
                </div>
            </div>

        </div>
    </footer>

    <!-- Dependências JS do Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

</body>
</html>
