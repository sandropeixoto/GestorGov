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

## 3. Implementação da Recepção (PHP)

Crie um arquivo `auth_sso.php` no seu projeto para receber o usuário:

```php
<?php
// 1. Defina a chave secreta (deve ser a mesma do Portal)
define('SSO_SECRET_KEY', 'GestorGov_Secure_Integration_Token_2026!');

// 2. Capture os dados da URL
$payload_base64 = $_GET['sso_payload'] ?? null;
$signature_received = $_GET['sso_sig'] ?? null;

if (!$payload_base64 || !$signature_received) {
    die("Acesso negado: Token SSO ausente.");
}

// 3. Valide a assinatura HMAC-SHA256
$expected_signature = hash_hmac('sha256', $payload_base64, SSO_SECRET_KEY);

if ($signature_received !== $expected_signature) {
    die("Acesso negado: Assinatura inválida.");
}

// 4. Decodifique o Payload
$payload_json = base64_decode($payload_base64);
$user_data = json_decode($payload_json, true);

// 5. Verifique a expiração (iat = Issued At, exp = Expires At)
if (time() > $user_data['exp']) {
    die("Acesso negado: Token expirado. Tente acessar novamente via Portal.");
}

// 6. Logique o usuário no seu sistema
session_start();
$_SESSION['usuario_id']     = $user_data['user_id'];
$_SESSION['usuario_nome']   = $user_data['user_name'];
$_SESSION['usuario_email']  = $user_data['user_email'];
$_SESSION['usuario_perfil'] = $user_data['user_level'];

// 7. Redirecione para a Home do seu módulo
header("Location: index.php");
exit;
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
