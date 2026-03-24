# Projeto - Cerebro Compartilhado SIGEP

## Objetivo

Desenhar uma arquitetura futura para criar um "cerebro compartilhado" do SIGEP no ambiente HML, permitindo que diferentes IAs, agentes e modelos locais trabalhem com contexto consistente, atualizado e reutilizavel sobre o sistema.

Este projeto sera apenas planejado neste momento. Nao ha execucao agora.

## Local de referencia

- Projeto HML: `C:\Sites\sigep_hml`
- Documentacao base: `C:\Sites\sigep_hml\docs`
- Plano atual: `C:\Sites\sigep_hml\docs\planos\projeto-cerebro-compartilhado-sigep.md`

## Visao do problema

Hoje, o conhecimento sobre o SIGEP esta espalhado em:

- codigo-fonte
- banco de dados
- documentacao em Markdown
- regras de ambiente
- experiencia humana acumulada
- contexto de conversas com IA

Isso torna dificil manter continuidade entre sessoes, agentes e modelos diferentes.

O objetivo do cerebro compartilhado e reduzir esse problema criando uma memoria de projeto consultavel, atualizavel e reaproveitavel.

## Resultado desejado

No estado futuro, qualquer IA que trabalhe no SIGEP devera conseguir:

- descobrir rapidamente a arquitetura do HML
- encontrar padroes corretos de UI, backend, ACL e banco
- recuperar decisoes tecnicas anteriores
- localizar dossies de modulos e planos em andamento
- entender relacao entre producao e homologacao
- usar contexto semantico do projeto sem depender de releitura completa manual

## O que este projeto nao pretende

- nao pretende substituir a leitura do codigo real em tarefas sensiveis
- nao pretende fazer a IA "lembrar" magicamente sem pipeline de contexto
- nao pretende eliminar documentacao viva
- nao pretende ser executado agora

## Conceito central

O cerebro compartilhado nao deve ser apenas uma base vetorial.

Ele deve combinar 4 camadas.

### Camada 1 - Conhecimento documental

Conteudo explicito e humano-legivel.

Exemplos:

- `docs/`
- workflows como `ArquitetoHML.md` e `MigrarModulos.md`
- planos em `docs/planos`
- guias de arquitetura, ACL, banco e desenvolvimento
- dossies de migracao por modulo
- changelog e decisoes tecnicas

### Camada 2 - Conhecimento estrutural

Informacao organizada sobre o projeto.

Exemplos:

- mapa de rotas
- mapa de controllers, models e views
- inventario de modulos legados
- inventario de recursos ACL
- inventario de tabelas e relacoes
- status de migracoes e features

### Camada 3 - Conhecimento semantico

Busca vetorial e recuperacao semantica de contexto.

Exemplos:

- embeddings de docs e partes selecionadas do codigo
- indexacao semantica de modulos, tabelas, ACL e workflows
- recuperacao dos trechos mais relevantes para uma tarefa

### Camada 4 - Orquestracao de memoria

Camada que decide como buscar, montar e entregar contexto para cada IA.

Exemplos:

- pipeline de reindexacao
- recuperacao por tarefa
- composicao de prompt contextual
- atualizacao automatica apos alteracoes relevantes

## Beneficios esperados

### Para voce

- menos repeticao de contexto entre sessoes
- retomada mais facil de projetos pausados
- mais previsibilidade no trabalho das IAs
- historico mais claro de decisoes e padroes

### Para o Cascade

- melhor descoberta de contexto do SIGEP
- menos risco de seguir padrao errado
- mais autonomia em migracoes e refactors controlados
- mais consistencia entre tarefas diferentes

### Para modelos locais via Ollama

- possibilidade de operar com contexto rico do projeto
- melhor capacidade de responder sobre o SIGEP
- maior utilidade em tarefas de suporte e exploracao

### Para o projeto

