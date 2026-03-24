# Checklist de Entrega

Use esta lista antes de encerrar qualquer tarefa no SIGEP HML.

## Ambiente

- confirmei que trabalhei em `C:\Sites\sigep_hml`
- confirmei que o banco alvo e `sigep_homologacao`
- nao misturei homologacao com `C:\Sites\sigep`

## Arquitetura

- alinhei a mudanca com Laravel + LaradminLTE atuais
- nao usei como base o padrao legado `modulos/[setor]/[nome]`
- revisei rota, controller, view e config relacionados

## Interface

- a tela segue o padrao `x-ladmin-panel` e Bootstrap existente, quando aplicavel
- mensagens de sucesso e erro estao visiveis
- o menu foi ajustado apenas se realmente necessario
- itens visuais respeitam ACL em `config/ladmin/menu.php`

## Permissoes

- o backend bloqueia acesso indevido
- a regra usada esta coerente com `AclService`
- se criei recurso novo, tratei catalogo ACL e visibilidade do menu
- considerei escopo por unidade quando a funcionalidade exige isso

## Banco de dados

- usei migration/seeder quando a mudanca e estrutural
- nao deixei dependencia oculta em SQL manual nao documentado
- considerei impacto em dados ja existentes

## Validacao

- revisei fluxo feliz e fluxo sem permissao
- conferi impacto em create/update/delete, se houver CRUD
- conferi nomes de rota, redirects e mensagens finais
- atualizei a documentacao se a mudanca alterou arquitetura, ACL ou processo operacional

## Fechamento

So considere a tarefa pronta quando a implementacao estiver funcional, coerente com homologacao e compreensivel para o proximo humano ou agente que assumir o contexto.
