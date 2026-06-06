<?php
require_once '../../fonctions.php';
require_once '../../config/connexion.php';

verifier_connexion_admin();

$erreurs = [];
$succes = false;
$csrf_token = generer_token_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_token_csrf($_POST['csrf_token'] ?? '')) {
        die("Erreur CSRF");
    }
    
    $prenom = nettoyer($_POST['prenom'] ?? '');
    $nom = nettoyer($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    if (!champ_requis($prenom)) {
        $erreurs['prenom'] = 'Le prénom est obligatoire.';
    }
    if (!champ_requis($nom)) {
        $erreurs['nom'] = 'Le nom est obligatoire.';
    }
    if (!champ_requis($email)) {
        $erreurs['email'] = 'L\'email est obligatoire.';
    } elseif (!valider_email($email)) {
        $erreurs['email'] = 'Email invalide.';
    }
    if (!champ_requis($mot_de_passe)) {
        $erreurs['mot_de_passe'] = 'Le mot de passe est obligatoire.';
    } elseif (strlen($mot_de_passe) < 8) {
        $erreurs['mot_de_passe'] = 'Le mot de passe doit faire au moins 8 caractères.';
    }
    
    if (empty($erreurs)) {
        $hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO administrateurs (prenom, nom, email, mot_de_passe, date_creation) VALUES (:prenom, :nom, :email, :mdp, NOW())");
            $stmt->execute([
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':email' => $email,
                ':mdp' => $hash
            ]);
            $succes = true;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $erreurs['email'] = 'Cet email est déjà utilisé.';
            } else {
                $erreurs['general'] = 'Erreur lors de la création.';
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
    <title>Créer un administrateur</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-nav">
        <a href="../dashboard.php">Dashboard</a>
        <a href="../projets/">Projets</a>
        <a href="index.php">Administrateurs</a>
        <a href="../deconnexion.php">Déconnexion</a>
    </div>
    
    <main>
        <section class="contact">
            <h1>Nouvel administrateur</h1>
            
            <?php if ($succes) : ?>
                <div class="success">Administrateur créé avec succès !</div>
            <?php endif; ?>
            <?php if (isset($erreurs['general'])) : ?>
                <div class="error"><?= $erreurs['general'] ?></div>
            <?php endif; ?>
            
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <label for="prenom">Prénom * :</label>
                <input type="text" id="prenom" name="prenom" value="<?= $_POST['prenom'] ?? '' ?>">
                <?php if (isset($erreurs['prenom'])) : ?>
                    <span class="error"><?= $erreurs['prenom'] ?></span>
                <?php endif; ?>
                
                <label for="nom">Nom * :</label>
                <input type="text" id="nom" name="nom" value="<?= $_POST['nom'] ?? '' ?>">
                <?php if (isset($erreurs['nom'])) : ?>
                    <span class="error"><?= $erreurs['nom'] ?></span>
                <?php endif; ?>
                
                <label for="email">Email * :</label>
                <input type="email" id="email" name="email" value="<?= $_POST['email'] ?? '' ?>">
                <?php if (isset($erreurs['email'])) : ?>
                    <span class="error"><?= $erreurs['email'] ?></span>
                <?php endif; ?>
                
                <label for="mot_de_passe">Mot de passe * (min 8 caractères) :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe">
                <?php if (isset($erreurs['mot_de_passe'])) : ?>
                    <span class="error"><?= $erreurs['mot_de_passe'] ?></span>
                <?php endif; ?>
                
                <button type="submit">Créer</button>
            </form>
        </section>
    </main>
</body>
</html>
