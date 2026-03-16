<?php
// launcher_action.php na raiz
require_once 'config.php';
require_once 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_launcher.php");
    exit;
}

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;

if ($action === 'reorder') {
    header('Content-Type: application/json');
    $data = json_decode($_POST['data'] ?? '[]', true);
    
    if (empty($data)) {
        echo json_encode(['success' => false, 'error' => 'Dados vazios']);
        exit;
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE launcher_modules SET display_order = ? WHERE id = ?");
        foreach ($data as $item) {
            $stmt->execute([$item['order'], $item['id']]);
        }
        $pdo->commit();
        
        logSistema($pdo, 'Launcher', 'Reorder Modules', 'launcher_modules', null, $data);
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

try {
    if ($action === 'create' || $action === 'update') {
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'icon' => $_POST['icon'] ?? 'ph-cube',
            'url' => $_POST['url'],
            'display_order' => $_POST['display_order'] ?? 0,
            'is_active' => $_POST['is_active'] ?? 1,
            'is_external' => isset($_POST['is_external']) ? 1 : 0,
            'open_in_new_tab' => isset($_POST['open_in_new_tab']) ? 1 : 0
        ];

        if ($action === 'create') {
            $cols = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $stmt = $pdo->prepare("INSERT INTO launcher_modules ($cols) VALUES ($placeholders)");
            $stmt->execute($data);
            $new_id = $pdo->lastInsertId();
            
            logSistema($pdo, 'Launcher', 'Create Module', 'launcher_modules', $new_id, $data);
        } else {
            $sets = [];
            foreach ($data as $key => $val) {
                $sets[] = "$key = :$key";
            }
            $stmt = $pdo->prepare("UPDATE launcher_modules SET " . implode(", ", $sets) . " WHERE id = :id");
            $data['id'] = $id;
            $stmt->execute($data);
            
            logSistema($pdo, 'Launcher', 'Update Module', 'launcher_modules', $id, $data);
        }
    } elseif ($action === 'delete' && $id) {
        $stmt = $pdo->prepare("DELETE FROM launcher_modules WHERE id = ?");
        $stmt->execute([$id]);
        
        logSistema($pdo, 'Launcher', 'Delete Module', 'launcher_modules', $id);
    }

    header("Location: manage_launcher.php?msg=success");
    exit;

} catch (PDOException $e) {
    die("Erro ao processar módulo: " . $e->getMessage());
}
?>
