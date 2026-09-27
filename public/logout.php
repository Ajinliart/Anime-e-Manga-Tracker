<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

// Il logout avviene solo via POST con token CSRF, per evitare logout forzati tramite link.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    logout_user();
    start_session();
    flash('info', 'Sei uscito dal tuo account.');
}

redirect('index.php');
