<?php
// Inicializa o sistema (autoload, sessão, conexão, constantes etc.)
require_once __DIR__ . '/../../../Sistema/init.php';
?>

<!doctype html>
<html lang="pt-br">
<head>
    <!-- Dados básicos da página -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ZéFish - Pescaria</title>

    <!-- Ícone do navegador -->
    <link rel="icon" href="/Zéfish/assets/img/fav_icon.png">

    <!-- Bootstrap principal -->
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">

    <!-- Ícones do Font Awesome -->
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>

    <!-- CSS personalizado do site -->
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">

    <!-- ===================== CSS específico da página ===================== -->
    <style>
        /* Fundo geral ajustado para esta página */
        body#pescaria {
            padding-top: 120px;
            background: linear-gradient(to bottom, #09c5daff, #00ffffff);
        }

        /* Centraliza títulos das colunas */
        #main-pescaria .col-md-6 h2 {
            text-align: center;
        }

        /* Título principal */
        .pescaria-title {
            margin-bottom: 3rem;
            font-weight: 900;
            letter-spacing: 0.1em;
            color: #1A3B6C;
            font-size: 4rem;
        }

        /* Títulos internos das seções */
        #main-pescaria .titulo-subsecao {
            color: #2E82E3;
            font-weight: 900;
            margin-bottom: 0.8rem;
        }

        /* Parágrafos com foco em legibilidade */
        #main-pescaria p {
            text-align: justify;
            line-height: 1.7;
            color: #222;
            margin-bottom: 1rem;
            text-indent: 2em;
        }

        /* Imagens principais da página */
        #main-pescaria .pesca-img {
            width: 100%;
            max-height: 320px;
            object-fit: cover;
        }

        /* Ajustes para dispositivos menores */
        @media (max-width: 767.98px) {
            .pescaria-title {
                font-size: 1.8rem;
            }
            #main-pescaria .pesca-img {
                max-height: 240px;
            }
        }

        /* Card branco que envolve todo o conteúdo */
        #card-pescaria {
            width: 100%;
            max-width: 1100px;
            margin: 40px auto;
            padding: 40px 50px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body id="pescaria">

<!-- ============================= NAVBAR ============================= -->
<header>
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
<div id="card-pescaria">
    <main id="main-pescaria" class="container" role="main" aria-labelledby="tipos-pesca-title">

        <!-- Título principal da página -->
        <h1 id="tipos-pesca-title" class="text-center pescaria-title">Tipos de Pesca</h1>

        <div class="row mt-4">

            <!-- ===================== SEÇÃO: PESCA ESPORTIVA ===================== -->
            <div class="col-md-6 mb-4">
            <article aria-labelledby="pesca-esportiva-title">
                <h2 id="pesca-esportiva-title" class="h3 titulo-subsecao">Pesca Esportiva</h2>

                <p>
                    A pesca esportiva é uma prática focada em técnica, desafio e respeito ao meio ambiente.
                    No ZéFish, ela segue o modelo “catch-and-release”, com instruções para minimizar danos ao peixe.
                </p>

                <p>
                    São utilizados equipamentos leves, anzóis sem farpa quando indicado e iscas artificiais.
                    A equipe orienta iniciantes sobre equipamentos, pontos do lago e técnicas recomendadas.
                </p>

                <p>
                    O manuseio correto inclui uso de passaguá, evitar manter o peixe fora d'água por mais de 2 minutos
                    e higienização das mãos. Isso preserva o bem-estar animal e garante uma boa experiência.
                </p>

                <p>
                    Regras específicas incluem limite por raia, horários, ração permitida e proibições como iscas oleosas.
                </p>
            </article>
            </div>

            <!-- ===================== SEÇÃO: PESQUE E PAGUE ===================== -->
            <div class="col-md-6 mb-4">
            <article aria-labelledby="pesque-pague-title">
                <h2 id="pesque-pague-title" class="h3 titulo-subsecao">Pesque e Pague</h2>

                <p>
                    Modalidade voltada para lazer familiar: você pesca e pode levar o peixe conforme o peso.
                    Acessível a iniciantes, famílias e visitantes ocasionais.
                </p>

                <p>
                    As áreas contam com suporte, instrução sobre ração e regras.
                    O restaurante pode preparar o peixe pescador sob solicitação.
                </p>

                <p>
                    Para organização, há limite de 1 pescador por raia e fechamento da comanda somente após recolhimento do material.
                </p>

                <p>
                    Também há opções infantis e pacotes para eventos, integrando lazer, gastronomia e natureza.
                </p>
            </article>
            </div>
        </div>

        <!-- ===================== IMAGEM FINAL ===================== -->
        <div class="row mt-1 mb-5">
            <div class="col-12">
                <img src="/Zéfish/assets/img/equipamento.jpg"
                     class="img-fluid w-100 d-block"
                     alt="Equipamento de pesca">
            </div>
        </div>

    </main>
</div>

<!-- ============================= RODAPÉ ============================= -->
<footer id="footer">
    <div class="container">

        <div class="row align-items-center mb-3">

            <!-- Logotipo -->
            <div class="col-md-4 text-center text-md-left mb-3">
                <a href="index.php">
                    <img src="/Zéfish/assets/img/logo.png" height="60" alt="ZéFish">
                </a>
            </div>

            <!-- Texto principal -->
            <div class="col-md-4 text-center">
                <p>O lugar ideal para quem ama pescar, relaxar e viver momentos inesquecíveis em família.</p>
            </div>

            <!-- Redes sociais -->
            <div class="col-md-4 d-flex justify-content-center justify-content-md-end">
                <ul class="socials d-flex list-unstyled m-0 p-0">
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/instagram.png" height="28"></a></li>
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/facebook.png" height="28"></a></li>
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/twitter.png" height="28"></a></li>
                </ul>
            </div>
        </div>

        <!-- Links úteis -->
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

<!-- ============================= SCRIPTS ============================= -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="/Zéfish/assets/js/bootstrap.min.js"></script>

</body>
</html>
