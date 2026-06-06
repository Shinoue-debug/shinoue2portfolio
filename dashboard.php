<?php
require_once '../fonctions.php';
require_once '../config/connexion.php';

verifier_connexion_admin();

// Statistiques
$stmt = $pdo->query("SELECT COUNT(*) as total FROM projets");
$total_projets = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM messages_contact WHERE lu = 0");
$messages_non_lus = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM demandes_projet WHERE lu = 0");
$demandes_non_lues = $stmt->fetch()['total'];

// 5 dernières visites
$stmt = $pdo->query("SELECT * FROM visites ORDER BY date_visite DESC LIMIT 5");
$dernieres_visites = $stmt->fetchAll();

// 5 dernières demandes
$stmt = $pdo->query("SELECT * FROM demandes_projet ORDER BY date_demande DESC LIMIT 5");
$dernieres_demandes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administration</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-nav { background: #333; padding: 1rem; margin-bottom: 2rem; }
        .admin-nav a { color: white; margin: 0 1rem; text-decoration: none; }
        .admin-nav a:hover { text-decoration: underline; }
        .stats { display: flex; gap: 2rem; margin-bottom: 2rem; }
        .stat-card { background: var(--card-bg); padding: 1.5rem; border-radius: 10px; text-align: center; flex: 1; }
        .stat-card h3 { font-size: 2rem; margin: 0; }
        .admin-sections { display: flex; gap: 1rem; flex-wrap: wrap; margin: 2rem 0; }
        .admin-sections a { background: #007bff; color: white; padding: 0.75rem 1.5rem; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="admin-nav">
        <span>Bonjour <?= nettoyer($_SESSION['admin_prenom']) ?></span>
        <a href="dashboard.php">Dashboard</a>
        <a href="projets/">Projets</a>
        <a href="utilisateurs/">Administrateurs</a>
        <a href="messages/">Messages</a>
        <a href="demandes/">Demandes</a>
        <a href="deconnexion.php">Déconnexion</a>
    </div>
    
    <main>
        <section>
            <h1>Tableau de bord</h1>
            
            <div class="stats">
                <div class="stat-card">
                    <h3><?= $total_projets ?></h3>
                    <p>Projets publiés</p>
                </div>
                <div class="stat-card">
                    <h3><?= $messages_non_lus ?></h3>
                    <p>Messages non lus</p>
                </div>
                <div class="stat-card">
                    <h3><?= $demandes_non_lues ?></h3>
                    <p>Demandes non lues</p>
                </div>
            </div>
            
            <h2>5 dernières visites</h2>
            <table border="1" cellpadding="8" style="width:100%; margin-bottom: 2rem;">
                <thead>
                    <tr><th>IP</th><th>Page</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($dernieres_visites as $visite) : ?>
                        <tr>
                            <td><?= nettoyer($visite['adresse_ip']) ?></td>
                            <td><?= nettoyer($visite['page']) ?></td>
                            <td><?= $visite['date_visite'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <h2>5 dernières demandes de projet</h2>
            <table border="1" cellpadding="8" style="width:100%;">
                <thead>
                    <tr><th>Nom</th><th>Email</th><th>Type</th><th>Date</th><th>Statut</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($dernieres_demandes as $demande) : ?>
                        <tr style="<?= $demande['lu'] ? '' : 'font-weight: bold; background: #fff3cd;' ?>">
                            <td><?= nettoyer($demande['nom']) ?></td>
                            <td><?= nettoyer($demande['email']) ?></td>
                            <td><?= nettoyer($demande['type_projet']) ?></td>
                            <td><?= $demande['date_demande'] ?></td>
                            <td><?= $demande['lu'] ? 'Lu' : 'Non lu' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
