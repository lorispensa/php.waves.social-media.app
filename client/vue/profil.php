<?php
require_once "../controler/fonction.php";

if (!isset($_SESSION['id_utilisateur'])) {
  header("Location: login.php");
  exit();
}


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
  $publications = getAllUserPublications($bdd, $userId);
  $totalPosts = countUserPosts($bdd, $userId);
  $suivi = countUserFollowing($bdd, $userId);
  $follower = countUserFollowers($bdd, $userId);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["publicationId"])) {
  $publicationId = $_POST["publicationId"];
  $userId = $_SESSION["id_utilisateur"]; 
  $result = likePublication($bdd, $publicationId, $userId);
} 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["publicationId"]) && isset($_POST["commentaire"])) {
  $publicationId = $_POST["publicationId"];
  $userId = $_SESSION["id_utilisateur"];
  $commentaire = $_POST["commentaire"];
  $result = CommentPublication($bdd, $publicationId, $userId, $commentaire);

  if ($result) {
    echo json_encode(array("success" => true));
  } else {
    echo json_encode(array("error" => "Une erreur s'est produite lors de l'ajout du commentaire."));
  }
}
?>  
<?php include "header.php"; ?>
<div class="profile-container">
  <div class="profile-header">
    <div class="profile-photo">
      <?php if (!empty($utilisateur['photo_profil'])): ?>
        <img src="<?php echo htmlspecialchars($utilisateur['photo_profil']); ?>" alt="Image de profil">
      <?php endif; ?>
    </div>
    <div class="profile-info">
      <h2><?php echo htmlspecialchars($nom . ' ' . $prenom); ?> <i class='bx bxs-certification'></i></h2>
      <div class="profile-stats">
        <span><strong><?php echo $totalPosts; ?></strong> publications</span>
        <span><strong><?php echo $follower; ?></strong> followers</span>
        <span><strong><?php echo $suivi; ?></strong> suivi(e)s</span>
      </div>
      <div class="profile-actions">
        <a href="edit_profil.php"><button type="button" class="edit-profile-btn">Modifier le profil</button></a>
        <a href="deconnexion.php"><button type="button" class="edit-profile-btn">Déconnexion</button></a>
      </div>
      <div class="profile-bio">
        <p><?php echo htmlspecialchars($bio); ?></p>
        <p>Date de naissance : <?php echo htmlspecialchars($datenaissance); ?></p>
        <p>Wave Localisation : <?php echo htmlspecialchars($pays .' ' .$ville); ?></p>
      </div>
    </div>
  </div>
  <div class="profile-stories">
    <div class="story new-story">
      <span>+</span>
    </div>
    <span class="story-text">Ajouter un souvenir</span>
  </div>
  <div class="profile-tabs">
    <ul>
      <li class="tab"><i class='bx bx-grid-alt'></i> MES PUBLICATIONS PERSONNELLES</li>
    </ul>
  </div>
  <div class="publi">
    <div class="gallery">
      <?php
      foreach ($publications as $publication) {
        $likesCount = getLikesCountForPublication($bdd, $publication['id']);
        $CommentCount = getCommentCountForPublication($bdd, $publication['id']);
        $isLiked = isPublicationLiked($bdd, $publication['id'], $userId);
        $isCommented = isPublicationCommented($bdd, $publication['id'], $userId);
        $likeClass = $isCommented ? 'commented' : '';
        $CommentClass = $isLiked ? 'liked' : '';
        echo '<div class="gallery-item" tabindex="0">';
        echo '<img src="' . htmlspecialchars($publication['image_article']) . '" class="gallery-image" alt="">';
          echo '<div class="gallery-item-info">';
            echo '<ul>';
              echo '<li class="gallery-item-likes"><span class="visually-hidden">Likes:</span><i class="bx bxs-heart" aria-hidden="true"></i> ' . $likesCount . '</li>';
              echo '<li class="gallery-item-comments"><span class="visually-hidden">Comments:</span><i class="bx bxs-message-rounded" aria-hidden="true"></i> ' . $CommentCount . '</li>';
            echo '</ul>';
          echo '</div>';
        echo '</div>';
      }
      ?>
    </div>
  </div>
</div>
<div id="image-popup" class="image-popup">
  <img src="" alt="" id="popup-image" class="popup-image">
  <span id="close-popup" class="close-popup">&times;</span>
</div>

<script>
  $(document).ready(function() {
    $(".gallery-item").click(function() {
      var imgSrc = $(this).find("img").attr("src");
      $("#popup-image").attr("src", imgSrc);
      $("#image-popup").fadeIn();
    });

    $("#close-popup").click(function() {
      $("#image-popup").fadeOut();
    });
  });
</script>
</body>
</html>
