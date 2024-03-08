<?php 

require_once 'config.php';


$bdd = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function showPage($page,$data=""){
    include("assets/pages/$page.php");
}

// MET A JOUR LE PROFIL : EDIT_PROFIL   
function updateProfile($bdd, $bio, $pays, $ville, $datenaissance, $genre, $userId) {
    try {
        $stmt = $bdd->prepare("UPDATE Utilisateurs SET bio = :bio, pays = :pays, ville = :ville, date_de_naissance = :datenaissance, genre = :genre WHERE id_utilisateur = :id_utilisateur");
        $stmt->bindParam(":bio", $bio);
        $stmt->bindParam(":pays", $pays);
        $stmt->bindParam(":ville", $ville);
        $stmt->bindParam(":datenaissance", $datenaissance);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":id_utilisateur", $userId);
        $stmt->execute();

        echo "Modifications enregistrées avec succès!";
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}

// ENVOI LA PHOTO DANS LE SERVER FILEZILLA : LINSERV
function uploadProfilePhoto($bdd, $userId) {
    $target_dir = "uploads/";
    $original_file_name = basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . $original_file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        echo "Fichier n'est pas une image.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        $file_extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid() . "." . $file_extension; 
        $target_file = $target_dir . $new_file_name;
    }

    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Désolé, votre fichier est trop volumineux.";
        $uploadOk = 0;
    }

    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        echo "Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "Le fichier ". htmlspecialchars($original_file_name). " a été téléchargé.";
            $stmt = $bdd->prepare("UPDATE Utilisateurs SET photo_profil = :photo_profil WHERE id_utilisateur = :id_utilisateur");
            $stmt->bindParam(":photo_profil", $target_file);
            $stmt->bindParam(":id_utilisateur", $userId);
            $stmt->execute();
        } else {
            echo "Désolé, il y a eu une erreur lors du téléchargement de votre fichier.";
        }
    }
}

function uploadPostImage($bdd, $userId, $postImage, $location, $comment) {
    $target_dir = "uploads/post/"; 
    $original_file_name = basename($postImage);
    $target_file = $target_dir . $original_file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["postImage"]["tmp_name"]);
    if ($check === false) {
        echo "Le fichier n'est pas une image.";
        $uploadOk = 0;
    }

    if ($_FILES["postImage"]["size"] > 5000000) { 
        echo "Désolé, votre fichier est trop volumineux.";
        $uploadOk = 0;
    }

    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        echo "Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["postImage"]["tmp_name"], $target_file)) {
            echo "Le fichier " . htmlspecialchars($original_file_name) . " a été téléchargé.";

            $stmt = $bdd->prepare("INSERT INTO post (id_utilisateur, image_article, texte_article, lieu, date_creation) VALUES (:id_utilisateur, :image_article, :texte_article, :lieu, current_timestamp())");
            $stmt->bindParam(":id_utilisateur", $userId);
            $stmt->bindParam(":image_article", $target_file);
            $stmt->bindParam(":texte_article", $comment);
            $stmt->bindParam(":lieu", $location);
            $stmt->execute();

            echo "Publication ajoutée avec succès.";
        } else {
            echo "Désolé, il y a eu une erreur lors du téléchargement de votre fichier.";
        }
    }
}

function getAllUserPublications($bdd, $userId) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM post WHERE id_utilisateur = :userId ORDER BY date_creation DESC");
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        return array();
    }
}

// RECUPERE LES INFO DEPUIS PHPMYADMIN 
function getUserInfo($bdd, $userId) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM Utilisateurs WHERE id_utilisateur = :id_utilisateur");
        $stmt->bindParam(":id_utilisateur", $userId);
        $stmt->execute();
        $utilisateur = $stmt->fetch();
        $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];

        return $utilisateur;
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        return null;
    }
}

