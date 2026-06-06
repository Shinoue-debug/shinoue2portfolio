<?php
require_once 'fonctions.php';
require_once 'config/connexion.php';

enregistrer_visite($pdo, 'about');

// Récupérer les statistiques depuis la base
$stmt_stats = $pdo->query("SELECT COUNT(*) as total FROM projets");
$total_projets = $stmt_stats->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Shinouee</title>
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
                <h1>Bienvenue sur mon portfolio</h1>
                <p>Je suis Shinouee, aspirante hackeuse, curieuse du code, exploratrice des failles.</p>
            </header>

            <section class="intro">
                <h2>À propos de moi</h2>
                <img src="images/profil.jpeg" alt="Shinouee" class="profile-photo">
                <p>Je suis une passionnée de technologie et de cybersécurité, toujours à la recherche de nouveaux défis pour apprendre et grandir. Mon parcours en développement web et en sécurité informatique m'a permis d'acquérir des compétences solides dans divers langages de programmation et outils de sécurité. Je suis motivée par l'idée de contribuer à un monde numérique plus sûr et plus innovant.</p>
                <p>« La curiosité est la clé de l'innovation. » – Albert Einstein.</p>
                <p>« La sécurité n'est pas un produit, mais un processus. » – Bruce Schneier.</p>
            </section>

            <section class="stats">
                <div class="stat">
                    <h3><?= $total_projets ?>+</h3>
                    <p>Projets réalisés</p>
                </div>
                <div class="stat">
                    <h3>+5</h3>
                    <p>Langages maîtrisés</p>
                </div>
                <div class="stat">
                    <h3>2+</h3>
                    <p>Années d'apprentissage</p>
                </div>
            </section>

            <section class="skills-section">
                <h2>Mes compétences</h2>
                <div class="skills">
                    <span>HTML</span>
                    <span>CSS</span>
                    <span>Python</span>
                    <span>SQL</span>
                    <span>C/C++</span>
                    <span>JavaScript</span>
                    <span>PHP</span>
                </div>
            </section>

            <section class="experiences">
                <h2>Mes expériences</h2>
                <div class="timeline">
                    <div class="timeline-item">
                        <h3>Stage chez Africatech21</h3>
                        <p>Développement et cybersécurité au sein de Africatech21.</p>
                        <span>2026 - Présent</span>
                    </div>
                    <div class="timeline-item">
                        <h3>Stage chez Ataaba</h3>
                        <p>Expérience en entreprise chez Ataaba.</p>
                        <span>2025</span>
                    </div>
                    <div class="timeline-item">
                        <h3>Étudiante en Développement</h3>
                        <p>Apprentissage des langages web et en cybersécurité.</p>
                        <span>2024 - Présent</span>
                    </div>
                    <div class="timeline-item">
                        <h3>Projets personnels</h3>
                        <p>Réalisation de projets IoT et web.</p>
                        <span>2023 - Présent</span>
                    </div>
                </div>
            </section>
        </main>

        <?php require 'pied-de-page.php'; ?>
    </div>
</body>
</html>