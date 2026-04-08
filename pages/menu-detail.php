<?php
/**
 * Page detail d'un menu
 * Appelle GET /menus/{id} et affiche les informations completes du menu.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api.php';

$id = intval($_GET['id'] ?? 0);

if ($id === 0) {
    header('Location: /pages/menus.php');
    exit;
}

// Appel API
$resultat = appelApi(API_MENUS . '/menus/' . $id);
$menu = $resultat['donnees'];

require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($resultat['code'] === 0): ?>
    <p class="msg-erreur">Impossible de contacter l'API Menus. Verifiez que le serveur est lance (port 3004).</p>
<?php elseif ($resultat['code'] === 404 || $menu === null): ?>
    <p class="msg-erreur">Menu introuvable.</p>
<?php else: ?>

    <h1><?= htmlspecialchars($menu['nom']) ?></h1>

    <table>
        <tbody>
            <tr>
                <th>Createur</th>
                <td><?= htmlspecialchars($menu['createurNom']) ?></td>
            </tr>
            <tr>
                <th>Date de creation</th>
                <td><?= htmlspecialchars($menu['dateCreation']) ?></td>
            </tr>
            <tr>
                <th>Derniere mise a jour</th>
                <td><?= htmlspecialchars($menu['dateMiseAJour']) ?></td>
            </tr>
            <tr>
                <th>Prix total</th>
                <td><?= number_format($menu['prixTotal'], 2, ',', ' ') ?> &euro;</td>
            </tr>
        </tbody>
    </table>

    <h2>Plats du menu</h2>

    <?php if (empty($menu['plats'])): ?>
        <p>Ce menu ne contient aucun plat.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menu['plats'] as $plat): ?>
                    <tr>
                        <td><?= htmlspecialchars($plat['nom']) ?></td>
                        <td><?= number_format($plat['prix'], 2, ',', ' ') ?> &euro;</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>

<p><a href="/pages/menus.php">Retour a la liste des menus</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