- memoria institucional menos dependente de uma unica pessoa
- documentacao com mais reaproveitamento real
- base mais preparada para crescimento e manutencao

## Requisitos de alto nivel

Para funcionar bem, o cerebro compartilhado precisara de:

- fontes canonicamente confiaveis
- estrategia de indexacao
- definicao do que entra e do que nao entra na memoria
- mecanismo de atualizacao
- politica de prioridade entre docs, codigo e banco
- mecanismo de recuperacao por relevancia
- estrategia de versionamento e auditoria

## Fontes candidatas para o cerebro compartilhado

### Prioridade alta

- `C:\Sites\sigep_hml\docs`
- `C:\Sites\sigep_hml\routes`
- `C:\Sites\sigep_hml\app`
- `C:\Sites\sigep_hml\config`
- `C:\Sites\sigep_hml\database\migrations`
- `C:\Sites\sigep_hml\database\seeders`
- `C:\Sites\sigep_hml\resources\views`
- `C:\Sites\sigep_hml\CHANGELOG.md`

### Prioridade media

- `C:\Sites\sigep\modulos`
- `C:\Sites\sigep\includes`
- `C:\Sites\sigep\paginas`
- mapas estruturados do banco `sigep_producao`
- mapas estruturados do banco `sigep_homologacao`

### Prioridade seletiva

- logs tecnicos relevantes
- inventarios gerados automaticamente
- memorias operacionais de migracao
- decisoes tecnicas registradas em arquivos dedicados

## O que provavelmente nao deve entrar diretamente

- binarios
- arquivos temporarios
- assets grandes sem valor semantico
- dumps brutos desnecessarios
- credenciais e segredos
- arquivos muito ruidosos sem utilidade real de busca

## Estrategia recomendada de implementacao futura

A implementacao deve ser feita em fases.

## Fase 0 - Definicao de escopo e governanca

### Objetivo

Decidir exatamente o que o cerebro compartilhado sera e quais responsabilidades ele tera.

### Perguntas a responder

- quais IAs vao consumir essa memoria?
- qual conhecimento precisa ser sempre recuperavel?
- que tipos de arquivo entram na indexacao?
- quem atualiza a memoria e quando?
- como distinguir fonte canonica de fonte auxiliar?
- o que deve ser publico para agentes e o que deve ser restrito?

### Entregavel

Documento funcional e de governanca.

## Fase 1 - Base documental forte

### Objetivo

Consolidar primeiro a memoria explicita, antes da camada vetorial.

### Escopo

- fortalecer `docs/`
- criar taxonomia clara por tema
- padronizar workflows
- criar dossies por modulo
- registrar decisoes tecnicas recorrentes
- organizar planos e status de execucao

### Justificativa

Uma base vetorial ruim indexando documentacao fraca so produz busca ruim.

## Fase 2 - Memoria estrutural do projeto

### Objetivo

Gerar ou manter inventarios tecnicos consultaveis.

### Escopo sugerido

- inventario de modulos legados
- inventario de rotas HML
- inventario de recursos ACL
- inventario de tabelas e relacoes principais
- inventario de migracoes de modulo ja realizadas

### Formato sugerido

Arquivos Markdown ou JSON controlados no repositorio.

## Fase 3 - Base vetorial / RAG

### Objetivo

Criar busca semantica sobre o conhecimento do SIGEP.

### Componentes provaveis

- chunking de documentos e trechos selecionados de codigo
- geracao de embeddings
- armazenamento vetorial
- metadados por origem, modulo, caminho, ambiente e data
- recuperacao por tarefa

### Observacao importante

A base vetorial nao sera a memoria completa por si so. Ela sera uma das camadas do sistema de memoria.

## Fase 4 - Pipeline de atualizacao

### Objetivo

Garantir que alteracoes de codigo e documentacao reflitam no cerebro compartilhado.

### Opcoes futuras

