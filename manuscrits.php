<?php
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bibliotheque');

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Requête pour récupérer les documents de la catégorie 'Thèse'
$sql = "SELECT * FROM documents WHERE categorie = 'Manuscrits de René Maran'";  // Vérifie que 'Thèse' est bien utilisé ici
$result = $conn->query($sql);

// Fermeture de la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manuscrits de René Maran - Club Histoire</title>
    <link rel="stylesheet" href="stylemanuscrits.css">
</head>
<body>
  <header>
        <div class="header">
        <a href="accueil.html"> <!-- Lien vers la page d'accueil -->
            <img src="logo.jpg" alt="Logo Club Histoire" class="logo">
        </a>
    </div>
        <h1>Manuscrits de René Maran Disponibles</h1>
        <nav>
            <ul>
                <li><a href="accueil.html">Accueil</a></li>
                <li><a href="theses.php">Thèses de l'UAO</a></li>
                <li><a href="docrares.php">Documents rares</a></li>
                <li><a href="articles.php">Articles de l'UAO</a></li>
                <li><a href="memoires.php">Mémoires de l'UAO</a></li>
                <li><a href="manuscrits.php">Manuscrits de René Maran</a></li>
            </ul>
        </nav>
    </header>
    <div class="manuscrits-list">
        <?php
        if ($result->num_rows > 0) {
            // Afficher les documents
            while ($row = $result->fetch_assoc()) {
                echo "<div class='document'>";
                echo "<h2>" . $row['titre'] . "</h2>";
                echo "<p>Auteur : " . $row['auteur'] . "</p>";
                echo "<p>" . $row['description'] . "</p>";
                echo "<a href='" . $row['fichier_path'] . "' target='_blank'>Télécharger le document</a>";
                echo "</div>";
            }
        } else {
            echo "<p>Aucun manuscrits trouvé.</p>";
        }
        ?>
    </div>
    <footer>
        <p>&copy; 2024 - Bibliothèque Numérique du Club Histoire de l'UAO</p>
    </footer>
</body>
</html>
