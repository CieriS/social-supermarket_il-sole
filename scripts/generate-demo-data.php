<?php

/*
    Writes db/02-demo-data.sql: an administrator account, a set of invented
    families, some shopping cards. Every family goes through the same code as
    a submitted form, so what is stored and how it is scored is exactly what
    the application would produce.

    Usage (from the web container): php scripts/generate-demo-data.php > db/02-demo-data.sql
    Scores use the ages of the day the file is generated; "Ricalcola" in the
    configuration page brings them up to date. Card dates are relative to the
    day the file is loaded.
*/

declare(strict_types=1);

require __DIR__ . '/../src/Html.php';
require __DIR__ . '/../src/Scoring.php';
require __DIR__ . '/../src/ApplicationForm.php';
require __DIR__ . '/../src/Rules.php';
require __DIR__ . '/../src/ApplicationRecord.php';
require __DIR__ . '/../src/ApplicationStore.php';

use function IlSole\Form\applicationFromForm;
use function IlSole\Record\formErrors;
use function IlSole\Record\recordFromForm;
use function IlSole\Scoring\defaultRules;
use function IlSole\Scoring\scoreApplication;

use const IlSole\Record\ASSETS;
use const IlSole\Record\EVENTS;
use const IlSole\Record\EXPENSES;
use const IlSole\Record\INCOME;
use const IlSole\Record\LIABILITIES;
use const IlSole\Store\APPLICANT_COLUMNS;
use const IlSole\Store\MEMBER_COLUMNS;
use const IlSole\Store\SCORE_COLUMNS;

const ISEE_BRACKETS = [
    ['ISEEmin' => 0, 'ISEEmax' => 1500, 'Punteggio' => 100],
    ['ISEEmin' => 1501, 'ISEEmax' => 3000, 'Punteggio' => 80],
    ['ISEEmin' => 3001, 'ISEEmax' => 4500, 'Punteggio' => 60],
    ['ISEEmin' => 4501, 'ISEEmax' => 6000, 'Punteggio' => 40],
    ['ISEEmin' => 6001, 'ISEEmax' => 7500, 'Punteggio' => 20],
];

/* Demo account: emporiosolidale / IlSole */
const ADMINISTRATOR_HASH = '$2y$12$FU6AcI57V/.0oH9moZSGzeF8BiTahpHuvuTqfYA9jCoJGmYRgZVyO';

