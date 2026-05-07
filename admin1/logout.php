<?php
setcookie("uuid", "", time() - 3600, "/");
header("Location:login.php");
?>