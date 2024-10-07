<?php
header('Content-Type: application/json');

// Configuration de la connexion à la base de données
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'bibliotheque';

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connexion échouée"]);
    exit;
}

// Vérifier si l'ID du document est passé en paramètre
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Supprimer le document de la base de données
    $sql = "DELETE FROM documents WHERE id=?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Erreur de préparation de la requête"]);
        exit;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Erreur lors de la suppression du document"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "ID non fourni"]);
}

$conn->close();
?>
