<?php 
session_start();
require('core/function.php');
// on verifie si la personne est deja connécté
if($_SESSION['connect'] == 1 && (!empty($_COOKIE['login']) || !empty($_COOKIE['password'])))  
{
    header('location:prive.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <?php include('inc/header.php'); ?>
    <form name="inscription" method="POST" action="action.php?e=inscription">
        <label for="login">Login:</label>
        <input type="text" name="login" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <br>
        <label for="password2">Répéter password:</label>
        <input type="password2" name="password2" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <br>
        <button type="submit" name="submit">Inscription</button>
    </form>
    <?php include('inc/footer.php'); ?>

</body>
</html>