/*
    One family per entry. Members: [name, birth date, relation, occupation,
    then optionally title of study, skills, languages, driving licence,
    current job, employer]. The first member is the applicant.
    Keys of events, income, expenses, assets and liabilities are the row
    numbers of the form.
*/
function families(): array
{
    $c = 'Casalecchio di Reno';
    $z = 'Zola Predosa';
    $s = 'Sasso Marconi';
    $v = 'Valsamoggia';
    $m = 'Monte San Pietro';

    return [
        ['Ferri', $c, 'Via dei Mille 12', 'Affitto Privato', 900, [
            ['Elena Ferri', '14/03/1986', 'Capofamiglia', 'Disoccupato', 'Diploma', 'Sarta', 'Italiano', 'B'],
            ['Tommaso Ferri', '02/09/2014', 'Figlio', ''],
            ['Giulia Ferri', '21/11/2017', 'Figlio', ''],
        ], 'events' => [1 => 2021, 7 => 2025], 'expenses' => [1 => 520, 3 => 140], 'income' => [7 => 250],
            'social' => 'Assistenza Sociale', 'how' => 'Servizi sociali', 'training' => 'Corso di taglio e cucito'],
        ['Benassi', $c, 'Via Porrettana 88', 'Popolare', 2400, [
            ['Marco Benassi', '30/06/1979', 'Capofamiglia', 'Disoccupato', 'Licenza Media', 'Magazziniere', 'Italiano', 'C'],
            ['Laura Conti', '11/01/1982', 'Coniuge', 'Semioccupato', 'Diploma', 'Pulizie', 'Italiano', 'B', 'Addetta Pulizie', 'Cooperativa'],
            ['Sara Benassi', '05/05/2012', 'Figlio', ''],
            ['Luca Benassi', '19/08/2015', 'Figlio', ''],
            ['Anna Benassi', '27/02/2020', 'Figlio', ''],
        ], 'income' => [1 => 480, 5 => 500], 'expenses' => [1 => 210, 3 => 160], 'social' => 'Si', 'how' => 'Parrocchia',
            'work' => 'Disponibile per carico e scarico merci'],
        ['El Amrani', $z, 'Via Risorgimento 5', 'Affitto Privato', 1800, [
            ['Youssef El Amrani', '08/12/1984', 'Capofamiglia', 'Semioccupato', 'Diploma', 'Muratore', 'Arabo, Francese, Italiano', 'B', 'Manovale', 'Impresa Edile'],
            ['Samira El Amrani', '17/04/1988', 'Coniuge', 'Disoccupato', 'Licenza Media', 'Cucina', 'Arabo, Italiano', ''],
            ['Adam El Amrani', '03/10/2016', 'Figlio', ''],
            ['Lina El Amrani', '22/06/2019', 'Figlio', ''],
        ], 'citizenship' => 'Marocchina', 'inItaly' => 2009, 'immigration' => 'PS12345678', 'income' => [1 => 700],
            'expenses' => [1 => 600, 3 => 150], 'events' => [7 => 2024, 11 => 2025], 'social' => 'No',
            'how' => 'Passaparola', 'assessment' => ['AltriDebiti' => 4]],
        ['Galli', $s, 'Via Stazione 3', 'Popolare', 0, [
            ['Rita Galli', '25/07/1958', 'Capofamiglia', 'Altro', 'Licenza Elementare', '', 'Italiano', ''],
        ], 'income' => [3 => 520], 'expenses' => [1 => 90, 4 => 110], 'social' => 'Assistenza Sociale', 'how' => 'Medico di base',
            'assessment' => ['InvaliditaTotaleAdulti' => 1], 'events' => [4 => 2019], 'notes' => 'Vive sola, consegna a domicilio'],
        ['Okafor', $c, 'Via Marconi 41', 'Emergenza Abitativa', 600, [
            ['Grace Okafor', '19/02/1993', 'Capofamiglia', 'Disoccupato', 'Diploma', 'Parrucchiera', 'Inglese, Italiano', ''],
            ['Daniel Okafor', '07/07/2021', 'Figlio', ''],
        ], 'citizenship' => 'Nigeriana', 'inItaly' => 2017, 'immigration' => 'PS23456789', 'events' => [1 => 2022, 10 => 2025],
            'social' => 'Assistenza Sociale', 'how' => 'Centro di ascolto', 'training' => 'Corso di italiano'],
        ['Marchetti', $v, 'Via Cassola 27', 'Proprieta', 5200, [
            ['Paolo Marchetti', '12/10/1971', 'Capofamiglia', 'Disoccupato', 'Diploma', 'Tornitore', 'Italiano', 'B'],
            ['Silvia Neri', '04/04/1974', 'Coniuge', 'Altro', 'Diploma', 'Segreteria', 'Italiano, Inglese', 'B', 'Impiegata', 'Studio'],
            ['Matteo Marchetti', '16/01/2004', 'Figlio', 'Disoccupato', 'Diploma', 'Informatica', 'Italiano, Inglese', 'B'],
        ], 'income' => [1 => 1250, 4 => 400], 'expenses' => [2 => 620, 3 => 190], 'liabilities' => [2 => 68000],
            'assets' => [1 => 95000, 2 => 3000], 'events' => [8 => 2025], 'social' => 'No', 'how' => 'Sito del Comune'],
        ['Popescu', $z, 'Via Roma 102', 'Affitto Privato', 3100, [
            ['Ioana Popescu', '28/08/1981', 'Capofamiglia', 'Semioccupato', 'Diploma', 'Assistenza anziani', 'Rumeno, Italiano', 'B', 'Badante', 'Privato'],
            ['Andrei Popescu', '09/03/2010', 'Figlio', ''],
            ['Maria Popescu', '15/12/1955', 'Altro', 'Altro', '', '', 'Rumeno', ''],
        ], 'citizenship' => 'Rumena', 'inItaly' => 2011, 'income' => [1 => 650, 2 => 180], 'expenses' => [1 => 550, 3 => 130],
            'assessment' => ['InvaliditaGraveAdulti' => 1], 'events' => [3 => 2023], 'social' => 'Area', 'how' => 'Passaparola'],
        ['Rinaldi', $c, 'Via Garibaldi 9', 'Convenzionato', 4300, [
            ['Giorgio Rinaldi', '01/05/1968', 'Capofamiglia', 'Altro', 'Licenza Media', 'Autista', 'Italiano', 'D', 'Autista', 'Ditta Trasporti'],
            ['Franca Rinaldi', '23/09/1970', 'Coniuge', 'Disoccupato', 'Licenza Media', 'Cucina', 'Italiano', ''],
        ], 'income' => [1 => 1100], 'expenses' => [1 => 430, 3 => 170, 5 => 150], 'liabilities' => [3 => 4200],
            'events' => [6 => 2025], 'social' => 'No', 'how' => 'Volantino'],
        ['Hoxha', $m, 'Via Lavino 60', 'Affitto Privato', 2700, [
            ['Arben Hoxha', '13/11/1976', 'Capofamiglia', 'Disoccupato', 'Diploma', 'Giardiniere', 'Albanese, Italiano', 'B'],
            ['Mirela Hoxha', '30/01/1980', 'Coniuge', 'Disoccupato', 'Diploma', 'Sarta', 'Albanese, Italiano', 'B'],
            ['Elira Hoxha', '18/06/2009', 'Figlio', ''],
            ['Dritan Hoxha', '02/02/2013', 'Figlio', ''],
        ], 'citizenship' => 'Albanese', 'inItaly' => 2003, 'income' => [4 => 780], 'expenses' => [1 => 580, 3 => 160],
            'events' => [7 => 2025, 9 => 2025], 'social' => 'Si', 'how' => 'Scuola', 'work' => 'Manutenzione del verde',
            'social_area' => 'Disponibili per il doposcuola'],
        ['Serra', $s, 'Via Setta 14', 'Popolare', 1300, [
            ['Daniela Serra', '06/06/1990', 'Capofamiglia', 'Semioccupato', 'Diploma', 'Barista', 'Italiano, Inglese', 'B', 'Barista', 'Bar'],
            ['Nicole Serra', '11/11/2018', 'Figlio', ''],
        ], 'income' => [1 => 560, 7 => 200], 'expenses' => [1 => 180, 3 => 120], 'events' => [1 => 2020],
            'assessment' => ['InvaliditaGraveMinori' => 1, 'AltriContributi' => 3], 'social' => 'Assistenza Sociale', 'how' => 'Pediatra'],
        ['Bianchini', $v, 'Via Provinciale 201', 'Affitto Privato', 6800, [
            ['Roberto Bianchini', '20/02/1965', 'Capofamiglia', 'Altro', 'Diploma', 'Elettricista', 'Italiano', 'B', 'Operaio', 'Officina'],
            ['Carla Bianchini', '08/08/1967', 'Coniuge', 'Altro', 'Licenza Media', '', 'Italiano', ''],
            ['Davide Bianchini', '14/04/1998', 'Figlio', 'Disoccupato', 'Laurea', 'Grafica', 'Italiano, Inglese', 'B'],
            ['Chiara Bianchini', '29/09/2001', 'Figlio', 'Semioccupato', 'Diploma', 'Commessa', 'Italiano, Inglese', 'B', 'Commessa', 'Negozio'],
        ], 'income' => [1 => 1500], 'expenses' => [1 => 700, 3 => 210], 'social' => 'No', 'how' => 'Passaparola'],
        ['Diallo', $c, 'Via Canale 7', 'Emergenza Abitativa', 0, [
            ['Mamadou Diallo', '10/10/1995', 'Capofamiglia', 'Disoccupato', 'Licenza Media', 'Saldatore', 'Francese, Italiano', ''],
        ], 'citizenship' => 'Senegalese', 'inItaly' => 2019, 'immigration' => 'PS34567890', 'events' => [10 => 2025, 11 => 2025],
            'social' => 'Area', 'how' => 'Centro di accoglienza', 'training' => 'Corso per carrellisti', 'work' => 'Qualsiasi mansione'],
        ['Fontana', $z, 'Via Dante 33', 'Proprieta', 7400, [
            ['Luisa Fontana', '17/01/1949', 'Capofamiglia', 'Altro', 'Licenza Elementare', '', 'Italiano', ''],
            ['Aldo Fontana', '03/03/1946', 'Coniuge', 'Altro', 'Licenza Elementare', '', 'Italiano', ''],
        ], 'income' => [2 => 1150, 3 => 520], 'expenses' => [3 => 230, 4 => 260], 'assets' => [1 => 110000],
            'assessment' => ['InvaliditaTotaleAdulti' => 1], 'events' => [4 => 2022], 'social' => 'Si', 'how' => 'Medico di base'],
        ['Kovac', $m, 'Via Calderino 18', 'Affitto Privato', 3900, [
            ['Jelena Kovac', '26/04/1987', 'Capofamiglia', 'Disoccupato', 'Laurea', 'Contabilita', 'Serbo, Italiano, Inglese', 'B'],
            ['Milan Kovac', '12/12/1985', 'Coniuge', 'Altro', 'Diploma', 'Meccanico', 'Serbo, Italiano', 'C', 'Meccanico', 'Officina'],
            ['Nina Kovac', '08/08/2022', 'Figlio', ''],
        ], 'citizenship' => 'Serba', 'inItaly' => 2014, 'income' => [1 => 1300], 'expenses' => [1 => 640, 3 => 150, 5 => 120],
            'liabilities' => [3 => 2500], 'social' => 'No', 'how' => 'Asilo nido'],
        ['Tedeschi', $s, 'Via Castello 2', 'Affitto Privato', 2100, [
            ['Simone Tedeschi', '31/03/1992', 'Capofamiglia', 'Disoccupato', 'Diploma', 'Cuoco', 'Italiano, Inglese', 'B'],
        ], 'income' => [4 => 610], 'expenses' => [1 => 450, 3 => 90], 'events' => [6 => 2024, 7 => 2025],
            'assessment' => ['AltriDebiti' => 8], 'liabilities' => [1 => 3800, 3 => 1500], 'social' => 'No', 'how' => 'Internet'],
        ['Rahman', $c, 'Via Bazzanese 54', 'Affitto Privato', 4700, [
            ['Abdul Rahman', '05/01/1978', 'Capofamiglia', 'Altro', 'Diploma', 'Cucina', 'Bengalese, Inglese, Italiano', 'B', 'Aiuto Cuoco', 'Ristorante'],
            ['Fatima Rahman', '14/07/1983', 'Coniuge', 'Altro', 'Licenza Media', '', 'Bengalese', ''],
            ['Imran Rahman', '21/03/2008', 'Figlio', ''],
            ['Ayesha Rahman', '09/09/2011', 'Figlio', ''],
            ['Nadia Rahman', '30/05/2016', 'Figlio', ''],
            ['Karim Rahman', '17/10/2023', 'Figlio', ''],
        ], 'citizenship' => 'Bengalese', 'inItaly' => 2006, 'immigration' => 'PS45678901', 'income' => [1 => 1350, 5 => 300],
            'expenses' => [1 => 690, 3 => 220], 'social' => 'Si', 'how' => 'Scuola'],
        ['Monti', $v, 'Via Mulino 76', 'Convenzionato', 1600, [
            ['Federica Monti', '22/12/1975', 'Capofamiglia', 'Disoccupato', 'Diploma', 'Operaia', 'Italiano', 'B'],
            ['Alessio Monti', '10/02/2006', 'Figlio', 'Disoccupato', 'Licenza Media', '', 'Italiano', ''],
            ['Greta Monti', '04/06/2010', 'Figlio', ''],
        ], 'income' => [4 => 540, 7 => 300], 'expenses' => [1 => 380, 3 => 140], 'events' => [1 => 2018, 11 => 2025],
            'social' => 'Assistenza Sociale', 'how' => 'Servizi sociali', 'training' => 'Corso OSS'],
        ['Vitale', $z, 'Via Gesso 120', 'Popolare', 5600, [
            ['Antonio Vitale', '18/09/1962', 'Capofamiglia', 'Altro', 'Licenza Media', 'Carpentiere', 'Italiano', 'B'],
            ['Rosa Vitale', '27/11/1964', 'Coniuge', 'Altro', 'Licenza Elementare', 'Cucina', 'Italiano', ''],
            ['Carmela Russo', '02/02/1938', 'Altro', 'Altro', '', '', 'Italiano', ''],
        ], 'income' => [2 => 980, 3 => 520], 'expenses' => [1 => 160, 3 => 200, 4 => 180],
            'assessment' => ['InvaliditaGraveAdulti' => 1, 'AltriContributi' => 2], 'social' => 'Si', 'how' => 'Parrocchia'],
        ['Nowak', $m, 'Via Landa 9', 'Affitto Privato', 3400, [
            ['Katarzyna Nowak', '15/05/1989', 'Capofamiglia', 'Semioccupato', 'Laurea', 'Traduzioni', 'Polacco, Italiano, Inglese', 'B', 'Collaboratrice', 'Agenzia'],
            ['Piotr Nowak', '01/01/1987', 'Coniuge', 'Semioccupato', 'Diploma', 'Imbianchino', 'Polacco, Italiano', 'B', 'Imbianchino', 'In proprio'],
            ['Zofia Nowak', '19/04/2019', 'Figlio', ''],
        ], 'citizenship' => 'Polacca', 'inItaly' => 2015, 'income' => [1 => 950], 'expenses' => [1 => 610, 3 => 140],
            'social' => 'No', 'how' => 'Passaparola', 'social_area' => 'Traduzioni per lo sportello'],
        ['Gentile', $c, 'Via Ronzani 31', 'Affitto Privato', 7900, [
            ['Stefano Gentile', '09/07/1983', 'Capofamiglia', 'Altro', 'Laurea', 'Vendite', 'Italiano, Inglese', 'B', 'Commesso', 'Negozio'],
            ['Martina Gentile', '25/10/1985', 'Coniuge', 'Disoccupato', 'Laurea', 'Educatrice', 'Italiano, Inglese', 'B'],
            ['Pietro Gentile', '13/01/2017', 'Figlio', ''],
        ], 'income' => [1 => 1450], 'expenses' => [1 => 720, 3 => 180], 'events' => [11 => 2025], 'social' => 'No', 'how' => 'Internet'],
    ];
}

