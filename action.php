<?php 
session_start();
require('core/function.php');
$db = pdo_connect();/*pour relié la function a la BDD*/ 
switch($_GET['e'])
{
    case 'inscription':
        // verif le'ensemble des champs saisie
        if(isset($_POST['submit']))
        {
            if(!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['password2']) && !empty($_POST['email']))
            { // verif si les mdp sont identiques
                if($_POST['password'] == $_POST['password2'])
                {
                    $verif_login = $db->prepare('SELECT User_ID FROM `table_user` WHERE User_Login = :login OR User_Email = :email');
                    $verif_login->bindParam(':login',$_POST['login'],PDO::PARAM_STR);// PARAM_STR = chaine de characteres sinon marche pas
                    $verif_login->bindParam(':email',$_POST['email'],PDO::PARAM_STR);
                    $verif_login->execute();
                    // on verif si un utilisateur est retourné est si il existe deja 
                    if($verif_login->rowCount() == 0)
                    { // cryptage mdp (a voir autres façon password_hash sur php.net)
                        $password = sha1(md5($_POST['password']));
                        $user = $db->prepare('INSERT INTO `table_user` SET
                                                User_Email = :email,
                                                User_Login = :login,
                                                User_password = :password,
                                                User_Date = CURDATE()
                                            ');
                        $user->bindValue(':email',$_POST['email'],PDO::PARAM_STR);
                        $user->bindValue(':login',$_POST['login'],PDO::PARAM_STR);
                        $user->bindValue(':password',$password,PDO::PARAM_STR);
                        if($user->execute())
                        {
                            // recupere l'id de l'user et recup la clé primaire 
                            $id_user = $db->lastInsertId();
                            // on crée son repertoire
                            if(!is_dir('upload/'.$id_user))
                            {
                                mkdir('upload/'.$id_user);
                            }
                            setcookie('id_user', $id_user,(time()+3600));
                            setcookie('pass_user',$password,(time()+3600));
                            $_SESSION['connect'] = 1;
                            // on redirige l'user vers sa page privée 
                            header('location:prive.php');
                            exit;
                        } 
                        else
                        {
                            // si il y a une erreur avec avec la requete 
                            $message = 'Une erreur SQL est survenue';
                        }
                    }
                    else
                    {
                        // si l'user existe deja
                        $message = 'login ou email deja enregistré';
                    }
                }
                else
                {
                    // si les 2 mdp ne sont pas identiques
                    $message = 'les 2 mdp ne correspondent pas!!!!';
                }
            }
        }
        header('location:inscription.php?message='.urlencode($message));

        break;

    case 'connexion':
        if(isset($_POST['submit']))
        {
            if(!empty($_POST['login']) && !empty($_POST['password']))
            {
                $verif_connect = $db->prepare('SELECT User_ID, User_Password FROM `table_user` WHERE User_Login = :login AND User_Password = :password');
                $verif_connect->bindParam(':login',$_POST['login'],PDO::PARAM_STR);
                $verif_connect->bindParam(':password',sha1(md5($_POST['password'])),PDO::PARAM_STR);
                $verif_connect->execute();
                if($verif_connect->rowCount() == 1)
                {
                    $user = $verif_connect->FETCH(PDO::FETCH_OBJ);
                    setcookie('id_user',$user->User_ID,(time()+3600));// fonctionne avec SELECT User_ID, User_Password
                    setcookie('pass_user',$user->User_Password,(time()+3600));// fonctionne avec SELECT User_ID, User_Password
                    $_SESSION['connect'] = 1;
                    header('location:prive.php');
                    exit;
                }
            }
            else
            {
                $message = 'Veuillez renseigner un login et mot de passe';
            }
            header('location:membres.php?message='.urlencode($message));
            exit;
        } 

    break;

    case 'deco':

        $_SESSION['connect'] = 0;
        setcookie('id_user',null,(time()-10));
        setcookie('pass_user',null,(time()-10));
        header('location:membres.php');

        break;/*This is a case statement in the switch block that checks if the value of the "e" GET parameter is "deco". If the case statement is entered, it sets the "connect" key in the $_SESSION superglobal variable to 0, sets the "login" and "password" cookies to null and the time to a negative value, this effectively deletes the cookies.
        Then it redirects the user to the "membres.php" page.
        This code is likely used as a logout function, it's important to note that it only sets the session and cookies to null but it doesn't destroy the session or unset the variables, this could make the session vulnerable to session hijacking.
        It's also important to validate the user is actually logged in before allowing them to log out and properly destroy the session and cookies.
        It's also important to note that the time value of -10 is not a recommended way of deleting cookies as it may not work in all browsers, it's recommended to use the setcookie function with an expired date in the past or use the unset() function.*/ 

    case 'upload':
        $user = verifUser();
        if($user){
            
            if(isset($_POST['submit']))
            {
                $uploads = uploadFichiers();
                header('location:prive.php?message='.serialize($uploads));
                exit;
            /* echo '<pre>'; // Correction
                print_r($_FILES['fichier']);
                echo '</pre>';
                exit;*/
                $i=0;
                $message = array();
                // on fait une boucle pour parcourir les fichiers
                foreach($_FILES['fichier'] as $fichier)
                //for($i=0;$i<=count($_FILES['fichier']);$i++)
                {
                    // verif si fichier envoyé
                    if(is_uploaded_file($_FILES['fichier']['tmp_name'][$i]))
                    {
                        $etat_fichier = uploadFichier($_FILES['fichier'][$i]);
                        if($etat_fichier)
                        {
                            // on stock le message dans la variable tableau
                            $message[$i] = 'Fichier '.$_FILES['fichier']['name'][$i].'envoyé'; /*affiche un message pour indiquer si l'envoye c bien passé pour chaque fichier*/ 
                        }
                        else
                        {
                            $message[$i] = 'Erreur avec le fichier '.$_FILES['fichier']['name'][$i];
                        }
                    }
                    // on incremente $i
                }
            }
            header('location:prive.php?message='.serialize($message));

            // verif si un fichier a été envoyer
            if(is_uploaded_file($_FILES['fichier']['tmp_name']) || is_uploaded_file($_FILES['fichier2']['tmp_name']))
            {
                $etat_fichier = uploadFichier($_FILES['fichier']);
                $etat_fichier2 = uploadFichier($_FILES['fichier2']);
                if($etat_fichier && $etat_fichier2)
                {
                    $message = 'Fichiers envoyés avec succès';
                    header('location:prive.php?message='.urlencode($message));
                }
            }
            else if(is_uploaded_file($_FILES['fichier']['tmp_name']) && !is_uploaded_file($_FILES['fichier2']['tmp_name']))
                {
                    $etat_fichier = uploadFichier($_FILES['fichier']);
                    if($etat_fichier)
                    {
                        $message = 'Le fichier a été envoyé avec succès';
                        header('location:prive.php?message='.urlencode($message));
                    }
                }
            else if(!is_uploaded_file($_FILES['fichier']['tmp_name']) && is_uploaded_file($_FILES['fichier2']['tmp_name']))
            {
                $etat_fichier = uploadFichier($_FILES['fichier2']);
                if($etat_fichier2)
                {
                    $message = 'Le fichier a été envoyé avec succès';
                    header('location:prive.php?message='.urlencode($message));
                }
            }
            $message = 'Il y a un problème avec l\'envoi des fichiers';
            header('location:prive.php?message='.urlencode($message));
        }
        break;/*This is a case statement in the switch block that checks if the value of the "e" GET parameter is "upload". If the case statement is entered, it checks if the "submit" POST parameter is set. If the "submit" POST parameter is set, it then checks if the "fichier" and "fichier2" files have been uploaded using the PHP built-in function is_uploaded_file().
        If both files have been uploaded, it calls a function named "uploadFichier" with the "fichier" and "fichier2" as arguments.
        Then it checks if both the returned values of the function are true, if so, it sets the message variable to "Fichiers envoyés avec succès" and redirects the user to the "prive.php" page with the message variable as a GET parameter.
        If only the first file has been uploaded, it calls the function "uploadFichier" with the "fichier" as argument, then it checks if the returned value of the function is true, if so, it sets the message variable to "Le fichier a été envoyé avec succès" and redirects the user to the "prive.php" page with the message variable as a GET parameter.
        If only the second file has been uploaded, it calls the function "uploadFichier" with the "fichier2" as argument, then it checks if the returned value of the function is true, if so, it sets the message variable to "Le fichier a été envoyé avec succès" and redirects the user to the "prive.php" page with the message variable as a GET parameter.
        If none of the files have been uploaded, it sets the message variable to "Il y a un problème avec l'envoi des fichiers" and redirects the user to the "prive.php" page with the message variable as a GET parameter.
        This code is likely used to handle file uploads, it's important to note that it does not check for the file type, size and extension before uploading, it's recommended to do this validation before uploading the files to prevent any security issues such as file injection or buffer overflow. It's also important to check if the user is authenticated before allowing them to upload files and to use a secure way to store the files on the server.*/ 

    case 'deletefichier':
            if(!empty($_GET['fichier']))
            {
                unlink('upload/'.$_COOKIE['login'].'/'.$_GET['fichier']);
                header('location:prive.php');
                exit;
            }
        break;/*This is a case statement in the switch block that checks if the value of the "e" GET parameter is "deletefichier". If the case statement is entered, it checks if the "fichier" GET parameter is not empty.
        If the "fichier" GET parameter is not empty, it uses the PHP built-in function unlink() to delete the file from the "upload" directory followed by the user's login name, which is retrieved from the cookie, and the file name is passed as a parameter to the function. It then redirects the user to the "prive.php" page.
        This code is likely used to handle file deletion, it's important to note that it does not check for the authenticity of the user and therefore could be vulnerable to malicious users manipulating the cookies or the directory path. It is also important to validate the user's permission to delete the files, and the type of file before deleting it. It's also important to check if the user is authenticated before allowing them to delete files.*/

        case 'download':


            if(!empty($_GET['id']))
            {
                // On prepare la requete Update
                $req = 'UPDATE Table_File SET File_Download = File_Download+1, File_Date_Download = CURDATE() WHERE File_ID = '.intval($_GET['id']);
                // On execute la requete Update
                $db->query($req);
                // On prépare le requete pour recupérer les infos sur le fichier 
                $fichier = 'SELECT * FROM Table_File WHERE File_ID = '.intval($_GET['id']);
                // On execute la requete de récupération d'infos sur le fichier et on la range dans la variable $execution
                $execution = $db->query($fichier);
                // On compte le nombre de ligne retourné par la requête
                $nb_ligne = $execution->rowCount();
                if($nb_ligne == 1)
                {
                    // Si on a 1 ligne retournée on créer l'objet avec les éléments du fichier 
                    $info = $execution->fetch(PDO::FETCH_OBJ);
                    // On prépare le header avec le renommage du fichier au bon format
                    header('Content-Disposition: attachement; filename="'.$info->File_Original_Name.'"');
                    // On lit le fichier sur le serveur
                    readFile('upload/'.$info->File_User_ID.'/'.$info->File_Name);
                }
            }
        break;
        

        case 'contact':
            // cest ici qu'on doit voir le code
            // vérifier si les champs du formulaire ont bien été rempli
            // enregistrer dans la table 
            //si le formuliare été soumis
            if(isset($_POST['submit']))
        {
            // verifier si tous les chaps sont rempli
            if(!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['sujet']) && !empty($_POST['message']))
            {
                if($_POST['captcha'] == $_SESSION['captcha'])
                {
                    $captcha2 = unserialize($_SESSION['captcha2']);
                        if(in_array($_POST['captcha2'],$captcha2))
                    {
               
                // pour faire la connexion a la base de données 
                $contact = $db->prepare('INSERT INTO table_contact SET
                                                    Contact_Prenom = :prenom,
                                                    Contact_Nom = :nom,
                                                    Contact_Email = :email,
                                                    Contact_Sujet = :sujet,
                                                    Contact_Message = :message,
                                                    Contact_Date = CURDATE()
                                    '); 
                                    // attribut les valeurs postées par le formulaire dans les champs de la base
                                    $contact->bindValue(':nom',$_POST['nom'],PDO::PARAM_STR);
                                    $contact->bindValue(':prenom',$_POST['prenom'],PDO::PARAM_STR);
                                    $contact->bindValue(':email',$_POST['email'],PDO::PARAM_STR);
                                    $contact->bindValue(':sujet',$_POST['sujet'],PDO::PARAM_STR);
                                    $contact->bindValue(':message',$_POST['message'],PDO::PARAM_STR);
                                    // lancer l'envoir vers la base 
                                    $contact->execute();
                    
                $message = 'formulaire envoyé !!';
                    
            }
            else
            {
                $message = 'Tu n\'as aucune culture générale';
            }


            }
            else
            {

                $message = 'erreur de captcha !!';
            }
            // pour afficher les message dans l'url
            header('location:contact.php?message='.urlencode($message));
        }
        
        break;
    }

            
}
?>
