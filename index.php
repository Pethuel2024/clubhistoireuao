<?php
// Initialisation des messages
$message = '';
$code_acces = '';

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'bibliotheque');

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération du code d'accès
    if (isset($_POST['code_acces_recup'])) {
        $code_acces_input = $_POST['code_acces_recup'];

        // Vérification du code d'accès
        $sql_recup = "SELECT * FROM utilisateurs WHERE code_acces = '$code_acces_input'";
        $result_recup = $conn->query($sql_recup);

        if ($result_recup->num_rows > 0) {
            $row = $result_recup->fetch_assoc();
            $code_acces = $row['code_acces'];
            // Rediriger vers la page Accueil
            header("Location: accueil.html");
            exit(); // S'assurer que le script s'arrête après la redirection
        } else {
            $message = "Le code d'accès saisi ne correspond à aucun utilisateur.";
        }
    } elseif (isset($_POST['numero_whatsapp_recup'])) {
        // Récupération du numéro pour le code d'accès
        $numero_whatsapp_recup = $_POST['numero_whatsapp_recup'];

        // Vérification du numéro de récupération
        $sql_recup = "SELECT code_acces FROM utilisateurs WHERE numero_whatsapp = '$numero_whatsapp_recup'";
        $result_recup = $conn->query($sql_recup);

        if ($result_recup->num_rows > 0) {
            $row = $result_recup->fetch_assoc();
            $code_acces = $row['code_acces'];
            $message = "Votre code d'accès est : " . $code_acces;
        } else {
            $message = "Le numéro WhatsApp saisi ne correspond à aucun utilisateur.";
        }
    } else {
        // Récupération des données du formulaire d'inscription
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $niveau_etude = $_POST['niveau_etude'];
        $numero_whatsapp = $_POST['numero_whatsapp'];

        // Vérification si le numéro WhatsApp existe déjà dans la base de données
        $sql_check = "SELECT * FROM utilisateurs WHERE numero_whatsapp = '$numero_whatsapp'";
        $result = $conn->query($sql_check);

        if ($result->num_rows > 0) {
            // Si le numéro existe déjà, demander de le ressaisir pour récupérer le code d'accès
            $message = "L'utilisateur existe déjà ! Veuillez saisir à nouveau votre numéro WhatsApp pour récupérer votre code d'accès.";
        } else {
            // Génération du code d'accès unique
            $code_acces = substr($numero_whatsapp, 0, 7) . strtoupper(substr($nom, 0, 1));

            // Insertion dans la base de données
            $sql = "INSERT INTO utilisateurs (nom, prenom, niveau_etude, numero_whatsapp, code_acces) 
                    VALUES ('$nom', '$prenom', '$niveau_etude', '$numero_whatsapp', '$code_acces')";

            if ($conn->query($sql) === TRUE) {
                // Message de succès avec le code d'accès
                $message = "Inscription réussie ! Votre code d'accès est : " . $code_acces;
            } else {
                $message = "Erreur : " . $conn->error;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Club Histoire</title>
    <link rel="stylesheet" href="stylesindex.css">
</head>
<body>
    <div class="header">
        <img src="logo.jpg" alt="Logo Club Histoire" class="logo">
    </div>
 <h1>Inscription - Club Histoire UAO</h1>
    <!-- Formulaire d'inscription ou récupération du code d'accès -->
    <div id="form_container">
        <?php if (strpos($message, "L'utilisateur existe déjà") !== false): ?>
            <form action="" method="POST">
                <label for="numero_whatsapp_recup">Veuillez saisir à nouveau votre numéro WhatsApp :</label>
                <input type="tel" id="numero_whatsapp_recup" name="numero_whatsapp_recup" required><br>
                <input type="submit" value="Récupérer le code">
            </form>
      <?php elseif (strpos($message, "Votre code d'accès est") !== false): ?>
    <div class="message" style="margin-top: 20px; color: green;">
        <?= $message; ?>
        <br><br>
        <!-- Lien pour rediriger vers la page d'accueil si le code est correct -->
        <a href="accueil.html" style="color: blue; text-decoration: underline;">Cliquez ici pour accéder à la page d'accueil</a>
    </div>
<?php else: ?>

            <form action="" method="POST">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" required><br>

    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" name="prenom" required><br>

    <label for="niveau_etude">Niveau d'études :</label>
    <select id="niveau_etude" name="niveau_etude" required>
         <option value=" "> </option>
        <option value="Licence 1">Licence 1</option>
        <option value="Licence 2">Licence 2</option>
        <option value="Licence 3">Licence 3</option>
        <option value="Master 1">Master 1</option>
        <option value="Master 2">Master 2</option>
        <option value="Other">Autre</option>
    </select><br>

    <label for="numero_whatsapp">Numéro WhatsApp :</label>
    <input type="tel" id="numero_whatsapp" name="numero_whatsapp" required><br>

    <input type="submit" value="S'inscrire">
</form>

            <p>
                <a href="#" id="deja_inscrit" onclick="toggleRecuperation()">Déjà inscrit ?</a>
            </p>
        <?php endif; ?>
    </div>

    <!-- Formulaire de récupération avec le code d'accès -->
    <div id="recuperation_form" style="display:none;">
        <form action="" method="POST">
            <label for="code_acces_recup">Veuillez entrer votre code d'accès :</label>
            <input type="text" id="code_acces_recup" name="code_acces_recup" required><br>
            <input type="submit" value="Connexion">
        </form>
    </div>

    <!-- Affichage du message d'erreur ou de succès général -->
    <?php if ($message != '' && strpos($message, 'L utilisateur existe déjà') === false && strpos($message, 'code d\'accès') === false): ?>
        <div class="message" style="margin-top: 20px; color: <?= strpos($message, 'Erreur') !== false ? 'red' : 'green'; ?>;">
            <?= $message; ?>
        </div>
    <?php endif; ?>

    <script>
        function toggleRecuperation() {
            var formContainer = document.getElementById("form_container");
            var recuperationForm = document.getElementById("recuperation_form");
            // Vérifier si le formulaire de récupération est déjà affiché
            if (recuperationForm.style.display === "none") {
                // Masquer le formulaire d'inscription
                formContainer.style.display = "none";
                // Afficher le formulaire de récupération
                recuperationForm.style.display = "block";
            } else {
                // Réinitialiser les formulaires
                recuperationForm.style.display = "none";
                formContainer.style.display = "block";
            }
        }
    </script>
</body>
</html>
