<?php

use App\Services\AclService;
use DFSmania\LaradminLte\Tools\Menu\Enums\MenuItemType;
use DFSmania\LaradminLte\Tools\Menu\Enums\MenuPlacement;

return [
    MenuPlacement::NAVBAR->value => [
        [
            'type' => MenuItemType::LINK,
            'icon' => 'bi bi-list fs-5',
            'url' => '#',
            'position' => 'left',
            'role' => 'button',
            'data-lte-toggle' => 'sidebar',
        ],
        [
            'type' => MenuItemType::FULLSCREEN_TOGGLER,
            'icon_expand' => 'bi bi-fullscreen fs-5',
            'icon_collapse' => 'bi bi-fullscreen-exit fs-5',
            'position' => 'right',
        ],
    ],

    MenuPlacement::SIDEBAR->value => [
        [
            'type' => MenuItemType::LINK,
            'label' => 'Início',
            'icon' => 'bi bi-house-door-fill',
            'route' => ['dashboard'],
        ],
        [
            'type' => MenuItemType::MENU,
            'label' => 'Administração',
            'icon' => 'bi bi-shield-lock-fill text-primary',
            'is_allowed' => fn() => app(AclService::class)->canManageUsers(auth()->user())
                || app(AclService::class)->canManageCatalog(auth()->user()),
            'submenu' => [
                [
                    'type' => MenuItemType::MENU,
                    'label' => 'Acessos',
                    'icon' => 'bi bi-key-fill',
                    'is_allowed' => fn() => app(AclService::class)->canManageUsers(auth()->user())
                        || app(AclService::class)->canManageCatalog(auth()->user()),
                    'submenu' => [
                        [
                            'type' => MenuItemType::LINK,
                            'label' => 'Usuários',
                            'icon' => 'bi bi-people-fill',
                            'route' => ['admin.users.index'],
                            'is_allowed' => fn() => app(AclService::class)->canManageUsers(auth()->user()),
                        ],
                        [
                            'type' => MenuItemType::LINK,
                            'label' => 'Setores',
                            'icon' => 'bi bi-diagram-3-fill',
                            'route' => ['admin.sectors.index'],
                            'is_allowed' => fn() => app(AclService::class)->canManageSectors(auth()->user()),
                        ],
                        [
                            'type' => MenuItemType::LINK,
                            'label' => 'Unidades',
                            'icon' => 'bi bi-building-fill-gear',
                            'route' => ['admin.units.index'],
                            'is_allowed' => fn() => app(AclService::class)->canManageUnits(auth()->user()),
                        ],
                        [
                            'type' => MenuItemType::LINK,
                            'label' => 'Recursos ACL',
                            'icon' => 'bi bi-sliders2-vertical',
                            'route' => ['admin.permissions.index'],
                            'is_allowed' => fn() => app(AclService::class)->canManageResources(auth()->user()),
                        ],
                    ],
                ],
                [
                    'type' => MenuItemType::MENU,
                    'label' => 'Importação',
                    'icon' => 'bi bi-file-earmark-arrow-up-fill text-info',
                    'is_allowed' => fn() => app(AclService::class)->userHasPermission(auth()->user(), 'pagina.importacao_ipen.relatorio_18', 'read')
                        || app(AclService::class)->userHasPermission(auth()->user(), 'pagina.importacao_laboral.relatorio_64', 'read'),
                    'submenu' => [
                        [
                            'type' => MenuItemType::LINK,
                            'label' => 'Relatório 1-8',
                            'icon' => 'bi bi-file-earmark-text',
                            'route' => ['admin.importacao.ipen.index'],
                            'is_allowed' => fn() => app(AclService::class)->userHasPermission(auth()->user(), 'pagina.importacao_ipen.relatorio_18', 'read'),
                        ],
                        [
                            'type' => MenuItemType::LINK,
                            'label' => 'Relatório 6-4 (Trabalho)',
                            'icon' => 'bi bi-briefcase',
                            'route' => ['admin.importacao.laboral.index'],
                            'is_allowed' => fn() => app(AclService::class)->userHasPermission(auth()->user(), 'pagina.importacao_laboral.relatorio_64', 'read'),
                        ],
                    ],
                ],
                [
                    'type' => MenuItemType::MENU,
                    'label' => 'Relatórios',
                    'icon' => 'bi bi-file-earmark-ruled text-warning',
                    'is_allowed' => fn() => app(AclService::class)->userHasPermission(auth()->user(), 'pagina.importacao_ipen.relatorio_18', 'read')
                        || app(AclService::class)->userHasPermission(auth()->user(), 'pagina.importacao_laboral.relatorio_64', 'read'),
                    'submenu' => [
                        [
                            'type' => MenuItemType::LINK,
                            'label' => 'Histórico Importação 1-8',
                            'icon' => 'bi bi-clock-history',
                            'route' => ['admin.importacao.ipen.historico'],
                            'is_allowed' => fn() => app(AclService::class)->userHasPermission(auth()->user(), 'pagina.importacao_ipen.relatorio_18', 'read'),
                        ],
                        [
                            'type' => MenuItemType::LINK,
                            'label' => 'Histórico Importação 6-4',
                            'icon' => 'bi bi-clock-history',
                            'route' => ['admin.importacao.laboral.historico'],
                            'is_allowed' => fn() => app(AclService::class)->userHasPermission(auth()->user(), 'pagina.importacao_laboral.relatorio_64', 'read'),
                        ],
                    ],
                ],
            ],
        ],
    ],
];
