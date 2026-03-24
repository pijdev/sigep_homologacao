# Interface Grafica e Navegacao

## Base visual atual

A interface atual usa LaradminLTE/AdminLTE 4 com Blade components. O padrao visivel nas telas administrativas e consistente e deve ser preservado.

### Componentes e convencoes ja em uso

- wrapper principal com `<x-ladmin-panel title="...">`
- cabecalho da pagina em `<x-slot name="contentHeader">`
- cards Bootstrap com `card`, `card-header`, `card-body`
- botoes com classes Bootstrap e icones `bi bi-*`
- mensagens de sucesso por `session('status')`
- mensagens de erro por `$errors->any()`
- tabelas responsivas com `table-responsive`

## Exemplo de pagina existente

As telas em `resources/views/admin/users/index.blade.php` e `resources/views/admin/users/form.blade.php` sao as melhores referencias para criar CRUDs novos no padrao atual.

## Menu e navegacao

O menu principal nao esta espalhado em includes antigos. O ponto central atual e:

- `config/ladmin/menu.php`

Ali ficam:

- links de navbar
- estrutura do sidebar
- submenus
- regra de visibilidade por `is_allowed`
- relacao entre item visual e rota Laravel

## Regra pratica para novas paginas

Se uma nova pagina precisa ficar navegavel no sistema, normalmente ela exige:

1. rota nomeada em `routes/web.php`
2. controller e metodo correspondentes
3. view Blade em `resources/views/...`
4. item de menu em `config/ladmin/menu.php`, se for navegacao primaria
5. regra de autorizacao coerente em `is_allowed`

## Padrao recomendado para tela index

Uma listagem administrativa nova deve seguir o formato:

- cabecalho com titulo, icone e descricao curta
- acao primaria de criacao no canto superior direito
- card principal com tabela ou lista
- coluna final de acoes com editar/excluir/detalhar
- paginacao no rodape quando necessario

## Padrao recomendado para tela form

Um formulario novo deve seguir o formato:

- botao de voltar no cabecalho
- card para dados principais
- cards auxiliares para configuracoes ou relacionamentos
- validacao server-side no controller
- mensagens de erro visiveis no topo
- submit claro com texto orientado a acao

## Login e autenticacao visual

A tela de login esta em `resources/views/auth/login.blade.php`.

Particularidades importantes:

- usa campo `login`, nao `email`
- exige selecao de `unidade_id`
- carrega unidades de `App\Models\UnidadePrisional`
- usa componentes `x-ladmin-auth-base`, `x-ladmin-checkbox` e `x-ladmin-button`

## O que evitar na interface

- criar paginas fora do padrao visual do LaradminLTE sem motivo forte
- esconder regra de permissao apenas na view; o bloqueio principal deve existir no controller
- inserir item de menu sem controlar visibilidade por ACL
- introduzir outra biblioteca UI sem necessidade real
- depender de JavaScript para validacoes criticas de negocio

## Checklist minimo para UI nova

- a rota abre sem erro
- o titulo da pagina corresponde ao objetivo
- o menu aponta para a rota correta, se aplicavel
- a tela respeita permissao e nao aparece para quem nao deve ver
- mensagens de sucesso e erro estao claras
- o layout se encaixa visualmente nas paginas administrativas existentes
