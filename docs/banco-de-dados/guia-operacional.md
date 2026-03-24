# Guia Operacional do Banco de Dados

## Banco correto

O ambiente de homologacao deve trabalhar com o banco:

- `sigep_homologacao`

Esse nome precisa permanecer consistente em qualquer analise, migration, seed, script ou consulta operacional voltada a homologacao.

## Fonte de verdade do schema

A referencia mais segura para estrutura de banco nao e uma documentacao estagnada, e sim:

- `database/migrations/`
- `app/Models/`
- `database/seeders/`

Sempre valide o estado real do banco contra esses arquivos.

## Tabelas principais observadas no codigo

### Autenticacao e acesso

- `users`
- `autenticacao_setores`
- `autenticacao_permissoes`
- `autenticacao_usuario_permissao`
- `autenticacao_usuario_unidade`
- `unidades_prisionais`

### Infra Laravel

- `migrations`
- `jobs`
- `cache`
- tabelas de password reset e 2FA herdadas da stack Laravel/Fortify

## Relacoes importantes

- `users.setor_id` -> `autenticacao_setores.id`
- `autenticacao_permissoes.parent_id` -> `autenticacao_permissoes.id`
- `autenticacao_permissoes.setor_id` -> `autenticacao_setores.id`
- `autenticacao_usuario_permissao.user_id` -> `users.id`
- `autenticacao_usuario_permissao.permissao_id` -> `autenticacao_permissoes.id`
- `autenticacao_usuario_permissao.unidade_id` -> `unidades_prisionais.id`
- `autenticacao_usuario_unidade.user_id` -> `users.id`
- `autenticacao_usuario_unidade.unidade_id` -> `unidades_prisionais.id`

## Seeders relevantes

### `DatabaseSeeder`

Executa atualmente:

- `UnidadePrisionalSeeder`
- `AclSeeder`

Tambem cria um usuario de teste simples se ele ainda nao existir.

### `AclSeeder`

Provisiona:

- setores base
- permissao raiz de administracao
- modulo/pagina/acoes iniciais de usuarios
- promocao do admin para `is_system_admin`

## Boas praticas para evolucao do banco

- prefira sempre `migration` em vez de ajuste manual direto no schema
- se um novo recurso depender de catalogos iniciais, crie ou ajuste `seeder`
- preserve chaves estrangeiras e indices coerentes com as consultas
- em recursos ACL, mantenha nomes e codigos estaveis para nao quebrar verificacoes existentes
- evite SQL manual sem descrever claramente objetivo e impacto

## Quando usar SQL manual

SQL manual pode ser util para diagnostico, carga pontual controlada ou saneamento em homologacao. Mesmo assim:

- confirme antes que esta em `sigep_homologacao`
- registre quais tabelas serao afetadas
- preserve possibilidade de reproducao por migration ou seeder quando a mudanca for estrutural
- nunca trate SQL manual como substituto permanente de migration

## Checklist antes de alterar dados sensiveis

- a mudanca e realmente de homologacao?
- a tabela impacta login, ACL ou unidade?
- existe migration/seeder mais adequado que SQL manual?
- a mudanca pode afetar testes, demos ou validacoes de interface?
- o efeito colateral foi pensado para usuarios ja cadastrados?

## Resets e bootstrap

Para reconstituir o ambiente de banco de forma reproduzivel, o caminho natural do projeto e usar migrations e seeders do Laravel. Em tarefas de alto impacto, priorize fluxo reproduzivel em vez de depender de alteracoes que so existem na sessao local de trabalho.