/* A made-up tax code: right shape, not a real one */
function taxCode(string $surname, string $name, string $birthDate, int $index): string
{
    $letters = fn (string $text) => str_pad(substr(preg_replace('/[^A-Z]/', '', strtoupper($text)), 0, 3), 3, 'X');
    [$day, $month, $year] = explode('/', $birthDate);
    return $letters($surname) . $letters($name) . substr($year, 2) . 'ABCDEHLMPRST'[(int) $month - 1] . $day
        . 'Z' . str_pad((string) (100 + $index), 3, '0', STR_PAD_LEFT) . 'DEMOXYZWKJQ'[$index % 11];
}

function formOf(array $family, int $index): array
{
    [$surname, $city, $street, $housing, $isee, $members] = $family;
    $applicant = $members[0];
    $firstName = explode(' ', $applicant[0])[0];

    $form = [
        'CodiceFiscale' => taxCode($surname, $firstName, $applicant[1], $index),
        'CognomeRichiedente' => $surname,
        'NomeRichiedente' => $firstName,
        'LuogoNascitaRichiedente' => isset($family['citizenship']) ? 'Estero' : 'Bologna',
        'DataNascitaRichiedente' => $applicant[1],
        'Cittadinanza' => $family['citizenship'] ?? 'Italiana',
        'InItaliaDallAnno' => (string) ($family['inItaly'] ?? ''),
        'Residenza' => $city,
        'Via' => $street,
        'TipoDiAlloggio' => $housing,
        'DocumentoDIdent' => 'CA' . str_pad((string) (10000 + $index * 37), 5, '0', STR_PAD_LEFT) . 'XX',
        'DocumentoDImmig' => $family['immigration'] ?? '',
        'Telefono' => '333000' . str_pad((string) (1000 + $index), 4, '0', STR_PAD_LEFT),
        'Mail' => strtolower($firstName . '.' . str_replace(' ', '', $surname)) . '@example.com',
        'ServiziSociali' => $family['social'],
        'ComeHaConosciutoLEmporio' => $family['how'],
        'nComponentiNucleoFamiliare' => (string) count($members),
        'IndiceISEE' => (string) $isee,
        'Formativo' => $family['training'] ?? '',
        'Lavorativo' => $family['work'] ?? '',
        'Sociale' => $family['social_area'] ?? '',
        'ValutCommissione' => $family['notes'] ?? '',
    ] + array_map('strval', $family['assessment'] ?? []);

    foreach ($members as $i => $member) {
        $row = $i + 1;
        $member += array_fill(0, 10, '');
        $form['Nome'][$row] = $member[0];
        $form['LuogoNascita'][$row] = isset($family['citizenship']) && $member[2] !== 'Figlio' ? 'Estero' : 'Bologna';
        $form['DataNascita'][$row] = $member[1];
        $form['Parentela'][$row] = $member[2];
        $form['Occupazione'][$row] = $member[3];
        $form['TitoloDiStudio'][$row] = $member[4];
        $form['Competenze'][$row] = $member[5];
        $form['ConoscenzaLingua'][$row] = $member[6];
        $form['Patente'][$row] = $member[7];
        $form['AttAtt'][$row] = $member[8];
        $form['Presso'][$row] = $member[9];
    }
    foreach ($family['events'] ?? [] as $row => $year) {
        $form['Anno'][$row] = (string) $year;
        $form['DocAll'][$row] = 'Documento allegato';
    }
    $tables = ['assets' => 'ValoreSPatt', 'liabilities' => 'ValoreSPpas', 'income' => 'ValoreENT', 'expenses' => 'ValoreUSC'];
    foreach ($tables as $key => $field) {
        foreach ($family[$key] ?? [] as $row => $amount) {
            $form[$field][$row] = (string) $amount;
        }
    }

    return $form;
}

