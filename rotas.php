<?php

use Pecee\SimpleRouter\SimpleRouter;
use sistema\Nucleo\Helpers;

try {

    /* ============================================================
       DEFINIÇÃO DO NAMESPACE PADRÃO PARA OS CONTROLADORES DO SITE
       ============================================================ */
    SimpleRouter::setDefaultNamespace('sistema\Controlador');

    /* ============================================================
       ROTAS PÚBLICAS DO SITE
       ============================================================ */

    // Página inicial (inclui suporte para index.php)
    SimpleRouter::get(URL_SITE, 'SiteControlador@index');
    SimpleRouter::get(URL_SITE . 'index.php', 'SiteControlador@index');

    // Página "Sobre nós"
    SimpleRouter::get(URL_SITE . 'sobre-nos', 'SiteControlador@sobre');

    // Página de leitura de post — separada por categoria e slug
    SimpleRouter::get(URL_SITE . 'post/{categoria}/{slug}', 'SiteControlador@post');

    // Listagem de posts por categoria com paginação opcional
    SimpleRouter::get(URL_SITE . 'categoria/{slug}/{pagina?}', 'SiteControlador@categoria');

    // Busca geral do site (POST)
    SimpleRouter::post(URL_SITE . 'buscar', 'SiteControlador@buscar');

    // Página de contato (aceita GET e POST)
    SimpleRouter::match(['get', 'post'], URL_SITE . 'contato', 'SiteControlador@contato');

    // Página de erro personalizada (404)
    SimpleRouter::get(URL_SITE . '404', 'SiteControlador@erro404');

    /* ============================================================
       ROTAS DO SISTEMA ADMINISTRATIVO
       ============================================================ */
    SimpleRouter::group(['namespace' => 'Admin'], function () {

        /* -----------------------
           LOGIN ADMINISTRATIVO
           ----------------------- */

        // Página inicial do painel (redirect normalmente)
        SimpleRouter::get(URL_ADMIN, 'AdminLogin@index');

        // Login (GET exibe formulário / POST processa login)
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'login', 'AdminLogin@login');

        /* -----------------------
           DASHBOARD
           ----------------------- */

        // Página principal do dashboard
        SimpleRouter::get(URL_ADMIN . 'dashboard', 'AdminDashboard@dashboard');

        // Logout
        SimpleRouter::get(URL_ADMIN . 'sair', 'AdminDashboard@sair');

        /* -----------------------
           GESTÃO DE USUÁRIOS
           ----------------------- */

        SimpleRouter::get(URL_ADMIN . 'usuarios/listar', 'AdminUsuarios@listar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'usuarios/cadastrar', 'AdminUsuarios@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'usuarios/editar/{id}', 'AdminUsuarios@editar');
        SimpleRouter::get(URL_ADMIN . 'usuarios/deletar/{id}', 'AdminUsuarios@deletar');

        // Endpoint para DataTables
        SimpleRouter::post(URL_ADMIN . 'usuarios/datatable', 'AdminUsuarios@datatable');

        /* -----------------------
           GESTÃO DE POSTS
           ----------------------- */

        SimpleRouter::get(URL_ADMIN . 'posts/listar', 'AdminPosts@listar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'posts/cadastrar', 'AdminPosts@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'posts/editar/{id}', 'AdminPosts@editar');
        SimpleRouter::get(URL_ADMIN . 'posts/deletar/{id}', 'AdminPosts@deletar');

        // Endpoint para DataTables
        SimpleRouter::post(URL_ADMIN . 'posts/datatable', 'AdminPosts@datatable');

        /* -----------------------
           GESTÃO DE CATEGORIAS
           ----------------------- */

        SimpleRouter::get(URL_ADMIN . 'categorias/listar', 'AdminCategorias@listar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'categorias/cadastrar', 'AdminCategorias@cadastrar');
        SimpleRouter::match(['get', 'post'], URL_ADMIN . 'categorias/editar/{id}', 'AdminCategorias@editar');
        SimpleRouter::get(URL_ADMIN . 'categorias/deletar/{id}', 'AdminCategorias@deletar');
    });

    /* ============================================================
       INICIALIZAÇÃO DAS ROTAS
       ============================================================ */
    SimpleRouter::start();


/* ============================================================
   EXCEÇÃO DE ROTA NÃO ENCONTRADA (404)
   ============================================================ */
} catch (Pecee\SimpleRouter\Exceptions\NotFoundHttpException $ex) {

    // Em ambiente local: exibe mensagem detalhada
    if (Helpers::localhost()) {
        echo '<strong>Rota não encontrada:</strong> ' . htmlspecialchars($ex->getMessage());

    // Em produção: redireciona para página 404 amigável
    } else {
        Helpers::redirecionar('404');
    }
}
