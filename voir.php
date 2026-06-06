<?php
require_once '../../fonctions.php';
require_once '../../config/connexion.php';

verifier_connexion_admin();

$id = (int)($_GET['id'] ?? 0);

// Marquer comme lu
$stmt = $pdo->prepare("UPDATE messages_contact SET lu = 1 WHERE id = :id");
$stmt->execute([':id' => $id]);

$stmt = $pdo->prepare("SELECT * FROM messages_contact WHERE id = :id");
$stmt->execute([':id' => $id]);
$message = $stmt->fetch();

if (!$message) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message #<?= $id ?></title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-nav">
        <a href="../dashboard.php">Dashboard</a>
        <a href="index.php">Messages</a>
        <a href="../deconnexion.php">Déconnexion</a>
    </div>
    
    <main>
        <section>
            <h1>Message de <?= nettoyer($message['nom']) ?></h1>
            <p><strong>Email :</strong> <?= nettoyer($message['email']) ?></p>
            <p><strong>Date :</strong> <?= $message['date_envoi'] ?></p>
            <hr>
            <p><?= nl2br(nettoyer($message['message'])) ?></p>
            <p><a href="index.php">← Retour à la liste</a></p>
        </section>
    </main>
</body>
</html>
