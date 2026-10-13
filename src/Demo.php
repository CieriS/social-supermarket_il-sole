<?php

/*
    Demo mode: anyone with the demo account can change the data, so every now
    and then it is put back as it was.
*/

declare(strict_types=1);

namespace IlSole\Demo;

use mysqli;
use RuntimeException;

/* Tables emptied by a reset, children before parents */
const TABLES = [
    'SkillsMembri', 'Membri', 'SituazioneSocioSanitaria', 'DispCollaborative', 'PatrAtt', 'PatrPass', 'Entrate',
    'Uscite', 'Punteggio', 'ValutazioneGriglia', 'StoricoTessere', 'TesseraRilasciata', 'Tessera', 'Richiedente',
    'Competenze', 'Administrator', 'TentativiAccesso', 'IndicatoriISEE', 'PunteggioMensile', 'Stallo',
    'RegolePunteggio',
];

/* Files that bring the rows back, in order */
const FILES = ['02-configuration.sql', '03-demo-data.sql'];

/* Pure: a reset is due if there never was one or the last is older than the interval */
function resetIsDue(?int $lastReset, int $now, int $intervalSeconds): bool
{
    return $lastReset === null || $lastReset > $now || $now - $lastReset >= $intervalSeconds;
}

function lastReset(mysqli $db): ?int
{
    $row = $db->query('SELECT MAX(UltimoRipristino) FROM StatoDemo')->fetch_row();
    return $row === null || $row[0] === null ? null : (int) $row[0];
}

/* Empties the data and configuration tables and loads the files of $directory again */
function resetData(mysqli $db, string $directory, int $now): void
{
    $sql = "SET FOREIGN_KEY_CHECKS = 0;\n";
    foreach (TABLES as $table) {
        $sql .= "DELETE FROM `$table`;\n";
    }
    $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    foreach (FILES as $file) {
        $content = @file_get_contents("$directory/$file");
        if ($content === false) {
            throw new RuntimeException("Demo data file missing: $file");
        }
        $sql .= $content . "\n";
    }
    $sql .= "DELETE FROM `StatoDemo`;\nINSERT INTO `StatoDemo` (`UltimoRipristino`) VALUES ($now);\n";

    $db->multi_query($sql);
    do {
        if ($result = $db->store_result()) {
            $result->free();
        }
    } while ($db->more_results() && $db->next_result());
}

/* Called when the login page is opened: at most one reset per interval */
function resetIfDue(mysqli $db, string $directory, int $now, int $intervalSeconds): bool
{
    if (!resetIsDue(lastReset($db), $now, $intervalSeconds)) {
        return false;
    }
    resetData($db, $directory, $now);
    return true;
}
