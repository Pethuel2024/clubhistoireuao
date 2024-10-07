<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents disponibles</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Documents Disponibles</h1>
        <nav>
            <ul>
                <li><a href="accueil.html">Accueil</a></li>
                <li><a href="documents.html">Documents</a></li>
            </ul>
        </nav>
    </header>

    <section id="documents">
        <h2>Liste des documents</h2>
        <ul class="document-list">
            <?php
            // Connexion à la base de données
            $conn = new mysqli('localhost', 'root', '', 'bibliotheque');

            // Vérifier la connexion
            if ($conn->connect_error) {
                die("Connexion échouée: " . $conn->connect_error);
            }

            // Récupérer les documents de la base de données
            $sql = "SELECT titre, auteur, description, fichier_path FROM documents";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Afficher les documents
                while($row = $result->fetch_assoc()) {
                    echo "<li><h3>" . $row["titre"] . "</h3><p>Auteur: " . $row["auteur"] . "</p><p>Description: " . $row["description"] . "</p><a href='" . $row["fichier_path"] . "'>Télécharger</a></li>";
                }
            } else {
                echo "Aucun document disponible.";
            }

            // Fermer la connexion à la base de données
            $conn->close();
            ?>
        </ul>
    </section>

    <footer>
        <p>&copy; 2024 - Bibliothèque Numérique</p>
    </footer>
</body>
</html>
