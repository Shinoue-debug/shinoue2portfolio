<?php
require_once '../../fonctions.php';
require_once '../../config/connexion.php';

verifier_connexion_admin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE id = :id");
$stmt->execute([':id' => $id]);
$admin = $stmt->fetch();

if (!$admin) {
    header('Location: index.php');
    exit();
}

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
    
    if (empty($erreurs)) {
        // Gestion du mot de passe
        if (!empty($mot_de_passe)) {
            if (strlen($mot_de_passe) < 8) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit faire au moins 8 caractères.';
            } else {
                $hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE administrateurs SET prenom = :prenom, nom = :nom, email = :email, mot_de_passe = :mdp WHERE id = :id");
                $stmt->execute([
                    ':prenom' => $prenom,
                    ':nom' => $nom,
                    ':email' => $email,
                    ':mdp' => $hash,
                    ':id' => $id
                ]);
                $succes = true;
            }
        } else {
            // Conserver l'ancien mot de passe
            $stmt = $pdo->prepare("UPDATE administrateurs SET prenom = :prenom, nom = :nom, email = :email WHERE id = :id");
            $stmt->execute([
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':email' => $email,
                ':id' => $id
            ]);
            $succes = true;
        }
        
        if ($succes) {
            // Rafraîchir les données
            $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $admin = $stmt->fetch();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier administrateur</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="admin-nav">
        <a href="../../admin/dashboard.php">Dashboard</a>
        <a href="../../admin/projets/">Projets</a>
        <a href="../utilisateurs/index.php">Administrateurs</a>
        <a href="../../admin/deconnexion.php">Déconnexion</a>
    </div>
    
    <main>
        <section class="contact">
            <h1>Modifier administrateur</h1>
            
            <?php if ($succes) : ?>
                <div class="success">Administrateur modifié avec succès !</div>
            <?php endif; ?>
            
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <label for="prenom">Prénom * :</label>
                <input type="text" id="prenom" name="prenom" value="<?= nettoyer($admin['prenom']) ?>">
                <?php if (isset($erreurs['prenom'])) : ?>
                    <span class="error"><?= $erreurs['prenom'] ?></span>
                <?php endif; ?>
                
                <label for="nom">Nom * :</label>
                <input type="text" id="nom" name="nom" value="<?= nettoyer($admin['nom']) ?>">
                <?php if (isset($erreurs['nom'])) : ?>
                    <span class="error"><?= $erreurs['nom'] ?></span>
                <?php endif; ?>
                
                <label for="email">Email * :</label>
                <input type="email" id="email" name="email" value="<?= nettoyer($admin['email']) ?>">
                <?php if (isset($erreurs['email'])) : ?>
                    <span class="error"><?= $erreurs['email'] ?></span>
                <?php endif; ?>
                
                <label for="mot_de_passe">Nouveau mot de passe (laisser vide pour conserver) :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe">
                <?php if (isset($erreurs['mot_de_passe'])) : ?>
                    <span class="error"><?= $erreurs['mot_de_passe'] ?></span>
                <?php endif; ?>
                
                <button type="submit">Modifier</button>
            </form>
        </section>
    </main>
</body>
</html>
