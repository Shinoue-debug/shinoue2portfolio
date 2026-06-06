<?php
require_once '../../fonctions.php';
require_once '../../config/connexion.php';

verifier_connexion_admin();

$message_succes = '';
$message_erreur = '';

if (isset($_GET['supprime']) && $_GET['supprime'] == 1) {
    $message_succes = 'Administrateur supprimé avec succès !';
}
if (isset($_GET['erreur']) && $_GET['erreur'] == 'auto') {
    $message_erreur = 'Vous ne pouvez pas supprimer votre propre compte.';
}

$stmt = $pdo->query("SELECT * FROM administrateurs ORDER BY date_creation DESC");
$admins = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des administrateurs - Administration</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .action-buttons a {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-modifier {
            background-color: #007bff;
            color: white;
        }
        .btn-supprimer {
            background-color: #c62828;
            color: white;
        }
        .btn-supprimer.disabled {
            background-color: #999;
            cursor: not-allowed;
        }
        .success-message {
            background-color: #dcedc8;
            color: #2e7d32;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-nav">
        <a href="../dashboard.php">Dashboard</a>
        <a href="../projets/">Projets</a>
        <a href="index.php">Administrateurs</a>
        <a href="../deconnexion.php">Déconnexion</a>
    </div>
    
    <main>
        <section>
            <h1>Gestion des administrateurs</h1>
            <p><a href="creer.php" style="background-color: #28a745; color: white; padding: 0.5rem 1rem; border-radius: 5px; text-decoration: none;">+ Nouvel administrateur</a></p>
            
            <?php if ($message_succes) : ?>
                <div class="success-message"><?= $message_succes ?></div>
            <?php endif; ?>
            <?php if ($message_erreur) : ?>
                <div class="error-message"><?= $message_erreur ?></div>
            <?php endif; ?>
            
            <table border="1" cellpadding="8" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin) : ?>
                        <tr>
                            <td><?= $admin['id'] ?></td>
                            <td><?= nettoyer($admin['prenom']) ?></td>
                            <td><?= nettoyer($admin['nom']) ?></td>
                            <td><?= nettoyer($admin['email']) ?></td>
                            <td><?= $admin['date_creation'] ?></td>
                            <td class="action-buttons">
                                <a href="modifier.php?id=<?= $admin['id'] ?>" class="btn-modifier">Modifier</a>
                                <?php if ($admin['id'] !== $_SESSION['admin_id']) : ?>
                                    <a href="supprimer.php?id=<?= $admin['id'] ?>" class="btn-supprimer">Supprimer</a>
                                <?php else : ?>
                                    <span class="btn-supprimer disabled" style="background-color: #999; padding: 0.25rem 0.5rem; border-radius: 3px;">(Votre compte)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
