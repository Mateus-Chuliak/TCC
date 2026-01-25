<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../Sistema/init.php';


// Webhook só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método não permitido
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

try {
    // Ler JSON vindo do PIX (AbacatePay, Mercado Pago etc.)
    $input = json_decode(file_get_contents('php://input'), true);

    // Log para depuração (importante)
    error_log("WEBHOOK PIX RECEBIDO: " . json_encode($input));

    // Extrair informações importantes
    $status = strtolower($input['status'] ?? '');
    $metadata = $input['metadata'] ?? [];

    $reservationId = $metadata['reservation_id'] ?? null;
    $paymentId = $metadata['payment_id'] ?? null;

    // Validar metadata
    if (!$reservationId || !$paymentId) {
        throw new Exception("Metadata ausente: precisa de reservation_id e payment_id");
    }

    // STATUS QUE REPRESENTAM PAGAMENTO APROVADO
    $statusConfirmado = ['paid', 'pago', 'approved', 'success'];

    // Se o pagamento foi confirmado
    if (in_array($status, $statusConfirmado)) {

        // 1 — Atualizar pagamento
        $stmt = $conexao->prepare("
            UPDATE pagamentos 
            SET status = 'confirmado', data_pagamento = NOW()
            WHERE id_pagamento = ?
        ");
        $stmt->bind_param("i", $paymentId);
        $stmt->execute();

        // 2 — Atualizar reserva
        $stmt = $conexao->prepare("
            UPDATE reservas 
            SET status = 'confirmada'
            WHERE id_reserva = ?
        ");
        $stmt->bind_param("i", $reservationId);
        $stmt->execute();

        error_log("Pagamento e reserva confirmados com sucesso.");
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {

    error_log("ERRO NO WEBHOOK PIX: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
