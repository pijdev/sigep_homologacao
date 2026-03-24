# Migracao de Modulos Legados para HML

## Finalidade

Este guia ensina como estudar um modulo legado em `C:\Sites\sigep` e reconstruir uma versao moderna no HML, sem carregar para o destino as limitacoes arquiteturais do sistema antigo.

## Visao geral do legado

No ambiente de producao, e comum encontrar:

- modulos em `modulos/[setor]/[nome]/`
- arquivos `*_view.php`
- arquivos `*_logica.php`
- assets locais por modulo
- consultas PDO diretas
- filtros, AJAX, HTML e relatorios misturados no mesmo arquivo
- regras de permissao baseadas em `$_SESSION`
- menus montados por `includes/sidebar_logica.php`

## Visao geral do destino HML

No HML, a referencia correta e:

- Laravel 13
- LaradminLTE/AdminLTE 4
- Blade components
- controllers dedicados
- Eloquent e migrations
- ACL por `AclService`
- menu centralizado em `config/ladmin/menu.php`

## Regra principal de traducao arquitetural

### No legado

- entrada por URL direta de pagina ou modulo
- PDO e SQL espalhados
- sessao crua decide visibilidade
- impressao e relatorio embarcados na mesma logica

### No HML

- entrada por rota Laravel nomeada
- persistencia organizada por models/services/controllers
- ACL centralizada e reproduzivel
- UI padronizada com Blade e LaradminLTE
- evolucao futura mais segura

## Processo recomendado de migracao

### Etapa 1 - Descoberta do modulo legado

Identifique:

- nome do modulo
- setor de origem
- paginas e fluxos principais
- ponto de entrada do menu em `includes/sidebar_logica.php`
- arquivos de view, logica, include e assets relacionados

### Etapa 2 - Inventario funcional

Monte um resumo curto contendo:

- o que o modulo faz
- quem usa
- quais operacoes existem: listar, filtrar, criar, editar, excluir, imprimir, exportar, dashboard
- quais regras de negocio existem
- quais campos e tabelas sao criticos
- quais dores do legado devem ser corrigidas

### Etapa 3 - Inventario tecnico do legado

Levante com precisao:

- arquivos PHP envolvidos
- includes secundarios
- endpoints AJAX
- scripts de manutencao e SQL auxiliares
- tabelas do `sigep_producao`
- colunas realmente usadas
- relatorios e impressos embutidos
- variaveis de sessao relevantes

### Etapa 4 - Separacao entre regra de negocio e divida tecnica

Classifique os comportamentos encontrados em dois grupos.

#### Preservar

- regras operacionais reais
- validacoes de negocio importantes
- filtros que fazem sentido ao usuario
- indicadores e relatorios relevantes
- dependencias de processo interno da unidade

#### Nao portar como esta

- SQL espalhado em view
- HTML misturado com logica critica
- acesso via URL tecnica sem ACL robusta
- sessao crua substituindo autorizacao formal
- duplicacoes e scripts temporarios que viraram permanentes
- impressao acoplada sem separacao de responsabilidade

### Etapa 5 - Desenho do modulo HML

Antes de codar, definir:

- nome do recurso no HML
- rotas Laravel
- controller ou controllers necessarios
- models envolvidos
- migrations necessarias
- ACL necessaria
- item de menu, se aplicavel
- telas: index, form, show, dashboard, relatorio, modal, etc.

## Template de mapeamento legado -> HML

Use esta traducao mental:

- `*_view.php` -> Blade view em `resources/views/...`
- `*_logica.php` -> controller, service, model query ou form request
- `$_SESSION['perm_x']` -> ACL no `AclService` e `config/ladmin/menu.php`
- PDO manual -> Eloquent, Query Builder ou service bem encapsulado
- `assets/js` legado -> JS so se realmente necessario, preferindo server-side claro
- SQL de estrutura -> migration
- SQL de catalogo inicial -> seeder
- menu legado -> item em `config/ladmin/menu.php`

## Como analisar o banco de producao

O banco de referencia para estudo e `sigep_producao`.

Ao investigar tabelas e consultas:

- descubra quais tabelas o modulo realmente usa
- identifique leitura vs escrita
- identifique colunas obrigatorias e calculadas
- localize chaves e relacoes implicitas
- descubra regras escondidas em SQL ou scripts auxiliares

## Como decidir o que vai para o banco HML

Nem tudo do `sigep_producao` deve ser espelhado automaticamente.

Perguntas obrigatorias:

- o HML precisa da mesma tabela ou de uma modelagem melhor?
- o dado pode continuar na tabela legada ou vale criar estrutura nova?
- o modulo depende de auditoria?
- existe necessidade de historico?
- o recurso precisa ser multiunidade desde o inicio?

## ACL durante a migracao

Toda migracao deve decidir explicitamente:

- quem pode ver a pagina
- quem pode criar
- quem pode editar
- quem pode excluir
- se o acesso e por setor, unidade ou ambos
- se o recurso precisa de codigos ACL proprios

Se o legado usa algo como `perm_censura`, `perm_laboral` ou `user_admin`, traduza isso para o modelo atual do HML em vez de repetir a mesma abordagem.

## UI e UX no destino

Ao migrar, use o legado como referencia funcional, nao como gabarito visual.

O HML deve seguir:

- `x-ladmin-panel`
- `contentHeader`
- cards Bootstrap
- botoes e icones coerentes com o sistema atual
- filtros claros
- mensagens de sucesso e erro amigaveis
- paginas mais legiveis e menos sobrecarregadas

## Sinais de que um modulo precisa ser quebrado em partes

Divida o modulo em mais de uma tela ou responsabilidade quando houver:

- dashboard e CRUD no mesmo arquivo legado
- relatorios misturados com cadastro
- muitos modos de impressao no mesmo endpoint
- varias entidades de negocio acopladas
- AJAX, filtros e manutencao tecnica todos no mesmo arquivo

## Fluxo de entrega recomendado por modulo

1. localizar o modulo em producao
2. fazer leitura dirigida dos arquivos principais
3. resumir regra de negocio
4. mapear banco e ACL legados
5. desenhar arquitetura do HML
6. implementar rotas, controller, views, model e ACL
7. validar UX, permissao e persistencia
8. documentar e atualizar `CHANGELOG.md`

## Checklist de migracao antes de considerar pronto

- o comportamento de negocio principal foi preservado
- a implementacao foi adaptada ao HML, e nao copiada do legado
- a tela segue o padrao LaradminLTE/AdminLTE 4
- a autorizacao esta no backend e refletida no menu
- o banco usado no destino e `sigep_homologacao`
- qualquer nova estrutura foi criada com migration
- qualquer novo catalogo foi provisionado por seeder, se preciso
- a documentacao foi atualizada

## Como o Cascade deve se comportar durante a migracao

- ler primeiro `docs/MigrarModulos.md`
- usar producao como fonte de descoberta
- usar HML como fonte de implementacao correta
- evitar refatoracao destrutiva em producao
- documentar as decisoes de traducao entre legado e HML
- propor melhorias de UX e arquitetura quando fizer sentido, sem perder a funcionalidade esperada

## Frase-guia

Aprender com o legado, preservar a regra de negocio, descartar a bagunca acidental e entregar uma versao HML melhor, segura e sustentavel.
