<?php

namespace Sistema\nucleo;


 // Métodos auxiliares para suporte a regras globais da aplicação.
 
class Helpers
{
    
     //Identifica se a aplicação está em ambiente local.

    public static function localhost(): bool
    {
        // Obtém o host da requisição atual ou string vazia
        $host = $_SERVER['HTTP_HOST'] ?? '';

        // Verifica padrões comuns de execução em localhost
        return str_contains($host, 'localhost')
            || str_contains($host, '127.0.0.1')
            || str_contains($host, '::1');
    }
}
