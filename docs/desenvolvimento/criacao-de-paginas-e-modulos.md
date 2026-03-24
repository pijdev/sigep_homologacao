# Criacao de Paginas e Modulos

## Principio base

No estado atual do SIGEP HML, criar uma nova pagina ou modulo significa expandir a aplicacao Laravel existente, e nao criar pastas no formato legado `modulos/[setor]/[nome]`.

O fluxo correto passa por rota, controller, view Blade, menu e ACL.

## Fluxo padrao

### 1. Definir o objetivo funcional

Antes de codar, responda:

- qual problema a nova pagina resolve?
- quem pode acessar?
- precisa aparecer no menu?
- depende de setor, unidade ou ambos?
- exige novas tabelas ou apenas CRUD em tabelas existentes?

### 2. Criar ou ajustar rota

Use `routes/web.php` dentro do grupo autenticado. Para CRUD administrativo, prefira manter o padrao existente com `Route::prefix('administracao')->name('admin.')->group(...)`.

Se fizer sentido, use `Route::resource(...)` como ja ocorre com:

- usuarios
- setores
- unidades
- permissoes

### 3. Criar controller

Local padrao atual:

- `app/Http/Controllers/Admin`

Padroes recomendados:

- injecao de `AclService` no construtor quando houver regra de permissao
- `abort_unless(..., 403)` no inicio de cada action relevante
- `Request` com validacao clara
- `DB::transaction(...)` ao salvar dados e relacoes juntas
- redirects com `with('status', '...')`

### 4. Criar views Blade

Locais padrao:

- `resources/views/admin/<recurso>/index.blade.php`
- `resources/views/admin/<recurso>/form.blade.php`

Padrao visual:

- `x-ladmin-panel`
- `contentHeader`
- `card` Bootstrap
- tabela para index
- formulario organizado em cards

### 5. Integrar no menu

Se o recurso precisa entrar na navegacao principal:

- editar `config/ladmin/menu.php`
- apontar para a rota nomeada correta
- definir `icon`
- incluir `is_allowed` coerente

### 6. Integrar com ACL

Perguntas obrigatorias:

- existe permissao ACL que representa este recurso?
- o controller deve bloquear por perfil administrativo ou por codigo ACL especifico?
- o menu deve esconder a opcao quando o usuario nao tiver acesso?
- a permissao pode ser limitada por unidade?

Se o recurso for novo no modelo de autorizacao, avalie criar registros em `autenticacao_permissoes` via seeder.

### 7. Validar ponta a ponta

Antes de considerar pronto:

- a rota responde
- a view renderiza sem erro
- o menu mostra ou esconde corretamente
- o controller devolve 403 quando necessario
- a gravacao persiste corretamente no banco `sigep_homologacao`
- mensagens de sucesso e erro estao claras

## Estrutura minima sugerida para CRUD novo

```text
app/Http/Controllers/Admin/NovoRecursoController.php
resources/views/admin/novo-recurso/index.blade.php
resources/views/admin/novo-recurso/form.blade.php
routes/web.php
config/ladmin/menu.php
```

## Quando houver impacto de banco

Se a tela precisar de tabela nova ou colunas novas:

- criar migration
- ajustar model, se aplicavel
- criar ou atualizar seeder, se precisar de dados iniciais
- revisar ACL se houver recurso novo protegido

## Quando nao colocar no menu

Nem toda pagina precisa de menu. Nao coloque no sidebar quando for:

- fluxo auxiliar acessado por link interno
- tela tecnica temporaria
- endpoint de manutencao sem navegacao principal
- pagina de detalhe subordinada a um recurso principal

## Erros comuns a evitar

- criar view sem rota nomeada
- criar rota sem bloqueio de permissao
- criar menu sem `is_allowed`
- salvar dados sem transaction quando ha multiplas relacoes
- misturar padrao Laravel atual com estrutura legada de `modulos/`
- usar documentacao antiga como se fosse arquitetura vigente