function literal(mixed $value): string
{
    if ($value === null) {
        return 'NULL';
    }
    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }
    return "'" . str_replace(['\\', "'"], ['\\\\', "''"], (string) $value) . "'";
}

/* One INSERT with every row, or nothing when there are no rows */
function insertInto(string $table, array $columns, array $rows): string
{
    if ($rows === []) {
        return '';
    }
    $lines = array_map(fn (array $row) => '(' . implode(', ', array_map(literal(...), $row)) . ')', $rows);
    return "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES\n" . implode(",\n", $lines) . ";\n\n";
}

$today = new DateTimeImmutable($argv[1] ?? 'today');
$rows = array_fill_keys([
    'Richiedente', 'Punteggio', 'ValutazioneGriglia', 'Membri', 'SituazioneSocioSanitaria', 'DispCollaborative',
    'PatrAtt', 'PatrPass', 'Entrate', 'Uscite',
], []);
$ranking = [];

foreach (families() as $index => $family) {
    $form = formOf($family, $index);
    $errors = formErrors($form, $today->format('Y-m-d'));
    if ($errors !== []) {
        fwrite(STDERR, "{$family[0]}: " . implode('; ', $errors) . "\n");
        exit(1);
    }
    $record = recordFromForm($form);
    $scores = scoreApplication(applicationFromForm($form), ISEE_BRACKETS, $today, defaultRules());
    $taxCode = $record['applicant']['Cod_Fiscale'];
    $ranking[$taxCode] = $scores['PunteggioTotale'];

    $rows['Richiedente'][] = [$taxCode, ...array_map(fn (string $c) => $record['applicant'][$c], APPLICANT_COLUMNS)];
    $rows['Punteggio'][] = [$taxCode, ...array_map(fn (string $c) => $scores[$c], SCORE_COLUMNS)];
    $rows['ValutazioneGriglia'][] = [$taxCode, ...array_values($record['assessment'])];
    foreach ($record['members'] as $number => $member) {
        $rows['Membri'][] = [$number, $taxCode, ...array_map(fn (string $c) => $member[$c], MEMBER_COLUMNS)];
    }
    foreach ($record['events'] as $number => $event) {
        $rows['SituazioneSocioSanitaria'][] = [$taxCode, $number, EVENTS[$number], $event['Anno'], $event['DocAll']];
    }
    $rows['DispCollaborative'][] = [$taxCode, ...array_values($record['collaboration'])];
    foreach (['PatrAtt' => ['assets', ASSETS], 'PatrPass' => ['liabilities', LIABILITIES], 'Entrate' => ['income', INCOME], 'Uscite' => ['expenses', EXPENSES]] as $table => [$key, $labels]) {
        foreach ($record[$key] as $number => $money) {
            $rows[$table][] = [$taxCode, $number, $labels[$number], $money['Valore'], ...($table === 'PatrAtt' ? [] : [$money['Scadenza']])];
        }
    }
}

