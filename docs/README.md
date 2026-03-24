# SIGEP HML - Base de Conhecimento

Esta pasta e o arquivo `ArquitetoHML.md` formam a base de contexto para agentes de IA trabalharem no ambiente de homologacao com seguranca.

## Leitura recomendada

1. `ArquitetoHML.md` - ponto de entrada para o Cascade
2. `arquitetura/visao-geral-hml.md` - arquitetura real do projeto
3. `arquitetura/interface-grafica-e-navegacao.md` - padroes de UI, Blade e menu
4. `arquitetura/acl-e-permissoes.md` - modelo de autorizacao e regras de escopo
5. `banco-de-dados/guia-operacional.md` - banco `sigep_homologacao`, migrations e seeders
6. `desenvolvimento/criacao-de-paginas-e-modulos.md` - passo a passo para criar telas novas
7. `desenvolvimento/checklist-de-entrega.md` - validacao antes de concluir qualquer tarefa

## Regras centrais

- Ambiente alvo: `C:\Sites\sigep_hml`
- Banco alvo: `sigep_homologacao`
- Nunca confundir com producao em `C:\Sites\sigep`
- O codigo ativo e Laravel 13 + LaradminLTE/AdminLTE 4, nao a estrutura legada `modulos/[setor]/[nome]`
- Autenticacao usa Laravel Fortify com login por `login` e selecao obrigatoria de `unidade_id`
- Controle de acesso usa `App\Services\AclService` + tabelas `autenticacao_*`
- Menus visiveis dependem de `config/ladmin/menu.php`
- Novas paginas devem respeitar rota, controller, view Blade e regra de autorizacao coerentes

## Fontes do proprio projeto

- `README.md`
- `.windsurfrules`
- `routes/web.php`
- `config/ladmin/menu.php`
- `app/Services/AclService.php`
- `app/Http/Controllers/Admin/*`
- `database/migrations/*`
- `database/seeders/*`

## Objetivo desta base

Permitir que o Cascade entre no chat, leia `ArquitetoHML.md`, siga os links desta pasta e trabalhe com contexto suficiente para:

- criar ou alterar paginas com baixo risco
- manter consistencia visual com LaradminLTE
- nao quebrar ACL nem navegacao
- nao usar banco ou ambiente errado
- evitar assumir estruturas antigas que nao representam o estado atual do codigo
