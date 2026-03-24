<?php

/*
|--------------------------------------------------------------------------
| Configuração Geral - SIGEP
|--------------------------------------------------------------------------
|
| Configurações principais do painel administrativo SIGEP.
| Personalize informações básicas, layout e funcionalidades.
|
*/
return [

    /*
    |--------------------------------------------------------------------------
    | Informações Básicas
    |--------------------------------------------------------------------------
    */

    'basic' => [
        // Nome da empresa
        'company' => 'Penitenciária Industrial de Joinville',

        // Site da empresa
        'company_url' => 'https://www.sap.sc.gov.br/penitenciaria-industrial-de-joinville-1/',

        // Ano de início do desenvolvimento
        'start_year' => 2025,

        // Versão do sistema
        'version' => '2.0.3',
    ],

    /*
    |--------------------------------------------------------------------------
    | Favicons
    |--------------------------------------------------------------------------
    */

    'favicons' => [
        // Suporte completo para favicons
        'full_support' => true,

        // Cor principal do logo
        'brand_logo_color' => '#000000',

        // Cor de fundo do logo
        'brand_background_color' => '#ffffff',

        // Tamanhos dos favicons PNG
        'png_sizes' => ['16x16', '32x32', '96x96'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    */

    'logo' => [
        // Caminho da imagem do logo
        'image' => '/vendor/ladmin/img/logo_512.png',

        // Texto alternativo do logo
        'image_alt' => 'Sistema Prisional Integrado',

        // Classes CSS da imagem
        'image_classes' => ['rounded-circle', 'shadow'],

        // Texto do logo
        'text' => 'SIGEP',

        // Classes CSS do texto
        'text_classes' => ['fw-bold'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Ícones
    |--------------------------------------------------------------------------
    */

    'icons' => [
        // Ícone do menu expansível
        'treeview_toggler' => 'bi bi-chevron-right',
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    'layout' => [
        // Tema Bootstrap: 'light' ou 'dark'
        'bootstrap_theme' => 'DARK',

        // Modo compacto
        'compact_mode' => false,

        // Rodapé fixo
        'fixed_footer' => false,

        // Barra superior fixa
        'fixed_navbar' => true,

        // Barra lateral fixa
        'fixed_sidebar' => true,

        // Layout RTL (direita para esquerda)
        'rtl' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Barra Superior
    |--------------------------------------------------------------------------
    */

    'navbar' => [
        // Classes CSS adicionais da barra
        'classes' => ['bg-body'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Barra Lateral
    |--------------------------------------------------------------------------
    */

    'sidebar' => [
        // Menu tipo acordeão
        'accordion' => false,

        // Tema da barra: 'light', 'dark' ou null
        'bootstrap_theme' => 'dark',

        // Classes CSS adicionais
        'classes' => ['bg-body-secondary', 'shadow'],

        // Iniciar contraído
        'default_collapsed' => false,

        // Breakpoint para expansão: 'sm', 'md', 'lg', 'xl', 'xxl'
        'expand_breakpoint' => 'lg',

        // Modo mini (apenas ícones)
        'mini_sidebar' => true,

        // Persistir estado no localStorage
        'persistence' => false,

        // Velocidade da animação (ms)
        'treeview_animation_speed' => 300,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rodapé
    |--------------------------------------------------------------------------
    */

    'footer' => [
        // Classes CSS adicionais do rodapé
        'classes' => ['bg-body'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Conteúdo Principal
    |--------------------------------------------------------------------------
    */

    'main_content' => [
        // Classes CSS adicionais do conteúdo
        'classes' => ['bg-body-tertiary'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Traduções do Menu
    |--------------------------------------------------------------------------
    */

    'menu_translations' => [
        // Habilitar traduções
        'enabled' => true,

        // Arquivo PHP de traduções
        'php_file' => 'ladmin_menu',
    ],
];
