<?php
// Copia questo file in config.php e modifica i valori.

return [
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'anime_tracker',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    // Percorso URL in cui è esposta la cartella public/
    // es. '/tracker' con Alias Apache, '' con un VirtualHost dedicato.
    'base_url' => '/tracker',

    // true in sviluppo: mostra gli errori PHP a video
    'debug' => true,

    // Titoli per pagina nel catalogo
    'per_page' => 20,

    // Voti minimi perché un titolo entri nel ranking
    'min_votes_for_rank' => 1,
];
