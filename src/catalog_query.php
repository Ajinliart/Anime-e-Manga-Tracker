<?php
declare(strict_types=1);

/**
 * Legge e valida i parametri del catalogo da $_GET ed esegue la ricerca.
 * Condiviso da public/catalog.php e public/api/search.php.
 */
function catalog_query(array $input): array
{
    $type = valid_type($input['type'] ?? 'anime') ?? 'anime';

    $q = trim((string) ($input['q'] ?? ''));
    $q = mb_substr($q, 0, 100);

    $genre = int_param($input, 'genre');
    $sort = (string) ($input['sort'] ?? 'title');
    if (!isset(TitleRepository::SORTS[$sort])) {
        $sort = 'title';
    }
    $page = int_param($input, 'page') ?? 1;
    $perPage = (int) config('per_page');

    $repo = new TitleRepository(db(), $type);
    $result = $repo->search($q, $genre, $sort, $page, $perPage);

    return [
        'type'    => $type,
        'repo'    => $repo,
        'items'   => $result['items'],
        'total'   => $result['total'],
        'page'    => $page,
        'perPage' => $perPage,
        'filters' => ['q' => $q, 'genre' => $genre, 'sort' => $sort],
    ];
}
