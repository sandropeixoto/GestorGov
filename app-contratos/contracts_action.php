<?php
// app-contratos/contracts_action.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: contratos.php");
    exit;
}

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;

try {
    if ($action === 'create' || $action === 'update') {
        $data = [
            'Objeto' => $_POST['Objeto'],
            'VigenciaInicio' => $_POST['VigenciaInicio'],
            'VigenciaFim' => $_POST['VigenciaFim'],
            'DataAssinatura' => $_POST['DataAssinatura'],
            'NumeroContrato' => $_POST['NumeroContrato'] ?: null,
            'SeqContrato' => $_POST['SeqContrato'],
            'AnoContrato' => $_POST['AnoContrato'],
            'PaiId' => $_POST['PaiId'] ?? 0,
            'FiscalContrato' => $_POST['FiscalContrato'] ?? null,
            'EmailFiscal' => $_POST['EmailFiscal'] ?? null,
            'FiscalSubstituto' => $_POST['FiscalSubstituto'] ?? null,
            'EmailFiscalSubstituto' => $_POST['EmailFiscalSubstituto'] ?? null,
            'PrestadorId' => $_POST['PrestadorId'],
            'ValorMensalContrato' => $_POST['ValorMensalContrato'] ?: null,
            'ValorGlobalContrato' => $_POST['ValorGlobalContrato'],
            'NProcesso' => $_POST['NProcesso'] ?? null,
            'ModalidadeId' => $_POST['ModalidadeId'] ?: null,
            'NumeroModalidade' => $_POST['NumeroModalidade'] ?? null,
            'DiretoriaId' => $_POST['DiretoriaId'] ?: null,
            'CategoriaContratoId' => $_POST['CategoriaContratoId'] ?: null,
            'FonteRecursosId' => $_POST['FonteRecursosId'] ?: null
        ];

        if ($action === 'create') {
            $cols = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $sql = "INSERT INTO Contratos ($cols) VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
        } else {
            $sets = [];
            foreach ($data as $key => $val) {
                $sets[] = "$key = :$key";
            }
            $sql = "UPDATE Contratos SET " . implode(", ", $sets) . " WHERE Id = :id";
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
        }
        
        header("Location: contratos.php?msg=success");
        exit;

    } elseif ($action === 'delete' && $id) {
        $stmt = $pdo->prepare("DELETE FROM Contratos WHERE Id = ?");
        $stmt->execute([$id]);
        header("Location: contratos.php?msg=deleted");
        exit;
    }

} catch (PDOException $e) {
    die("Erro ao processar ação: " . $e->getMessage());
}

header("Location: contratos.php");
exit;
?>