arsort($ranking);
$top = array_keys($ranking);
$cards = array_map(fn (int $n) => 'ES' . str_pad((string) $n, 10, '0', STR_PAD_LEFT), range(1, 12));

echo "-- Demo data: an administrator account (emporiosolidale / IlSole), invented\n";
echo "-- families and some shopping cards. Loaded after 01-schema.sql; the application\n";
echo "-- itself does not need it.\n";
echo "-- Generated by scripts/generate-demo-data.php on {$today->format('Y-m-d')}: edit the script, not this file.\n\n";
echo "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\nSTART TRANSACTION;\n\n";

echo insertInto('Administrator', ['Username', 'Password'], [['emporiosolidale', ADMINISTRATOR_HASH]]);
echo insertInto('Richiedente', ['Cod_Fiscale', ...APPLICANT_COLUMNS], $rows['Richiedente']);
echo insertInto('Punteggio', ['CodFamiglia', ...SCORE_COLUMNS], $rows['Punteggio']);
echo insertInto('ValutazioneGriglia', ['CodFamiglia', 'InvaliditaTotaleAdulti', 'InvaliditaGraveAdulti', 'InvaliditaGraveMinori', 'AltriDebiti', 'AltriContributi', 'NoteCommissione'], $rows['ValutazioneGriglia']);
echo insertInto('Membri', ['nMembro', 'CodFamiglia', ...MEMBER_COLUMNS], $rows['Membri']);
echo insertInto('SituazioneSocioSanitaria', ['CodFamiglia', 'N', 'Evento', 'Anno', 'DocAll'], $rows['SituazioneSocioSanitaria']);
echo insertInto('DispCollaborative', ['CodFamiglia', 'AmbitoFormativo', 'AmbitoLavorativo', 'AmbitoSociale'], $rows['DispCollaborative']);
echo insertInto('PatrAtt', ['CodFamiglia', 'N', 'Elemento', 'Valore'], $rows['PatrAtt']);
echo insertInto('PatrPass', ['CodFamiglia', 'N', 'Elemento', 'Valore', 'Scadenza'], $rows['PatrPass']);
echo insertInto('Entrate', ['CodFamiglia', 'N', 'Elemento', 'Valore', 'Scadenza'], $rows['Entrate']);
echo insertInto('Uscite', ['CodFamiglia', 'N', 'Elemento', 'Valore', 'Scadenza'], $rows['Uscite']);
echo insertInto('Tessera', ['Codice'], array_map(fn (string $card) => [$card], $cards));

