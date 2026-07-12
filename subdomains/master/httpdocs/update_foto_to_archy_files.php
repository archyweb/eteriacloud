<?php

$main_root = dirname(dirname(dirname(dirname(__FILE__))));
    $subdomain_root = dirname(__FILE__);
    require_once $main_root . '/private/_common/classes/load_classes.php';
    $config=require $subdomain_root.'/config.php';
    $app = new App($subdomain_root);

    $mysqli = new mysqli('localhost', $config['db']['user'], $config['db']['pass'], $config['db']['name']);
    if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
             exit();
    }
$fotoInserite = 0;

$sql = "
    SELECT
        id,
        foto_ufficiale,
        foto_ufficiale_secondaria
    FROM archy_scheda_catalogo
";

$result = $mysqli->query($sql);

while ($row = $result->fetch_assoc()) {

    $id = (int)$row['id'];

    $campiFoto = [
        'foto_ufficiale'            => $row['foto_ufficiale'],
        'foto_ufficiale_secondaria' => $row['foto_ufficiale_secondaria']
    ];

    foreach ($campiFoto as $campo => $valore) {

        if (empty($valore)) {
            continue;
        }

        $valore = $mysqli->real_escape_string($valore);

        $query = "
            INSERT INTO archy_files
            VALUES (
                NULL,
                {$id},
                'archy_art_catalog',
                '{$campo}',
                0,
                0,
                1,
                '{$valore}',
                '{$valore}.jpg',
                'img',
                'jpg',
                '',
                '',
                ''
            )
        ";

        if ($mysqli->query($query)) {
            $fotoInserite++;
        } else {
            echo "Errore ID {$id} ({$campo}): " . $mysqli->error . "<br>";
        }
    }
}

$pesoPerImmagine = 1024 * 1024; // 1 MB
$pesoTotale = $fotoInserite * $pesoPerImmagine;

echo "Foto inserite: {$fotoInserite}<br>";
echo "Peso totale: {$pesoTotale} bytes<br>";

$mysqli->close();