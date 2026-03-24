---
description: Migrar Módulos para SIGEP HML
---

# MigrarModulos

Use este arquivo como workflow-base quando a tarefa for migrar ou reimaginar um modulo do sistema de producao para o ambiente HML.

## Missao

Voce deve estudar um modulo existente no ambiente de producao, compreender sua regra de negocio, seus fluxos, dependencias, consultas SQL, pontos de entrada de menu e comportamento de interface, e depois reconstruir uma versao nova e melhor em `C:\Sites\sigep_hml`, usando os padroes atuais do projeto HML.

## Escopo dos ambientes

### Origem legada

- Codigo-fonte de referencia: `C:\Sites\sigep`
- Banco-fonte de referencia: `sigep_producao`

### Destino moderno

- Codigo-alvo: `C:\Sites\sigep_hml`
- Banco-alvo: `sigep_homologacao`

## Objetivo da migracao

Nao copiar o legado literalmente.

O objetivo e:

- aprender a logica de negocio do modulo legado
- identificar o que precisa ser preservado
- separar regra de negocio de gambiarra historica
- reconstruir a funcionalidade no HML com Laravel, LaradminLTE, MySQL e ACL modernos
- melhorar UX, organizacao de codigo, seguranca e manutenibilidade

## Ordem obrigatoria de leitura

Antes de iniciar uma migracao, leia nesta ordem:

1. `C:\Sites\sigep_hml\docs\ArquitetoHML.md`
2. `C:\Sites\sigep_hml\docs\arquitetura\visao-geral-hml.md`
3. `C:\Sites\sigep_hml\docs\arquitetura\interface-grafica-e-navegacao.md`
4. `C:\Sites\sigep_hml\docs\arquitetura\acl-e-permissoes.md`
5. `C:\Sites\sigep_hml\docs\banco-de-dados\guia-operacional.md`
6. `C:\Sites\sigep_hml\docs\desenvolvimento\criacao-de-paginas-e-modulos.md`
7. `C:\Sites\sigep_hml\docs\desenvolvimento\migracao-de-modulos-legados.md`

## Especializacao esperada do agente

Ao atuar neste workflow, voce deve se comportar como especialista em:

- Laravel 13
- LaradminLTE / AdminLTE 4
- Blade e componentes `x-ladmin-*`
- Bootstrap 5 e Bootstrap Icons
- MySQL
- modelagem incremental de banco
- migracao de SQL legado com PDO para arquitetura Laravel
- ACL e autorizacao no HML via `AclService`
- ergonomia de telas administrativas e operacionais

Referencia oficial util do pacote visual adotado no HML:

- `https://github.com/dfsmania/LaradminLTE`

## Regras centrais da migracao

- producao e fonte de estudo, nao de copia cega
- HML e o ambiente de reconstrucao
- nunca portar um `_view.php` ou `_logica.php` apenas trocando extensao
- nunca assumir que o legado expressa a melhor arquitetura possivel
- preservar comportamento de negocio relevante
- melhorar seguranca, organizacao, validacao, UX e ACL no destino
- sempre documentar as decisoes estruturais tomadas durante a migracao

## Como localizar um modulo em producao

O modulo legado pode estar em mais de um lugar. Verifique, nesta ordem:

1. `C:\Sites\sigep\includes\sidebar_logica.php`
2. `C:\Sites\sigep\modulos\[setor]\[nome]\`
3. `C:\Sites\sigep\paginas\`
4. `C:\Sites\sigep\includes\`
5. `C:\Sites\sigep\auth\` quando houver acoplamento com sessao/permissao
6. `C:\Sites\sigep\assets\` ou `assets/` locais do modulo

## O que estudar no modulo legado

Para cada modulo, levante no minimo:

- ponto de entrada no menu
- arquivo principal de view
- arquivo principal de logica
- assets JS/CSS locais
- includes compartilhados usados pelo modulo
- tabelas consultadas e atualizadas no banco `sigep_producao`
- filtros, formularios, relatorios, impressao e AJAX
- regras de permissao baseadas em sessao
- comportamentos especificos por unidade, setor ou perfil
- dores de UX, duplicacoes e riscos tecnicos do legado

## Como reconstruir no HML

A reconstrucao deve ser feita no formato moderno do HML:

- rota Laravel nomeada
- controller em `app/Http/Controllers`
- model Eloquent quando fizer sentido
- migrations e seeders quando houver impacto estrutural
- views Blade em `resources/views`
- menu em `config/ladmin/menu.php`
- autorizacao via `AclService` e catalogo ACL

## Entregaveis esperados por migracao

Cada migracao deve produzir, idealmente nesta ordem:

1. diagnostico do modulo legado
2. mapa de dados e tabelas envolvidas
3. desenho do modulo equivalente no HML
4. implementacao incremental no HML
5. validacao funcional e de permissao
6. atualizacao da documentacao e do `CHANGELOG.md`

## Regra de ouro

Migrar nao e clonar. Migrar e absorver a regra de negocio do ambiente `C:\Sites\sigep` e recria-la com excelencia no ambiente `C:\Sites\sigep_hml`.
