<?php
session_start();

// Rediriger si déjà connecté
if(!isset($_SESSION['id'])) {
    header('Location: ../connexion.php');
    exit;
}