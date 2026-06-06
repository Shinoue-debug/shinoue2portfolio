<?php
session_start();

/**
 * Vérifie qu'un champ n'est pas vide après nettoyage.
 */
function champ_requis(string $valeur): bool {
    return trim($valeur) !== '';
}

/**
 * Nettoie une valeur pour l'afficher sans risque dans du HTML.
 */
function nettoyer(string $valeur): string {
    return htmlspecialchars(trim($valeur), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Valide une adresse email
 */
function valider_email(string $email): bool {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Génère un token CSRF
 */
function generer_token_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie le token CSRF
 */
function verifier_token_csrf(string $token): bool {
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Récupère l'adresse IP réelle du visiteur
 */
function get_ip_visiteur(): string {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Enregistre une visite dans la base de données
 */
function enregistrer_visite(PDO $pdo, string $page): void {
    try {
        $ip = get_ip_visiteur();
        $stmt = $pdo->prepare("INSERT INTO visites (adresse_ip, page, date_visite) VALUES (:ip, :page, NOW())");
        $stmt->execute([
            ':ip' => $ip,
            ':page' => $page
        ]);
    } catch (PDOException $e) {
        error_log("Erreur enregistrement visite : " . $e->getMessage());
    }
}

/**
 * Vérifie si un administrateur est connecté
 */
function est_connecte(): bool {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_prenom']);
}

/**
 * Redirige vers la page de connexion si non connecté
 */
function verifier_connexion_admin(): void {
    if (!est_connecte()) {
        header('Location: connexion.php');
        exit();
    }
}

/**
 * Récupère un projet par son ID
 */
function get_projet_par_id(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM projets WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $projet = $stmt->fetch();
    return $projet ?: null;
}

/**
 * Upload d'image sécurisé
 */
function upload_image(array $file, string $dossier): ?string {
    $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $extensions_autorisees)) {
        return null;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $nom_unique = uniqid() . '.' . $extension;
    $chemin = $dossier . '/' . $nom_unique;
    
    if (!is_dir($dossier)) {
        mkdir($dossier, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $chemin)) {
        return $nom_unique;
    }
    
    return null;
}
?>
