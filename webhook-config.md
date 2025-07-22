# OtterSynca - Sistema de Webhook para Deploy Automático

## Visão Geral

O OtterSynca agora inclui um sistema de webhook que permite deploy automático quando há commits na branch configurada do seu repositório GitHub.

## Funcionalidades

- **Deploy Automático**: Deploy automático baseado em commits do GitHub
- **Verificação de Branch**: Só faz deploy de commits na branch configurada
- **Verificação de Segurança**: Suporte a webhook secret para autenticação
- **Prevenção de Duplicação**: Evita deploy do mesmo commit múltiplas vezes
- **Logs Detalhados**: Registra todos os deploys e erros
- **Interface Administrativa**: Painel para configurar e monitorar deploys
- **Controle Total**: Webhook só é ativo quando o deploy automático está habilitado

## Comportamento Importante

### Quando o Deploy Automático está DESATIVADO:
- O endpoint do webhook (`/otter-synca-webhook/`) **NÃO** é registrado
- Qualquer tentativa de acessar o webhook retorna 404
- **Nenhum deploy automático acontece**, mesmo se houver webhook configurado no GitHub
- O plugin funciona apenas com deploy manual

### Quando o Deploy Automático está ATIVADO:
- O endpoint do webhook é registrado e fica ativo
- Webhooks do GitHub são processados
- Deploy automático acontece para commits na branch configurada

## Configuração

### 1. Configurações Básicas

1. Acesse **WordPress Admin > OtterSynca**
2. Configure as seguintes opções:
   - **GitHub Token**: Token de acesso pessoal do GitHub
   - **Repository**: Nome do repositório (ex: `usuario/repositorio`)
   - **Branch**: Branch para monitorar (ex: `main` ou `master`)
   - **Tipo de Deploy**: Plugin ou Tema
   - **Slug do Alvo**: Nome da pasta do plugin/tema

### 2. Ativar Deploy Automático

1. Marque a opção **"Ativar deploy automático via webhook"**
2. Configure o **Webhook Secret** (recomendado para segurança)
3. Copie a **URL do Webhook** exibida
4. **Salve as configurações** - isso ativa o endpoint do webhook

### 3. Configurar Webhook no GitHub

1. Vá para seu repositório no GitHub
2. Acesse **Settings > Webhooks**
3. Clique em **"Add webhook"**
4. Configure:
   - **Payload URL**: Cole a URL do webhook copiada
   - **Content type**: `application/json`
   - **Secret**: Cole o secret configurado (opcional)
   - **Which events**: Selecione **"Just the push event"**
5. Clique em **"Add webhook"**

## Como Funciona

### Fluxo de Deploy Automático

1. **Commit no GitHub**: Desenvolvedor faz push para a branch configurada
2. **Webhook Disparado**: GitHub envia notificação para o webhook
3. **Verificação**: Plugin verifica:
   - Se o deploy automático está ativado
   - Se é o repositório correto
   - Se é a branch configurada
   - Se o commit já foi deployado
4. **Deploy**: Se todas as verificações passarem, o deploy é executado
5. **Registro**: O resultado é registrado no banco de dados

### Verificações de Segurança

- **Webhook Secret**: Verifica a assinatura do webhook (se configurado)
- **Repository Match**: Só processa webhooks do repositório configurado
- **Branch Match**: Só faz deploy de commits na branch configurada
- **Deploy Lock**: Evita múltiplos deploys simultâneos
- **Commit Duplication**: Evita deploy do mesmo commit

### Commits Ignorados

O sistema automaticamente ignora:
- Commits de merge (`Merge branch...`)
- Commits com `[skip-deploy]` na mensagem
- Commits com `[no-deploy]` na mensagem

## Monitoramento

### Painel Administrativo

O painel mostra:
- **Status do Deploy Automático**: Ativado/Desativado
- **Último Deploy**: Data, status e detalhes
- **Último Commit Deployado**: Hash do commit
- **URL do Webhook**: Para configuração no GitHub

### Logs

Todos os eventos são registrados no log de erros do WordPress:
- Webhooks recebidos
- Verificações de segurança
- Deploys executados
- Erros encontrados

## Testando

### Teste do Status do Webhook

Acesse: `https://seu-site.com/wp-content/plugins/otter-synca/test-webhook-status.php`

Este arquivo verifica:
- Se o deploy automático está ativado
- Se o endpoint do webhook está registrado
- Se as regras de rewrite estão corretas
- O comportamento esperado baseado na configuração

### Teste do Webhook

1. No painel administrativo, clique em **"Testar Webhook"**
2. O sistema simulará um webhook e mostrará o resultado

### Teste Manual

Acesse: `https://seu-site.com/wp-content/plugins/otter-synca/test-webhook.php`

**Nota**: Este arquivo só funciona com `WP_DEBUG` ativado e para usuários administradores.

## Troubleshooting

### Webhook Não Funciona

1. **Verifique se o Deploy Automático está Ativado**: O webhook só funciona quando ativado
2. **Verifique a URL**: Confirme se a URL do webhook está correta
3. **Verifique o Secret**: Se configurado, confirme se está igual no GitHub
4. **Verifique as Permissões**: Confirme se o token do GitHub tem permissões adequadas
5. **Verifique os Logs**: Consulte o log de erros do WordPress
6. **Flush Rewrite Rules**: Vá em Settings > Permalinks e clique "Save Changes"

### Deploy Não Executa

1. **Verifique se o Deploy Automático está Ativado**: Esta é a causa mais comum
2. **Verifique a Branch**: Confirme se o commit foi na branch configurada
3. **Verifique o Repository**: Confirme se o webhook veio do repositório correto
4. **Verifique o Deploy Lock**: Aguarde se há outro deploy em andamento

### Erros Comuns

- **404 Not Found**: Webhook endpoint não registrado (deploy automático desativado)
- **401 Unauthorized**: Webhook secret incorreto
- **400 Bad Request**: Payload do webhook inválido
- **500 Internal Server Error**: Erro interno do plugin

## Configurações Avançadas

### Variáveis de Opção

- `otter_synca_auto_deploy`: Ativa/desativa deploy automático
- `otter_synca_webhook_secret`: Secret do webhook
- `otter_synca_last_deployed_commit`: Hash do último commit deployado
- `otter_synca_last_deploy`: Informações do último deploy
- `otter_synca_deploy_lock`: Lock para evitar deploys simultâneos

### Hooks Disponíveis

- `otter_synca_webhook_received`: Disparado quando webhook é recebido
- `otter_synca_deploy_started`: Disparado quando deploy inicia
- `otter_synca_deploy_completed`: Disparado quando deploy termina
- `otter_synca_deploy_failed`: Disparado quando deploy falha

## Suporte

Para suporte técnico ou dúvidas sobre o sistema de webhook, consulte a documentação ou entre em contato com o desenvolvedor. 