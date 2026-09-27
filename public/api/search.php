<?php
declare(strict_types=1);

// Ricerca live del catalogo: restituisce i risultati come JSON (dati + HTML già renderizzato).

require __DIR__ . '/../../src/bootstrap.php';

$data = catalog_query($_GET);

json_response([
    'ok'    => true,
    'type'  => $data['type'],
    'total' => $data['total'],
    'page'  => $data['page'],
    'items' => array_map(fn(array $item) => [
        'id'        => $item['id'],
        'title'     => $item['title'],
        'year'      => $item['year'],
        'avg_score' => $item['avg_score'],
        'votes'     => $item['votes'],
        'url'       => url('detail.php?type=' . $data['type'] . '&id=' . $item['id']),
    ], $data['items']),
    'html'  => render('partials/results', $data),
]);
