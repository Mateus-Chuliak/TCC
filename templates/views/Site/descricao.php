<?php
// Inicializa o sistema (autoload, sessão, configurações e conexão)
require_once __DIR__ . '/../../../Sistema/init.php';


// Inicializa o sistema (autoload, sessão, configurações e conexão)
// Consulta todos os eventos ordenados pela data mais recente
$sql = "
    SELECT DISTINCT 
        p.id_peixe,
        p.nome,
        p.descricao,
        p.imagem
    FROM peixes p
    INNER JOIN tanque_peixes tp 
        ON tp.id_peixe = p.id_peixe
    WHERE tp.status = 'ativo'
";
$res = $conexao->query($sql);
$peixes = $res->fetch_all(MYSQLI_ASSOC); // retorna array associativo
?>

<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ZéFish - Pescaria</title>

    <!-- Ícone da aba -->
    <link rel="icon" href="/Zéfish/assets/img/fav_icon.png">

    <!-- Bootstrap CSS (tema principal do site) -->
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">

    <!-- Ícones -->
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>

    <!-- Estilos globais personalizados -->
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">

    <!-- =========================================================
         ESTILOS PERSONALIZADOS ESPECÍFICOS PARA ESTA PÁGINA
         Contém o visual dos cards de peixes e o card gigante.
    ========================================================== -->
    <style>
    /* =========================================================
       CARD PRINCIPAL DO PEIXE
       ---------------------------------------------------------
       • Bordas arredondadas
       • Sombra
       • Crescimento suave no hover
    ========================================================== */
    .peixe-card {
        border-radius: 15px;
        overflow: hidden;
        transition: all .3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 18px rgba(0,0,0,0.12);
        background: #fff;
        position: relative;
        z-index: 1;
    }

    .peixe-card:hover {
        transform: scale(1.05);
        z-index: 10;
    }

    /* =========================================================
       IMAGEM DO PEIXE
    ========================================================== */
    .peixe-img {
        width: 100%;
        height: 180px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .peixe-img img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* =========================================================
       CARD GIGANTE QUE CONTÉM TODOS OS CARDS DE PEIXES
    ========================================================== */
    .peixe-wrapper {
        background: #ffffff;
        border-radius: 18px;
        padding: 40px;
        margin: 0 auto;
        margin-top: 30px;
        margin-bottom: 60px;
        max-width: 1100px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.12);
    }

    /* =========================================================
       FUNDO DA PÁGINA
    ========================================================== */
    body {
        padding-top: 120px;
        background: linear-gradient(to bottom, #09c5daff, #00ffffff);
    }

    /* =========================================================
       DESCRIÇÃO DO PEIXE (4 linhas + expansão no hover)
    ========================================================== */
    .descricao-peixe {
        text-align: left;
        display: block;
        font-size: 0.95rem;
        line-height: 1.4em;
        max-height: 5.6em; /* 4 linhas */
        overflow: hidden;
        position: relative;
        transition: max-height .4s ease;
    }

    .descricao-peixe::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 1.5em;
        background: linear-gradient(to bottom, rgba(255,255,255,0), #ffffff);
        transition: opacity .3s ease;
    }

    .peixe-card:hover .descricao-peixe {
        max-height: 500px; /* expande */
    }

    .peixe-card:hover .descricao-peixe::after {
        opacity: 0; /* remove o fade ao expandir */
    }
    </style>
</head>

<!-- =========================================================
     HEADER / MENU SUPERIOR
     Mesmo padrão de todas as páginas do ZéFish
========================================================= -->
<header>
    <nav id="navbarHeader" class="navbar navbar-expand-xl fixed-top navbar-light navbar-transparente">
        <div class="container">

            <!-- Logo -->
            <a href="../../../index.php" class="navbar-brand">
                <img src="/Zéfish/assets/img/logo.png" width="115" class="img-fluid">
            </a>

            <!-- Botão mobile -->
            <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-principal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
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

                    <!-- Gastronomia -->
                    <li class="nav-item">
                        <a href="cardapio.php" class="nav-link">
                            <i class="fa fa-cutlery pr-4"></i>Gastronomia
                        </a>
                    </li>

                    <!-- Eventos -->
                    <li class="nav-item">
                        <a href="eventos.php" class="nav-link">
                            <i class="fa fa-calendar pr-4"></i>Eventos
                        </a>
                    </li>

                    <!-- Comunidade -->
                    <li class="nav-item">
                        <a href="comunidade.php" class="nav-link">
                            <i class="fa fa-users pr-4"></i>Comunidade
                        </a>
                    </li>

                    <!-- Regras -->
                    <li class="nav-item">
                        <a href="regras.php" class="nav-link">
                            <i class="fa fa-exclamation-triangle pr-4"></i>Regras
                        </a>
                    </li>

                    <li class="divisor"></li>

                    <!-- Ícone de usuário -->
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


<!-- =========================================================
     SEÇÃO: Nossos Peixes
========================================================= -->
<section class="mt-5 mb-5">
    
    <!-- Card Pai (caixa grande branca) -->
    <div class="peixe-wrapper">

        <!-- Título -->
        <h2 class="text-center pescaria-title" style="font-size: 3rem;">Nossos Peixes</h2>

        <p class="text-center text-muted mb-4" style="font-size: 1.1rem;">
            Conheça as espécies disponíveis para pesca hoje no ZéFish.
        </p>

        <!-- Grid de peixes -->
        <div class="container pb-5">
            <div class="row gy-4 gx-4">

                <?php foreach ($peixes as $p): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card peixe-card">

                        <!-- Imagem -->
                        <?php if (!empty($p['imagem'])): ?>
                            <img src="/Zéfish/<?= $p['imagem'] ?>" class="peixe-img" alt="<?= htmlspecialchars($p['nome']) ?>">
                        <?php else: ?>
                            <img src="/Zéfish/assets/img/sem-foto.png" class="peixe-img" alt="Sem imagem">
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            
                            <!-- Nome -->
                            <h5 class="card-title text-center fw-bold">
                                <?= htmlspecialchars($p['nome']) ?>
                            </h5>

                            <!-- Descrição limitada -->
                            <p class="descricao-peixe mt-2">
                                <?= nl2br(htmlspecialchars($p['descricao'])) ?>
                            </p>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Caso não haja peixes -->
                <?php if (count($peixes) === 0): ?>
                    <p class="text-center mt-5">Nenhum peixe disponível no momento.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

</section>


<!-- =========================================================
     RODAPÉ
========================================================= -->
<footer id="footer">
    <div class="container">

        <div class="row align-items-center mb-3">

            <!-- Logo -->
            <div class="col-md-4 text-center text-md-left mb-3">
                <a href="index.php">
                    <img src="/Zéfish/assets/img/logo.png" height="60" alt="ZéFish">
                </a>
            </div>

            <!-- Texto -->
            <div class="col-md-4 text-center">
                <p>O lugar ideal para quem ama pescar, relaxar e viver momentos inesquecíveis em família.</p>
            </div>

            <!-- Redes -->
            <div class="col-md-4 d-flex justify-content-center justify-content-md-end">
                <ul class="socials d-flex list-unstyled m-0 p-0">
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/instagram.png" height="28"></a></li>
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/facebook.png" height="28"></a></li>
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/twitter.png" height="28"></a></li>
                </ul>
            </div>
        </div>

        <!-- Links adicionais -->
        <div class="row">
            <div class="col text-center">
                <a href="index.php">Início</a> |
                <a href="/Zéfish/templates/views/Site/regras.php">Regras</a> |
                <a href="/Zéfish/templates/views/Site/cardapio.php">Gastronomia</a> |
                <a href="index.php">Localização</a>
            </div>
        </div>

        <!-- Direitos -->
        <div class="row mt-4">
            <div class="col text-center">
                <small>&copy; 2025 ZéFish. Todos os direitos reservados.</small>
            </div>
        </div>

    </div>
</footer>

<!-- Scripts JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="/Zéfish/assets/js/bootstrap.min.js"></script>
</body>
</html>
