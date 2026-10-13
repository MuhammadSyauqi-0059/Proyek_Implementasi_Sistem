<?php
session_start();
session_destroy();
header('Location: /kopi-pekalongan/admin/login.php');
exit;
?>