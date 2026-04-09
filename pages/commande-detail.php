<?php
/**
 * Page detail d'une commande
 * Appelle GET /commandes/{id} et affiche les informations completes.
 * Permet de modifier l'adresse et la date de livraison via PUT /commandes/{id}.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api.php';

$id = intval($_GET['id'] ?? 0);

if ($id === 0) {
    header('Location: /pages/commandes.php');
    exit;
}

$msgSucces = null;
$msgErreur = null;

// Etape 23 : Modifier adresse / date de livraison
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier') {
    $adresseLivraison = trim($_POST['adresseLivraison'] ?? '');
    $dateLivraison = trim($_POST['dateLivraison'] ?? '');

    if ($adresseLivraison === '' || $dateLivraison === '') {
        $msgErreur = 'Veuillez remplir tous les champs.';
    } else {
        $resultatPut = appelApi(API_COMMANDES . '/commandes/' . $id, 'PUT', [
            'adresseLivraison' => $adresseLivraison,
            'dateLivraison' => $dateLivraison,
        ]);

        if ($resultatPut['code'] === 200) {
            $msgSucces = 'Commande mise a jour.';
        } else {
            $msgErreur = 'Erreur lors de la modification (code ' . $resultatPut['code'] . ').';
        }
    }
}

// Etape 24 : Annuler la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'annuler') {
    appelApi(API_COMMANDES . '/commandes/' . $id, 'DELETE');
    header('Location: /pages/commandes.php');
    exit;
}

// Appel API (apres traitement pour avoir les donnees a jour)
$resultat = appelApi(API_COMMANDES . '/commandes/' . $id);
$commande = $resultat['donnees'];

require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($resultat['code'] === 0): ?>
    <p class="msg-erreur">Impossible de contacter l'API Commandes. Verifiez que le serveur est lance.</p>
<?php elseif ($resultat['code'] === 404 || $commande === null): ?>
    <p class="msg-erreur">Commande introuvable.</p>
<?php else: ?>

    <h1>Commande n°<?= intval($commande['id']) ?></h1>

    <?php if ($msgSucces): ?>
        <p class="msg-succes"><?= htmlspecialchars($msgSucces) ?></p>
    <?php endif; ?>
    <?php if ($msgErreur): ?>
        <p class="msg-erreur"><?= htmlspecialchars($msgErreur) ?></p>
    <?php endif; ?>

    <table>
        <tbody>
            <tr>
                <th>Abonne</th>
                <td>Abonne n°<?= intval($commande['abonneId']) ?></td>
            </tr>
            <tr>
                <th>Date de commande</th>
                <td><?= htmlspecialchars($commande['dateCommande']) ?></td>
            </tr>
            <tr>
                <th>Adresse de livraison</th>
                <td><?= htmlspecialchars($commande['adresseLivraison']) ?></td>
            </tr>
            <tr>
                <th>Date de livraison</th>
                <td><?= htmlspecialchars($commande['dateLivraison']) ?></td>
            </tr>
            <tr>
                <th>Prix total</th>
                <td><?= number_format($commande['prixTotal'], 2, ',', ' ') ?> &euro;</td>
            </tr>
        </tbody>
    </table>

    <h2>Lignes de commande</h2>

    <?php if (empty($commande['lignes'])): ?>
        <p>Aucune ligne dans cette commande.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Quantite</th>
                    <th>Prix unitaire</th>
                    <th>Prix ligne</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commande['lignes'] as $ligne): ?>
                    <tr>
                        <td><?= htmlspecialchars($ligne['menuNom']) ?></td>
                        <td><?= intval($ligne['quantite']) ?></td>
                        <td><?= number_format($ligne['prixUnitaire'], 2, ',', ' ') ?> &euro;</td>
                        <td><?= number_format($ligne['prixLigne'], 2, ',', ' ') ?> &euro;</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Etape 23 : Formulaire de modification -->
    <h2>Modifier la livraison</h2>
    <form method="POST" class="formulaire">
        <input type="hidden" name="action" value="modifier">

        <label for="adresseLivraison">Adresse de livraison</label>
        <textarea id="adresseLivraison" name="adresseLivraison" required><?= htmlspecialchars($commande['adresseLivraison']) ?></textarea>

        <label for="dateLivraison">Date de livraison</label>
        <input type="date" id="dateLivraison" name="dateLivraison" required
               value="<?= htmlspecialchars($commande['dateLivraison']) ?>">

        <button type="submit" class="btn btn-principal">Modifier</button>
    </form>

    <!-- Etape 24 : Annuler la commande -->
    <form method="POST" style="margin-top: 1.5rem;">
        <input type="hidden" name="action" value="annuler">
        <button type="submit" class="btn btn-danger" onclick="return confirm('Annuler cette commande ?')">Annuler la commande</button>
    </form>

<?php endif; ?>

<p><a href="/pages/commandes.php">Retour a la liste des commandes</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
