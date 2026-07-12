<?php

require_once __DIR__ . '/private/_common/classes/ArchyPassport/PassportService.php';

// qui usa la tua connessione PDO già esistente
// esempio:
// require_once __DIR__ . '/config/bootstrap.php';

$catalogId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($catalogId <= 0) {
    http_response_code(404);
    exit('Opera non trovata');
}

$passportService = new PassportService(
    $pdo,
    'https://passport.archyart.it'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'enable') {
        $passportService->enable($catalogId);
    }

    if ($action === 'disable') {
        $passportService->disable($catalogId);
    }

    if ($action === 'regenerate') {
        $passportService->regenerateToken($catalogId);
    }

    header('Location: passport_manage.php?id=' . $catalogId);
    exit;
}

$passport = $passportService->getOrCreate($catalogId);
$publicUrl = $passportService->getPublicUrl($passport['public_token']);
?>

<div class="container py-4">

    <h1>Passaporto Digitale</h1>

    <p>
        Gestisci l’accesso pubblico sicuro alla scheda completa dell’opera.
    </p>

    <div class="card mb-3">
        <div class="card-body">

            <p>
                <strong>Stato:</strong>
                <?= !empty($passport['enabled']) ? 'Attivo' : 'Disattivato' ?>
            </p>

            <p>
                <strong>URL pubblico:</strong><br>
                <input 
                    type="text" 
                    class="form-control" 
                    value="<?= htmlspecialchars($publicUrl) ?>" 
                    readonly
                    onclick="this.select();"
                >
            </p>

            <p>
                <a 
                    href="<?= htmlspecialchars($publicUrl) ?>" 
                    target="_blank" 
                    class="btn btn-outline-secondary"
                >
                    Apri anteprima
                </a>
            </p>

            <form method="post" class="d-inline">
                <?php if (!empty($passport['enabled'])): ?>
                    <button 
                        type="submit" 
                        name="action" 
                        value="disable" 
                        class="btn btn-warning"
                    >
                        Disabilita
                    </button>
                <?php else: ?>
                    <button 
                        type="submit" 
                        name="action" 
                        value="enable" 
                        class="btn btn-success"
                    >
                        Abilita
                    </button>
                <?php endif; ?>
            </form>

            <form method="post" class="d-inline" onsubmit="return confirm('Rigenerare il token? Il vecchio QR non funzionerà più.');">
                <button 
                    type="submit" 
                    name="action" 
                    value="regenerate" 
                    class="btn btn-danger"
                >
                    Rigenera token
                </button>
            </form>

        </div>
    </div>

</div>