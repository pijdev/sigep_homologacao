# Visao Geral do SIGEP HML

## Escopo do ambiente

O ambiente de homologacao fica em `C:\Sites\sigep_hml` e existe para validar alteracoes antes de qualquer movimento em producao. O banco associado a este ambiente e `sigep_homologacao`.

Nunca trate `C:\Sites\sigep_hml` como espelho descartavel de producao. Ele e o ambiente seguro para experimentar, validar ACL, revisar interface e testar regras de negocio com previsibilidade.

## Stack real do projeto

- Framework: Laravel 13
- PHP: 8.4
- UI base: `dfsmania/laradminlte` sobre AdminLTE 4
- Frontend: Blade, Bootstrap 5, Bootstrap Icons
- Autenticacao: Laravel Fortify
- Banco principal: MySQL com database `sigep_homologacao`
- Cache/servicos auxiliares: Redis e Ollama aparecem na documentacao e configuracoes do projeto

## Estrutura real relevante

```text
app/
  Http/Controllers/Admin/        CRUDs administrativos atuais
  Models/                        Modelos Eloquent centrais
  Services/AclService.php        Regra principal de autorizacao
  Services/CustomUserProvider.php Login customizado
bootstrap/app.php                Registro de middleware web
config/auth.php                  Provider customizado
config/fortify.php               Login por campo login
config/ladmin/menu.php           Menu lateral e regras visuais de acesso
resources/views/admin/           Telas administrativas em Blade
resources/views/auth/login.blade.php
routes/web.php                   Rotas web autenticadas
database/migrations/             Estrutura de banco versionada
database/seeders/                Seeders de unidades e ACL
```

## Fluxo de requisicao resumido

1. O usuario acessa `/`.
2. Se nao estiver autenticado, vai para `/login`.
3. O login usa `login`, `password` e `unidade_id`.
4. O provider customizado valida usuario ativo e acesso a unidade.
5. A unidade escolhida e salva em sessao como `unidade_selecionada`.
6. Depois do login, o usuario navega para `laradminlte-welcome` e demais rotas autenticadas.
7. Controllers e menu aplicam restricoes usando `AclService`.

## Rotas atuais mais importantes

- `/` redireciona para login ou dashboard conforme sessao
- `/dashboard` e `/laradminlte-welcome` usam view do painel
- `/administracao/usuarios` CRUD de usuarios
- `/administracao/setores` CRUD de setores
- `/administracao/unidades` CRUD de unidades
- `/administracao/permissoes` CRUD do catalogo ACL

## Arquitetura de CRUD atual

O padrao implementado hoje para paginas administrativas e:

- rota em `routes/web.php`
- controller em `App\Http\Controllers\Admin`
- view Blade em `resources/views/admin/...`
- validacao no controller
- bloqueio de acesso com `abort_unless(...)` + `AclService`
- persistencia via Eloquent e `DB::transaction(...)` quando ha multiplas relacoes
- feedback ao usuario com `->with('status', '...')`

## O que nao assumir

Nao assumir que o sistema usa hoje:

- estrutura `modulos/[setor]/[nome]`
- `_view.php` e `_logica.php`
- navegacao SPA legada por jQuery como padrao dominante
- controle de permissao disperso em includes antigos

Se alguma demanda futura citar esse formato, trate como requisito especial e valide primeiro com o codigo ativo.

## Fontes canonicamente confiaveis

Em caso de conflito entre docs antigas e implementacao, considere como fonte principal:

1. `routes/web.php`
2. `app/Services/AclService.php`
3. `config/ladmin/menu.php`
4. `app/Http/Controllers/Admin/*`
5. `database/migrations/*`
6. `database/seeders/*`
