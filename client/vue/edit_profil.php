<?php
    require_once "../controler/fonction.php";

    $userId = '';
    $nom = '';
    $prenom = '';
    $datenaissance = '';
    $bio = '';
    $type_compte = '';
    $ville = '';
    $pays = '';
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

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        updateProfile($bdd, $bio, $pays, $ville, $datenaissance, $genre, $userId);
        uploadProfilePhoto($bdd, $userId);
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
        <h2>Modifier le profil</h2>
        </div>
        <form class="edit-profile-form" method="post" enctype="multipart/form-data">
        <div class="profile-picture-section">
            <?php if (!empty($utilisateur['photo_profil'])): ?>
                <img src="<?php echo htmlspecialchars($utilisateur['photo_profil']); ?>" class="profile-picture" alt="Image de profil">
            <?php endif; ?>
            <div class="profile-name-button-section">
                <strong class="profile-name"><?php echo htmlspecialchars($nom) . " " . htmlspecialchars($prenom); ?></strong>
                <button type="button" class="change-picture-btn" onclick="document.getElementById('fileToUpload').click();">Modifier la photo</button>
                <input type="file" name="fileToUpload" id="fileToUpload" display="none">
            </div>
        </div>
        <div class="input-conteneur mdp">
            <label for ="bio">Biographie</label>
            <input id="bio" name="bio" placeholder="<?php echo htmlspecialchars($bio); ?>" type="text">
        </div>
        <div class="input-conteneur mdp">
            <label for ="pays">Pays</label>
            <input id="pays" name="pays" placeholder="<?php echo htmlspecialchars($pays); ?>" type="text">
        </div>
        <div class="input-conteneur mdp">
            <label for ="ville">Ville</label>
            <input id="ville" name="ville" placeholder="<?php echo htmlspecialchars($ville); ?>" type="text">
        </div>
        <div class="input-conteneur nom">
            <label for="date">Date de naissance</label>
            <input id="date" name="date_de_naissance" type="date" >
        </div>
        <div class="input-conteneur nom">
            <label for="genre">Genre sexuel</label>
            <select name="genre" >
                <option value="" disabled selected>à choisir</option>
                <option value="Femme">Femme</option>
                <option value="Homme">Homme</option>
                <option value="Autre">Autre</option>
            </select>
        </div> 
        <div class="submit-section">
            <button type="submit" value="Télécharger Image" class="change-picture-btn">Sauvegarder la modification</button>
        </div>
        </form>
    </div>
    </div>
</body>
</html>
