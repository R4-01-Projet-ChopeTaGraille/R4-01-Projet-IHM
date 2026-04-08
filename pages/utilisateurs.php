<?php
/**
 * Page de liste des utilisateurs (abonnes)
 * Appelle GET /utilisateurs et affiche le resultat dans un tableau HTML.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api.php';
require_once __DIR__ . '/../includes/header.php';

// Appel API
$resultat = appelApi(API_PLATS_UTILISATEURS . '/utilisateurs');
$utilisateurs = $resultat['donnees'];
?>

<h1>Les abonnes</h1>

<?php if ($resultat['code'] === 0): ?>
    <p class="msg-erreur">Impossible de contacter l'API Utilisateurs. Verifiez que le serveur est lance (port 3003).</p>
<?php elseif (empty($utilisateurs)): ?>
    <p>Aucun abonne inscrit pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Email</th>
                <th>Adresse</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <tr>
                    <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
                    <td><?= htmlspecialchars($utilisateur['prenom']) ?></td>
                    <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                    <td><?= htmlspecialchars($utilisateur['adresse']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
