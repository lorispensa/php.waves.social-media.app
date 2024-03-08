<?php
    require_once "../controler/fonction.php";

    $userId = '';
    $nom = '';
    $prenom = '';
    $datenaissance = '';
    $bio = '';
    $type_compte = '';
    $erreur = false;
    $messageErreur = "";

    if (isset($_SESSION['id_utilisateur'])) {
      $userId = $_SESSION['id_utilisateur'];
      $userId = 10;
      $utilisateur = getUserInfo($bdd, $userId);
      $nom = $utilisateur['nom'];
      $prenom = $utilisateur['prenom'];
      $pays = $utilisateur['pays'];
      $ville = $utilisateur['ville'];
      $datenaissance = $utilisateur['date_de_naissance'];
      $genre = $utilisateur['genre'];
      $bio = $utilisateur['bio'];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userId = $_SESSION['id_utilisateur']; 
        $postImage = $_FILES["postImage"]["name"]; 
        $location = $_POST["Lieux"]; 
        $comment = $_POST["comment"];
        uploadPostImage($bdd, $userId, $postImage, $location, $comment);
    }

?> 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>&copy; Waves</title>
<link rel="stylesheet" href="css/profile.css">
<link rel="stylesheet" href="css/login.css">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="edit-profile">
        <div class="edit-profile-header">
        <h2>Ajouter une publication</h2>
        </div>
        <form enctype="multipart/form-data"  method="post">
            <div class="input-conteneur mdp">
                <label for ="publication">Choisir votre publication</label>
                <input id="publication" type="file" name="postImage" accept=".png, .jpg, .jpeg">
            </div>
            <div class="input-conteneur mdp">
                <label for ="Lieux">Lieu de l'image</label>
                <input id="Lieux" name="Lieux" placeholder="" type="text">
            </div>
            <div class="input-conteneur nom">
                <label for="comment">Commentaire de la publication</label>
                <input id="comment" name="comment" type="text">
            </div>
            <div class="submit-section">
                <button type="submit" value="Publier" class="change-picture-btn">Publier ma publication</button>
            </div>
        </form>
    </div>
    </div>
</body>
</html>
