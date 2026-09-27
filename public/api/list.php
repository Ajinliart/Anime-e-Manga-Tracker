<?php
declare(strict_types=1);

/**
 * Aggiunge / aggiorna / rimuove un titolo dalla lista personale.
 * Risponde in JSON se la richiesta ha "Accept: application/json" (fetch da app.js),
 * altrimenti reindirizza alla pagina di provenienza (fallback senza JavaScript).
 */

require __DIR__ . '/../../src/bootstrap.php';

$type = valid_type($_POST['type'] ?? null);
$id = int_param($_POST, 'id');
$returnTo = match (true) {
    ($_POST['return'] ?? '') === 'profile' => 'profile.php' . ($type ? '?type=' . $type : ''),
    $type !== null && $id !== null         => 'detail.php?type=' . $type . '&id=' . $id,
    default                                => 'index.php',
};

/** Termina con un errore, in JSON o come messaggio flash. */
function fail(string $message, int $status, string $returnTo): never
{
    if (wants_json()) {
        json_response(['ok' => false, 'error' => $message], $status);
    }
    flash('error', $message);
    redirect($returnTo);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail('Metodo non consentito.', 405, $returnTo);
}

$user = require_login($returnTo);

if (!csrf_check()) {
    fail('Sessione scaduta, ricarica la pagina e riprova.', 403, $returnTo);
}
if ($type === null || $id === null) {
    fail('Richiesta non valida.', 400, $returnTo);
}

$titles = new TitleRepository(db(), $type);
$title = $titles->find($id);
if ($title === null) {
    fail('Titolo non trovato.', 404, 'index.php');
}

$lists = new ListRepository(db());
$userId = (int) $user['id'];
$action = $_POST['action'] ?? 'save';

if ($action === 'remove') {
    $lists->remove($userId, $type, $id);
    $message = 'Titolo rimosso dalla lista.';
    $entry = null;
} elseif ($action === 'save') {
    $status = $_POST['status'] ?? '';
    if (!in_array($status, STATUSES, true)) {
        fail('Stato non valido.', 422, $returnTo);
    }

    $score = null;
    if (($_POST['score'] ?? '') !== '') {
        $score = filter_var($_POST['score'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]);
        if ($score === false) {
            fail('Il voto deve essere un numero da 1 a 10.', 422, $returnTo);
        }
    }

    $total = $title[TYPES[$type]['total']] !== null ? (int) $title[TYPES[$type]['total']] : null;
    $progress = null;
    if (($_POST['progress'] ?? '') !== '') {
        $progress = filter_var($_POST['progress'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => $total ?? 65535]]);
        if ($progress === false) {
            fail('Progresso non valido' . ($total !== null ? " (massimo $total)." : '.'), 422, $returnTo);
        }
    }
    if ($status === 'completed' && $total !== null) {
        $progress = $total;
    }

    $wasInList = $lists->get($userId, $type, $id) !== null;
    $lists->save($userId, $type, $id, $status, $score, $progress);
    $message = $wasInList ? 'Lista aggiornata.' : 'Aggiunto alla tua lista!';
    $entry = $lists->get($userId, $type, $id);
} else {
    fail('Azione non valida.', 400, $returnTo);
}

if (wants_json()) {
    $updated = $titles->find($id);
    $rank = $titles->rankOf($id);
    json_response([
        'ok'        => true,
        'message'   => $message,
        'in_list'   => $entry !== null,
        'entry'     => $entry ? [
            'status'       => $entry['status'],
            'status_label' => status_label($type, $entry['status']),
            'score'        => $entry['score'] !== null ? (int) $entry['score'] : null,
            'progress'     => $entry['progress'] !== null ? (int) $entry['progress'] : null,
        ] : null,
        'avg_score' => format_score($updated['avg_score']),
        'votes'     => $updated['votes'],
        'rank'      => $rank !== null ? '#' . $rank : '—',
    ]);
}

flash('success', $message);
redirect($returnTo);
