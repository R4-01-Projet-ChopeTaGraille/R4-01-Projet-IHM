<?php
/**
 * Page de liste des commandes
 * Appelle GET /commandes et affiche le resultat dans un tableau HTML.
 * Permet de filtrer par abonne via GET /commandes?abonneId=X.
 * Permet de supprimer une commande via DELETE /commandes/{id}.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api.php';

// Etape 24 : Suppression d'une commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer') {
    $commandeId = intval($_POST['commandeId'] ?? 0);
    if ($commandeId > 0) {
        appelApi(API_COMMANDES . '/commandes/' . $commandeId, 'DELETE');
    }
    header('Location: /pages/commandes.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';

// Charger les utilisateurs pour le filtre
$resultatUtilisateurs = appelApi(API_PLATS_UTILISATEURS . '/utilisateurs');
$utilisateurs = $resultatUtilisateurs['donnees'] ?? [];

// Appel API avec filtre optionnel par abonne
$abonneIdFiltre = intval($_GET['abonneId'] ?? 0);
$urlCommandes = API_COMMANDES . '/commandes';
if ($abonneIdFiltre > 0) {
    $urlCommandes .= '?abonneId=' . $abonneIdFiltre;
}
$resultat = appelApi($urlCommandes);
$commandes = $resultat['donnees'];
?>

<h1>Les commandes</h1>

<p><a href="/pages/commande-creer.php" class="btn btn-principal">Nouvelle commande</a></p>

<!-- Filtre par abonne -->
<form method="GET" class="formulaire" style="margin-bottom: 1.5rem;">
    <label for="abonneId">Filtrer par abonne</label>
    <select id="abonneId" name="abonneId" onchange="this.form.submit()">
        <option value="">-- Tous les abonnes --</option>
        <?php foreach ($utilisateurs as $utilisateur): ?>
            <option value="<?= intval($utilisateur['id']) ?>"
                <?= ($abonneIdFiltre === intval($utilisateur['id'])) ? 'selected' : '' ?>>
                <?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($resultat['code'] === 0): ?>
    <p class="msg-erreur">Impossible de contacter l'API Commandes. Verifiez que le serveur est lance (port 3005).</p>
<?php elseif (empty($commandes)): ?>
    <p>Aucune commande pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Date de commande</th>
                <th>Adresse de livraison</th>
                <th>Date de livraison</th>
                <th>Prix total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $commande): ?>
                <tr>
                    <td><?= intval($commande['id']) ?></td>
                    <td><?= htmlspecialchars($commande['dateCommande']) ?></td>
                    <td><?= htmlspecialchars($commande['adresseLivraison']) ?></td>
                    <td><?= htmlspecialchars($commande['dateLivraison']) ?></td>
                    <td><?= number_format($commande['prixTotal'], 2, ',', ' ') ?> &euro;</td>
                    <td>
                        <a href="/pages/commande-detail.php?id=<?= intval($commande['id']) ?>" class="btn btn-secondaire">Voir</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="commandeId" value="<?= intval($commande['id']) ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Annuler cette commande ?')">Annuler</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
