<?php
if(isset($_FILES['image_post']) && $_FILES['image_post']['error'] === 0) {
    $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
    $fileType = $_FILES['image_post']['type'];

    if(in_array($fileType, $allowedTypes)) {
        $uploadDir = 'images/post/';
        $fileName = uniqid() . '_' . $_FILES['image_post']['name'];
        $uploadPath = $uploadDir . $fileName;

        if(move_uploaded_file($_FILES['image_post']['tmp_name'], $uploadPath)) {
            header('Location: profil.php');
            exit();
        } else {
            echo "Erreur lors du téléchargement de l'image.";
        }
    } else {
        echo "Le type de fichier n'est pas autorisé. Seuls les fichiers PNG, JPG et JPEG sont acceptés.";
    }
} else {
    echo "Aucun fichier n'a été téléchargé.";
}
?>