<?php
// app-contratos/settings_action.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: settings.php");
    exit;
}

$action = $_POST['action'] ?? '';
$tab = $_POST['tab'] ?? '';
$pk_value = $_POST['pk_value'] ?? null;

// Re-mapear configurações para processamento
$tables = [
    'diretorias' => ['table' => 'Diretorias', 'pk' => 'IdDiretoria', 'fields' => ['SiglaDiretoria', 'NomeDiretoria']],
    'fontes' => ['table' => 'FontesRecursos', 'pk' => 'IdFonte', 'fields' => ['NomeFonte']],
    'categorias' => ['table' => 'CategoriaContrato', 'pk' => 'Id', 'fields' => ['Codigo', 'Descricao']],
    'modalidades' => ['table' => 'Modalidade', 'pk' => 'Id', 'fields' => ['Codigo', 'Descricao']]
];

if (!isset($tables[$tab])) {
    header("Location: settings.php");
    exit;
}

$current = $tables[$tab];
$table = $current['table'];
$pk = $current['pk'];

try {
    if ($action === 'create' || $action === 'update') {
        $data = [];
        foreach ($current['fields'] as $field) {
            $data[$field] = $_POST[$field] ?? null;
        }

        if ($action === 'create') {
            $cols = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $stmt = $pdo->prepare("INSERT INTO $table ($cols) VALUES ($placeholders)");
            $stmt->execute($data);
        } else {
            $sets = [];
            foreach ($data as $key => $val) {
                $sets[] = "$key = :$key";
            }
            $stmt = $pdo->prepare("UPDATE $table SET " . implode(", ", $sets) . " WHERE $pk = :pk_value");
            $data['pk_value'] = $pk_value;
            $stmt->execute($data);
        }
    } elseif ($action === 'delete' && $pk_value) {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE $pk = ?");
        $stmt->execute([$pk_value]);
    }

    header("Location: settings.php?tab=$tab&msg=success");
    exit;

} catch (PDOException $e) {
    die("Erro ao processar alteração: " . $e->getMessage());
}
?>
