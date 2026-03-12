<?php
// users_action.php na raiz
require_once 'config.php';
require_once 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_users.php");
    exit;
}

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;

try {
    if ($action === 'create' || $action === 'update') {
        $data = [
            'nome' => $_POST['nome'],
            'email' => trim($_POST['email']),
            'nivel' => $_POST['nivel'],
            'status' => $_POST['status']
        ];

        if ($action === 'create') {
            $cols = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $stmt = $pdo->prepare("INSERT INTO usuarios ($cols) VALUES ($placeholders)");
            $stmt->execute($data);
        } else {
            $sets = [];
            foreach ($data as $key => $val) {
                $sets[] = "$key = :$key";
            }
            $stmt = $pdo->prepare("UPDATE usuarios SET " . implode(", ", $sets) . " WHERE id = :id");
            $data['id'] = $id;
            $stmt->execute($data);
        }
    } elseif ($action === 'delete' && $id) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'send_welcome' && $id) {
        $stmt = $pdo->prepare("SELECT nome, email FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
            $host = $_SERVER['HTTP_HOST'];
            $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $base_url = "$protocol://$host$path";
            $login_url = "$base_url/index.php";
            
            $assunto = "Bem-vindo ao GestorGov";
            $mensagem = "
                <div style='font-family: \"Inter\", sans-serif, Arial; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background-color: #ffffff; color: #1e293b;'>
                    <div style='background-color: #0f172a; padding: 30px; text-align: center;'>
                        <h1 style='color: #ffffff; margin: 0; font-size: 28px; letter-spacing: -0.025em;'>GestorGov</h1>
                    </div>
                    <div style='padding: 40px;'>
                        <h2 style='font-size: 20px; font-weight: 600; margin-bottom: 16px;'>Olá, ".htmlspecialchars($user['nome'])."!</h2>
                        <p style='line-height: 1.6; margin-bottom: 24px;'>É um prazer ter você conosco. Seu acesso ao sistema <strong>GestorGov</strong> foi configurado com sucesso.</p>
                        
                        <div style='background-color: #f8fafc; border-left: 4px solid #570df8; padding: 20px; margin-bottom: 24px;'>
                            <h3 style='margin-top: 0; font-size: 16px; color: #570df8;'>Instruções de Acesso:</h3>
                            <ul style='padding-left: 20px; margin-bottom: 0;'>
                                <li style='margin-bottom: 10px;'>O acesso é exclusivo para e-mails institucionais <strong>@sefa.pa.gov.br</strong>.</li>
                                <li style='margin-bottom: 10px;'>No login, informe seu e-mail da SEFA.</li>
                                <li style='margin-bottom: 10px;'>Você receberá um e-mail com um <strong>token de acesso</strong> seguro.</li>
                                <li>Ao clicar no link do token, sua sessão será validada por <strong>30 dias</strong> neste navegador.</li>
                            </ul>
                        </div>

                        <div style='text-align: center; margin: 40px 0;'>
                            <a href='$login_url' style='background-color: #570df8; color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; display: inline-block; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);'>Acessar o Sistema</a>
                        </div>

                        <p style='font-size: 14px; color: #64748b; margin-bottom: 8px;'>Ou copie e cole o link abaixo no seu navegador:</p>
                        <p style='font-size: 13px; color: #570df8; word-break: break-all; background-color: #f1f5f9; padding: 10px; border-radius: 4px;'>$login_url</p>
                    </div>
                    <div style='background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;'>
                        <p style='font-size: 12px; color: #94a3b8; margin: 0;'>&copy; ".date('Y')." GestorGov - Secretaria da Fazenda do Pará</p>
                    </div>
                </div>
            ";

            $enviou = enviarEmailViaSocket($user['email'], $assunto, $mensagem);
            
            if ($enviou === true) {
                header("Location: manage_users.php?msg=email_sent");
                exit;
            } else {
                die("Erro ao enviar e-mail: " . $enviou);
            }
        }
    }

    header("Location: manage_users.php?msg=success");
    exit;

} catch (PDOException $e) {
    die("Erro ao processar usuário: " . $e->getMessage());
}
?>
