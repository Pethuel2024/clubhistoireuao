<?php
// Configuration de la connexion à la base de données
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'bibliotheque';

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Initialiser une variable pour stocker le message
$message = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $categorie = $_POST['categorie'];
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $description = $_POST['description'];
    
    // Récupération du fichier téléversé
    $fichier = $_FILES['fichier'];

    // Vérification s'il y a une erreur dans le téléchargement
    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        $message = "Erreur lors du téléchargement : " . $fichier['error'];
    } else {
        // Définir le répertoire cible et le chemin final du fichier
        $target_dir = "upload/";
        $target_file = $target_dir . basename($fichier["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérification du type de fichier (PDF uniquement)
        if ($fileType != "pdf") {
            $message = "Désolé, seuls les fichiers PDF sont acceptés.";
        } else {
            // Vérifier si le dossier de destination existe, sinon le créer
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // Déplacer le fichier vers le dossier de destination
            if (move_uploaded_file($fichier["tmp_name"], $target_file)) {
                // Préparer et exécuter la requête d'insertion
                $sql = "INSERT INTO documents (titre, auteur, description, fichier_path, categorie) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                
                // Vérifier si la préparation de la requête a échoué
                if ($stmt === false) {
                    die("Erreur de préparation de la requête : " . $conn->error);
                }

                $stmt->bind_param("sssss", $titre, $auteur, $description, $target_file, $categorie);

                if ($stmt->execute()) {
                    $message = "Le document a été ajouté avec succès.";
                } else {
                    $message = "Erreur lors de l'exécution de la requête : " . $stmt->error;
                }

                $stmt->close();
            } else {
                $message = "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
            }
        }
    }

    // Fermer la connexion à la base de données
    $conn->close();
}
?>
