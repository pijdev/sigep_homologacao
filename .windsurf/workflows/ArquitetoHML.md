---
description: Arquiteto SIGEP HML
---

# ArquitetoHML

Use este arquivo como prompt-base e workflow operacional para qualquer agente IA que va atuar no SIGEP em homologacao.

## Missao

Voce esta trabalhando exclusivamente no ambiente de homologacao do SIGEP.

- Projeto: `C:\Sites\sigep_hml`
- Documentacao-base: `C:\Sites\sigep_hml\docs`
- Banco correto: `sigep_homologacao`
- Ambiente de producao que nao deve ser alterado por engano: `C:\Sites\sigep`

Seu objetivo e executar tarefas com precisao tecnica, sem assumir arquitetura legada e sem misturar homologacao com producao.

## Ordem obrigatoria de leitura

Antes de propor ou editar qualquer coisa, leia nesta ordem:

1. `C:\Sites\sigep_hml\docs\arquitetura\visao-geral-hml.md`
2. `C:\Sites\sigep_hml\docs\arquitetura\interface-grafica-e-navegacao.md`
3. `C:\Sites\sigep_hml\docs\arquitetura\acl-e-permissoes.md`
4. `C:\Sites\sigep_hml\docs\banco-de-dados\guia-operacional.md`
5. `C:\Sites\sigep_hml\docs\desenvolvimento\criacao-de-paginas-e-modulos.md`
6. `C:\Sites\sigep_hml\docs\desenvolvimento\checklist-de-entrega.md`

Se a tarefa envolver migracao de modulo do ambiente de producao para o HML, leia tambem:

7. `C:\Sites\sigep_hml\docs\MigrarModulos.md`
8. `C:\Sites\sigep_hml\docs\desenvolvimento\migracao-de-modulos-legados.md`

## Verdades do ambiente

- O projeto ativo e Laravel 13 com PHP 8.4 e pacote `dfsmania/laradminlte`.
- A interface usa Blade + componentes `x-ladmin-*` + Bootstrap 5 + Bootstrap Icons.
- O login usa Fortify, campo `login` e exige `unidade_id`.
- A unidade escolhida e salva na sessao em `unidade_selecionada`.
- O ACL atual esta concentrado em `App\Services\AclService`.
- A visibilidade do menu lateral depende de `config/ladmin/menu.php`.
- O CRUD administrativo atual fica em `App\Http\Controllers\Admin` e `resources/views/admin`.
- As tabelas principais de autorizacao sao `autenticacao_setores`, `autenticacao_permissoes`, `autenticacao_usuario_permissao` e `autenticacao_usuario_unidade`.
- O estado atual do projeto nao corresponde ao padrao legado `modulos/[setor]/[nome]`. Nao proponha nem implemente novos recursos nesse formato, salvo instrucao humana explicita.

## Regras de atuacao

- Sempre confirme mentalmente se esta atuando em `sigep_hml` e nao em `sigep`.
- Ao lidar com banco, preserve o nome `sigep_homologacao` em toda analise, script, migration e validacao.
- Nao exponha nem replique credenciais do `.env` na documentacao ou nas respostas.
- Antes de criar tela nova, identifique: rota, controller, policy/ACL, item de menu, view Blade e dados necessarios.
- Antes de alterar permissoes, identifique o codigo ACL e o nivel exigido: `read`, `write` ou `owner`.
- Sempre que for criar recurso administrativo novo, mantenha padrao de UX existente: `x-ladmin-panel`, `contentHeader`, cards Bootstrap, feedback de status e validacao server-side.
- Se encontrar documentacao antiga conflitante com o codigo, confie primeiro no codigo atual e atualize a documentacao.

## Workflow padrao para qualquer tarefa

1. Confirmar escopo em homologacao.
2. Ler rota, controller, view e config relacionados ao recurso.
3. Mapear impacto em ACL e menu.
4. Mapear impacto em banco, migration, seeders e dados existentes.
5. Implementar seguindo os padroes descritos nesta pasta.
6. Validar navegacao, permissao, mensagens, consistencia visual e risco de regressao.
7. Registrar na propria documentacao se a alteracao mudar arquitetura, ACL, menu ou processo operacional.
8. Atualizar o `C:\Sites\sigep_hml\CHANGELOG.md` com a alteracao.

## Quando a tarefa envolver banco de dados

- Verifique primeiro `docs/banco-de-dados/guia-operacional.md`.
- Prefira migrations e seeders reproduziveis em vez de alteracoes manuais irreversiveis.
- Se a tarefa exigir SQL manual, deixe claro o objetivo, tabelas afetadas e impacto no ACL.
- Qualquer nova tabela de seguranca ou catalogo deve manter nomenclatura consistente com o dominio atual.

## Quando a tarefa envolver interface grafica

- Verifique primeiro `docs/arquitetura/interface-grafica-e-navegacao.md`.
- Preserve a linguagem visual do LaradminLTE/AdminLTE 4.
- Evite criar telas fora do padrao de cards, tabelas e formularios ja usados em `resources/views/admin`.
- Se a pagina precisar aparecer no menu, ajuste `config/ladmin/menu.php` com regra `is_allowed` coerente.

## Quando a tarefa envolver permissao

- Verifique primeiro `docs/arquitetura/acl-e-permissoes.md`.
- Use `AclService` como fonte principal da regra de negocio.
- Se criar um novo recurso funcional, pense no ciclo completo:
    - cadastrar o recurso em `autenticacao_permissoes`
    - decidir tipo (`menu`, `setor`, `subsetor`, `modulo`, `pagina`, `acao`)
    - definir `codigo`, `slug`, `parent_id`, `setor_id`, `ordem`, `ativo`
    - decidir como o controller vai bloquear acesso
    - decidir como o menu vai esconder ou exibir a opcao

## Entrega esperada

Ao concluir qualquer tarefa, a solucao precisa estar:

- alinhada ao ambiente de homologacao
- coerente com o codigo Laravel atual
- consistente com a ACL vigente
- documentada quando mudar comportamento estrutural
- segura para evolucao futura por humanos ou outros agentes
