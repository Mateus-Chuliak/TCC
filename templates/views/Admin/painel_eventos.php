<?php
// Inicializa sessão, configuração e conexão com o banco
require_once __DIR__ . '/../../../Sistema/init.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Restringe acesso ao painel apenas para administradores
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: painel_usuario.php");
    exit();
}

// Gera token CSRF para proteção de formulários
function gerar_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Valida token CSRF recebido no formulário
function verificar_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

// Sanitização básica de entrada de dados
function limpar($v) {
    return trim((string)$v);
}

// Diretório físico e URL pública para imagens de eventos
define("PASTA_EVENTOS", $_SERVER['DOCUMENT_ROOT'] . "/Zéfish/uploads/eventos/");
define("URL_EVENTOS", "/Zéfish/uploads/eventos/");

// Garante a existência do diretório de upload
if (!is_dir(PASTA_EVENTOS)) {
    mkdir(PASTA_EVENTOS, 0777, true);
}

$csrf = gerar_csrf_token();

// Processa ações enviadas via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verificar_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['mensagem'] = "Token inválido.";
        $_SESSION['tipo_mensagem'] = "danger";
        header("Location: painel_eventos.php");
        exit();
    }

    $acao = $_POST['acao'] ?? '';

    // Cadastro de novo evento
    if ($acao === "adicionar_evento") {

        $titulo       = limpar($_POST['titulo'] ?? '');
        $descricao    = limpar($_POST['descricao'] ?? '');
        $data_evento  = $_POST['data_evento'] ?? null;
        $imagem       = null;

        // Processa upload de imagem, se enviada
        if (!empty($_FILES['imagem']['name'])) {
            $nomeFinal = time() . "_" . preg_replace(
                '/[^a-zA-Z0-9._-]/',
                '_',
                basename($_FILES["imagem"]["name"])
            );

            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], PASTA_EVENTOS . $nomeFinal)) {
                $imagem = $nomeFinal;
            }
        }

        try {
            $stmt = $conexao->prepare(
                "INSERT INTO eventos (titulo, descricao, data_evento, imagem)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $titulo, $descricao, $data_evento, $imagem);
            $stmt->execute();

            $_SESSION['mensagem'] = "Evento criado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao criar evento.";
            $_SESSION['tipo_mensagem'] = "danger";
        }

        header("Location: painel_eventos.php");
        exit();
    }

    // Remoção de evento existente
    if ($acao === "remover_evento") {

        $id_evento = (int) $_POST['id_evento'];

        try {
            // Obtém imagem associada para exclusão física
            $sel = $conexao->prepare("SELECT imagem FROM eventos WHERE id_evento = ?");
            $sel->bind_param("i", $id_evento);
            $sel->execute();
            $img = $sel->get_result()->fetch_assoc();

            $del = $conexao->prepare("DELETE FROM eventos WHERE id_evento = ?");
            $del->bind_param("i", $id_evento);
            $del->execute();

            if (!empty($img['imagem'])) {
                $arquivo = PASTA_EVENTOS . $img['imagem'];
                if (is_file($arquivo)) {
                    unlink($arquivo);
                }
            }

            $_SESSION['mensagem'] = "Evento removido.";
            $_SESSION['tipo_mensagem'] = "success";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao remover evento.";
            $_SESSION['tipo_mensagem'] = "danger";
        }

        header("Location: painel_eventos.php");
        exit();
    }

    // Atualização de dados do evento
    if ($acao === "editar_evento") {

        $id_evento   = (int) $_POST['id_evento'];
        $titulo      = limpar($_POST['titulo']);
        $descricao   = limpar($_POST['descricao']);
        $data_evento = $_POST['data_evento'];

        $novaImagem = null;

        // Upload de nova imagem, se fornecida
        if (!empty($_FILES['imagem']['name'])) {
            $nomeFinal = time() . "_" . preg_replace(
                '/[^a-zA-Z0-9._-]/',
                '_',
                basename($_FILES["imagem"]["name"])
            );

            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], PASTA_EVENTOS . $nomeFinal)) {
                $novaImagem = $nomeFinal;
            }
        }

        try {
            if ($novaImagem) {
                $stmt = $conexao->prepare(
                    "UPDATE eventos
                     SET titulo = ?, descricao = ?, data_evento = ?, imagem = ?
                     WHERE id_evento = ?"
                );
                $stmt->bind_param("ssssi", $titulo, $descricao, $data_evento, $novaImagem, $id_evento);
            } else {
                $stmt = $conexao->prepare(
                    "UPDATE eventos
                     SET titulo = ?, descricao = ?, data_evento = ?
                     WHERE id_evento = ?"
                );
                $stmt->bind_param("sssi", $titulo, $descricao, $data_evento, $id_evento);
            }

            $stmt->execute();

            $_SESSION['mensagem'] = "Evento atualizado!";
            $_SESSION['tipo_mensagem'] = "success";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao atualizar evento.";
            $_SESSION['tipo_mensagem'] = "danger";
        }

        header("Location: painel_eventos.php");
        exit();
    }
}

// Consulta listagem de eventos
$sql = "SELECT * FROM eventos ORDER BY data_evento DESC";
$res = $conexao->query($sql);
$eventos = $res->fetch_all(MYSQLI_ASSOC);

