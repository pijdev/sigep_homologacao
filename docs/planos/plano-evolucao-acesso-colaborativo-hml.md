# Plano - Evolucao do Modelo de Acesso Colaborativo no SIGEP HML

## Objetivo

Evoluir o sistema de autenticacao e autorizacao do ambiente `C:\Sites\sigep_hml` para suportar, de forma controlada e auditavel:

- leitura ampla para perfis institucionais como diretor de presidio
- concessao de acesso entre unidades
- compartilhamento granular de recursos com nivel `read` ou `write`
- continuidade segura da administracao por ACL sem quebrar o modelo ja existente

O banco de dados alvo deste plano e `sigep_homologacao`.

## Problema atual

O HML ja possui base de ACL com setores, permissoes, niveis e escopo opcional por unidade, mas ainda nao entrega bem alguns cenarios de negocio:

- conceder leitura ampla a um diretor de unidade de maneira simples e reproduzivel
- permitir colaboracao entre unidades sem transformar tudo em privilegio de admin global
- oferecer experiencia clara de compartilhamento de acesso, semelhante a modelos colaborativos modernos
- reduzir administracao manual excessiva de permissoes individuais

Hoje existem pecas tecnicas importantes:

- `AclService`
- `autenticacao_permissoes`
- `autenticacao_usuario_permissao`
- `autenticacao_usuario_unidade`
- menu condicional em `config/ladmin/menu.php`

Mas falta um modelo funcional completo para perfis amplos, heranca e compartilhamento formal.

## Resultado esperado

Ao final das fases deste plano, o sistema devera permitir:

1. dar ao diretor de uma unidade acesso de leitura amplo, com governanca clara
2. permitir que um gestor compartilhe acesso controlado a usuarios de outra unidade
3. limitar esse compartilhamento por recurso, unidade, nivel e validade
4. auditar quem concedeu, quando concedeu e com qual justificativa
5. manter compatibilidade com o ACL existente, sem reescrever toda a plataforma

## Principios de desenho

- homologacao primeiro: tudo nasce em `sigep_hml`
- compatibilidade incremental: expandir o ACL atual, nao jogar fora
- backend como fonte de verdade: menu e tela so complementam a seguranca
- minimo privilegio: compartilhamento deve ser granular e justificavel
- auditabilidade: toda concessao especial deve ser rastreavel
- experiencia operacional simples: o usuario administrador deve entender o que esta compartilhando

## Diagnostico tecnico do estado atual

### Pontos fortes

- usuarios podem ser vinculados a multiplas unidades
- permissoes aceitam `nivel` e `unidade_id`
- `AclService` centraliza boa parte da regra de autorizacao
- CRUD administrativo ja existe para usuarios, setores, unidades e catalogo ACL

### Limitacoes atuais

- nao existe perfil pronto como `diretor com leitura global`
- nao existe heranca clara e sistematica de ACL por arvore de recursos
- nao existe conceito formal de compartilhamento entre unidades
- nao existe trilha de auditoria especifica para concessoes colaborativas
- menu e controllers usam ACL administrativa, mas ainda nao ha camada especifica para compartilhamento delegated

## Visao de arquitetura alvo

A evolucao recomendada e composta por 4 camadas.

### Camada 1 - Perfis de acesso prontos

Criar perfis institucionais reutilizaveis para reduzir atribuicao manual.

Perfis iniciais sugeridos:

- Diretor Unidade - Leitura Ampla
- Diretor Unidade - Leitura e Escrita Operacional
- Gestor Setorial Unidade
- Colaborador Convidado - Leitura
- Colaborador Convidado - Escrita

Esses perfis podem ser implementados de dois modos:

- como conjunto predefinido de permissoes ACL provisionadas por seeder
- como nova camada de perfis/pacotes de permissao vinculados a usuarios

Recomendacao inicial: comecar com conjunto predefinido provisionado por seeder, porque impacta menos o modelo atual.

### Camada 2 - Heranca de recursos ACL

Criar ou formalizar comportamento de heranca por arvore de recursos.

Exemplo:

- se o usuario tem `read` em um modulo, ele deve herdar `read` para paginas filhas quando essa opcao estiver habilitada
- se o usuario tem `write` em um recurso-pai, pode receber equivalencia em filhos operacionais conforme regra definida

A heranca nao deve ser implicita sem controle. O ideal e ter uma regra documentada e implementada no `AclService`.

### Camada 3 - Compartilhamento entre unidades

Introduzir o conceito de concessao delegada, semelhante a convite ou compartilhamento interno.

Elementos minimos de uma concessao:

