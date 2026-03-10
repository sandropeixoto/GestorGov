<?php
// app-contratos/prestadores_action.php
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/auth_module.php';

if (!CONTRATOS_GESTOR) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: prestadores.php");
    exit;
}

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;

try {
    if ($action === 'create' || $action === 'update') {
        $data = [
            'Nome' => $_POST['Nome'],
            'Tipo' => $_POST['Tipo'],
            'CNPJ' => $_POST['CNPJ'],
            'Email' => $_POST['Email'],
            'Telefone' => $_POST['Telefone'],
            'Endereco' => $_POST['Endereco']
        ];

        if ($action === 'create') {
            $cols = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $stmt = $pdo->prepare("INSERT INTO Prestador ($cols) VALUES ($placeholders)");
            $stmt->execute($data);
        } else {
            $sets = [];
            foreach ($data as $key => $val) {
                $sets[] = "$key = :$key";
            }
            $stmt = $pdo->prepare("UPDATE Prestador SET " . implode(", ", $sets) . " WHERE Id = :id");
            $data['id'] = $id;
            $stmt->execute($data);
        }
    } elseif ($action === 'delete' && $id) {
        $stmt = $pdo->prepare("DELETE FROM Prestador WHERE Id = ?");
        $stmt->execute([$id]);
    }

    header("Location: prestadores.php?msg=success");
    exit;

} catch (PDOException $e) {
    die("Erro ao processar fornecedor: " . $e->getMessage());
}
?>
