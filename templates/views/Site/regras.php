<?php
// Inicializa o sistema (sessão, configurações, conexão, autoload etc.)
require_once __DIR__ . '/../../../Sistema/init.php';
?>
<head>
    <title>ZéFish - Pesqueiro</title>

    <!-- Ícone do navegador -->
    <link rel="icon" href="/Zéfish/assets/img/fav_icon.png">

    <!-- Metadados essenciais -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap principal -->
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">

    <!-- Font Awesome (ícones do site) -->
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>

    <!-- CSS customizado do projeto -->
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">
</head>

<body class="pagina-interna">
<!-- ============================= NAVBAR ============================= -->
<header>
    <!-- Espaço para compensar navbar fixed -->
<div style="height:120px;"></div>

    <nav id="navbarHeader" class="navbar navbar-expand-xl fixed-top navbar-light navbar-transparente">
        <div class="container">

            <!-- Logotipo -->
            <a href="../../../index.php" class="navbar-brand">
                <img src="/Zéfish/assets/img/logo.png" width="115" class="img-fluid">
            </a>

            <!-- Botão do menu mobile -->
            <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-principal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu principal -->
            <div class="collapse navbar-collapse" id="nav-principal">
                <ul class="navbar-nav">

                    <!-- Dropdown Pescaria -->
                    <li class="nav-item dropdown">
                        <a href="pescaria.php" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-anchor pr-4"></i>Pescaria
                        </a>
                        <div class="dropdown-menu">
                            <a href="reserva.php" class="dropdown-item">Faça sua reserva</a>
                            <a href="pescaria.php" class="dropdown-item">Tipos de pesca</a>
                            <a href="descricao.php" class="dropdown-item">Nossos Peixes</a>
                        </div>
                    </li>

                    <!-- Link Gastronomia -->
                    <li class="nav-item">
                        <a href="cardapio.php" class="nav-link">
                            <i class="fa fa-cutlery pr-4"></i>Gastronomia
                        </a>
                    </li>

                    <!-- Link Eventos -->
                    <li class="nav-item">
                        <a href="eventos.php" class="nav-link">
                            <i class="fa fa-calendar pr-4"></i>Eventos
                        </a>
                    </li>

                    <!-- Link Comunidade -->
                    <li class="nav-item">
                        <a href="comunidade.php" class="nav-link">
                            <i class="fa fa-users pr-4"></i>Comunidade
                        </a>
                    </li>

                    <!-- Link Regras -->
                    <li class="nav-item">
                        <a href="regras.php" class="nav-link">
                            <i class="fa fa-exclamation-triangle pr-4"></i>Regras
                        </a>
                    </li>

                    <!-- Divisor entre menus e login -->
                    <li class="divisor"></li>

                    <!-- Ícone de login -->
                    <li class="nav-item d-flex align-items-center white header-user">
                        <a href="../Admin/login.php" class="nav-link">
                            <i class="fa fa-user-circle fa-2x"></i>
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </nav>
</header>


<!-- ============================= CONTEÚDO PRINCIPAL ============================= -->
<section id="home" class="d-flex">
    <div class="container text-center my-5">

        <!-- Título principal da página -->
        <h2 class="titulo-principal">Regras do Pesqueiro</h2>

        <div class="row">
            <!-- ===================== CARD 1 ===================== -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fa-solid fa-fish-fins mb-3 icon-card"></i>
                        <h5 class="titulo-card">Pesque com Responsabilidade</h5>
                        <p class="indent">Respeite as instruções do pesqueiro e da equipe.</p>
                    </div>
                </div>
            </div>

            <!-- ===================== CARD 2 ===================== -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fa-solid fa-music mb-3 icon-card"></i>
                        <h5 class="titulo-card">Proibido som alto</h5>
                        <p class="indent">Não é permitido usar caixas de som ou volume elevado.</p>
                    </div>
                </div>
            </div>

            <!-- ===================== CARD 3 ===================== -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fa-solid fa-beer-mug-empty mb-3 icon-card"></i>
                        <h5 class="titulo-card">Consumo Moderado</h5>
                        <p class="indent">Bebidas são permitidas com responsabilidade.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================= LISTA DE REGRAS ========================= -->
        <div class="container my-4">
            <h4 class="mt-3 titulo-secao">DEMAIS REGRAS</h4>

            <!-- Lista com todas as regras internas -->
            <ul class="list-group list-group-flush mt-3">
                <li class="list-group-item indent">Horário de pesca: 07:00 às 18:00.</li>
                <li class="list-group-item indent">Comanda só fecha após recolher o material.</li>
                <li class="list-group-item indent">Não cruzar linhas; 1 pescador por raia.</li>
                <li class="list-group-item indent">Proibido nadar e fazer fogueiras.</li>
                <li class="list-group-item indent">Proibido trazer caixas térmicas.</li>
                <li class="list-group-item indent">Não trazer alimentos/bebidas.</li>
                <li class="list-group-item indent">Somente ração do pesqueiro.</li>
                <li class="list-group-item indent">Proibido sebo, carne moída, etc.</li>
                <li class="list-group-item indent">Proibido multifilamento.</li>
                <li class="list-group-item indent">Usar passaguá ao retirar o peixe.</li>
                <li class="list-group-item indent">Se engolir anzol, cortar linha.</li>
                <li class="list-group-item indent">Não jogar restos no lago.</li>
                <li class="list-group-item indent">Pets pequenos permitidos.</li>
                <li class="list-group-item indent">Advertência/multa em caso de infração.</li>
                <li class="list-group-item indent">Crianças sempre com responsável.</li>
                <li class="list-group-item indent">Utilizar equipamentos autorizados.</li>
                <li class="list-group-item indent">Recolher o material ao final.</li>
                <li class="list-group-item indent">Comunicar acidentes.</li>
                <li class="list-group-item indent">Regras podem mudar conforme situação.</li>
            </ul>
        </div>
    </div>
</section>

<!-- ============================= RODAPÉ ============================= -->
<footer id="footer">
    <div class="container">

        <div class="row align-items-center mb-3">

            <!-- Logo do rodapé -->
            <div class="col-md-4 text-center text-md-left mb-3">
                <a href="../../../index.php">
                    <img src="/Zéfish/assets/img/logo.png" height="60" alt="ZéFish">
                </a>
            </div>

            <!-- Texto central -->
            <div class="col-md-4 text-center">
                <p>O lugar ideal para quem ama pescar, relaxar e viver momentos inesquecíveis em família.</p>
            </div>

            <!-- Ícones das redes sociais -->
            <div class="col-md-4 d-flex justify-content-center justify-content-md-end">
                <ul class="socials d-flex list-unstyled m-0 p-0">
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/instagram.png" height="28"></a></li>
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/facebook.png" height="28"></a></li>
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/twitter.png" height="28"></a></li>
                </ul>
            </div>
        </div>

        <!-- Links rápidos do rodapé -->
        <div class="row">
            <div class="col text-center">
                <a href="../../../index.php">Início</a> |
                <a href="/Zéfish/templates/views/Site/regras.php">Regras</a> |
                <a href="#">Gastronomia</a> |
                <a href="../../../index.php">Localização</a>
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

<!-- ============================= SCRIPTS ============================= -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="/Zéfish/assets/js/bootstrap.min.js"></script>

</body>
</html>