- usuario concedente
- usuario destinatario
- recurso ACL compartilhado
- unidade de contexto
- nivel concedido: `read` ou `write`
- data de inicio
- data de expiracao opcional
- justificativa
- status ativo/inativo/revogado

Essa camada nao substitui a ACL base. Ela complementa a ACL existente com governanca colaborativa.

### Camada 4 - Auditoria e interface administrativa

Adicionar:

- tela para conceder compartilhamentos
- tela para listar compartilhamentos ativos, expirados e revogados
- filtros por unidade, setor, recurso e usuario
- registro de auditoria da concessao e da revogacao

## Estrategia recomendada de implementacao

A recomendacao e seguir em fases pequenas e entregaveis.

## Fase 0 - Refinamento funcional e alinhamento de negocio

### Objetivo

Fechar regras de negocio antes de alterar banco ou `AclService`.

### Perguntas que precisam ser respondidas

- diretor de unidade deve ler tudo da propria unidade ou tudo do sistema?
- `leitura ampla` inclui configuracoes administrativas sensiveis?
- gestores setoriais podem compartilhar apenas recursos do proprio setor ou qualquer recurso da unidade?
- o acesso entre unidades exige aprovacao extra ou basta ter autoridade local?
- compartilhamento pode ter validade temporaria obrigatoria?
- usuarios convidados podem convidar outros usuarios?
- compartilhamento pode ser herdado por paginas filhas automaticamente?

### Entregavel

Documento funcional curto respondendo essas perguntas.

### Status sugerido

Pode ser iniciado imediatamente.

## Fase 1 - Modelo minimo para diretor com leitura ampla

### Objetivo

Resolver primeiro o caso de maior valor: diretor com leitura ampla controlada.

### Escopo tecnico

- mapear quais recursos ACL representam `leitura ampla`
- criar seeders ou catalogos para esse pacote de acesso
- decidir se o escopo e por unidade ou global
- ajustar `AclService` para reconhecer esse conjunto de acesso de forma consistente
- validar menu e controllers para refletir o acesso de leitura sem liberar escrita indevida

### Abordagem sugerida

Implementar como pacote inicial baseado em permissoes existentes, sem criar ainda um sistema completo de perfis.

### Beneficio

Entrega rapida e valida a direcao arquitetural sem grande refatoracao.

## Fase 2 - Heranca de permissoes por arvore ACL

### Objetivo

Reduzir administracao manual e criar semantica robusta para recursos pai e filho.

### Escopo tecnico

- revisar relacao `parent_id` de `autenticacao_permissoes`
- definir regra de heranca no `AclService`
- decidir quando a verificacao sobe ou desce na arvore
- evitar ambiguidades entre `menu`, `modulo`, `pagina` e `acao`
- criar testes de cenarios com `read`, `write` e `owner`

### Observacao

Essa fase melhora muito o caso do diretor e prepara o terreno para compartilhamento colaborativo.

## Fase 3 - Compartilhamento colaborativo entre unidades

### Objetivo

Permitir que um gestor compartilhe acesso com usuario de outra unidade sem promover o usuario a admin do sistema.

### Escopo tecnico sugerido

Criar uma nova tabela, por exemplo:

- `autenticacao_compartilhamentos`

Campos sugeridos:

- `id`
- `granted_by_user_id`
- `target_user_id`
- `permissao_id`
- `unidade_id`
- `nivel`
- `status`
- `justificativa`
- `starts_at`
- `expires_at`
- `revoked_at`
- `revoked_by_user_id`
- `created_at`
- `updated_at`

### Regras de negocio sugeridas

- quem concede precisa ter autoridade sobre o recurso compartilhado
- ninguem pode conceder nivel maior do que possui
- concessao entre unidades deve respeitar regras de escopo definidas na fase 0
- concessoes expiradas nao produzem acesso efetivo
- revogacao deve ser imediata e auditavel

### Impacto no codigo

- novo model Eloquent
- nova migration
- possivel seeder para status/regras padrao, se necessario
- extensao do `AclService` para considerar compartilhamentos ativos
- possivel service dedicado, por exemplo `SharedAccessService`

## Fase 4 - Interface administrativa de compartilhamento

### Objetivo

Oferecer usabilidade real ao modelo colaborativo.

### Escopo tecnico

- controller administrativo para compartilhamentos
- index com filtros
- formulario de concessao
- acao de revogacao
- visualizacao de validade e justificativa
- feedback visual claro para acesso herdado, direto e compartilhado

### Padrao de UI

Seguir o padrao atual do HML:

- `x-ladmin-panel`
- cards Bootstrap
- validacao no controller
- menu condicionado por ACL

