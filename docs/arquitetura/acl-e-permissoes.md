# ACL e Permissoes

## Nucleo da autorizacao

A autorizacao do ambiente atual e centralizada em `App\Services\AclService`.

Esse service implementa a regra de negocio de acesso e deve ser considerado a principal referencia para qualquer nova funcionalidade com controle de permissao.

## Niveis de acesso

O sistema trabalha com tres niveis hierarquicos:

- `read`
- `write`
- `owner`

Ordem de poder:

- `read` < `write` < `owner`

No `AclService`, isso e representado por `LEVEL_RANK`.

## Perfis praticos existentes

### Admin do sistema

- `is_system_admin = true`
- enxerga e gerencia todo o catalogo
- pode atribuir `owner`
- bypass de verificacoes em `AclService`

### Gestor setorial

- nao e admin do sistema
- precisa ter permissao do tipo `setor` com nivel `owner`
- passa a gerenciar usuarios do proprio escopo setorial
- pode atribuir permissoes do seu setor, mas nao `owner`

### Usuario comum

- depende das permissoes atribuidas em `autenticacao_usuario_permissao`
- pode ainda ter escopo por unidade

## Tabelas centrais do ACL

### `autenticacao_setores`

Catalogo de setores. Campos mais relevantes:

- `id`
- `slug`
- `nome`
- `descricao`
- `ativo`

### `autenticacao_permissoes`

Catalogo hierarquico de recursos ACL. Campos mais relevantes:

- `id`
- `parent_id`
- `setor_id`
- `codigo`
- `nome`
- `tipo`
- `slug`
- `descricao`
- `ordem`
- `ativo`

Tipos hoje previstos na aplicacao e nas telas:

- `menu`
- `setor`
- `subsetor`
- `modulo`
- `pagina`
- `acao`

### `autenticacao_usuario_permissao`

Atribui permissoes a usuarios. Campos mais relevantes:

- `user_id`
- `permissao_id`
- `unidade_id` opcional
- `nivel`
- `permitido`
- `observacao`

Existe unicidade por `user_id + permissao_id + unidade_id`.

### `autenticacao_usuario_unidade`

Vincula usuarios as unidades prisionais que podem acessar. Essa tabela impacta diretamente o login e o escopo operacional por unidade.

## Como a aplicacao decide acesso

### Acesso administrativo

Controllers administrativos usam verificacoes como:

- `canManageUsers()`
- `canManageSectors()`
- `canManageUnits()`
- `canManageResources()`
- `canManageTargetUser()`

Esses metodos bloqueiam acesso via `abort_unless(..., 403)`.

### Acesso fino por codigo ACL

O metodo `userHasPermission($user, $permissionCode, $requiredLevel, $unidadeId)` verifica:

1. se o usuario existe
2. se e admin do sistema
3. se o recurso existe em `autenticacao_permissoes`
4. se ha atribuicao permitida ao usuario
5. se o nivel atribuido atende o nivel exigido
6. se a unidade requerida bate com `unidade_id` ou se a permissao e global

## Como o menu usa ACL

`config/ladmin/menu.php` aplica `is_allowed` para esconder ou mostrar grupos e links. Isso protege a navegacao visual, mas nao substitui o bloqueio do controller.

Regra importante:

- menu deve esconder o link
- controller deve impedir o acesso direto pela URL

## Seeder atual relevante

`database/seeders/AclSeeder.php`:

- cria setores padrao
- cria `menu:administracao`
- cria modulo administrativo de usuarios
- cria paginas e acoes iniciais
- eleva o usuario admin para `is_system_admin = true`

## Regras para criar novo recurso ACL

Ao introduzir um novo recurso funcional:

1. decida o tipo do recurso
2. defina `codigo` estavel e legivel
3. defina `slug` coerente com a rota ou modulo
4. defina `parent_id` para manter hierarquia
5. defina `setor_id` quando o recurso pertencer a um setor especifico
6. atualize seeders ou mecanismo de provisionamento
7. use `AclService` ou metodo equivalente no controller
8. ajuste `config/ladmin/menu.php` se a navegacao depender dessa permissao

## Cuidados que evitam erro

- nao usar apenas `is_system_admin` para tudo; preserve granularidade quando fizer sentido
- nao criar codigo ACL sem pensar em onde ele sera consultado
- nao exibir menu para usuario sem garantir o bloqueio por backend
- nao atribuir `owner` para atores nao autorizados
- nao esquecer do escopo por unidade quando o recurso for sensivel ao contexto operacional
