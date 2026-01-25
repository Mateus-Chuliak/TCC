<?php
// Define resposta em JSON para consumo por frontend ou API externa
header('Content-Type: application/json');

// Libera acesso CORS para chamadas externas
header('Access-Control-Allow-Origin: *');

// Restringe a API ao método POST
header('Access-Control-Allow-Methods: POST');

// Define cabeçalhos permitidos na requisição
header('Access-Control-Allow-Headers: Content-Type');

// Importa configuração e conexão com o banco de dados
require_once '../config/database.php';

// Valida se o método HTTP é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

try {
    // Obtém e decodifica o corpo JSON da requisição
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Define campos obrigatórios para criação da reserva
    $requiredFields = ['name', 'email', 'cpf', 'phone', 'date', 'package', 'price', 'people'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Campo obrigatório ausente: $field");
        }
    }
    
    // Estabelece conexão com o banco de dados
    $conn = getConnection();
    if (!$conn) {
        throw new Exception("Erro ao conectar com o banco de dados");
    }
    
    // Inicia transação para garantir consistência dos dados
    $conn->beginTransaction();
    
    // Consulta usuário existente pelo e-mail
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->execute([$input['email']]);
    $user = $stmt->fetch();
    
    // Reutiliza usuário existente ou cria um novo
    if ($user) {
        $userId = $user['id_usuario'];
    } else {
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nome, email, cpf, tipo) 
            VALUES (?, ?, ?, 'cliente')
        ");
        $stmt->execute([
            $input['name'],
            $input['email'],
            $input['cpf']
        ]);
        $userId = $conn->lastInsertId();
    }
    
    // Busca restaurante padrão para associação da reserva
    $stmt = $conn->prepare("SELECT id_restaurante FROM restaurantes LIMIT 1");
    $stmt->execute();
    $restaurant = $stmt->fetch();
    
    // Cria restaurante padrão caso não exista
    if (!$restaurant) {
        $stmt = $conn->prepare("
            INSERT INTO restaurantes (nome, endereco, telefone, horario_funcionamento, capacidade) 
            VALUES ('ZéFish Pesqueiro', 'Endereço do Pesqueiro', '(11) 99999-9999', '6:00 - 18:00', 100)
        ");
        $stmt->execute();
        $restaurantId = $conn->lastInsertId();
    } else {
        $restaurantId = $restaurant['id_restaurante'];
    }
    
    // Registra a reserva vinculada ao usuário e restaurante
    $stmt = $conn->prepare("
        INSERT INTO reservas (usuario_id, restaurante_id, data_reserva, horario, status) 
        VALUES (?, ?, ?, '08:00:00', 'pendente')
    ");
    $stmt->execute([
        $userId,
        $restaurantId,
        $input['date']
    ]);
    $reservationId = $conn->lastInsertId();
    
    // Converte valor de centavos para reais
    $valorEmReais = $input['price'] / 100;
    
    // Registra pagamento inicial como pendente
    $stmt = $conn->prepare("
        INSERT INTO pagamentos (reserva_id, usuario_id, valor, metodo, status) 
        VALUES (?, ?, ?, 'PIX', 'pendente')
    ");
    $stmt->execute([
        $reservationId,
        $userId,
        $valorEmReais
    ]);
    $paymentId = $conn->lastInsertId();
    
    // Monta payload para criação da cobrança na AbacatePay
    $abacatePayData = [
        'frequency' => 'ONE_TIME',
        'methods' => ['PIX'],
        'products' => [[
            'externalId' => $input['package'],
            'name' => 'Reserva ZéFish - ' . $input['packageName'],
            'description' => "Reserva para {$input['people']} pessoa(s) no dia " . date('d/m/Y', strtotime($input['date'])),
            'quantity' => 1,
            'price' => (int)$input['price']
        ]],
        'customer' => [
            'name' => $input['name'],
            'cellphone' => $input['phone'],
            'email' => $input['email'],
            'taxId' => $input['cpf']
        ],
        'returnUrl' => $input['returnUrl'] ?? 'https://seusite.com',
        'completionUrl' => $input['completionUrl'] ?? 'https://seusite.com/sucesso',
        'metadata' => [
            'reservation_id' => $reservationId,
            'payment_id' => $paymentId
        ]
    ];
    
    // Inicializa chamada HTTP para a API de pagamento
    $ch = curl_init('https://api.abacatepay.com/v1/billing/create');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($abacatePayData));
    
    // Define cabeçalhos de autenticação e conteúdo
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . ($input['apiKey'] ?? 'abc_dev_EwXMWsUrsL4TzKSSN3cQRH6F')
    ]);
    
    // Executa requisição e captura resposta
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Valida sucesso da comunicação com gateway de pagamento
    if ($httpCode !== 200 && $httpCode !== 201) {
        throw new Exception("Erro ao processar pagamento: " . $response);
    }
    
    // Decodifica resposta do gateway de pagamento
    $paymentResponse = json_decode($response, true);
    
    // Confirma a transação no banco
    $conn->commit();
    
    // Retorna sucesso com identificadores gerados
    echo json_encode([
        'success' => true,
        'reservationId' => $reservationId,
        'paymentId' => $paymentId,
        'abacatePayResponse' => $paymentResponse
    ]);
    
} catch (Exception $e) {
    // Reverte transação em caso de falha
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Registra erro para auditoria e monitoramento
    error_log("Erro ao criar reserva: " . $e->getMessage());
    
    // Retorna erro padronizado ao cliente
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>