## Fase 5 - Auditoria, testes e endurecimento

### Objetivo

Tornar a feature segura para evolucao e posterior ida a producao.

### Escopo tecnico

- registrar logs de concessao e revogacao
- revisar impactos em menu e acesso direto por URL
- criar testes automatizados para cenarios criticos
- validar comportamento com usuarios multiunidade
- validar expiracao de compartilhamentos
- revisar edge cases de heranca + compartilhamento + admin global

## Ordem de execucao recomendada

1. Fase 0 - Refinar regras de negocio
2. Fase 1 - Diretor com leitura ampla
3. Fase 2 - Heranca de ACL
4. Fase 3 - Compartilhamento entre unidades
5. Fase 4 - Interface administrativa
6. Fase 5 - Auditoria e testes

Essa ordem reduz risco porque primeiro resolve o caso mais urgente, depois estrutura a base tecnica e so entao expoe compartilhamento via interface.

## Backlog tecnico detalhado

### Bloco A - Analise e desenho

- inventariar codigos ACL atuais em `autenticacao_permissoes`
- mapear quais recursos devem compor `leitura ampla`
- definir escopo exato de diretor e gestor setorial
- identificar conflitos entre recursos administrativos e operacionais

### Bloco B - Banco de dados

- desenhar migration para compartilhamentos, se aprovada
- definir indices necessarios
- definir foreign keys e estrategia de revogacao
- preparar seeders para pacotes iniciais de acesso

### Bloco C - Backend

- evoluir `AclService`
- criar service especifico para compartilhamentos, se necessario
- revisar controllers para uso consistente da nova regra
- manter compatibilidade com verificacoes existentes

### Bloco D - Interface

- criar CRUD de compartilhamentos
- exibir origem do acesso quando util
- permitir filtros por unidade, setor e recurso
- destacar expiracoes e revogacoes

### Bloco E - Qualidade

- testes de permissao direta
- testes de permissao herdada
- testes de permissao compartilhada
- testes de expiracao
- testes de proibicao de escalacao indevida

## Riscos e mitigacoes

### Risco 1 - Escalada indevida de privilegio

Mitigacao:

- impedir concessao acima do nivel do concedente
- auditar toda concessao
- revisar backend antes da UI

### Risco 2 - Complexidade excessiva no `AclService`

Mitigacao:

- extrair servicos auxiliares quando a regra crescer
- separar permissao direta, herdada e compartilhada em metodos distintos

### Risco 3 - Menu e backend divergirem

Mitigacao:

- manter controller como fonte de bloqueio
- tratar menu apenas como camada de experiencia

### Risco 4 - Administracao operacional dificil

Mitigacao:

- criar perfis prontos antes de abrir liberdade total
- priorizar casos reais de negocio sobre flexibilidade abstrata

## Criterios de pronto por marco

### Marco 1 - Diretor leitura ampla

Pronto quando:

- um diretor puder receber leitura ampla conforme regra fechada
- o acesso funcionar no backend
- o menu refletir a visibilidade correta
- escrita continuar bloqueada quando nao autorizada

### Marco 2 - Heranca funcional

Pronto quando:

- permissoes pai influenciarem filhos de forma previsivel
- testes cobrirem cenarios principais
- a administracao manual de ACL for reduzida

### Marco 3 - Compartilhamento entre unidades

Pronto quando:

- um gestor autorizado puder conceder `read` ou `write` a outro usuario
- o acesso compartilhado puder ser revogado e expirar
- o sistema registrar quem concedeu e quando

### Marco 4 - Interface completa

Pronto quando:

- houver tela administrativa operavel para conceder, listar e revogar compartilhamentos
- filtros e mensagens estiverem claros
- o comportamento for coerente com o ACL vigente

## Sugestao de proximo passo pratico

O melhor proximo passo para comecar sem travar em excesso e:

1. fechar a regra funcional do `Diretor Unidade - Leitura Ampla`
2. inventariar os codigos ACL que compoem essa leitura
3. implementar esse primeiro pacote no HML
4. depois partir para heranca e compartilhamento formal

## Instrucoes para continuidade por outro agente

Se outro agente, inclusive o Cascade, assumir a execucao, ele deve:

1. ler `C:\Sites\sigep_hml\docs\ArquitetoHML.md`
2. ler `C:\Sites\sigep_hml\docs\arquitetura\acl-e-permissoes.md`
3. ler este plano inteiro
4. iniciar pela Fase 0 ou, se o negocio ja tiver respondido as perguntas, pela Fase 1
5. registrar na documentacao toda decisao estrutural tomada durante a implementacao
