<?php
declare(strict_types=1);

/*
 * Protezione CSRF (Cross-Site Request Forgery).
 * Ogni form del sito invia un codice segreto casuale, salvato nella sessione.
 * Un altro sito non può leggere le nostre pagine, quindi non conosce il codice:
 * le richieste "falsificate" che partono da lì vengono rifiutate da csrf_check().
 */

/** Restituisce il codice segreto della sessione (lo crea la prima volta). */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Campo nascosto da mettere dentro ogni <form method="post">. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Verifica il codice inviato dal form (o dall'header X-CSRF-Token per le chiamate fetch). */
function csrf_check(): bool
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    // hash_equals confronta le stringhe in tempo costante
    return is_string($token) && $token !== '' && hash_equals(csrf_token(), $token);
}
