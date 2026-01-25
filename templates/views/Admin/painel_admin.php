<?php
// Inicializa sessão, configurações e conexão
require_once __DIR__ . '/../../../Sistema/init.php';


// Restringe acesso a usuários com perfil administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: painel_usuario.php");
    exit();
}

// Gera e mantém token CSRF na sessão
function gerar_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Valida token CSRF recebido via formulário
function verificar_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitização básica de entradas
function limpar($v) {
    return trim((string)$v);
}

$csrf = gerar_csrf_token();
// Dados simulados armazenados em sessão
$tanques  = $_SESSION['tanques']  ?? [];
$cardapio = $_SESSION['cardapio'] ?? [];
$eventos  = $_SESSION['eventos']  ?? [];

// Formata números para exibição
function formatarNumero($numero) {
    return number_format($numero, 0, ',', '.');
}

// Retorna tempo relativo a partir de uma data
function calcularTempo($data) {
    $agora = time();
    $tempo = $agora - strtotime($data);

    if ($tempo < 60)   return 'Agora mesmo';
    if ($tempo < 3600) return floor($tempo / 60) . ' min atrás';
    if ($tempo < 86400) return floor($tempo / 3600) . ' h atrás';

    return floor($tempo / 86400) . ' dias atrás';
}

// Processa formulários enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
//Valida o token crsf
if (!verificar_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['mensagem'] = 'Token de segurança inválido ou expirado.';
        $_SESSION['tipo_mensagem'] = 'danger';
        header('Location: painel_admin.php');
        exit();
    }
    // Cadastro de novo tanque
    if (isset($_POST['cadastrar_tanque'])) {
        $tanques[] = [
            'id'         => uniqid(),
            'nome'       => $_POST['nome'],
            'capacidade' => $_POST['capacidade'],
            'especie'    => $_POST['especie'],
            'quantidade' => $_POST['quantidade'],
            'status'     => $_POST['status'],
            'cadastro'   => date('Y-m-d H:i:s')
        ];
        $_SESSION['tanques'] = $tanques;
    }

    // Cadastro de item do cardápio
    if (isset($_POST['cadastrar_item'])) {
        $cardapio[] = [
            'id'        => uniqid(),
            'nome'      => $_POST['nome_item'],
            'categoria' => $_POST['categoria'],
            'preco'     => $_POST['preco'],
            'descricao' => $_POST['descricao'],
            'status'    => $_POST['status_item'],
            'cadastro'  => date('Y-m-d H:i:s')
        ];
        $_SESSION['cardapio'] = $cardapio;
    }

    // Cadastro de novo evento
    if (isset($_POST['cadastrar_evento'])) {
        $eventos[] = [
            'id'        => uniqid(),
            'nome'      => $_POST['nome_evento'],
            'data'      => $_POST['data_evento'],
            'local'     => $_POST['local'],
            'descricao' => $_POST['descricao_evento'],
            'status'    => $_POST['status_evento'],
            'cadastro'  => date('Y-m-d H:i:s')
        ];
        $_SESSION['eventos'] = $eventos;
    }
}

// Processa exclusão de registros
if (isset($_GET['deletar'], $_GET['tipo'], $_GET['id'])) {
    $tipo = $_GET['tipo'];
    $id   = $_GET['id'];

    if ($tipo === 'tanque') {
        $_SESSION['tanques'] = array_values(array_filter($tanques, fn($t) => $t['id'] !== $id));
    } elseif ($tipo === 'cardapio') {
        $_SESSION['cardapio'] = array_values(array_filter($cardapio, fn($c) => $c['id'] !== $id));
    } elseif ($tipo === 'evento') {
        $_SESSION['eventos'] = array_values(array_filter($eventos, fn($e) => $e['id'] !== $id));
    }

    header('Location: painel_admin.php');
    exit();
}

// Indicadores gerais para o dashboard
$totalTanques   = count($tanques);
$tanquesAtivos  = count(array_filter($tanques, fn($t) => $t['status'] === 'ativo'));

$totalItens     = count($cardapio);
$itensAtivos    = count(array_filter($cardapio, fn($c) => $c['status'] === 'ativo'));

$totalEventos   = count($eventos);
$eventosAtivos  = count(array_filter($eventos, fn($e) => $e['status'] === 'ativo'));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>ZéFish - Painel Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/Zéfish/assets/img/fav_icon.png">
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">
</head>

    
    <style>
        body {
            background-color: #f5f6fa;
        }

        /* ==== SIDEBAR ==== */
        .sidebar {
            width: 260px !important; /* aumentada */
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #1a1a1d;
            color: white;
            padding-left: 1px;
            padding-top: 30px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
        }

        .sidebar h5 {
            padding-left: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .sidebar .nav-link {
            color: #ccc !important;
            margin: 5px 0;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: #0d6efd;
            color: white !important;
            border-radius: 6px;
        }

        /* ==== MAIN CONTENT ==== */
        main {
            margin-left: 270px; 
            padding: 30px;
        }
        .powerbi-card {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
<!-- Conteúdo principal -->
<div class="w-100 mt-1 pt-1">
    <div class="row">
        <!-- Sidebar -->
       <aside class="sidebar">
            <h5>Painel Administrativo</h5>
            <nav class="nav flex-column">
                <a class="nav-link" href="painel_admin.php"><i class="fas fa-user-friends me-2"></i> Painel Administrativo</a>
                <a class="nav-link" href="tanques.php"><i class="fas fa-fish me-2"></i> Tanques</a>
                <a class="nav-link" href="painel_pratos.php"><i class="fas fa-utensils me-2"></i> Pratos</a>
                <a class="nav-link" href="painel_eventos.php"><i class="fas fa-calendar me-2"></i> Eventos</a>
                <a class="nav-link" href="/Zéfish/Sistema/logout.php"><i class="fas fa-home me-2"></i> Voltar ao site</a>
            </nav>
        </aside>
<!-- CONTEÚDO -->
<main>
    <h3 class="mb-4"><i ></i> Dashboard Geral — Power BI</h3>

    <!-- POWER BI -->
    <div class="card shadow-sm border-0 powerbi-card">
        <div class="card-header bg-gradient text-white"
             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Relatório Integrado — Tanques, Pratos, Eventos, Reservas e Pagamentos</h5>
        </div>

        <div class="card-body p-0">
            <iframe title="painel" width="1300" height="550" src="https://app.powerbi.com/view?r=eyJrIjoiM2MxYTE5OGEtYWJkNS00YzJiLThjNGUtNzlmM2E1NTFmZjYyIiwidCI6ImNmNzJlMmJkLTdhMmItNDc4My1iZGViLTM5ZDU3YjA3Zjc2ZiIsImMiOjR9" frameborder="0" allowFullScreen="true"></iframe>
        </div>
    </div>
</main>

<script src="/Zéfish/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>
