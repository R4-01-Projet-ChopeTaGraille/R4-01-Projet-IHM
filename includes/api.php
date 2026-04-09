<?php
/**
 * Fonction generique d'appel API avec cURL
 *
 * Envoie une requete HTTP (GET, POST, PUT, DELETE) a une URL donnee
 * et retourne la reponse decodee en tableau PHP.
 *
 * @param string      $url     URL complete de l'endpoint
 * @param string      $methode Methode HTTP (GET, POST, PUT, DELETE)
 * @param array|null  $donnees Donnees a envoyer en JSON (POST, PUT)
 * @return array      ['code' => int, 'donnees' => mixed]
 */
function appelApi(string $url, string $methode = 'GET', ?array $donnees = null): array
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $methode);

    // Envoi du corps JSON pour POST et PUT
    if ($donnees !== null && in_array($methode, ['POST', 'PUT'])) {
        $json = json_encode($donnees);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json),
        ]);
    }

    $reponse = curl_exec($ch);
    $codeHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erreur = curl_error($ch);
    curl_close($ch);

    // Erreur reseau (API injoignable)
    if ($reponse === false) {
        return [
            'code' => 0,
            'donnees' => null,
            'erreur' => $erreur,
        ];
    }

    return [
        'code' => $codeHttp,
        'donnees' => json_decode($reponse, true),
    ];
}
