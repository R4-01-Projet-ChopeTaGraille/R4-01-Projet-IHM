<?php
/**
 * Page de liste des menus
 * Appelle GET /menus et affiche le resultat dans un tableau HTML.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api.php';

// Etape 16 : Suppression d'un menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
    $menuId = intval($_POST['menuId'] ?? 0);
    if ($menuId > 0) {
        appelApi(API_MENUS . '/menus/' . $menuId, 'DELETE');
    }
    header('Location: /pages/menus.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';

// Appel API
$resultat = appelApi(API_MENUS . '/menus');
$menus = $resultat['donnees'];
?>

<h1>Les menus</h1>

<p><a href="/pages/menu-creer.php" class="btn btn-principal">Creer un menu</a></p>

<?php if ($resultat['code'] === 0): ?>
    <p class="msg-erreur">Impossible de contacter l'API Menus. Verifiez que le serveur est lance (port 3004).</p>
<?php elseif (empty($menus)): ?>
    <p>Aucun menu disponible pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Createur</th>
                <th>Nombre de plats</th>
                <th>Prix total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menus as $menu): ?>
                <tr>
                    <td><?= htmlspecialchars($menu['nom']) ?></td>
                    <td><?= htmlspecialchars($menu['createurNom']) ?></td>
                    <td><?= count($menu['plats']) ?></td>
                    <td><?= number_format($menu['prixTotal'], 2, ',', ' ') ?> &euro;</td>
                    <td>
                        <a href="/pages/menu-detail.php?id=<?= intval($menu['id']) ?>" class="btn btn-secondaire">Voir</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="menuId" value="<?= intval($menu['id']) ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer ce menu ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
