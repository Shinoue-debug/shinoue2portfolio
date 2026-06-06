<?php
require_once 'fonctions.php';
require_once 'config/connexion.php';

enregistrer_visite($pdo, 'projets');

// Récupérer les projets depuis la base
$mot_cle = nettoyer($_GET['q'] ?? '');

if ($mot_cle !== '') {
    $stmt = $pdo->prepare("SELECT * FROM projets WHERE titre LIKE :search OR description LIKE :search ORDER BY date_creation DESC");
    $stmt->execute([':search' => "%$mot_cle%"]);
    $projets = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC");
    $projets = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets - Shinouee</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <input type="checkbox" id="darkModeToggle" class="dark-mode-checkbox">
    <div class="wrapper">
        <label for="darkModeToggle" class="theme-toggle-label">🌙 Mode sombre</label>

        <?php require 'navigation.php'; ?>

        <main>
            <header class="page-header">
                <h1>Projets récents</h1>
                <p>« Quand on n’a rien à perdre, on peut tout accomplir. » – Naruto Uzumaki.</p>
            </header>

            <section class="featured-projects">
                <form class="search-bar" method="get" action="projets.php">
                    <input type="search" id="search" name="q" value="<?= $mot_cle ?>" placeholder="Rechercher un projet...">
                    <button type="submit">Rechercher</button>
                </form>

                <?php if ($mot_cle !== '') : ?>
                    <p>Résultats pour « <?= $mot_cle ?> » : <?= count($projets) ?> projet(s).</p>
                <?php endif; ?>

                <div class="projects">
                    <?php if (empty($projets)) : ?>
                        <p>Aucun projet trouvé.</p>
                    <?php else : ?>
                        <?php foreach ($projets as $projet) : ?>
                            <div class="project-card">
                                <?php if ($projet['image'] && file_exists('images/projets/' . $projet['image'])) : ?>
                                    <img src="images/projets/<?= $projet['image'] ?>" alt="<?= nettoyer($projet['titre']) ?>">
                                <?php else : ?>
                                    <div class="no-image">Image non disponible</div>
                                <?php endif; ?>
                                <h3><?= nettoyer($projet['titre']) ?></h3>
                                <p><?= nettoyer($projet['description']) ?></p>
                                <div class="technologies">
                                    <?php 
                                    $techs = explode(',', $projet['technologies']);
                                    foreach ($techs as $tech) : ?>
                                        <span class="badge"><?= nettoyer($tech) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($projet['lien']) : ?>
                                    <a href="<?= $projet['lien'] ?>" target="_blank">Voir le projet →</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <?php require 'pied-de-page.php'; ?>
    </div>
</body>
</html>