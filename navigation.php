<?php
$page_courante = basename($_SERVER['PHP_SELF']);
$menu = [
    'index.php'   => 'Accueil',
    'about.php'   => 'À propos',
    'projets.php' => 'Projets',
    'contact.php' => 'Contact',
];
?>
<nav>
    <ul>
        <?php foreach ($menu as $href => $titre) : ?>
            <li>
                <a href="<?= $href ?>" <?php if ($page_courante === $href) echo 'class="actif"'; ?>>
                    <?= $titre ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>