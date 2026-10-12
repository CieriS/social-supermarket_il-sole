<?php

/*
    Reads and writes applications in the database. The record has the shape
    built by IlSole\Record\recordFromForm().
*/

declare(strict_types=1);

namespace IlSole\Store;

use DateTimeImmutable;
use mysqli;
use Throwable;

use function IlSole\Form\applicationFromForm;
use function IlSole\Record\formFromRecord;

use function IlSole\Rules\rulesFromRows;
use function IlSole\Scoring\scoreApplication;

use const IlSole\Form\ASSESSMENT_FIELDS;
use const IlSole\Record\ASSETS;
use const IlSole\Record\EVENTS;
use const IlSole\Record\EXPENSES;
use const IlSole\Record\INCOME;
use const IlSole\Record\LIABILITIES;

const APPLICANT_COLUMNS = [
    'Cognome', 'Nome', 'LuogoNascita', 'DataNascita', 'Cittadinanza', 'InItaliaDallAnno', 'Residenza', 'Via',
    'TipoAlloggio', 'DocumentoIdent', 'DocumentoImmig', 'Telefono', 'Mail', 'ServiziSociali',
    'ComeHaConosciutoEmporio', 'nMembri', 'ISEE',
];

const MEMBER_COLUMNS = [
    'NomeCognome', 'LuogoNascita', 'DataNascita', 'Parentela', 'Occupazione', 'TitoloDiStudio', 'Competenze',
    'ConoscenzaLingua', 'Patente', 'AttualeAtt', 'Presso', 'Termine', 'SenzaLavoroDa',
];

const SCORE_COLUMNS = [
    'PunteggioISEE', 'PunteggioPresenzaMinori', 'PunteggioDisoccupazione', 'PunteggioInvalidi',
    'PunteggioSituazioneDebitoria', 'PunteggioBenefit', 'PunteggioTotale',
];

/* Money tables: record key => [table, row labels, has an expiry date] */
const MONEY_TABLES = [
    'assets' => ['PatrAtt', ASSETS, false],
    'liabilities' => ['PatrPass', LIABILITIES, true],
    'income' => ['Entrate', INCOME, true],
    'expenses' => ['Uscite', EXPENSES, true],
];

function rows(mysqli $db, string $sql, array $params = []): array
{
    return $db->execute_query($sql, $params)->fetch_all(MYSQLI_ASSOC);
}

function loadRules(mysqli $db): array
{
    return rulesFromRows(rows($db, 'SELECT Chiave, Punti FROM RegolePunteggio'));
}

function loadIseeBrackets(mysqli $db): array
{
    return rows($db, 'SELECT ISEEmin, ISEEmax, Punteggio FROM IndicatoriISEE');
}

function applicantExists(mysqli $db, string $taxCode): bool
{
    return rows($db, 'SELECT 1 FROM Richiedente WHERE Cod_Fiscale = ?', [$taxCode]) !== [];
}

function placeholders(int $count): string
{
    return implode(', ', array_fill(0, $count, '?'));
}

function insert(mysqli $db, string $table, array $values): void
{
    $columns = implode(', ', array_keys($values));
    $db->execute_query(
        "INSERT INTO $table ($columns) VALUES (" . placeholders(count($values)) . ')',
        array_values($values)
    );
}

function loadRecord(mysqli $db, string $taxCode): ?array
{
    $applicant = rows($db, 'SELECT * FROM Richiedente WHERE Cod_Fiscale = ?', [$taxCode])[0] ?? null;
    if ($applicant === null) {
        return null;
    }

    $members = [];
    foreach (rows($db, 'SELECT * FROM Membri WHERE CodFamiglia = ? ORDER BY nMembro', [$taxCode]) as $row) {
        $members[(int) $row['nMembro']] = array_intersect_key($row, array_flip(MEMBER_COLUMNS));
    }

    $events = [];
    foreach (rows($db, 'SELECT * FROM SituazioneSocioSanitaria WHERE CodFamiglia = ? ORDER BY N', [$taxCode]) as $row) {
        $events[(int) $row['N']] = ['Anno' => (int) $row['Anno'], 'DocAll' => (string) $row['DocAll']];
    }

    $record = [
        'applicant' => ['Cod_Fiscale' => $applicant['Cod_Fiscale']]
            + array_intersect_key($applicant, array_flip(APPLICANT_COLUMNS)),
        'members' => $members,
        'events' => $events,
        'collaboration' => rows(
            $db,
            'SELECT AmbitoFormativo, AmbitoLavorativo, AmbitoSociale FROM DispCollaborative WHERE CodFamiglia = ?',
            [$taxCode]
        )[0] ?? [],
        'assessment' => rows($db, 'SELECT * FROM ValutazioneGriglia WHERE CodFamiglia = ?', [$taxCode])[0] ?? [],
    ];
    $record['applicant']['nMembri'] = (int) $applicant['nMembri'];

    foreach (MONEY_TABLES as $key => [$table, , $hasDate]) {
        $record[$key] = [];
        foreach (rows($db, "SELECT * FROM $table WHERE CodFamiglia = ? ORDER BY N", [$taxCode]) as $row) {
            $record[$key][(int) $row['N']] = ['Valore' => (float) $row['Valore']]
                + ($hasDate ? ['Scadenza' => $row['Scadenza']] : []);
        }
    }

    return $record;
}

