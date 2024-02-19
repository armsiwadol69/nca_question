<?php
session_start();
session_destroy();

header('location: ../view/v_login.php');
?>