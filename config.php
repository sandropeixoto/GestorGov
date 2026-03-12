<?php
// config.php na raiz
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "192.185.214.25";
$user = "eventoss_vocegov";
$pass = "Senh@2025";
$db = "eventoss_vocegov";

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

/**
 * Função global para envio de e-mail via Socket (SMTP Autenticado)
 */
function enviarEmailViaSocket($destinatario, $assunto, $mensagem) {
    $host = 'mail.eventossefa.com.br';
    $user = 'email@eventossefa.com.br';
    $pass = 'Senh@2025'; 
    $port = 465;
    $timeout = 30;
    $localhost = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $nl = "\r\n"; 

    $socket = @fsockopen("ssl://" . $host, $port, $errno, $errstr, $timeout);
    if (!$socket) return "ERRO: Não conectou ao servidor de e-mail. $errstr ($errno)";

    $ler_smtp = function($socket) {
        $data = "";
        while ($str = fgets($socket, 515)) {
            $data .= $str;
            if (substr($str, 3, 1) == " ") break;
        }
        return $data;
    };

    $ler_smtp($socket);
    fputs($socket, "EHLO $localhost$nl");
    $ler_smtp($socket);
    fputs($socket, "AUTH LOGIN$nl");
    $ler_smtp($socket);
    fputs($socket, base64_encode($user) . $nl);
    $ler_smtp($socket);
    fputs($socket, base64_encode($pass) . $nl);
    $auth = $ler_smtp($socket);

    if (strpos($auth, '235') === false) {
        fclose($socket);
        return "ERRO DE LOGIN SMTP: $auth";
    }

    // Identificar o sistema no remetente
    $from_name = "Sistema GestorGov";
    $headers  = "MIME-Version: 1.0$nl";
    $headers .= "Content-type: text/html; charset=UTF-8$nl";
    $headers .= "From: $from_name <$user>$nl";
    $headers .= "To: $destinatario$nl";
    $headers .= "Subject: =?UTF-8?B?".base64_encode($assunto)."?=$nl";

    fputs($socket, "MAIL FROM: <$user>$nl");
    $ler_smtp($socket);
    fputs($socket, "RCPT TO: <$destinatario>$nl");
    $ler_smtp($socket);
    fputs($socket, "DATA$nl");
    $ler_smtp($socket);
    fputs($socket, "$headers$nl$mensagem$nl.$nl");
    $envio = $ler_smtp($socket);
    fputs($socket, "QUIT$nl");
    fclose($socket);

    return (strpos($envio, '250') !== false) ? true : "ERRO NO ENVIO: $envio";
}
?>
