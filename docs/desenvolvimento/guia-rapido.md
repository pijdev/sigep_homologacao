# Guia Rápido - Setup SIGEP Homologação

## 🚀 Setup Inicial (5 minutos)

### Pré-requisitos
- PHP 8.4+
- MySQL 8.0
- Docker Desktop
- Composer
- Node.js (opcional)

### 1. Clonar o Projeto
```bash
git clone <repositorio> sigep_hml
cd sigep_hml
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
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sigep_homologacao
DB_USERNAME=sigep
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

---

## 🤖 Configurar IA Local (Ollama)

### 1. Iniciar Container Ollama
```bash
docker start ollama-server
```

### 2. Verificar Modelos
```bash
docker exec ollama-server ollama list
```

### 3. Modelos Disponíveis
- `sigep-adminlte:latest` - Desenvolvimento
- `sigep-chatbot:latest` - Chatbot usuários
- `qwen2.5-coder:7b` - Programação
- `phi4-mini:latest` - Tarefas rápidas

---

## 👥 Usuários Padrão

### Login Disponível
- **Admin**: `admin@sigep.local` / `sigep123`
- **Usuário**: `usuario@sigep.local` / `sigep123`

---

## 📁 Estrutura Básica

### Criar Novo Módulo
```bash
# Estrutura
modulos/[setor]/[nome]/
├── [nome]_view.php      # Interface
├── [nome]_logica.php    # Controller
├── assets/
│   ├── css/[nome].css   # Estilos
│   └── js/[nome].js     # JavaScript
└── README.md            # Docs
```

### Exemplo Prático
```bash
mkdir -p modulos/ti/novo_modulo
touch modulos/ti/novo_modulo/novo_modulo_view.php
touch modulos/ti/novo_modulo/novo_modulo_logica.php
mkdir -p modulos/ti/novo_modulo/assets/{css,js}
touch modulos/ti/novo_modulo/assets/css/novo_modulo.css
touch modulos/ti/novo_modulo/assets/js/novo_modulo.js
```

---

## 🔧 Comandos Úteis

### Banco de Dados
```bash
# Fresh migrate
php artisan migrate:fresh --seed

# Criar migration
php artisan make:migration create_tabela_exemplo

# Criar model
php artisan make:model Exemplo

# Tinker (testes)
php artisan tinker
```

### Cache
```bash
# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Desenvolvimento
```bash
# Instalar pacote
composer require nome/pacote

# Publicar assets
php artisan vendor:publish --tag=ladmin-assets

# Criar controller
php artisan make:controller ExemploController
```

### IA Local
```bash
# Listar modelos
docker exec ollama-server ollama list

# Testar modelo
curl -s http://localhost:11434/api/generate -d '{
  "model": "sigep-adminlte:latest",
  "prompt": "Como criar um módulo SIGEP?",
  "stream": false
}'

# Criar modelo
docker exec ollama-server ollama create novo-modelo -f /path/to/modelfile
```

---

## 🐛 Debug e Troubleshooting

### Verificar Configurações
```bash
# Verificar PHP
php --version
php -m | grep redis

# Verificar MySQL
mysql -u sigep -p sigep_homologacao

# Verificar Redis
docker exec ollama-server ollama list
```

### Logs Importantes
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# MySQL logs
tail -f /var/log/mysql/error.log

# Docker logs
docker logs ollama-server
```

### Problemas Comuns

#### 1. Erro de Conexão MySQL
```bash
# Verificar se MySQL está rodando
sc query mysql

# Resetar senha MySQL
mysql -u root -p
ALTER USER 'sigep'@'localhost' IDENTIFIED BY 'nova_senha';
FLUSH PRIVILEGES;
```

#### 2. Erro de Cache Redis
```bash
# Verificar se Redis está rodando
docker ps | findstr ollama

# Reiniciar container
docker restart ollama-server
```

#### 3. Erro de Permissões
```bash
# Verificar permissões do storage
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

---

## 🎯 Primeiros Passos

### 1. Explorar o Sistema
- Acesse http://localhost:8000
- Faça login com usuário admin
- Navegue pelos menus existentes
- Explore a estrutura de módulos

### 2. Criar Hello World
```php
// modulos/ti/teste/teste_view.php
<h1>Hello SIGEP!</h1>
<p>Módulo de teste funcionando!</p>

// modulos/ti/teste/teste_logica.php
<?php
echo "Módulo teste carregado com sucesso!";
?>
```

### 3. Adicionar ao Menu
```php
// includes/sidebar_logica.php
$menu['ti']['items'][] = [
    'title' => 'Teste',
    'icon' => 'fas fa-bug',
    'page' => '/modulos/ti/teste/teste_view.php'
];
```

---

## 📚 Próximos Passos

1. **Estudar Arquitetura**: [Arquitetura MVC](../arquitetura/mvc-adminlte.md)
2. **Criar Módulo Real**: [Criando Módulos](criando-modulos.md)
3. **Entender Frontend**: [Frontend Guide](frontend-guide.md)
4. **Configurar IA**: [Modelos Ollama](../ia-e-automacao/modelos-ollama.md)

---

## 🆘 Suporte

### Documentação
- [Documentação Completa](../README.md)
- [Regras do Ambiente](../.windsurfrules)

### Chatbot SIGEP
Use o modelo `sigep-chatbot:latest` para ajuda básica:
```bash
curl -s http://localhost:11434/api/generate -d '{
  "model": "sigep-chatbot:latest",
  "prompt": "Como funciona o sistema SIGEP?",
  "stream": false
}'
```

---

**Próximo**: [Padrões de Código](padroes-codigo.md)  
**Anterior**: [README](../README.md)