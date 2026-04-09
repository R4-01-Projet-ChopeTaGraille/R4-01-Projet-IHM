<?php
/**
 * Page de creation d'une commande
 * Formulaire : abonne, menus avec quantites, adresse et date de livraison.
 * Traite le POST vers l'API Commandes.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api.php';

$erreur = null;

// --- Traitement du formulaire (etape 21) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $abonneId = intval($_POST['abonneId'] ?? 0);
    $adresseLivraison = trim($_POST['adresseLivraison'] ?? '');
    $dateLivraison = trim($_POST['dateLivraison'] ?? '');
    $quantites = $_POST['quantite'] ?? [];

    // Construire le tableau de lignes (menuId + quantite) en ignorant les quantites a 0
    $lignes = [];
    foreach ($quantites as $menuId => $qte) {
        $qte = intval($qte);
        if ($qte > 0) {
            $lignes[] = [
                'menuId' => intval($menuId),
                'quantite' => $qte,
            ];
        }
    }

    // Validation
    if ($abonneId === 0) {
        $erreur = 'Veuillez selectionner un abonne.';
    } elseif (empty($lignes)) {
        $erreur = 'Veuillez selectionner au moins un menu avec une quantite superieure a 0.';
    } elseif ($adresseLivraison === '') {
        $erreur = 'Veuillez saisir une adresse de livraison.';
    } elseif ($dateLivraison === '') {
        $erreur = 'Veuillez saisir une date de livraison.';
    } else {
        $resultat = appelApi(API_COMMANDES . '/commandes', 'POST', [
            'abonneId' => $abonneId,
            'adresseLivraison' => $adresseLivraison,
            'dateLivraison' => $dateLivraison,
            'lignes' => $lignes,
        ]);

        if ($resultat['code'] === 201) {
            $commandeCree = $resultat['donnees'];
            header('Location: /pages/commande-detail.php?id=' . $commandeCree['id']);
            exit;
        } else {
            $erreur = 'Erreur lors de la creation de la commande (code ' . $resultat['code'] . ').';
        }
    }
}

// --- Chargement des donnees pour le formulaire ---
$resultatUtilisateurs = appelApi(API_PLATS_UTILISATEURS . '/utilisateurs');
$utilisateurs = $resultatUtilisateurs['donnees'] ?? [];

$resultatMenus = appelApi(API_MENUS . '/menus');
$menus = $resultatMenus['donnees'] ?? [];

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Nouvelle commande</h1>

<?php if ($resultatUtilisateurs['code'] === 0 || $resultatMenus['code'] === 0): ?>
    <p class="msg-erreur">Impossible de contacter les APIs. Verifiez que les serveurs sont lances.</p>
<?php else: ?>

    <?php if ($erreur): ?>
        <p class="msg-erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="POST" class="formulaire" style="max-width: 800px;">

        <!-- Etape 18 : Selection de l'abonne -->
        <label for="abonneId">Abonne</label>
        <select id="abonneId" name="abonneId" required>
            <option value="">-- Choisir un abonne --</option>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <option value="<?= intval($utilisateur['id']) ?>"
                    <?= (intval($_POST['abonneId'] ?? 0) === intval($utilisateur['id'])) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Etape 19 : Selection des menus et quantites -->
        <label>Menus et quantites</label>
        <table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Prix</th>
                    <th>Quantite</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $menu): ?>
                    <tr>
                        <td><?= htmlspecialchars($menu['nom']) ?></td>
                        <td><?= number_format($menu['prixTotal'], 2, ',', ' ') ?> &euro;</td>
                        <td>
                            <input type="number" name="quantite[<?= intval($menu['id']) ?>]" min="0"
                                   value="<?= intval($_POST['quantite'][$menu['id']] ?? 0) ?>"
                                   style="width: 80px;">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Etape 20 : Adresse et date de livraison -->
        <label for="adresseLivraison">Adresse de livraison</label>
        <textarea id="adresseLivraison" name="adresseLivraison" required><?= htmlspecialchars($_POST['adresseLivraison'] ?? '') ?></textarea>

        <label for="dateLivraison">Date de livraison</label>
        <input type="date" id="dateLivraison" name="dateLivraison" required
               value="<?= htmlspecialchars($_POST['dateLivraison'] ?? '') ?>">

        <button type="submit" class="btn btn-principal">Valider la commande</button>
    </form>

<?php endif; ?>

<p><a href="/pages/commandes.php">Retour a la liste des commandes</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
