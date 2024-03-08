<?php

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>&copy; Waves</title>
<link rel="stylesheet" href="css/profile.css">
<link rel="stylesheet" href="css/footer.css">
<link rel="stylesheet" href="css/login.css">
<link rel="stylesheet" href="css/grid.css">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
          <img src="images/logo.png" alt="Profile Picture" class="sidebar-profile-pic">
        </div>
        <nav class="navigation-menu">
          <ul>
            <li><a href="main.php"><i class='bx bx-home-alt-2' ></i> Accueil</a></li>
            <li><a href="#search"><i class='bx bx-search' ></i> Recherche</a></li>
            <li><a href="#discover"><i class='bx bx-compass' ></i> Découvrir</a></li>
            <li><a href="#messages"><i class='bx bx-send' ></i> Messages</a></li>
            <li><a href="posting.php"><i class='bx bx-message-square-add' ></i> Publier</a></li>
            <li><a href="#notifications"><i class='bx bx-bell' ></i> Notifications</a></li>
            <li><a href="#notifications"><i class='bx bx-cog'></i> Paramètre</a></li>
            <li><a href="profil.php" type='solid'><img src="<?php echo htmlspecialchars($utilisateur['photo_profil']); ?>" alt="Image de profil"> Profil</a></li>
            <?php
            echo "<li><a href='admin'>></i> Administrateur</a></li>";
            ?>
          </ul>
        </nav>
    </div>