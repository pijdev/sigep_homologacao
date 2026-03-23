# Virtual Host Apache - SIGEP Homologação

## 📋 Configuração do Virtual Host

### 🎯 Objetivo
Configurar o Apache para servir o ambiente de homologação SIGEP através de `http://sigep-hml.localhost` sem necessidade de executar `php artisan serve`.

## 🔧 Arquivos Configurados

### 1. Virtual Host Configuration
**Arquivo**: `C:\Program Files\Apache24\conf\extra\httpd-vhosts.conf`

```apache
# SIGEP Homologação VirtualHost
<VirtualHost *:80>
    ServerAdmin webmaster@sigep-hml.localhost
    DocumentRoot "C:/Sites/sigep_hml/public"
    ServerName sigep-hml.localhost
    ServerAlias www.sigep-hml.localhost
    ErrorLog "logs/sigep-hml-error_log"
    CustomLog "logs/sigep-hml-access_log" common
    
    <Directory "C:/Sites/sigep_hml/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 2. Hosts File
**Arquivo**: `C:\Windows\System32\drivers\etc\hosts`

```hosts
127.0.0.1 sigep-hml.localhost
```

## 🚀 Como Usar

### 1. Acessar o Sistema
- **URL**: `http://sigep-hml.localhost`
- **Login**: `http://sigep-hml.localhost/login`
- **Dashboard**: `http://sigep-hml.localhost/laradminlte-welcome`

### 2. Fluxo de Navegação
1. Acessa `http://sigep-hml.localhost/`
2. Sistema verifica sessão (`Auth::check()`)
3. **Não logado**: Redireciona para `/login`
4. **Logado**: Redireciona para `/laradminlte-welcome`

## 🛠️ Configurações Apache

### Módulos PHP
```apache
LoadModule php_module "C:/Program Files/PHP/php8apache2_4.dll"
AddHandler application/x-httpd-php .php
PHPIniDir "C:/Program Files/PHP"
```

### Permissões
- `AllowOverride All`: Permite .htaccess
- `Require all granted`: Acesso liberado
- `Options Indexes FollowSymLinks`: Listagem e symlinks

## 📊 Logs

### Error Log
- **Arquivo**: `C:\Program Files\Apache24\logs\sigep-hml-error_log`
- **Uso**: Debug de erros PHP/Apache

### Access Log
- **Arquivo**: `C:\Program Files\Apache24\logs\sigep-hml-access_log`
- **Uso**: Monitoramento de acessos

## 🔍 Verificação

### Testar Virtual Host
```bash
# Verificar configuração
httpd -S

# Testar resposta
curl -I http://sigep-hml.localhost
```

### Status Apache
```bash
# Reiniciar Apache
net stop Apache2.4
net start Apache2.4

# Verificar portas
netstat -an | findstr ":80"
```

## 🎯 Benefícios

### ✅ Vantagens
- **Sem artisan serve**: Usa Apache diretamente
- **URL amigável**: `sigep-hml.localhost`
- **Performance**: Apache otimizado
- **Logs separados**: Debug facilitado
- **Produção similar**: Ambiente realista

### 🔄 Comparação
| Método | URL | Porta | Performance |
|--------|-----|-------|-------------|
| `php artisan serve` | `localhost:8000` | 8000 | PHP dev server |
| Apache VirtualHost | `sigep-hml.localhost` | 80 | Apache otimizado |

## 🚨 Troubleshooting

### Problemas Comuns

#### 1. 403 Forbidden
- **Causa**: Permissões negadas
- **Solução**: Verifique `Require all granted`

#### 2. 404 Not Found
- **Causa**: DocumentRoot incorreto
- **Solução**: Verifique caminho em `DocumentRoot`

#### 3. 500 Internal Error
- **Causa**: Erro PHP/.htaccess
- **Solução**: Verifique error log

#### 4. DNS Resolution
- **Causa**: Hosts não configurado
- **Solução**: Adicione entrada no hosts file

### Comandos Úteis
```bash
# Limpar cache DNS
ipconfig /flushdns

# Verificar configuração Apache
httpd -t

# Verificar módulos PHP
php -m
```

## 📝 Manutenção

### Backup Config
```bash
# Backup virtual hosts
copy "C:\Program Files\Apache24\conf\extra\httpd-vhosts.conf" "C:\backup\httpd-vhosts.conf.bak"
```

### Monitoramento
- **Accessos**: Monitorar `sigep-hml-access_log`
- **Erros**: Monitorar `sigep-hml-error_log`
- **Performance**: Usar Apache benchmark

---

**Resultado**: Sistema SIGEP Homologação acessível via `http://sigep-hml.localhost` com Apache otimizado! 🚀
