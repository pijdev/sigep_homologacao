# Modelos de IA Local - Ollama

## 🤖 Visão Geral dos Modelos

O SIGEP Homologação utiliza 4 modelos de IA especializados rodando localmente via Docker Ollama.

### 📋 Modelos Disponíveis

| Modelo | Base | Tamanho | Uso Principal | Contexto |
|--------|------|---------|---------------|----------|
| `sigep-adminlte:latest` | qwen2.5-coder:7b | 4.7 GB | Desenvolvimento SIGEP | 32.768 tokens |
| `sigep-chatbot:latest` | phi4-mini:latest | 2.5 GB | Chatbot usuários | 131.072 tokens |
| `qwen2.5-coder:7b` | qwen2.5-coder:7b | 4.7 GB | Programação geral | 32.768 tokens |
| `phi4-mini:latest` | phi4-mini:latest | 2.5 GB | Tarefas rápidas | 131.072 tokens |

---

## 🚀 Setup dos Modelos

### 1. Verificar Container Ollama
```bash
docker start ollama-server
docker ps | findstr ollama
```

### 2. Listar Modelos
```bash
docker exec ollama-server ollama list
```

### 3. Testar Conexão
```bash
curl -s http://localhost:11434/api/tags
```

---

## 🎯 sigep-adminlte:latest

### Propósito
Modelo especializado para desenvolvimento do SIGEP v2.0 com AdminLTE 4.

### Conhecimento Especializado
- **Tecnologias**: Laravel 13.x + PHP 8.4 + AdminLTE 4
- **Arquitetura**: MVC tradicional + SPA
- **Banco**: MySQL 8.0 + Redis
- **Padrões**: PSR-4, PDO, Bootstrap 5

### Exemplo de Uso
```bash
curl -s http://localhost:11434/api/generate -d '{
  "model": "sigep-adminlte:latest",
  "prompt": "Como criar um módulo SIGEP com CRUD completo?",
  "stream": false
}'
```

### Casos de Uso
- Criar estrutura de módulos
- Desenvolver controllers MVC
- Implementar interfaces AdminLTE
- Otimizar queries MySQL
- Configurar cache Redis

---

## 💬 sigep-chatbot:latest

### Propósito
Chatbot para atendimento e suporte aos usuários do SIGEP.

### Personalidade
- **Tom**: Profissional e governamental
- **Foco**: Ajudar usuários do sistema
- **Linguagem**: Acessível e clara
- **Conhecimento**: Funcionalidades do SIGEP

### Exemplo de Uso
```bash
curl -s http://localhost:11434/api/generate -d '{
  "model": "sigep-chatbot:latest",
  "prompt": "Como faço para cadastrar um novo interno?",
  "stream": false
}'
```

### Conhecimento do Sistema
- **Módulos**: Autenticação, Censura, Eclusa, Escolta
- **Setores**: Administração, TI, Coordenação
- **Funcionalidades**: CRUD, relatórios, permissões
- **Processos**: Workflows penitenciários

---

## 💻 qwen2.5-coder:7b

### Propósito
Programação geral e desenvolvimento de código.

### Características
- **Capacidades**: completion, tools, insert
- **Linguagens**: PHP, JavaScript, SQL, CSS
- **Frameworks**: Laravel, jQuery, Bootstrap

### Exemplo de Uso
```bash
curl -s http://localhost:11434/api/generate -d '{
  "model": "qwen2.5-coder:7b",
  "prompt": "Crie uma função PHP para validar CPF",
  "stream": false
}'
```

### Casos de Uso
- Debugging de código
- Otimização de performance
- Refatoração de código
- Geração de snippets

---

## ⚡ phi4-mini:latest

### Propósito
Tarefas rápidas e respostas concisas.

### Vantagens
- **Velocidade**: Respostas rápidas
- **Contexto**: 131.072 tokens (maior)
- **Eficiência**: Baixo consumo de recursos

### Exemplo de Uso
```bash
curl -s http://localhost:11434/api/generate -d '{
  "model": "phi4-mini:latest",
  "prompt": "Qual a diferença entre GET e POST?",
  "stream": false
}'
```

### Casos de Uso
- Perguntas rápidas
- Resumo de conceitos
- Geração de texto simples
- Ajuda imediata

---

## 🔧 Configuração no Laravel

