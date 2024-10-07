<?ph<?php
// Initialisation de la variable de message
$message = '';

// Configuration de la connexion à la base de données
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'bibliotheque';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données des différents documents
    $categories = $_POST['categorie'];
    $titres = $_POST['titre'];
    $auteurs = $_POST['auteur'];
    $descriptions = $_POST['description'];
    $fichiers = $_FILES['fichier'];

    // Boucler sur les documents soumis
    for ($i = 0; $i < count($titres); $i++) {
        // Vérification s'il y a une erreur dans le téléchargement
        if ($fichiers['error'][$i] !== UPLOAD_ERR_OK) {
            $message .= "Erreur lors du téléchargement du fichier " . $fichiers['name'][$i] . ": " . $fichiers['error'][$i] . "<br>";
            continue;  // Passer au document suivant en cas d'erreur
        }

        $categorie = $categories[$i];
        $titre = $titres[$i];
        $auteur = $auteurs[$i];
        $description = $descriptions[$i];
        $fichier_name = $fichiers['name'][$i];

        // Définir le répertoire cible et le chemin final du fichier
        $target_dir = "upload/";
        $target_file = $target_dir . basename($fichiers["name"][$i]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($fileType != "pdf") {
            $message .= "Désolé, seuls les fichiers PDF sont acceptés pour le fichier " . $fichier_name . ".<br>";
        } else {
            // Vérifier si le dossier de destination existe, sinon le créer
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // Déplacer le fichier vers le dossier de destination
            if (move_uploaded_file($fichiers["tmp_name"][$i], $target_file)) {
                // Connexion à la base de données
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connexion échouée : " . $conn->connect_error);
                }

                // Préparer et exécuter la requête d'insertion
                $sql = "INSERT INTO documents (titre, auteur, description, fichier_path, categorie) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    die("Erreur de préparation de la requête : " . $conn->error);
                }

                $stmt->bind_param("sssss", $titre, $auteur, $description, $target_file, $categorie);

                if ($stmt->execute()) {
                    $message .= "Le document '" . htmlspecialchars($titre) . "' a été ajouté avec succès.<br>";
                } else {
                    $message .= "Erreur lors de l'exécution de la requête pour le document '" . htmlspecialchars($titre) . "': " . $stmt->error . "<br>";
                }

                $stmt->close();
                $conn->close();
            } else {
                $message .= "Erreur lors du téléchargement du fichier " . $fichier_name . ".<br>";
            }
        }
    }

    // Redirection pour éviter la soumission du formulaire à nouveau après actualisation
    header("Location: admin.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Ajouter des documents</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h1>Interface d'Administration</h1>
        <nav>
            <ul>
                <li><a class="nav-link" href="accueil.html">Accueil</a></li>
                <li><a class="nav-link" href="admin.php">Admin</a></li>
            </ul>
        </nav>
    </header>

    <section id="admin">
        <h2>Ajouter des Documents</h2>

        <!-- Affichage du message de confirmation ou d'erreur -->
        <?php if (!empty($message)): ?>
            <div style="color: red;">
                <?= nl2br(htmlspecialchars($message)) ?>
            </div>
        <?php endif; ?>

        <form action="admin.php" method="POST" enctype="multipart/form-data" id="documentForm">
            <div id="documentsContainer">
                <div class="document-group">
                    <label for="categorie">Catégorie du document :</label>
                    <select id="categorie" name="categorie[]" required>
                        <option value="" disabled selected>-- Sélectionnez une catégorie --</option>
                        <option value="Thèses de l'UAO">Thèses de l'UAO</option>
                        <option value="Documents rares">Documents rares</option>
                        <option value="Articles de l'UAO">Articles de l'UAO</option>
                        <option value="Mémoires de l'UAO">Mémoires de l'UAO</option>
                        <option value="Manuscrits de René Maran">Manuscrits de René Maran</option>
                    </select>

                    <label for="titre">Titre du document :</label>
                    <input type="text" id="titre" name="titre[]" required>

                    <label for="auteur">Auteur :</label>
                    <input type="text" id="auteur" name="auteur[]" required>

                    <label for="description">Description :</label>
                    <textarea id="description" name="description[]"></textarea>

                    <label for="fichier">Téléverser un fichier :</label>
                    <input type="file" id="fichier" name="fichier[]" accept=".pdf" required>
                </div>
            </div>

            <!-- Bouton pour ajouter un autre document -->
            <button type="button" id="addDocumentBtn">Ajouter un autre document</button>
            <button type="submit">Ajouter les Documents</button>
        </form>
    </section>

    <h2>Documents Téléversés</h2>
    <div class="documents-list">
        <?php
        // Connexion à la base de données pour récupérer les documents
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connexion échouée : " . $conn->connect_error);
        }

        // Récupérer les documents téléversés
        $sql = "SELECT * FROM documents ORDER BY categorie";
        $result = $conn->query($sql);

        $previousCategory = '';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($previousCategory != $row['categorie']) {
                    if ($previousCategory != '') {
                        echo "</div>"; // Ferme le conteneur de la catégorie précédente
                    }
                    $previousCategory = $row['categorie'];
                    echo "<h3>" . htmlspecialchars($previousCategory) . "</h3>";
                    echo "<div class='documents-category'>";
                }

                echo "<div class='document-item' id='document-" . $row['id'] . "'>" .
                     "<span class='document-title'>" . htmlspecialchars($row['titre']) . "</span>" .
                     " <span class='document-author'>Auteur: " . htmlspecialchars($row['auteur']) . "</span>" .
                     " <span class='document-description'>Description: " . htmlspecialchars($row['description']) . "</span>" .
                     " <button class='delete-button' style='background-color: #dc3545; color: white; padding: 8px 15px; border-radius: 4px;' onclick='supprimerDocument(" . $row['id'] . ")'>Supprimer</button>" .
                     "</div>";
            }
            echo "</div>"; // Ferme le dernier conteneur de catégorie
        } else {
            echo "<p>Aucun document trouvé.</p>";
        }

        $conn->close();
        ?>
    </div>

    <footer>
        <p>&copy; 2024 - Bibliothèque Numérique</p>
    </footer>

    <!-- Script JavaScript pour ajouter des groupes de champs dynamiquement -->
    <script>
    document.getElementById('addDocumentBtn').addEventListener('click', function() {
        // Récupérer le conteneur des documents
        var container = document.getElementById('documentsContainer');

        // Dupliquer le premier groupe de champs
        var newDocumentGroup = container.querySelector('.document-group').cloneNode(true);

        // Vider les champs du nouveau groupe
        var inputs = newDocumentGroup.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            if (input.type === 'file') {
                input.value = ''; // Effacer le champ de fichier
            } else {
                input.value = '';
            }
        });

        // Ajouter le nouveau groupe au conteneur
        container.appendChild(newDocumentGroup);
    });
    </script>

    <!-- Script JavaScript pour la suppression asynchrone des documents -->
    <script>
    function supprimerDocument(id) {
        if (confirm("Êtes-vous sûr de vouloir supprimer ce document ?")) {
            // Requête AJAX pour supprimer le document
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "delete.php?id=" + id, true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    // Supprimer l'élément du DOM si la suppression est réussie
                    var documentElement = document.getElementById("document-" + id);
                    documentElement.remove();
                } else {
                    alert("Erreur lors de la suppression du document.");
                }
            };
            xhr.send();
        }
    }
    </script>

</body>
</html>
