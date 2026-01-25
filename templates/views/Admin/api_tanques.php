<?php
/* Esta API foi desenvolvida como uma alternativa de integração com o Power BI.
Apesar de totalmente funcional, ela não está sendo utilizada no momento, pois o sistema atual faz a conexão diretamente ao banco MySQL.
O arquivo permanece no projeto como solução de contingência, caso seja necessário substituir ou complementar a conexão direta por uma API REST futuramente.*/

// Endpoint JSON utilizado como alternativa de integração para consumo analítico
header('Content-Type: application/json; charset=utf-8');

// Permite acesso externo para ferramentas como Power BI
header('Access-Control-Allow-Origin: *');

// Inicializa ambiente, configurações e conexão
require_once __DIR__ . '/../../../Sistema/init.php';

try {
    // Consulta consolidada com métricas e dados derivados
    $sql = "SELECT 
        t.id_tanque as ID_Tanque,
        t.restaurante_id as Restaurante_ID,
        COALESCE(r.nome, 'N/A') as Restaurante_Nome,
        COALESCE(r.local, 'N/A') as Restaurante_Local,
        t.especie_peixe as Especie,
        t.quantidade as Quantidade,
        t.medida_tanque as Medida_m3,
        t.capacidade as Capacidade,
        ROUND((t.quantidade / NULLIF(t.capacidade, 0)) * 100, 2) as Taxa_Ocupacao,
        CASE 
            WHEN t.quantidade = 0 THEN 'Vazio'
            WHEN (t.quantidade / NULLIF(t.capacidade, 0)) < 0.5 THEN 'Baixa'
            WHEN (t.quantidade / NULLIF(t.capacidade, 0)) < 0.8 THEN 'Média'
            ELSE 'Alta'
        END as Status_Ocupacao,
        t.criado_em as Data_Criacao,
        DATE_FORMAT(t.criado_em, '%Y-%m-%d') as Data,
        YEAR(t.criado_em) as Ano,
        MONTH(t.criado_em) as Mes,
        DAY(t.criado_em) as Dia,
        DAYNAME(t.criado_em) as Dia_Semana
    FROM tanques t
    LEFT JOIN restaurantes r ON t.restaurante_id = r.id_restaurante
    ORDER BY t.criado_em DESC";
    
    // Executa a consulta ao banco
    $result = $conexao->query($sql);
    
    // Valida execução da query
    if (!$result) {
        throw new Exception("Erro na execução da consulta");
    }
    
    // Converte o resultado em array associativo
    $dados = [];
    while ($row = $result->fetch_assoc()) {
        $dados[] = $row;
    }
    
    // Cálculo de indicadores agregados
    $totalTanques = count($dados);
    $totalPeixes = array_sum(array_column($dados, 'Quantidade'));
    $tanquesVazios = count(array_filter($dados, fn($t) => $t['Quantidade'] == 0));
    
    // Estrutura final da resposta da API
    $response = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'total_registros' => $totalTanques,
        'estatisticas' => [
            'total_tanques' => $totalTanques,
            'total_peixes' => $totalPeixes,
            'tanques_vazios' => $tanquesVazios,
            'tanques_com_peixe' => $totalTanques - $tanquesVazios
        ],
        'dados' => $dados
    ];
    
    // Retorna resposta JSON formatada
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {

    // Retorna erro padrão em caso de falha no processamento
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
}