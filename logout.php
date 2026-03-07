<?php
// logout.php na raiz
session_start();
session_destroy();
setcookie('gestorgov_session', '', time() - 3600, "/");
header("Location: index.php");
exit;
?>
