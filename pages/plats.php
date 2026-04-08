<?php
/**
 * Page de liste des plats
 * Appelle GET /plats et affiche le resultat dans un tableau HTML.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api.php';
require_once __DIR__ . '/../includes/header.php';

// Appel API
$resultat = appelApi(API_PLATS_UTILISATEURS . '/plats');
$plats = $resultat['donnees'];
?>

<h1>Les plats disponibles</h1>

<?php if ($resultat['code'] === 0): ?>
    <p class="msg-erreur">Impossible de contacter l'API Plats. Verifiez que le serveur est lance (port 3003).</p>
<?php elseif (empty($plats)): ?>
    <p>Aucun plat disponible pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plats as $plat): ?>
                <tr>
                    <td><?= htmlspecialchars($plat['nom']) ?></td>
                    <td><?= htmlspecialchars($plat['description']) ?></td>
                    <td><?= number_format($plat['prix'], 2, ',', ' ') ?> &euro;</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
