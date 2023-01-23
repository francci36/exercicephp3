<?php 
session_start();
require('core/function.php');
// on verifie si la personne est deja connécté
if($_SESSION['connect'] == 1 && (!empty($_COOKIE['login']) || !empty($_COOKIE['password']))) /*Conditional statement in PHP that checks if the following conditions are true:
The value of the "connect" key in the $_SESSION superglobal variable is equal to 1. This variable is likely used to check if the user is logged in.Either the "login" key or the "password" key in the $_COOKIE superglobal variable is not empty.
If both conditions are true, then the code inside the if statement will execute. This check is likely used to confirm that the user is logged in and that the login and password cookies are set.
It is important to note that this type of check is not a secure way to check if the user is authenticated as cookies can be tampered with and sessions can be hijacked. It is recommended to use a more secure method such as using secure tokens and checking them against a database or using a secure session management library.*/ 
{
    // si c'est le cas redirection vers la page prive.php
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
    <title>Document</title>
</head>
<body>
    <?php include('inc/header.php'); ?>
    <h1>Connectez vous à l'espace membres</h1>

    <?php
        if(isset($_GET['message']))
        {
            $message = urldecode($_GET['message']);
            echo '<div style="background:red;width:100%;border-radius:30px;color:white;">'.$message.'</div>';
        }
    ?><!--This PHP code checks if a GET parameter named 'message' is set. If it is, it decodes the message, and then echoes it out as a red colored div with a white text and rounded corners. The width of the div is set to be 100% of the parent container and it will show the message passed in the querystring.-->

    <form name="membres" action="action.php?e=connexion" method="post">
        <label for="login">login:</label>
        <input type="text" name="login" />
        <br />
        <label for="password">Password:</label>
        <input type="password" name="password" />
        <br />
        <button type="submit" name="submit">Se connecter</button>
    </form>
</body>
</html>