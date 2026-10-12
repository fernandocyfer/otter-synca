# OtterSynca — configuração segura do webhook

O webhook do OtterSynca Pro permite iniciar um deploy quando o GitHub envia um evento de `push` para a branch configurada.

## Requisitos

- OtterSynca Pro ativo
- Deploy automático habilitado
- Token do GitHub com acesso ao repositório
- Repositório, branch, tipo e slug de destino válidos
- Secret de webhook com pelo menos 32 caracteres
- HTTPS ativo no site

O endpoint recusa deploys quando o Pro está inativo, o deploy automático está desabilitado ou o secret não está configurado.

## Configuração no WordPress

1. Acesse **WordPress Admin > OtterSynca > Settings**.
2. Preencha o token, repositório, branch, tipo e slug de destino.
3. Com o OtterSynca Pro ativo, habilite **Auto Deploy**.
4. Gere um secret criptograficamente seguro em **Generate Secret**.
5. Salve as configurações.
6. Copie a URL exibida no campo **Webhook URL**.
7. Use **Validate Webhook** para confirmar a configuração local.

O botão de validação não envia o secret nem outros dados a serviços externos.

## Configuração no GitHub

1. Abra o repositório e acesse **Settings > Webhooks > Add webhook**.
2. Em **Payload URL**, informe a URL copiada do WordPress.
3. Selecione `application/json` em **Content type**.
4. Em **Secret**, informe exatamente o mesmo secret configurado no WordPress.
5. Selecione **Just the push event**.
6. Ative o webhook e salve.

## Proteções aplicadas

- Assinatura HMAC SHA-256 obrigatória
- Aceita somente eventos `push`
- Limite de 1 MB para o payload
- Validação estrita de repositório, branch, commit e slug
- Comparação com o repositório e a branch configurados
- Lock único contra deploys simultâneos
- Download com timeout, streaming e limite de 100 MB
- Bloqueio de path traversal e links simbólicos em ZIPs
- Troca do diretório de destino com rollback
- Respostas públicas sem detalhes internos

Commits com `[skip-deploy]` ou `[no-deploy]` na mensagem são ignorados.

## Solução de problemas

- **404**: Pro inativo ou deploy automático desabilitado.
- **401**: assinatura inválida; confira o secret nos dois lados.
- **400**: payload, repositório, branch ou commit inválido.
- **413**: payload maior que 1 MB.
- **500**: o deploy falhou; consulte o status do último deploy no painel do OtterSynca.

No GitHub, a entrega do webhook deve mostrar o cabeçalho `X-GitHub-Event: push` e uma assinatura `X-Hub-Signature-256`.
