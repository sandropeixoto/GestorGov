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

---

## 5. Gestão de Usuários, Perfis e Acesso ao Sistema

### 5.1 Níveis de Acesso via Payload SSO

O campo `user_level` do payload define o perfil do usuário no módulo receptor:

| `user_level` | Perfil         | Permissões                                         |
|:---:|----------------|-----------------------------------------------------|
| 1  | Administrador  | CRUD completo, configurações do módulo               |
| 2  | Operador       | Criação e edição de registros                        |
| 3  | Visualizador   | Somente leitura (sem criação, edição ou exclusão)    |

O módulo deve inspecionar esse campo e aplicar as restrições de interface e de API correspondentes antes de renderizar qualquer conteúdo.

### 5.2 Liga/Desliga — Acesso Público para Consulta

O módulo pode implementar um **interruptor de acesso público de leitura**, que, quando ativado, permite que **qualquer pessoa autenticada via SSO** acesse o sistema em modo **somente leitura** (visualização/consulta), independentemente do `user_level` recebido.

> [!IMPORTANT]
> Esta configuração **não substitui** a autenticação SSO. O token ainda deve ser válido e não expirado. O que muda é que, em vez de negar o acesso a usuários sem perfil explicitamente cadastrado, o sistema os recebe com permissões mínimas de consulta.

**Comportamento esperado quando o interruptor estiver LIGADO:**
- Usuários com `user_level` não mapeado localmente são tratados como **Visitante/Consulta**.
- Nenhuma ação de escrita (POST/PUT/DELETE) é permitida via interface ou API.
- Botões de criação, edição e exclusão devem ser **ocultados ou desabilitados**.
- Uma faixa informativa deve ser exibida: *"Você está acessando em modo somente leitura."*

**Comportamento esperado quando o interruptor estiver DESLIGADO:**
- Apenas usuários com perfil cadastrado localmente (`user_level` 1 ou 2) têm acesso.
- Demais usuários devem ser redirecionados para uma página de acesso negado.

**Exemplo de implementação da verificação (PHP):**

```php
// Em config.php ou similar
define('PUBLIC_READONLY_ACCESS', true); // true = ligado, false = desligado

// Em auth_sso.php, após validar o token:
$user_level = (int)($user_data['user_level'] ?? 0);
$is_local_user = in_array($user_level, [1, 2]); // níveis com acesso completo

if (!$is_local_user) {
    if (!PUBLIC_READONLY_ACCESS) {
        header("Location: acesso_negado.php");
        exit;
    }
    // Acesso público: força modo de somente leitura
    $_SESSION['readonly_mode'] = true;
    $_SESSION['user_profile']  = 'visitante';
} else {
    $_SESSION['readonly_mode'] = false;
    $_SESSION['user_profile']  = ($user_level === 1) ? 'admin' : 'operador';
}
```

> [!TIP]
> Armazene a chave `PUBLIC_READONLY_ACCESS` em uma tabela de configurações do banco de dados para permitir que administradores do módulo alterem o interruptor via painel, sem necessidade de deploy.

---

## 6. Log de Acesso de Usuários

### 6.1 Por que registrar acessos?

O registro de acessos é **obrigatório** em sistemas integrados ao GestorGov por três razões principais:

1. **Rastreabilidade e auditoria** — permite identificar quem consultou ou alterou informações sensíveis e em qual momento.
2. **Segurança** — detecta tentativas de acesso indevido ou padrões anômalos (ex: volume alto de consultas por um visitante).
3. **Conformidade** — atende às exigências da LGPD quanto ao controle de acesso a dados de terceiros.

### 6.2 Distinção entre Usuários Cadastrados e Visitantes

É fundamental diferenciar os dois tipos de acesso no log:

| Tipo            | Origem                          | Critério de Identificação                            |
|-----------------|---------------------------------|------------------------------------------------------|
| **Cadastrado**  | `user_level` 1 ou 2 (local DB)  | `user_id` mapeado na tabela local de usuários        |
| **Visitante**   | SSO válido, sem cadastro local  | `user_id` presente no payload mas ausente localmente |

> [!WARNING]
> **Não registre apenas os acessos de usuários cadastrados.** O acesso de visitantes (modo somente leitura) também deve ser persistido — eles representam um vetor de risco caso o interruptor de acesso público esteja ativado inadvertidamente.

### 6.3 Estrutura Recomendada da Tabela de Log

```sql
CREATE TABLE access_log (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT          NOT NULL COMMENT 'ID do GestorGov (do payload SSO)',
    user_email   VARCHAR(255) NOT NULL,
    user_profile ENUM('admin','operador','visitante') NOT NULL,
    action       VARCHAR(100) NOT NULL COMMENT 'Ex: LOGIN, VIEW_CONTRACT, EXPORT',
    resource_id  INT          NULL     COMMENT 'ID do recurso acessado, se aplicável',
    ip_address   VARCHAR(45)  NOT NULL,
    accessed_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user    (user_id),
    INDEX idx_profile (user_profile),
    INDEX idx_time    (accessed_at)
);
```

### 6.4 Exemplo de Registro de Acesso (PHP)

```php
function log_access(PDO $pdo, array $user_data, string $action, ?int $resource_id = null): void {
    $stmt = $pdo->prepare("
        INSERT INTO access_log (user_id, user_email, user_profile, action, resource_id, ip_address)
        VALUES (:uid, :email, :profile, :action, :rid, :ip)
    ");
    $stmt->execute([
        ':uid'     => $user_data['user_id'],
        ':email'   => $user_data['user_email'],
        ':profile' => $_SESSION['user_profile'] ?? 'visitante',
        ':action'  => $action,
        ':rid'     => $resource_id,
        ':ip'      => $_SERVER['REMOTE_ADDR'],
    ]);
}

// Uso após autenticação SSO bem-sucedida:
log_access($pdo, $user_data, 'LOGIN');

// Uso ao acessar um contrato:
log_access($pdo, $user_data, 'VIEW_CONTRACT', $contract_id);
```

> [!NOTE]
> Retenha os logs por no mínimo **12 meses** e implemente uma rotina de purge automático para registros mais antigos, garantindo conformidade com políticas de retenção de dados.
