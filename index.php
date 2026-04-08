<?php
/**
 * Page d'accueil -- ChopeTaGraille
 * Point d'entree de l'application IHM.
 */
require_once __DIR__ . '/includes/header.php';
?>

<h1>Bienvenue sur ChopeTaGraille</h1>
<p>Application de livraison de repas a domicile.</p>

<div class="accueil-sections">
    <a href="/pages/plats.php" class="accueil-carte">
        <h2>Plats</h2>
        <p>Decouvrez nos plats disponibles</p>
    </a>
    <a href="/pages/menus.php" class="accueil-carte">
        <h2>Menus</h2>
        <p>Composez vos menus personnalises</p>
    </a>
    <a href="/pages/commandes.php" class="accueil-carte">
        <h2>Commandes</h2>
        <p>Passez et suivez vos commandes</p>
    </a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