### Variáveis de Ambiente
```env
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=sigep-adminlte:latest
OLLAMA_CODING_MODEL=qwen2.5-coder:7b
OLLAMA_CHATBOT_MODEL=sigep-chatbot:latest
OLLAMA_LIGHT_MODEL=phi4-mini:latest
```

### Helper Function
```php
// app/Helpers/OllamaHelper.php
function askOllama($prompt, $model = null) {
    $model = $model ?? config('ollama.default_model');
    $url = config('ollama.base_url') . '/api/generate';
    
    $response = Http::post($url, [
        'model' => $model,
        'prompt' => $prompt,
        'stream' => false
    ]);
    
    return $response->json('response');
}
```

### Controller Example
```php
// app/Http/Controllers/IAController.php
class IAController extends Controller
{
    public function ask(Request $request)
    {
        $prompt = $request->input('prompt');
        $model = $request->input('model', 'sigep-adminlte:latest');
        
        $response = askOllama($prompt, $model);
        
        return response()->json([
            'response' => $response,
            'model' => $model
        ]);
    }
}
```

---

## 📊 Performance dos Modelos

### Comparativo
| Modelo | Velocidade | Qualidade | Uso de Memória | Contexto |
|--------|-----------|-----------|----------------|---------|
| sigep-adminlte | Média | Alta | Médio | 32K |
| sigep-chatbot | Rápida | Média | Baixo | 131K |
| qwen2.5-coder | Média | Alta | Médio | 32K |
| phi4-mini | Rápida | Média | Baixo | 131K |

### Recomendações
- **Desenvolvimento**: `sigep-adminlte:latest`
- **Suporte**: `sigep-chatbot:latest`
- **Código**: `qwen2.5-coder:7b`
- **Quick**: `phi4-mini:latest`

---

## 🔄 Criação de Modelos Customizados

### Criar Modelfile
```dockerfile
FROM phi4-mini:latest

SYSTEM """
Você é um assistente especializado em [tópico].
[contexto específico]
"""

PARAMETER temperature 0.7
PARAMETER top_p 0.9
```

### Criar Modelo
```bash
docker cp modelfile ollama-server:/modelfile
docker exec ollama-server ollama create custom-model -f /modelfile
```

### Testar Modelo
```bash
docker exec ollama-server ollama run custom-model "Teste do modelo"
```

---

## 🐛 Troubleshooting

### Problemas Comuns

#### 1. Modelo Não Responde
```bash
# Verificar se container está rodando
docker ps | findstr ollama

# Reiniciar container
docker restart ollama-server

# Verificar logs
docker logs ollama-server
```

#### 2. Resposta Lenta
```bash
# Verificar uso de memória
docker stats ollama-server

# Usar modelo mais leve
phi4-mini:latest
```

#### 3. Erro de Conexão
```bash
# Verificar porta
netstat -an | findstr 11434

# Testar API
curl -s http://localhost:11434/api/tags
```

### Logs e Debug
```bash
# Logs do container
docker logs -f ollama-server

# Verificar modelos
docker exec ollama-server ollama list

# Testar modelo específico
docker exec ollama-server ollama show sigep-adminlte:latest
```

---

## 🚀 Integração com o Sistema

### Chatbot Integration
```javascript
// assets/js/chatbot.js
function askChatbot(message) {
    return fetch('/api/chatbot/ask', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            prompt: message,
            model: 'sigep-chatbot:latest'
        })
    })
    .then(response => response.json())
    .then(data => data.response);
}
```

### Development Assistant
```php
// Helper para desenvolvimento
function generateCode($description) {
    return askOllama(
        "Crie código PHP para: " . $description,
        'sigep-adminlte:latest'
    );
}
```

---

## 📈 Melhorias Futuras

### Roadmap
- [ ] Fine-tuning com dados SIGEP
- [ ] Integração com sistema de tickets
- [ ] Automação de testes
- [ ] Geração de documentação

### Monitoramento
```bash
# Estatísticas de uso
docker exec ollama-server ollama ps

# Informações do modelo
docker exec ollama-server ollama show sigep-adminlte:latest
```

---

**Próximo**: [Chatbot Integração](chatbot-integracao.md)  
**Anterior**: [Guia Rápido](../desenvolvimento/guia-rapido.md)