- reindexacao manual sob comando
- reindexacao incremental por pasta alterada
- reindexacao apos merge ou deploy interno
- reindexacao agendada

### Requisito

A memoria precisa envelhecer devagar. Sem atualizacao, perde valor rapidamente.

## Fase 5 - Orquestracao para agentes e modelos

### Objetivo

Fazer com que Cascade, agentes locais e modelos Ollama realmente usem esse cerebro.

### Escopo futuro

- fluxo de consulta por tarefa
- recuperacao automatica de contexto relevante
- montagem de prompt com base nas fontes encontradas
- regras diferentes para migracao, arquitetura, ACL, banco e UI

### Resultado esperado

Agentes diferentes acessam uma base comum de conhecimento, cada um com contexto mais consistente.

## Fase 6 - Auditoria e confiabilidade

### Objetivo

Evitar que o cerebro compartilhado vire fonte de erro silencioso.

### Escopo futuro

- marcar fonte canonica vs auxiliar
- registrar ultima atualizacao dos artefatos
- permitir verificacao de origem do contexto recuperado
- evitar indexacao de conteudo obsoleto sem sinalizacao

## Arquitetura conceitual sugerida

```text
Fontes do Projeto
  -> Docs, codigo, rotas, migrations, views, inventarios, decisoes

Pipeline de Preparacao
  -> limpeza, chunking, metadados, classificacao por dominio

Camada de Armazenamento
  -> arquivos versionados + memoria estrutural + base vetorial

Camada de Recuperacao
  -> busca por modulo, tema, ACL, banco, UI, migracao

Camada de Orquestracao
  -> entrega contexto certo para Cascade, Ollama e outros agentes
```

## Principios de qualidade

- contexto util vale mais que volume bruto
- fonte canonica deve ter prioridade sobre memoria derivada
- documentacao viva e essencial
- a memoria compartilhada deve ajudar a descobrir, nao inventar
- toda automacao precisa continuar auditavel por humano

## Riscos previstos

### Risco 1 - Base vetorial desatualizada

Mitigacao:

- pipeline de reindexacao
- metadados de ultima atualizacao
- preferencia por docs e codigo atuais

### Risco 2 - Excesso de ruido

Mitigacao:

- selecionar bem as fontes
- excluir arquivos sem valor semantico
- criar taxonomia clara

### Risco 3 - Falsa confianca da IA

Mitigacao:

- manter leitura do codigo real em tarefas criticas
- exigir citacao de origem quando possivel
- marcar contexto como derivado ou canonico

### Risco 4 - Complexidade alta demais no inicio

Mitigacao:

- comecar por documentacao e memoria estrutural
- so depois entrar em vetorial e orquestracao

## Criterios de sucesso do projeto

Este projeto sera considerado bem sucedido no futuro quando:

- a documentacao do SIGEP estiver mais facil de localizar e reutilizar
- o Cascade conseguir iniciar tarefas com menos contexto manual
- modelos locais conseguirem responder melhor sobre o SIGEP usando contexto recuperado
- migracoes e evolucoes do HML ganharem mais continuidade entre sessoes
- o custo de onboarding de uma IA ou humano cair perceptivelmente

## Proximo passo recomendado quando chegar a hora

Quando decidir executar, a melhor ordem sera:

1. consolidar documentacao e memorias explicitas
2. criar inventarios estruturados do projeto
3. definir arquitetura da base vetorial
4. definir pipeline de atualizacao
5. integrar a memoria com agentes e modelos locais

## Instrucoes para continuidade futura

Quando este projeto for retomado, o agente responsavel deve:

1. ler `C:\Sites\sigep_hml\docs\ArquitetoHML.md`
2. ler `C:\Sites\sigep_hml\docs\MigrarModulos.md`, se a memoria tambem for servir a migracoes
3. ler este plano integralmente
4. validar o estado atual da documentacao em `docs/`
5. propor uma implementacao faseada, sem tentar resolver tudo de uma vez
