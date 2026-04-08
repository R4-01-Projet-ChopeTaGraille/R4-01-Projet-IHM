<?php
/**
 * Configuration de l'application IHM
 *
 * URLs de base des 3 APIs REST.
 * En dev  : JSON-Server (ports 3003, 3004, 3005)
 * En prod : APIs Jakarta EE (ports 8080, 8081, 8082)
 */

// --- Mode developpement (JSON-Server) ---
define('API_PLATS_UTILISATEURS', 'http://localhost:3003');
define('API_MENUS',              'http://localhost:3004');
define('API_COMMANDES',          'http://localhost:3006');

// --- Mode production (Jakarta EE) ---
// define('API_PLATS_UTILISATEURS', 'http://localhost:8080/plats-utilisateurs/api');
// define('API_MENUS',              'http://localhost:8081/menus/api');
// define('API_COMMANDES',          'http://localhost:8082/commandes/api');