// INSCRIPTION : SIGNUP
function registerUser($bdd, $nom, $prenom, $adr_mail, $mdp, $datenaissance, $genre, $ville, $pays, $bio, $photo_profil, $est_admin, $type_compte) {
    try {
        $stmt = $bdd->prepare("INSERT INTO Utilisateurs (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_de_naissance`, `genre`, `ville`, `pays`, `bio`, `photo_profil`, `est_admin`, `type_compte`, `date_inscription`, `confidentialite`)
        VALUES (:nom, :prenom, :email, :mot_de_passe, :date_de_naissance, :genre, :ville, :pays, :bio, 'uploads/default.png', '0', 'Particulier',current_timestamp(),'public')");
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":prenom", $prenom);
        $stmt->bindParam(":email", $adr_mail);
        $stmt->bindParam(":mot_de_passe", $mdp);
        $stmt->bindParam(":date_de_naissance", $datenaissance);
        $stmt->bindParam(":genre", $genre);
        $stmt->bindParam(":ville", $ville);
        $stmt->bindParam(":pays", $pays);
        $stmt->bindParam(":bio", $bio);
        $stmt->bindParam(":photo_profil", $photo_profil);
        $stmt->bindParam(":est_admin", $est_admin, PDO::PARAM_BOOL);
        $stmt->bindParam(":type_compte", $type_compte);
        $stmt->execute();

        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// SE CONNECTER -> LOGIN
function loginUser($bdd, $email, $mdp) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM Utilisateurs WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $utilisateur = $stmt->fetch();

        if ($utilisateur && $mdp === $utilisateur['mot_de_passe']) {
            session_start();
            $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
            $_SESSION['nom'] = $utilisateur['nom'];
            $_SESSION['prenom'] = $utilisateur['prenom'];
            $_SESSION['email'] = $utilisateur['email'];
            $_SESSION['logged_in'] = false;
            $_SESSION['est_admin'] = $utilisateur['est_admin'];
            
                header("Location: profil.php");
            exit();
        } else {
            return "Adresse e-mail ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        return "Erreur : " . $e->getMessage();
    }
}

function like($post_id, $current_user_id){
    global $bdd;
    $query = "INSERT INTO likes (post_id, user_id) VALUES ($post_id, $current_user_id)";
    $poster_id = getPosterId($post_id);
   
    if ($poster_id != $current_user_id){
        createNotification($current_user_id, $poster_id, "liked your post !", $post_id);
    }
    return mysqli_query($db, $query);
}

function addComment($post_id, $comment, $current_user_id){
    global $bdd;
    $comment = mysqli_real_escape_string($db, $comment);

    $query = "INSERT INTO comments (user_id, post_id, comment) VALUES ($current_user_id, $post_id, '$comment')";
    $poster_id = getPosterId($post_id);

    if ($poster_id != $current_user_id){
        createNotification($current_user_id, $poster_id, "commented on your post", $post_id);
    }

    return mysqli_query($db, $query);
}

function createNotification($from_user_id, $to_user_id, $msg, $post_id = 0){
    global $bdd;
    $query = "INSERT INTO notifications (from_user_id, to_user_id, message, post_id) VALUES ($from_user_id, $to_user_id, '$msg', $post_id)";
    mysqli_query($db, $query);
}

function getComments($post_id){
    global $bdd;
    $query="SELECT * FROM comments WHERE post_id=$post_id ORDER BY id DESC";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}

function getNotifications($current_user_id){
    global $db;
    $query = "SELECT * FROM notifications WHERE to_user_id = $current_user_id ORDER BY id DESC";
    $run = mysqli_query($db, $query);
    return mysqli_fetch_all($run, true);
}

function show_time($time){
    return '<time style="font-size:small" class="timeago text-muted text-small" datetime="'.$time.'"></time>';
}

function getLikes($post_id){
    global $bdd;
    $query="SELECT * FROM likes WHERE post_id=$post_id";
    $run = mysqli_query($db,$query);
    return mysqli_fetch_all($run,true);
}

function unlike($post_id){
    global $bdd;
    $query="DELETE FROM likes WHERE user_id=$current_user && post_id=$post_id";
    $poster_id = getPosterId($post_id);
    if($poster_id!=$current_user){
        createNotification($current_user,$poster_id,"unliked your post !",$post_id);
    }
    return mysqli_query($db,$query);
}

function unfollowUser($user_id){
    global $db;
    $query="DELETE FROM follow_list WHERE follower_id=$current_user && user_id=$user_id";
    createNotification($current_user,$user_id,"Unfollowed you !");
    return mysqli_query($db,$query); 
}

function showError($field){
    if(isset($_SESSION['error'])){
        $error =$_SESSION['error'];
        if(isset($error['field']) && $field==$error['field']){
           ?>
    <div class="alert alert-danger my-2" role="alert">
      <?=$error['msg']?>
    </div>
           <?php
        }
    }
}
   
function showFormData($field){
    if(isset($_SESSION['formdata'])){
        $formdata =$_SESSION['formdata'];
        return $formdata[$field];
    }
}

function isEmailRegistered($email){
    global $db;
    $query="SELECT count(*) as row FROM users WHERE email='$email'";
    $run=mysqli_query($db,$query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row'];
}

function isUsernameRegistered($username){
    global $db;
    $query="SELECT count(*) as row FROM users WHERE username='$username'";
    $run=mysqli_query($db,$query);
    $return_data = mysqli_fetch_assoc($run);
    return $return_data['row'];
}

function checkUser($login_data){
    global $db;
    $username_email = $login_data['username_email'];
    $password=md5($login_data['password']);
    $query = "SELECT * FROM users WHERE (email='$username_email' || username='$username_email') && password='$password'";
    $run = mysqli_query($db,$query);
    $data['user'] = mysqli_fetch_assoc($run)??array();
    if(count($data['user'])>0){
        $data['status']=true;
    }
    else{
        $data['status']=false;

    }
    return $data;
}

function followUser($user_id){
    global $db;
    $cu = getUser($_SESSION['userdata']['id']);
    $current_user=$_SESSION['userdata']['id'];
    $query="INSERT INTO follow_list(follower_id,user_id) VALUES($current_user,$user_id)";
  
    createNotification($cu['id'],$user_id,"started following you !");
    return mysqli_query($db,$query);
    
}

function blockUser($blocked_user_id){
    global $db;
    $cu = getUser($_SESSION['userdata']['id']);
    $current_user=$_SESSION['userdata']['id'];
    $query="INSERT INTO block_list(user_id,blocked_user_id) VALUES($current_user,$blocked_user_id)";
  
    createNotification($cu['id'],$blocked_user_id,"blocked you");
    $query2="DELETE FROM follow_list WHERE follower_id=$current_user && user_id=$blocked_user_id";
    mysqli_query($db,$query2);
    $query3="DELETE FROM follow_list WHERE follower_id=$blocked_user_id && user_id=$current_user";
    mysqli_query($db,$query3);
    return mysqli_query($db,$query);
    
}

function unblockUser($user_id){
    global $db;
    $current_user=$_SESSION['userdata']['id'];
    $query="DELETE FROM block_list WHERE user_id=$current_user && blocked_user_id=$user_id";
    createNotification($current_user,$user_id,"Unblocked you !");
    return mysqli_query($db,$query);   
}

function getLikesCountForPublication($bdd, $publicationId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM likes WHERE id_article = :publicationId");
        $stmt->bindParam(":publicationId", $publicationId);
        $stmt->execute();
        $likesCount = $stmt->fetchColumn();
        return $likesCount;
    } catch (PDOException $e) {
        return 0; 
    }
}

function likePublication($bdd, $publicationId, $userId) {
    try {
        if (!isPublicationLiked($bdd, $publicationId, $userId)) {
            $stmt = $bdd->prepare("INSERT INTO likes (id_article, id_utilisateur) VALUES (:publicationId, :userId)");
            $stmt->bindParam(":publicationId", $publicationId);
            $stmt->bindParam(":userId", $userId);
            $stmt->execute();
            return true; 
        } else {
            return false; 
        }
    } catch (PDOException $e) {
        return false;
    }
}

function unlikePublication($bdd, $publicationId, $userId) {
    try {
        if (isPublicationLiked($bdd, $publicationId, $userId)) {
            $stmt = $bdd->prepare("DELETE FROM likes WHERE id_article = :publicationId AND id_utilisateur = :userId");
            $stmt->bindParam(":publicationId", $publicationId);
            $stmt->bindParam(":userId", $userId);
            $stmt->execute();
            return true; 
        } else {
            return false; 
        }
    } catch (PDOException $e) {
        return false;
    }
}


function isPublicationLiked($bdd, $publicationId, $userId) {
    try {
        $stmt_check = $bdd->prepare("SELECT * FROM likes WHERE id_article = :publicationId AND id_utilisateur = :userId");
        $stmt_check->bindParam(":publicationId", $publicationId);
        $stmt_check->bindParam(":userId", $userId);
        $stmt_check->execute();
        return $stmt_check->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

function CommentPublication($bdd, $publicationId, $userId, $commentaire) {
    try {
        $stmt = $bdd->prepare("INSERT INTO commentaires (id_article, id_utilisateur, commentaire, date_creation) VALUES (:id_article, :id_utilisateur, :commentaire, current_timestamp())");
        $stmt->bindParam(":id_article", $publicationId);
        $stmt->bindParam(":id_utilisateur", $userId);
        $stmt->bindParam(":commentaire", $commentaire);
        $stmt->execute();
        $commentId = $bdd->lastInsertId();
        return ($commentId > 0);
    } catch (PDOException $e) {
        return false;
    }
}


function getCommentCountForPublication($bdd, $publicationId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM commentaires WHERE id_article = :id_article");
        $stmt->bindParam(":id_article", $publicationId);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count;
    } catch (PDOException $e) {
        return false;
    }
}

function isPublicationCommented($bdd, $publicationId, $userId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM commentaires WHERE id_article = :id_article AND id_utilisateur = :id_utilisateur");
        $stmt->bindParam(":id_article", $id_article);
        $stmt->bindParam(":id_utilisateur", $id_utilisateur);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return ($count > 0);
    } catch (PDOException $e) {
        return false;
    }
}

function deleteComment($bdd, $id_commentaire) {
    try {
        $stmt = $bdd->prepare("DELETE FROM commentaires WHERE id = :id_commentaire");
        $stmt->bindParam(":id_commentaire", $id_commentaire);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function countUserPosts($bdd, $userId) {
    try {
        $query = "SELECT COUNT(*) as total_posts FROM post WHERE id_utilisateur = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && isset($result['total_posts'])) {
            return $result['total_posts'];
        } else {
            return 0;
        }
    } catch (PDOException $e) {
        return -1;
    }
}

function addFriend($bdd, $userId, $friendId) {
    try {
        $query = "SELECT * FROM following WHERE id_follower = :userId AND id_utilisateur = :friendId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "INSERT INTO following (id_follower, id_utilisateur) VALUES (:userId, :friendId)";
            $stmt = $bdd->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':friendId', $friendId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return false;
    }
}

function removeFriend($bdd, $userId, $friendId) {
    try {
        $query = "DELETE FROM following WHERE id_follower = :userId AND id_utilisateur = :friendId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function getFriendsList($bdd, $userId) {
    try {
        $query = "SELECT id_utilisateur FROM following WHERE id_follower = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $friends = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $friends[] = $row['id_utilisateur'];
        }
        return $friends;
    } catch (PDOException $e) {
        return [];
    }
}

function sontAmis($bdd, $userId, $friendId) {
    try {
        $query = "SELECT * FROM following WHERE id_follower = :userId AND id_utilisateur = :friendId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

function getNonFriends($bdd, $userId) {
    $query = "
        SELECT *
        FROM Utilisateurs
        WHERE id_utilisateur != :userId
        AND id_utilisateur NOT IN (
            SELECT id_utilisateur
            FROM following
            WHERE id_follower = :userId
        )
    ";

    $stmt = $bdd->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countUserFollowing($bdd, $userId) {
    try {
        $query = "SELECT COUNT(*) FROM following WHERE id_follower = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count;
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return 0;
    }
}

function countUserFollowers($bdd, $userId) {
    try {
        $query = "SELECT COUNT(*) FROM following WHERE id_utilisateur = :userId";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count;
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return 0;
    }
}

function getFollowingPublications($bdd, $userId) {
    try {
        $followingQuery = "SELECT id_utilisateur FROM following WHERE id_follower = :userId";
        $followingStmt = $bdd->prepare($followingQuery);
        $followingStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $followingStmt->execute();
        $followingUsers = $followingStmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($followingUsers)) {
            return [];
        }

        $followingUsersString = implode(',', array_map('intval', $followingUsers));
        $postsQuery = "SELECT post.*, Utilisateurs.nom, Utilisateurs.prenom,
                              (SELECT COUNT(*) FROM likes WHERE likes.id_article = post.id) AS nombre_likes,
                              (SELECT COUNT(*) FROM commentaires WHERE commentaires.id_article = post.id) AS nombre_commentaires
                       FROM post
                       INNER JOIN Utilisateurs ON post.id_utilisateur = Utilisateurs.id_utilisateur
                       WHERE post.id_utilisateur IN ($followingUsersString)
                       ORDER BY post.date_creation DESC";
        $postsStmt = $bdd->query($postsQuery);

        return $postsStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return [];
    }
}