/* The six families with the highest score hold a card; one gave it back last month */
echo "-- Cards held today, released over the last months\n";
foreach (array_slice($top, 0, 6) as $i => $taxCode) {
    $days = 20 + $i * 25;
    echo "INSERT INTO `TesseraRilasciata` (`CodiceTessera`, `CodFamiglia`, `DataOraRilascioTessera`, `DataOraScadenzaTessera`) VALUES ("
        . literal($cards[$i]) . ', ' . literal($taxCode) . ", CURDATE() - INTERVAL $days DAY, CURDATE() - INTERVAL $days DAY + INTERVAL 6 MONTH);\n";
    echo "INSERT INTO `StoricoTessere` (`CodiceTessera`, `CodFamiglia`, `DataRilascio`) VALUES ("
        . literal($cards[$i]) . ', ' . literal($taxCode) . ", CURDATE() - INTERVAL $days DAY);\n";
}
$returned = $top[6];
echo "\n-- A card given back: the family waits before it can have another one\n";
echo "INSERT INTO `StoricoTessere` (`CodiceTessera`, `CodFamiglia`, `DataRilascio`, `DataRitiro`) VALUES ("
    . literal($cards[6]) . ', ' . literal($returned) . ", CURDATE() - INTERVAL 7 MONTH, CURDATE() - INTERVAL 1 MONTH);\n";
echo "UPDATE `Richiedente` SET `DataOraRitiroTessera` = CURDATE() - INTERVAL 1 MONTH, `DataOraStalloTessera` = CURDATE() + INTERVAL 5 MONTH WHERE `Cod_Fiscale` = "
    . literal($returned) . ";\n";

echo "\nCOMMIT;\nSET FOREIGN_KEY_CHECKS = 1;\n";
