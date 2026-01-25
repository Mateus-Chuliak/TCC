<?php
require_once __DIR__ . '/../../../Sistema/init.php';
header('Content-Type: application/json; charset=utf-8');

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método inválido.");
    }

    // Campos vindos do formulário
    $data        = $_POST['data_reserva'] ?? null;
    $pacote      = $_POST['pacote']       ?? null;
    $valor       = isset($_POST['valor']) ? (int)$_POST['valor'] : 0;
    $numPessoas  = isset($_POST['num_pessoas']) ? (int)$_POST['num_pessoas'] : 0;
    $nome        = trim($_POST['nome'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $telefone    = trim($_POST['telefone'] ?? '');
    $cpf         = trim($_POST['cpf'] ?? '');

    // Restaurante e horário fixos por enquanto
    $restauranteId = 1;
    $horario       = '09:00:00';

    // Usuário logado (tabela reservas exige usuario_id NOT NULL)
    $usuarioId = $_SESSION['id_usuario'] ?? null;
    if (!$usuarioId) {
        throw new Exception("Você precisa estar logado para fazer uma reserva.");
    }

    // Validação básica
    if (!$data || !$pacote || !$nome || !$email || !$telefone) {
        throw new Exception("Preencha todos os campos obrigatórios.");
    }

    // 🔒 Verifica se já existe reserva nesse dia/horário para o restaurante
    $check = $conexao->prepare("
        SELECT COUNT(*)
        FROM reservas
        WHERE restaurante_id = ?
          AND data_reserva   = ?
          AND horario        = ?
          AND status IN ('pendente','confirmada')
    ");
    $check->bind_param("iss", $restauranteId, $data, $horario);
    $check->execute();
    $check->bind_result($total);
    $check->fetch();
    $check->close();

    if ($total > 0) {
        throw new Exception("Este dia e horário já estão reservados. Escolha outra data.");
    }

    // ✅ Insere a reserva (seguindo exatamente as colunas da sua tabela)
    $stmt = $conexao->prepare("
        INSERT INTO reservas
            (usuario_id, restaurante_id, data_reserva, horario, status)
        VALUES
            (?, ?, ?, ?, 'pendente')
    ");
    $stmt->bind_param("iiss", $usuarioId, $restauranteId, $data, $horario);
    $stmt->execute();
    $stmt->close();

    // 🔑 Gera uma chave PIX FICTÍCIA (apenas para exibir o QR Code)
    // Não precisa estar na tabela, é só para o fluxo visual funcionar.
    $pixKey = 'PIX-ZEFISH-' . strtoupper(bin2hex(random_bytes(5)));

    echo json_encode([
        'success' => true,
        'message' => 'Reserva criada com sucesso.',
        'pix_key' => $pixKey,
    ]);

} catch (Exception $e) {

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
    ]);
}
