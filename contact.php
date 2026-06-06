<?php
require_once 'fonctions.php';
require_once 'config/connexion.php';

enregistrer_visite($pdo, 'contact');

$contact_nom = '';
$contact_email = '';
$contact_message = '';

$demande_nom = '';
$demande_email = '';
$demande_type = '';
$demande_description = '';
$demande_budget = '';

$erreurs_contact = [];
$erreurs_demande = [];
$succes_contact = false;
$succes_demande = false;

// Générer token CSRF
$csrf_token = generer_token_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_token_csrf($_POST['csrf_token'] ?? '')) {
        die("Erreur CSRF : formulaire invalide.");
    }
    
    $formulaire = $_POST['formulaire'] ?? '';

    // Traitement formulaire contact
    if ($formulaire === 'contact') {
        $contact_nom = nettoyer($_POST['nom'] ?? '');
        $contact_email = trim($_POST['email'] ?? '');
        $contact_message = nettoyer($_POST['message'] ?? '');

        if (!champ_requis($contact_nom)) {
            $erreurs_contact['nom'] = 'Le nom est obligatoire.';
        }

        if (!champ_requis($contact_email)) {
            $erreurs_contact['email'] = 'L\'adresse e-mail est obligatoire.';
        } elseif (!valider_email($contact_email)) {
            $erreurs_contact['email'] = 'L\'adresse e-mail est invalide.';
        }

        if (!champ_requis($contact_message)) {
            $erreurs_contact['message'] = 'Le message ne peut pas être vide.';
        }

        if (empty($erreurs_contact)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, message, date_envoi) VALUES (:nom, :email, :message, NOW())");
                $stmt->execute([
                    ':nom' => $contact_nom,
                    ':email' => $contact_email,
                    ':message' => $contact_message
                ]);
                $succes_contact = true;
                
                // Réinitialiser les champs
                $contact_nom = $contact_email = $contact_message = '';
            } catch (PDOException $e) {
                error_log("Erreur insertion message contact : " . $e->getMessage());
                $erreurs_contact['general'] = "Une erreur est survenue. Veuillez réessayer.";
            }
        }
    }

    // Traitement formulaire demande projet
    if ($formulaire === 'demande_projet') {
        $demande_nom = nettoyer($_POST['nom'] ?? '');
        $demande_email = trim($_POST['email'] ?? '');
        $demande_type = nettoyer($_POST['type_projet'] ?? '');
        $demande_description = nettoyer($_POST['description'] ?? '');
        $demande_budget = nettoyer($_POST['budget'] ?? '');

        if (!champ_requis($demande_nom)) {
            $erreurs_demande['nom'] = 'Le nom est obligatoire.';
        }

        if (!champ_requis($demande_email)) {
            $erreurs_demande['email'] = 'L\'adresse e-mail est obligatoire.';
        } elseif (!valider_email($demande_email)) {
            $erreurs_demande['email'] = 'L\'adresse e-mail est invalide.';
        }

        if (!champ_requis($demande_type)) {
            $erreurs_demande['type_projet'] = 'Le type de projet est obligatoire.';
        }

        if (!champ_requis($demande_description)) {
            $erreurs_demande['description'] = 'La description est obligatoire.';
        }

        if (empty($erreurs_demande)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO demandes_projet (nom, email, type_projet, description, budget, date_demande) VALUES (:nom, :email, :type, :description, :budget, NOW())");
                $stmt->execute([
                    ':nom' => $demande_nom,
                    ':email' => $demande_email,
                    ':type' => $demande_type,
                    ':description' => $demande_description,
                    ':budget' => $demande_budget ?: null
                ]);
                $succes_demande = true;
                
                // Réinitialiser les champs
                $demande_nom = $demande_email = $demande_type = $demande_description = $demande_budget = '';
            } catch (PDOException $e) {
                error_log("Erreur insertion demande projet : " . $e->getMessage());
                $erreurs_demande['general'] = "Une erreur est survenue. Veuillez réessayer.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Shinouee</title>
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
                <p>Je suis Haby Sow, aspirante hackeuse, curieuse du code, exploratrice des failles.</p>
            </header>

            <section class="contact">
                <h2>Contactez-moi</h2>
                <?php if ($succes_contact) : ?>
                    <div class="success">Merci ! Votre message a bien été envoyé.</div>
                <?php endif; ?>
                <?php if (isset($erreurs_contact['general'])) : ?>
                    <div class="error"><?= $erreurs_contact['general'] ?></div>
                <?php endif; ?>

                <form action="contact.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="formulaire" value="contact">
                    
                    <div class="form-group">
                        <label for="nom_contact">Nom :</label>
                        <input type="text" id="nom_contact" name="nom" value="<?= nettoyer($contact_nom) ?>">
                        <?php if (isset($erreurs_contact['nom'])) : ?>
                            <span class="error"><?= $erreurs_contact['nom'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email_contact">Email :</label>
                        <input type="email" id="email_contact" name="email" value="<?= nettoyer($contact_email) ?>">
                        <?php if (isset($erreurs_contact['email'])) : ?>
                            <span class="error"><?= $erreurs_contact['email'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="message_contact">Message :</label>
                        <textarea id="message_contact" name="message"><?= nettoyer($contact_message) ?></textarea>
                        <?php if (isset($erreurs_contact['message'])) : ?>
                            <span class="error"><?= $erreurs_contact['message'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit">Envoyer</button>
                </form>

                <h2>Demande de projet</h2>
                <?php if ($succes_demande) : ?>
                    <div class="success">Votre demande de projet a bien été prise en compte.</div>
                <?php endif; ?>
                <?php if (isset($erreurs_demande['general'])) : ?>
                    <div class="error"><?= $erreurs_demande['general'] ?></div>
                <?php endif; ?>

                <form action="contact.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="formulaire" value="demande_projet">
                    
                    <div class="form-group">
                        <label for="nom_demande">Nom :</label>
                        <input type="text" id="nom_demande" name="nom" value="<?= nettoyer($demande_nom) ?>">
                        <?php if (isset($erreurs_demande['nom'])) : ?>
                            <span class="error"><?= $erreurs_demande['nom'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email_demande">Email :</label>
                        <input type="email" id="email_demande" name="email" value="<?= nettoyer($demande_email) ?>">
                        <?php if (isset($erreurs_demande['email'])) : ?>
                            <span class="error"><?= $erreurs_demande['email'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="type_projet">Type de projet :</label>
                        <input type="text" id="type_projet" name="type_projet" value="<?= nettoyer($demande_type) ?>">
                        <?php if (isset($erreurs_demande['type_projet'])) : ?>
                            <span class="error"><?= $erreurs_demande['type_projet'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="description_demande">Description :</label>
                        <textarea id="description_demande" name="description"><?= nettoyer($demande_description) ?></textarea>
                        <?php if (isset($erreurs_demande['description'])) : ?>
                            <span class="error"><?= $erreurs_demande['description'] ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="budget">Budget estimé :</label>
                        <input type="text" id="budget" name="budget" value="<?= nettoyer($demande_budget) ?>">
                    </div>
                    
                    <button type="submit">Envoyer demande</button>
                </form>
            </section>
        </main>

        <?php require 'pied-de-page.php'; ?>
    </div>
</body>
</html>