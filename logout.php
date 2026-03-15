<?php
// logout.php na raiz
require_once 'config.php';
session_start();
logSistema($pdo, 'Portal', 'Logout');
session_destroy();
setcookie('gestorgov_session', '', time() - 3600, "/");
header("Location: index.php");
exit;
?>
