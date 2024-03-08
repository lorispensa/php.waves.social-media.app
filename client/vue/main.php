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
    $publications = getAllUserPublications($bdd, $userId);
    $totalPosts = countUserPosts($bdd, $userId);
    $nonFriends = getNonFriends($bdd, $userId);    
    $publicationsSuivies = getFollowingPublications($bdd, $userId);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_utilisateur"])) {
    $idUtilisateur = $_POST["id_utilisateur"];
    $idUtilisateurConnecte = $_SESSION['id_utilisateur'];
    if (!sontAmis($bdd, $idUtilisateurConnecte, $idUtilisateur)) {
        addFriend($bdd, $idUtilisateurConnecte, $idUtilisateur);
    } 
}

?>
<?php include "header.php"; ?>
<div class="profile-container">
<div class="content-wrapper">
    <div class="publications-container">
        <?php foreach ($publicationsSuivies as $publication): ?>
            <div class="publication">
                <div class="publication-header">
                    <span><?php echo htmlspecialchars($publication['nom']) . ' ' . htmlspecialchars($publication['prenom']); ?></span>
                </div>
                <img src="<?php echo htmlspecialchars($publication['image_article']); ?>" alt="Image de l'article">
                <p><?php echo htmlspecialchars($publication['texte_article']); ?></p>
                <div class="publication-footer">
                    <span><i class='bx bxs-heart'></i> <?php echo htmlspecialchars($publication['nombre_likes']); ?></span>
                    <span><i class='bx bxs-message-rounded'></i> <?php echo htmlspecialchars($publication['nombre_commentaires']); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="suggestions-container">
        <h3>Suggestions d'amis</h3>
        <?php foreach ($nonFriends as $user): ?>
            <form method="post" class="suggestion-item">
            <img src="<?php echo htmlspecialchars($user['photo_profil']); ?>" alt="Profil" class="user-profile-image">
                <span><?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']); ?></span>
                <input type="hidden" name="id_utilisateur" value="<?php echo htmlspecialchars($user['id_utilisateur']); ?>">
                <button type="submit">Devenir Abonner</button>
            </form>
        <?php endforeach; ?>
    </div>
</div>

</div>
</body>
</html>
</div>
</body>
</html>
