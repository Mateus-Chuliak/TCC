<?php

namespace sistema\Controlador;

use sistema\Nucleo\Controlador;
use sistema\Modelo\TanqueModelo;
use sistema\Modelo\EventoModelo;
use sistema\Nucleo\Helpers;

class SiteControlador extends Controlador
{
    public function __construct()
    {
        parent::__construct('templates/site/views');
    }

    /**
     * Página inicial do site
     */
    public function index(): void
    {
        $tanques = (new TanqueModelo())->busca()->ordem('id DESC')->limite(5)->resultado(true);
        $eventos = (new EventoModelo())->busca()->ordem('data_evento ASC')->limite(3)->resultado(true);

        echo $this->template->renderizar('index.html', [
            'tanques' => $tanques,
            'eventos' => $eventos,
        ]);
    }

    /**
     * Página com lista completa de tanques
     */
    public function tanques(): void
    {
        $tanques = (new TanqueModelo())->busca()->ordem('id DESC')->resultado(true);

        echo $this->template->renderizar('tanques.html', [
            'tanques' => $tanques,
        ]);
    }

    /**
     * Página de eventos
     */
    public function eventos(): void
    {
        $eventos = (new EventoModelo())->busca()->ordem('data_evento ASC')->resultado(true);

        echo $this->template->renderizar('eventos.html', [
            'eventos' => $eventos,
        ]);
    }

    /**
     * Página "Sobre"
     */
    public function sobre(): void
    {
        echo $this->template->renderizar('sobre.html', [
            'titulo' => 'Sobre o Pesqueiro Zéfish',
        ]);
    }

    /**
     * Página "Contato"
     */
    public function contato(): void
    {
        echo $this->template->renderizar('contato.html', [
            'titulo' => 'Entre em Contato',
        ]);
    }

    /**
     * Página de erro 404
     */
    public function erro404(): void
    {
        echo $this->template->renderizar('404.html', [
            'titulo' => 'Página não encontrada',
        ]);
    }
}
