<?php

namespace sistema\Biblioteca;

/**
 * Responsável por validação, renomeação e upload de arquivos.
 */
class Upload
{
    // Diretório base onde os arquivos serão armazenados
    private ?string $diretorio;

    // Dados do arquivo recebido via upload
    private ?array $arquivo;

    // Nome final do arquivo salvo
    private ?string $nome;

    // Subdiretório interno para organização
    private ?string $subDiretorio;

    // Tamanho máximo permitido em MB
    private ?int $tamanho;

    // Resultado final do upload (nome do arquivo)
    private ?string $resultado = null;

    // Mensagem de erro em caso de falha
    private ?string $erro;

    /**
     * Retorna o nome final do arquivo enviado.
     */
    public function getResultado(): ?string
    {
        return $this->resultado;
    }

    /**
     * Retorna a mensagem de erro gerada no processo.
     */
    public function getErro(): ?string
    {
        return $this->erro;
    }

    /**
     * Inicializa o diretório base de uploads.
     */
    public function __construct(string $diretorio = null)
    {
        // Define diretório padrão caso nenhum seja informado
        $this->diretorio = $diretorio ?? 'uploads';

        // Cria o diretório caso não exista
        if (!file_exists($this->diretorio) && !is_dir($this->diretorio)) {
            mkdir($this->diretorio, 0755);
        }
    }

    /**
     * Valida e processa o upload do arquivo.
     */
    public function arquivo(array $arquivo, string $nome = null, string $subDiretorio = null, int $tamanho = null)
    {
        // Inicializa propriedades do upload
        $this->arquivo = $arquivo;
        $this->nome = $nome ?? pathinfo($this->arquivo['name'], PATHINFO_FILENAME);
        $this->subDiretorio = $subDiretorio ?? 'arquivos';
        $this->tamanho = $tamanho ?? 1;

        // Obtém extensão do arquivo
        $extensao = pathinfo($this->arquivo['name'], PATHINFO_EXTENSION);

        // Extensões permitidas
        $extensoesValidas = ['pdf', 'png', 'docx', 'jpg', 'gif', 'txt'];

        // MIME types permitidos
        $tiposValidos = [
            'application/pdf',
            'text/plain',
            'image/png',
            'image/x-png',
            'image/gif',
            'image/jpeg',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        // Validação da extensão do arquivo
        if (!in_array($extensao, $extensoesValidas)) {
            $this->erro = 'Erro: Extensão de arquivo não permitida.';
        
        // Validação do tipo MIME
        } elseif (!in_array($this->arquivo['type'], $tiposValidos)) {
            $this->erro = 'Erro: Tipo de arquivo inválido.';
        
        // Validação do tamanho máximo permitido
        } elseif ($this->arquivo['size'] > $this->tamanho * (1024 * 1024)) {
            $this->erro = "Erro: O arquivo excede o tamanho máximo de {$this->tamanho}MB.";
        
        // Processamento do upload
        } else {
            $this->criarSubDiretorio();
            $this->renomarArquivo();
            $this->moverArquivo();
        }
    }

    /**
     * Cria o subdiretório de destino se necessário.
     */
    private function criarSubDiretorio(): void
    {
        $path = $this->diretorio . DIRECTORY_SEPARATOR . $this->subDiretorio;

        // Cria o subdiretório caso não exista
        if (!file_exists($path) && !is_dir($path)) {
            mkdir($path, 0755);
        }
    }

    /**
     * Garante nome único para o arquivo salvo.
     */
    private function renomarArquivo(): void
    {
        // Define nome base com extensão
        $arquivo = $this->nome . strrchr($this->arquivo['name'], '.');

        // Gera nome único caso já exista
        if (file_exists($this->diretorio . DIRECTORY_SEPARATOR . $this->subDiretorio . DIRECTORY_SEPARATOR . $arquivo)) {
            $arquivo = $this->nome . '-' . uniqid() . strrchr($this->arquivo['name'], '.');
        }

        $this->nome = $arquivo;
    }

    /**
     * Move o arquivo para o destino final.
     */
    private function moverArquivo(): void
    {
        // Executa upload e define resultado
        if (move_uploaded_file(
            $this->arquivo['tmp_name'],
            $this->diretorio . DIRECTORY_SEPARATOR . $this->subDiretorio . DIRECTORY_SEPARATOR . $this->nome
        )) {
            $this->resultado = $this->nome;
        } else {
            $this->resultado = null;
            $this->erro = 'Erro ao mover o arquivo para o destino final.';
        }
    }
}
