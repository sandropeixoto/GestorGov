<?php
// auth_action.php na raiz
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método inválido']);
    exit;
}

$prefix = trim($_POST['email_prefix'] ?? '');
if (empty($prefix)) {
    echo json_encode(['success' => false, 'error' => 'Informe o e-mail']);
    exit;
}

$email = $prefix . '@sefa.pa.gov.br';

try {
    // Agora permitimos qualquer e-mail da SEFA. 
    // Apenas verificamos se o e-mail segue o padrão (já garantido pelo sufixo)
    
    $token = bin2hex(random_bytes(32));

    // Salva token no banco usando NOW() do banco para evitar conflito de timezone
    $stmt = $pdo->prepare("INSERT INTO login_tokens (email, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))");
    $stmt->execute([$email, $token]);

    // Prepara e-mail
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $link = "$protocol://$host$path/verify.php?token=$token";
    
    $assunto = "Link de Acesso - GestorGov";
    $mensagem = "
        <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #eee; border-radius: 10px; padding: 40px;'>
            <h1 style='color: #0f172a; text-align: center;'>GestorGov</h1>
            <p style='font-size: 16px; color: #444;'>Olá,</p>
            <p style='font-size: 16px; color: #444;'>Você solicitou acesso ao sistema <strong>GestorGov</strong>. Clique no botão abaixo para entrar automaticamente:</p>
            <div style='text-align: center; margin: 40px 0;'>
                <a href='$link' style='background-color: #570df8; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 18px;'>Entrar no Sistema</a>
            </div>
            <p style='font-size: 12px; color: #888; text-align: center;'>Este link é válido por 30 dias. Se você não solicitou este acesso, ignore este e-mail.</p>
        </div>
    ";

    $enviou = enviarEmailViaSocket($email, $assunto, $mensagem);

    if ($enviou === true) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Falha ao enviar e-mail: ' . $enviou]);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erro de banco de dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erro inesperado: ' . $e->getMessage()]);
}

/**
 * Gabarito de Envio de E-mail via Socket
 */
function enviarEmailViaSocket($destinatario, $assunto, $mensagem) {
    $host = 'mail.eventossefa.com.br';
    $user = 'email@eventossefa.com.br';
    $pass = 'Senh@2025'; 
    $port = 465;
    $timeout = 30;
    $localhost = $_SERVER['SERVER_NAME'];
    $nl = "\r\n"; 

    $socket = fsockopen("ssl://" . $host, $port, $errno, $errstr, $timeout);
    if (!$socket) return "ERRO: Não conectou. $errstr ($errno)";

    if (!function_exists('ler_smtp')) {
        function ler_smtp($socket) {
            $data = "";
            while ($str = fgets($socket, 515)) {
                $data .= $str;
                if (substr($str, 3, 1) == " ") break;
            }
            return $data;
        }
    }

    ler_smtp($socket);
    fputs($socket, "EHLO $localhost$nl");
    ler_smtp($socket);
    fputs($socket, "AUTH LOGIN$nl");
    ler_smtp($socket);
    fputs($socket, base64_encode($user) . $nl);
    ler_smtp($socket);
    fputs($socket, base64_encode($pass) . $nl);
    $auth = ler_smtp($socket);

    if (strpos($auth, '235') === false) {
        fclose($socket);
        return "ERRO DE LOGIN SMTP: $auth";
    }

    $headers  = "MIME-Version: 1.0$nl";
    $headers .= "Content-type: text/html; charset=UTF-8$nl";
    $headers .= "From: Sistema GestorGov <$user>$nl";
    $headers .= "To: $destinatario$nl";
    $headers .= "Subject: $assunto$nl";

    fputs($socket, "MAIL FROM: <$user>$nl");
    ler_smtp($socket);
    fputs($socket, "RCPT TO: <$destinatario>$nl");
    ler_smtp($socket);
    fputs($socket, "DATA$nl");
    ler_smtp($socket);
    fputs($socket, "$headers$nl$mensagem$nl.$nl");
    $envio = ler_smtp($socket);
    fputs($socket, "QUIT$nl");
    fclose($socket);

    return (strpos($envio, '250') !== false) ? true : "ERRO NO ENVIO: $envio";
}
?>
