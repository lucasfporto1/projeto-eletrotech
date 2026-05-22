<?php
require_once './../lib-php/src/checkSession.php';
session_start();

session_unset();

session_destroy();

setcookie(session_name(), '', time() - 3600, '/');

header("Location: ../view/telas/loginView.php");
