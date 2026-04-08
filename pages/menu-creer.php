<?php
/**
 * Page de creation d'un menu
 * Affiche un formulaire (nom + createur) et traite le POST vers l'API Menus.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api.php';

$erreur = null;

// --- Traitement du formulaire (etape 11) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $createurId = intval($_POST['createurId'] ?? 0);

    if ($nom === '' || $createurId === 0) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $resultat = appelApi(API_MENUS . '/menus', 'POST', [
            'nom' => $nom,
            'createurId' => $createurId,
        ]);

        if ($resultat['code'] === 201) {
            // Redirection vers la page detail du menu cree
            $menuCree = $resultat['donnees'];
            header('Location: /pages/menu-detail.php?id=' . $menuCree['id']);
            exit;
        } else {
            $erreur = 'Erreur lors de la creation du menu (code ' . $resultat['code'] . ').';
        }
    }
}

// --- Chargement des utilisateurs pour le select ---
$resultatUtilisateurs = appelApi(API_PLATS_UTILISATEURS . '/utilisateurs');
$utilisateurs = $resultatUtilisateurs['donnees'];

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Creer un menu</h1>

<?php if ($resultatUtilisateurs['code'] === 0): ?>
    <p class="msg-erreur">Impossible de contacter l'API Utilisateurs. Verifiez que le serveur est lance (port 3003).</p>
<?php else: ?>

    <?php if ($erreur): ?>
        <p class="msg-erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="POST" class="formulaire">
        <label for="nom">Nom du menu</label>
        <input type="text" id="nom" name="nom" required
               value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">

        <label for="createurId">Createur</label>
        <select id="createurId" name="createurId" required>
            <option value="">-- Choisir un abonne --</option>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <option value="<?= $utilisateur['id'] ?>"
                    <?= (intval($_POST['createurId'] ?? 0) === $utilisateur['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-principal">Creer</button>
    </form>

<?php endif; ?>

<p><a href="/pages/menus.php">Retour a la liste des menus</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
