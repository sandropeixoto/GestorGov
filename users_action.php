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
    }

    header("Location: manage_users.php?msg=success");
    exit;

} catch (PDOException $e) {
    die("Erro ao processar usuário: " . $e->getMessage());
}
?>
