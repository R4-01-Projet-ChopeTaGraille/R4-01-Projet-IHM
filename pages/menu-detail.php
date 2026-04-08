<?php
/**
 * Page detail d'un menu
 * Appelle GET /menus/{id} et affiche les informations completes du menu.
 * Permet de renommer le menu via PUT /menus/{id}.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api.php';

$id = intval($_GET['id'] ?? 0);

if ($id === 0) {
    header('Location: /pages/menus.php');
    exit;
}

$msgSucces = null;
$msgErreur = null;

// --- Traitement des actions POST ---
$action = $_POST['action'] ?? '';

// Etape 16 : Supprimer le menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'supprimer') {
    appelApi(API_MENUS . '/menus/' . $id, 'DELETE');
    header('Location: /pages/menus.php');
    exit;
}

// Etape 14 : Ajouter un plat au menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'ajouter_plat') {
    $platId = intval($_POST['platId'] ?? 0);

    if ($platId === 0) {
        $msgErreur = 'Veuillez selectionner un plat.';
    } else {
        $resultatPut = appelApi(API_MENUS . '/menus/' . $id . '/plats/' . $platId, 'PUT');

        if ($resultatPut['code'] === 200) {
            $msgSucces = 'Plat ajoute au menu.';
        } elseif ($resultatPut['code'] === 409) {
            $msgErreur = 'Ce plat est deja present dans le menu.';
        } else {
            $msgErreur = 'Erreur lors de l\'ajout du plat (code ' . $resultatPut['code'] . ').';
        }
    }
}

// Etape 15 : Retirer un plat du menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'retirer_plat') {
    $platId = intval($_POST['platId'] ?? 0);

    $resultatDelete = appelApi(API_MENUS . '/menus/' . $id . '/plats/' . $platId, 'DELETE');

    if ($resultatDelete['code'] === 200) {
        $msgSucces = 'Plat retire du menu.';
    } else {
        $msgErreur = 'Erreur lors du retrait du plat (code ' . $resultatDelete['code'] . ').';
    }
}

// Etape 13 : Renommer le menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'renommer') {
    $nouveauNom = trim($_POST['nom'] ?? '');
    $createurId = intval($_POST['createurId'] ?? 0);

    if ($nouveauNom === '') {
        $msgErreur = 'Le nom du menu ne peut pas etre vide.';
    } else {
        $resultatPut = appelApi(API_MENUS . '/menus/' . $id, 'PUT', [
            'nom' => $nouveauNom,
            'createurId' => $createurId,
        ]);

        if ($resultatPut['code'] === 200) {
            $msgSucces = 'Menu renomme avec succes.';
        } else {
            $msgErreur = 'Erreur lors du renommage (code ' . $resultatPut['code'] . ').';
        }
    }
}

// Appel API (apres le traitement pour avoir les donnees a jour)
$resultat = appelApi(API_MENUS . '/menus/' . $id);
$menu = $resultat['donnees'];

// Charger la liste des plats disponibles pour le formulaire d'ajout
$resultatPlats = appelApi(API_PLATS_UTILISATEURS . '/plats');
$platsDisponibles = $resultatPlats['donnees'] ?? [];

require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($resultat['code'] === 0): ?>
    <p class="msg-erreur">Impossible de contacter l'API Menus. Verifiez que le serveur est lance (port 3004).</p>
<?php elseif ($resultat['code'] === 404 || $menu === null): ?>
    <p class="msg-erreur">Menu introuvable.</p>
<?php else: ?>

    <h1><?= htmlspecialchars($menu['nom']) ?></h1>

    <?php if ($msgSucces): ?>
        <p class="msg-succes"><?= htmlspecialchars($msgSucces) ?></p>
    <?php endif; ?>
    <?php if ($msgErreur): ?>
        <p class="msg-erreur"><?= htmlspecialchars($msgErreur) ?></p>
    <?php endif; ?>

    <!-- Formulaire de renommage -->
    <form method="POST" class="formulaire" style="margin-bottom: 1.5rem;">
        <input type="hidden" name="action" value="renommer">
        <input type="hidden" name="createurId" value="<?= $menu['createurId'] ?>">
        <label for="nom">Renommer le menu</label>
        <input type="text" id="nom" name="nom" required
               value="<?= htmlspecialchars($menu['nom']) ?>">
        <button type="submit" class="btn btn-principal">Renommer</button>
    </form>

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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menu['plats'] as $plat): ?>
                    <tr>
                        <td><?= htmlspecialchars($plat['nom']) ?></td>
                        <td><?= number_format($plat['prix'], 2, ',', ' ') ?> &euro;</td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="retirer_plat">
                                <input type="hidden" name="platId" value="<?= $plat['id'] ?>">
                                <button type="submit" class="btn btn-danger">Retirer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Formulaire d'ajout de plat -->
    <h2>Ajouter un plat</h2>
    <form method="POST" class="formulaire">
        <input type="hidden" name="action" value="ajouter_plat">
        <label for="platId">Plat a ajouter</label>
        <select id="platId" name="platId" required>
            <option value="">-- Choisir un plat --</option>
            <?php foreach ($platsDisponibles as $plat): ?>
                <option value="<?= $plat['id'] ?>">
                    <?= htmlspecialchars($plat['nom']) ?> (<?= number_format($plat['prix'], 2, ',', ' ') ?> &euro;)
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-principal">Ajouter</button>
    </form>

<?php endif; ?>

    <!-- Bouton supprimer le menu -->
    <form method="POST" style="margin-top: 1.5rem;">
        <input type="hidden" name="action" value="supprimer">
        <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer ce menu ?')">Supprimer ce menu</button>
    </form>

<p><a href="/pages/menus.php">Retour a la liste des menus</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
