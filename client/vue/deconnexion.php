<?php
session_start();

if (isset($_SESSION['id_utilisateur'])) {
    $_SESSION = array();
    session_unset();
    session_destroy();
}

header("Location: login.php");
exit();
?>
