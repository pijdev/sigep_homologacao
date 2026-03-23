<div align="center">
  </a>
  <a href="https://github.com/pijdev/sigep_homologacao/releases">
    <img src="https://img.shields.io/github/v/release/pijdev/sigep_homologacao" alt="Latest Version">
  </a>
  <a href="https://github.com/pijdev/sigep_homologacao/blob/main/LICENSE">
    <img src="https://img.shields.io/github/license/pijdev/sigep_homologacao" alt="License">
  </a>
  <a href="https://github.com/dfsmania/LaradminLTE">
    <img src="https://img.shields.io/badge/Based_on-LaradminLTE-blue" alt="Based on LaradminLTE">
  </a>
</div>

# SIGEP Homologação v2.0

> Sistema de Gestão Empresarial v2.0 - Ambiente de Homologação

Um sistema completo de gestão empresarial desenvolvido com base no [LaradminLTE](https://github.com/dfsmania/LaradminLTE), utilizando as mais modernas tecnologias web, focado em performance, segurança e escalabilidade.

## 🏗️ Base do Projeto

Este projeto é desenvolvido como uma customização do [LaradminLTE](https://github.com/dfsmania/LaradminLTE), um pacote Laravel que integra AdminLTE com funcionalidades de administração modernas.

## 🚀 Stack Tecnológico

### Backend

- **Framework**: Laravel 13.x
- **Linguagem**: PHP 8.4.16
- **Arquitetura**: MVC tradicional com navegação SPA
- **Autenticação**: Laravel Fortify
- **ORM**: Eloquent com MySQL 8.0

### Frontend

- **UI Framework**: AdminLTE 4
- **CSS Framework**: Bootstrap 5
- **JavaScript**: jQuery 3.6.0
- **Arquitetura**: Single Page Application (SPA)
- **Responsividade**: Mobile-first

### Banco de Dados & Cache

- **Database**: MySQL 8.0
- **Charset**: utf8mb4_unicode_ci
- **Cache**: Redis com Predis
- **Migrations**: Laravel Schema
- **Relações**: Foreign Keys com RESTRICT

### Inteligência Artificial

- **Engine**: Ollama Docker
- **Modelos**: 4 modelos especializados
- **Cliente**: Predis (PHP puro)
- **Aplicações**: Development assistant, Chatbot, Code generation

### DevOps & Infraestrutura

- **Containerização**: Docker Desktop
- **Versionamento**: Git + GitHub
- **Ambiente**: Homologação local
- **Servidor**: PHP Artisan Serve

## 🏗️ Arquitetura do Sistema

### Estrutura de Módulos

```
modulos/[setor]/[nome]/
├── [nome]_view.php      # Interface AdminLTE
├── [nome]_logica.php    # Controller PHP
├── assets/
│   ├── css/[nome].css   # Estilos customizados
│   └── js/[nome].js     # JavaScript + AJAX
└── README.md            # Documentação
```

### Padrões de Projeto

- **MVC**: Controller + View + Model
- **SPA**: Navegação via AJAX sem refresh
- **Modular**: Componentes independentes
- **Seguro**: Validação em todas as camadas

## 🤖 Modelos de IA Disponíveis

| Modelo                  | Base             | Uso          | Contexto    |
| ----------------------- | ---------------- | ------------ | ----------- |
| `sigep-adminlte:latest` | qwen2.5-coder:7b | Development  | 32K tokens  |
| `sigep-chatbot:latest`  | phi4-mini:latest | User Support | 131K tokens |
| `qwen2.5-coder:7b`      | qwen2.5-coder:7b | Programming  | 32K tokens  |
| `phi4-mini:latest`      | phi4-mini:latest | Quick Tasks  | 131K tokens |

## 📋 Pré-requisitos

### Sistema

- **OS**: Windows 10/11, Linux, macOS
- **PHP**: 8.4+
- **Composer**: 2.0+
- **Docker**: Desktop 4.0+

### Serviços

- **MySQL**: 8.0+
- **Redis**: Via Docker Ollama
- **Node.js**: 18+ (opcional)

## 🚀 Setup Rápido

### 1. Clonar Repositório

```bash
git clone https://github.com/pijdev/sigep_homologacao.git
cd sigep_homologacao
```

### 2. Instalar Dependências

```bash
composer install
npm install
```

### 3. Configurar Ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar Banco de Dados

Configure as credenciais no arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 5. Executar Migrations

```bash
php artisan migrate:fresh --seed
```

### 6. Iniciar Servidor

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Acesse: http://localhost:8000

## 🔧 Configurações Adicionais

### IA Local (Ollama)

```bash
# Iniciar container
docker start ollama-server

# Listar modelos
docker exec ollama-server ollama list
```

### Cache Redis

```env
CACHE_STORE=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### Variáveis de Ambiente Principais

```env
APP_NAME="SIGEP - Homologação"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=pt_BR
OLLAMA_BASE_URL=http://localhost:11434
```

## 📊 Features Técnicas

### Performance

- **Cache**: Redis para dados frequentes
- **Opcache**: Otimização PHP
- **Lazy Loading**: Componentes sob demanda
- **Compression**: Assets minificados

### Segurança

- **Autenticação**: Laravel Fortify
- **Validação**: Server-side + Client-side
- **Sanitização**: PDO prepared statements
- **CSRF**: Protection em todos os forms

### Internacionalização

- **Idioma**: Português Brasil (pt_BR)
- **Timezone**: America/Sao_Paulo
- **Charset**: utf8mb4_unicode_ci
- **Traduções**: AdminLTE + Laravel

## 📚 Documentação

### Documentação Completa

- [📋 Guia Principal](docs/README.md)
- [🏗️ Arquitetura](docs/arquitetura/)
- [💻 Desenvolvimento](docs/desenvolvimento/)
- [🤖 IA Local](docs/ia-e-automacao/)
- [🗄️ Banco de Dados](docs/banco-de-dados/)

### Regras do Ambiente

- [📖 .windsurfrules](.windsurfrules) - Regras de desenvolvimento
- [📝 CHANGELOG.md](CHANGELOG.md) - Histórico de mudanças

## 🧪 Testes

### Testes Automáticos

```bash
# Executar todos os testes
php artisan test

# Testes de unidade
php artisan test --testsuite=Unit

# Testes de feature
php artisan test --testsuite=Feature
```

### Testes Manuais

- **Login**: admin@sigep.local / sigep123
- **Interface**: Responsividade mobile/desktop
- **Performance**: Tempo de carregamento
- **Cache**: Redis functionality

## 📈 Performance

### Métricas

- **Cache Hit Rate**: 95%+
- **Page Load**: < 2s
- **Database Queries**: Otimizadas
- **Memory Usage**: < 128MB

### Monitoramento

- **Logs**: storage/logs/laravel.log
- **Debug**: APP_DEBUG=true
- **Telescope**: Development insights
- **Clockwork**: Performance profiling

## 🔄 CI/CD

### GitHub Actions

```yaml
name: Tests
on: [push, pull_request]
jobs:
    test:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v3
            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.4"
            - name: Install dependencies
              run: composer install
            - name: Run tests
              run: php artisan test
```

## 🚀 Deploy

### Ambiente de Homologação

- **Server**: Local development
- **Database**: MySQL local
- **Cache**: Redis local
- **URL**: http://localhost:8000

### Deploy Automatizado (Futuro)

- [ ] GitHub Actions
- [ ] Docker containers
- [ ] Kubernetes deployment
- [ ] Monitoring integration

## 📝 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🆘 Suporte

### Documentação

- [📚 Documentação Completa](docs/)
- [🔧 Regras do Ambiente](.windsurfrules)
- [📋 Guia Rápido](docs/desenvolvimento/guia-rapido.md)

### Issues

- [🐛 Reportar Bug](https://github.com/pijdev/sigep_homologacao/issues)
- [💡 Feature Request](https://github.com/pijdev/sigep_homologacao/issues/new)

### Chatbot

Use o modelo `sigep-chatbot:latest` para ajuda básica:

```bash
curl -s http://localhost:11434/api/generate -d '{
  "model": "sigep-chatbot:latest",
  "prompt": "Como funciona o sistema?",
  "stream": false
}'
```

## 🌟 Créditos

### Tecnologias

- [Laravel](https://laravel.com/) - Framework PHP
- [AdminLTE](https://adminlte.io/) - Template Admin
- [Bootstrap](https://getbootstrap.com/) - CSS Framework
- [jQuery](https://jquery.com/) - JavaScript Library
- [MySQL](https://www.mysql.com/) - Database
- [Redis](https://redis.io/) - Cache
- [Ollama](https://ollama.com/) - IA Local

### Autor

- **[@pijdev](https://github.com/pijdev)** - Desenvolvedor Principal

---

<div align="center">
  <p>Feito com ❤️ e ☕ por pijdev</p>
  <p>© 2026 SIGEP Homologação v2.0</p>
</div>
# Teste de commit em português