// Indicadores do dashboard
$totalEventos     = count($eventos);
$proximosEventos  = count(array_filter(
    $eventos,
    fn($e) => $e['data_evento'] >= date("Y-m-d")
));
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Painel - Eventos</title>

    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>

    <style>
        body { background:#f4f6f9; }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            position: fixed;
            background: #1a1a1d;
            color: white;
            padding-top: 25px;
        }
        .sidebar .nav-link { color: #ccc; }
        .sidebar .nav-link:hover { background: #0d6efd; color: white; }
        main { margin-left: 270px; padding: 30px; }
    </style>
</head>

<body>

<!-- MENU LATERAL -->
<aside class="sidebar">
    <h5 class="px-3">Painel Administrativo</h5>
    <nav class="nav flex-column">
        <a class="nav-link" href="painel_admin.php"><i class="fas fa-user-friends me-2"></i> Painel Administrativo</a>
        <a class="nav-link" href="tanques.php"><i class="fas fa-fish me-2"></i> Tanques</a>
        <a class="nav-link" href="painel_pratos.php"><i class="fas fa-utensils me-2"></i> Pratos</a>
        <a class="nav-link active" href="painel_eventos.php"><i class="fas fa-calendar me-2"></i> Eventos</a>
        <a class="nav-link" href="/Zéfish/Sistema/logout.php"><i class="fas fa-home me-2"></i> Voltar ao site</a>
    </nav>
</aside>

<!-- CONTEÚDO PRINCIPAL -->
<main>

<div class="d-flex justify-content-between mb-4">
    <h3>Dashboard Administrativo - Eventos</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovo">
        <i class="fas fa-plus"></i> Novo Evento
    </button>
</div>

<?php if (isset($_SESSION['mensagem'])): ?>
<div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?> alert-dismissible fade show">
    <?= $_SESSION['mensagem'] ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']); endif; ?>

<!-- CARDS -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow p-3 text-center">
            <h6>Total de Eventos</h6>
            <h2><?= $totalEventos ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow p-3 text-center">
            <h6>Eventos Futuros</h6>
            <h2 class="text-primary"><?= $proximosEventos ?></h2>
        </div>
    </div>
</div>

<!-- LISTA -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Lista de Eventos</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Imagem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>

<?php foreach ($eventos as $ev): ?>
<tr>
    <td><?= $ev['id_evento'] ?></td>
    <td><?= htmlspecialchars($ev['titulo']) ?></td>
    <td><?= date("d/m/Y", strtotime($ev['data_evento'])) ?></td>
    <td>
        <?php if ($ev['imagem']): ?>
            <img src="<?= URL_EVENTOS . htmlspecialchars($ev['imagem']) ?>"
                 width="70" class="rounded">
        <?php endif; ?>
    </td>

    <td>
        <!-- Editar -->
        <button class="btn btn-warning btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalEditar<?= $ev['id_evento'] ?>">
            <i class="fas fa-edit"></i>
        </button>

        <!-- Remover -->
        <form method="post" class="d-inline" onsubmit="return confirm('Remover evento?');">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="acao" value="remover_evento">
            <input type="hidden" name="id_evento" value="<?= $ev['id_evento'] ?>">
            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
        </form>
    </td>
</tr>
<?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- ========== MODAIS DE EDIÇÃO ========== -->
<?php foreach ($eventos as $ev): ?>
<div class="modal fade" id="modalEditar<?= $ev['id_evento'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="acao" value="editar_evento">
            <input type="hidden" name="id_evento" value="<?= $ev['id_evento'] ?>">

            <div class="modal-header">
                <h5 class="modal-title">Editar Evento</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control"
                       value="<?= htmlspecialchars($ev['titulo']) ?>" required>

                <label class="form-label mt-3">Descrição</label>
                <textarea name="descricao" class="form-control"><?= htmlspecialchars($ev['descricao']) ?></textarea>

                <label class="form-label mt-3">Data</label>
                <input type="date" name="data_evento" class="form-control"
                       value="<?= htmlspecialchars($ev['data_evento']) ?>" required>

                <label class="form-label mt-3">Imagem (opcional)</label>
                <input type="file" name="imagem" class="form-control">

                <?php if ($ev['imagem']): ?>
                    <img src="<?= URL_EVENTOS . htmlspecialchars($ev['imagem']) ?>"
                         width="120" class="rounded border mt-2">
                <?php endif; ?>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Salvar alterações</button>
            </div>

        </form>
    </div>
</div>
<?php endforeach; ?>

<!-- MODAL NOVO EVENTO -->
<div class="modal fade" id="modalNovo" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="acao" value="adicionar_evento">

            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Novo Evento</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>

                <label class="form-label mt-3">Descrição</label>
                <textarea name="descricao" class="form-control"></textarea>

                <label class="form-label mt-3">Data</label>
                <input type="date" name="data_evento" class="form-control" required>

                <label class="form-label mt-3">Imagem (opcional)</label>
                <input type="file" name="imagem" class="form-control">

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Salvar Evento</button>
            </div>

        </form>
    </div>
</div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
