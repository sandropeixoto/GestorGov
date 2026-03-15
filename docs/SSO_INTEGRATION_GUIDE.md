# 🔐 Guia de Integração SSO - GestorGov

Este documento descreve como integrar módulos externos ao portal **GestorGov** utilizando o mecanismo de Single Sign-On (SSO) baseado em tokens assinados (HMAC-SHA256).

## 1. Fluxo de Autenticação
1. O usuário está logado no Portal GestorGov.
2. O usuário clica para acessar o seu módulo.
3. O portal gera um payload assinado e redireciona o usuário para a sua URL com os parâmetros `sso_payload` e `sso_sig`.
4. O seu sistema valida a assinatura e os dados.
5. Se válido, o seu sistema inicia a sessão local para o usuário.

## 2. Requisitos de Configuração
Ambos os sistemas devem compartilhar a mesma **Chave Secreta**.
- **Chave Atual:** `GestorGov_Secure_Integration_Token_2026!`
- **Algoritmo:** `HMAC-SHA256`

## 3. Implementação da Recepção (Exemplos)

### PHP (Simples)
Crie um arquivo `auth_sso.php` no seu projeto:

```php
<?php
define('SSO_SECRET_KEY', 'GestorGov_Secure_Integration_Token_2026!');
$payload_base64 = $_GET['sso_payload'] ?? null;
$signature_received = $_GET['sso_sig'] ?? null;

if (!$payload_base64 || !$signature_received) die("Acesso negado: Token SSO ausente.");

// Validação
$expected_signature = hash_hmac('sha256', $payload_base64, SSO_SECRET_KEY);
if ($signature_received !== $expected_signature) die("Acesso negado: Assinatura inválida.");

$user_data = json_decode(base64_decode($payload_base64), true);
if (time() > $user_data['exp']) die("Acesso negado: Token expirado.");

// Sessão Local
session_start();
$_SESSION['usuario_email'] = $user_data['user_email'];
header("Location: dashboard.php");
```

### Node.js (Express)
Ideal para backends que servem aplicações **React / Vue / Angular**:

```javascript
const crypto = require('crypto');

app.get('/auth/sso', (req, res) => {
    const { sso_payload, sso_sig } = req.query;
    const secret = 'GestorGov_Secure_Integration_Token_2026!';

    // 1. Validar Assinatura
    const expectedSig = crypto.createHmac('sha256', secret)
                              .update(sso_payload)
                              .digest('hex');

    if (sso_sig !== expectedSig) return res.status(403).send('Assinatura Inválida');

    // 2. Decodificar e Validar Expiração
    const userData = JSON.parse(Buffer.from(sso_payload, 'base64').toString());
    if (Date.now() / 1000 > userData.exp) return res.status(403).send('Token Expirado');

    // 3. Login no seu sistema (ex: JWT próprio)
    req.session.user = userData;
    res.redirect('/dashboard');
});
```

### Python (FastAPI / Flask)
```python
import hmac, hashlib, base64, json, time

@app.get("/auth/sso")
def sso_login(sso_payload: str, sso_sig: str):
    secret = "GestorGov_Secure_Integration_Token_2026!"
    
    # 1. Validar HMAC
    expected_sig = hmac.new(secret.encode(), sso_payload.encode(), hashlib.sha256).hexdigest()
    if not hmac.compare_digest(sso_sig, expected_sig):
        return {"error": "Invalid signature"}

    # 2. Decodificar
    user_data = json.loads(base64.b64decode(sso_payload).decode())
    if time.time() > user_data['exp']:
        return {"error": "Token expired"}

    return {"status": "authenticated", "user": user_data}
```

## 4. Estrutura do Payload (JSON)
O `sso_payload` decodificado conterá:
- `user_id`: ID único do usuário no banco do portal.
- `user_name`: Nome completo do usuário.
- `user_email`: E-mail oficial.
- `user_level`: Nível de acesso (1=Admin, 2=Operador, etc).
- `iat`: Timestamp de criação do token.
- `exp`: Timestamp de expiração (limite para realizar o login).

## 💡 Dicas para a IA do Projeto Destino
- Utilize o e-mail recebido para vincular o usuário a um registro existente no seu banco de dados local.
- Se o usuário não existir localmente, você pode optar por criá-lo automaticamente (Just-in-Time Provisioning) usando os dados do payload.
- Mantenha a `SSO_SECRET_KEY` protegida em variáveis de ambiente (`.env`).
