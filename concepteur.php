<?php
// Initialisation des messages
$message = '';
$email = '';
$password = '';

// Vérification si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération de l'e-mail et du mot de passe
    $email_input = $_POST['email'];
    $password_input = $_POST['password'];

    // Remplacez ces valeurs par celles que vous souhaitez
    $email_concepteur = '2015yobouetpethuel@gmail.com'; // e-mail du concepteur
    $password_concepteur = 'MASSENIE@2024'; // mot de passe du concepteur

    // Vérification de l'e-mail et du mot de passe
    if ($email_input === $email_concepteur && $password_input === $password_concepteur) {
        // Rediriger vers la page d'administration
        header("Location: admin.php");
        exit();
    } else {
        $message = "E-mail ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Admin Club Histoire</title>
    <link rel="stylesheet" href="stylesindex.css">
</head>
<body>
    <div class="header">
        <a href="accueil.html"> <!-- Lien vers la page d'accueil -->
            <img src="logo.jpg" alt="Logo Club Histoire" class="logo">
        </a>
    </div>
    <h1>Connexion - Administrateur Club Histoire</h1>
    <!-- Formulaire de connexion -->
    <div id="form_container">
        <form action="" method="POST">
            <label for="email">E-mail :</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="Se connecter">
        </form>

        <!-- Affichage du message d'erreur -->
        <?php if ($message != ''): ?>
            <div class="message" style="margin-top: 20px; color: red;">
                <?= $message; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