/*
    Stores the application and its scores in one transaction. Members are
    updated in place, so the skills linked to them survive; the rows of the
    other tables are replaced.
*/
function saveRecord(mysqli $db, array $record, array $scores, bool $isNew): void
{
    $taxCode = $record['applicant']['Cod_Fiscale'];
    $applicant = array_intersect_key($record['applicant'], array_flip(APPLICANT_COLUMNS));

    $db->begin_transaction();
    try {
        if ($isNew) {
            insert($db, 'Richiedente', ['Cod_Fiscale' => $taxCode] + $applicant);
        } else {
            $assignments = implode(', ', array_map(fn (string $column) => "$column = ?", array_keys($applicant)));
            $db->execute_query(
                "UPDATE Richiedente SET $assignments WHERE Cod_Fiscale = ?",
                [...array_values($applicant), $taxCode]
            );
        }

        saveScores($db, $taxCode, $scores);

        $db->execute_query('DELETE FROM ValutazioneGriglia WHERE CodFamiglia = ?', [$taxCode]);
        insert(
            $db,
            'ValutazioneGriglia',
            ['CodFamiglia' => $taxCode]
                + array_intersect_key($record['assessment'], array_flip([...ASSESSMENT_FIELDS, 'NoteCommissione']))
        );

        $kept = array_keys($record['members']);
        $db->execute_query(
            'DELETE FROM Membri WHERE CodFamiglia = ?'
                . ($kept === [] ? '' : ' AND nMembro NOT IN (' . placeholders(count($kept)) . ')'),
            [$taxCode, ...$kept]
        );
        $updates = implode(', ', array_map(fn (string $column) => "$column = VALUES($column)", MEMBER_COLUMNS));
        foreach ($record['members'] as $number => $member) {
            $values = ['nMembro' => $number, 'CodFamiglia' => $taxCode]
                + array_intersect_key($member, array_flip(MEMBER_COLUMNS));
            $db->execute_query(
                'INSERT INTO Membri (' . implode(', ', array_keys($values)) . ') VALUES ('
                    . placeholders(count($values)) . ") ON DUPLICATE KEY UPDATE $updates",
                array_values($values)
            );
        }

        $db->execute_query('DELETE FROM SituazioneSocioSanitaria WHERE CodFamiglia = ?', [$taxCode]);
        foreach ($record['events'] as $number => $event) {
            insert($db, 'SituazioneSocioSanitaria', [
                'N' => $number, 'CodFamiglia' => $taxCode, 'Evento' => EVENTS[$number],
                'Anno' => $event['Anno'], 'DocAll' => $event['DocAll'],
            ]);
        }

        $db->execute_query('DELETE FROM DispCollaborative WHERE CodFamiglia = ?', [$taxCode]);
        insert($db, 'DispCollaborative', ['CodFamiglia' => $taxCode] + $record['collaboration']);

        foreach (MONEY_TABLES as $key => [$table, $labels, $hasDate]) {
            $db->execute_query("DELETE FROM $table WHERE CodFamiglia = ?", [$taxCode]);
            foreach ($record[$key] as $number => $money) {
                insert($db, $table, [
                    'N' => $number, 'CodFamiglia' => $taxCode, 'Elemento' => $labels[$number],
                    'Valore' => $money['Valore'],
                ] + ($hasDate ? ['Scadenza' => $money['Scadenza']] : []));
            }
        }

        $db->commit();
    } catch (Throwable $error) {
        $db->rollback();
        throw $error;
    }
}

function saveScores(mysqli $db, string $taxCode, array $scores): void
{
    $db->execute_query('DELETE FROM Punteggio WHERE CodFamiglia = ?', [$taxCode]);
    insert($db, 'Punteggio', ['CodFamiglia' => $taxCode] + array_intersect_key($scores, array_flip(SCORE_COLUMNS)));
}

/*
    Scores every stored application again, with the points and the ISEE
    brackets configured now and the ages of $today. Returns how many were
    scored. All or nothing: one application that cannot be scored leaves
    every score as it was.
*/
function recalculateScores(mysqli $db, DateTimeImmutable $today): int
{
    $rules = loadRules($db);
    $brackets = loadIseeBrackets($db);
    $taxCodes = array_column(rows($db, 'SELECT Cod_Fiscale FROM Richiedente'), 'Cod_Fiscale');

    $db->begin_transaction();
    try {
        foreach ($taxCodes as $taxCode) {
            $application = applicationFromForm(formFromRecord(loadRecord($db, $taxCode)));
            saveScores($db, $taxCode, scoreApplication($application, $brackets, $today, $rules));
        }
        $db->commit();
    } catch (Throwable $error) {
        $db->rollback();
        throw $error;
    }

    return count($taxCodes);
}
