# SIGEP Homologação - Documentação Completa

## 📋 Visão Geral

O Sistema de Gestão Penitenciária (SIGEP) v2.0 é um sistema governamental completo para gestão penitenciária desenvolvido em Laravel + AdminLTE 4.

## 🏗️ Estrutura da Documentação

```
docs/
├── README.md                           # Este arquivo
├── arquitetura/                        # Arquitetura do sistema
│   ├── visao-geral.md                 # Visão geral e conceitos
│   ├── mvc-adminlte.md                # Padrão MVC + AdminLTE
│   ├── navegacao-spa.md               # Sistema de navegação
│   └── seguranca.md                   # Segurança e permissões
├── desenvolvimento/                     # Guias de desenvolvimento
│   ├── guia-rapido.md                 # Setup inicial
│   ├── padroes-codigo.md              # Padrões de codificação
│   ├── criando-modulos.md             # Como criar módulos
│   ├── frontend-guide.md              # Guia frontend
│   └── deploy-producao.md             # Deploy para produção
├── banco-de-dados/                     # Documentação do banco
│   ├── schema.md                       # Schema completo
│   ├── migrations.md                   # Migrations Laravel
│   └── queries-comuns.md               # Queries úteis
├── ia-e-automacao/                     # IA e automação
│   ├── modelos-ollama.md              # Modelos de IA
│   ├── chatbot-integracao.md          # Chatbot SIGEP
│   └── automacao-tarefas.md           # Automação de tarefas
└── api/                               # Documentação de APIs
    ├── endpoints.md                    # Endpoints disponíveis
    └── exemplos.md                      # Exemplos de uso
```

## 🚀 Começando Rápido

1. **Setup do Ambiente**: Veja [desenvolvimento/guia-rapido.md](desenvolvimento/guia-rapido.md)
2. **Criar Módulo**: Siga [desenvolvimento/criando-modulos.md](desenvolvimento/criando-modulos.md)
3. **Banco de Dados**: Consulte [banco-de-dados/schema.md](banco-de-dados/schema.md)
4. **IA Local**: Veja [ia-e-automacao/modelos-ollama.md](ia-e-automacao/modelos-ollama.md)

## 📚 Principais Tópicos

### 🏗️ Arquitetura
- [Visão Geral](arquitetura/visao-geral.md) - Conceitos e estrutura
- [MVC + AdminLTE](arquitetura/mvc-adminlte.md) - Padrão de desenvolvimento
- [Navegação SPA](arquitetura/navegacao-spa.md) - Sistema de navegação
- [Segurança](arquitetura/seguranca.md) - Autenticação e permissões

### 💻 Desenvolvimento
- [Guia Rápido](desenvolvimento/guia-rapido.md) - Setup inicial
- [Padrões de Código](desenvolvimento/padroes-codigo.md) - Regras e convenções
- [Criando Módulos](desenvolvimento/criando-modulos.md) - Guia passo a passo
- [Frontend Guide](desenvolvimento/frontend-guide.md) - JavaScript e CSS

### 🗄️ Banco de Dados
- [Schema](banco-de-dados/schema.md) - Estrutura completa
- [Migrations](banco-de-dados/migrations.md) - Migrations Laravel
- [Queries Comuns](banco-de-dados/queries-comuns.md) - Queries úteis

### 🤖 IA e Automação
- [Modelos Ollama](ia-e-automacao/modelos-ollama.md) - Modelos de IA disponíveis
- [Chatbot SIGEP](ia-e-automacao/chatbot-integracao.md) - Chatbot para usuários
- [Automação](ia-e-automacao/automacao-tarefas.md) - Tarefas automatizadas

## 🔧 Configurações Rápidas

### Variáveis de Ambiente Principais
```env
APP_NAME="SIGEP - Homologação"
APP_URL=http://localhost:8000
DB_DATABASE=sigep_homologacao
CACHE_STORE=redis
OLLAMA_BASE_URL=http://localhost:11434
```

### Comandos Essenciais
```bash
# Iniciar servidor
php artisan serve --host=0.0.0.0 --port=8000

# Resetar banco
php artisan migrate:fresh --seed

# Limpar cache
php artisan config:clear && php artisan cache:clear

# Listar modelos IA
docker exec ollama-server ollama list
```

## 📖 Recursos Adicionais

- **[.windsurfrules](../.windsurfrules)** - Regras do ambiente
- **[.env.example](../.env.example)** - Exemplo de configuração
- **[composer.json](../composer.json)** - Dependências do projeto

## 🤝 Contribuição

1. Siga os padrões de codificação
2. Documente novas funcionalidades
3. Teste antes de commitar
4. Mantenha a documentação atualizada

## 📞 Suporte

Para dúvidas e suporte:
- Consulte a documentação relevante
- Verifique os logs de erro
- Use o chatbot SIGEP para ajuda básica

---

**Última atualização**: 2026-03-23  
**Versão**: v2.0-homologação