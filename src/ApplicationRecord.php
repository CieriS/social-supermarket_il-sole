<?php

/*
    An application as it is stored: built from the submitted form, checked,
    and turned back into form fields to fill the form again.

    The form array has the shape of $_POST, with the field names of
    FileToBeExecuted/ModuloDomanda.php. Pure: no database, no request.
*/

declare(strict_types=1);

namespace IlSole\Record;

use function IlSole\Form\assessmentFromForm;
use function IlSole\Form\cell;
use function IlSole\Form\databaseDate;
use function IlSole\Scoring\parseDate;

const MAX_MEMBERS = 22;

const KIND_NEW = 'Domanda Nuova';
const KIND_UPDATE = 'Domanda Di Modifica';
const KIND_REPEATED = 'Domanda Reiterata';
const KINDS = [KIND_NEW, KIND_UPDATE, KIND_REPEATED];

const HOUSING = ['Proprieta', 'Affitto Privato', 'Convenzionato', 'Popolare', 'Emergenza Abitativa'];
const SOCIAL_SERVICES = ['Si', 'No', 'Area', 'Assistenza Sociale'];
const RELATIONS = ['Capofamiglia', 'Coniuge', 'Figlio', 'Altro'];
const OCCUPATIONS = ['Disoccupato', 'Semioccupato', 'Altro'];
const LICENCES = ['AM', 'A1', 'A2', 'A', 'B1', 'B', 'C1', 'C', 'D1', 'D', 'BE', 'C1E', 'CE', 'D1E', 'DE'];

/* Rows of the tables of the form, as they are labelled on screen and stored */
const EVENTS = [
    1 => 'Mancanza del partner per...',
    'Violenze e maltrattamenti familiari',
    'Presenza invalidit&agrave; media (oltre il 75%)',
    'Presenza invalidit&agrave; grave (oltre il 100%)',
    'Restrizioni della libert&agrave;',
    'Pignoramenti in corso',
    'Morosit&agrave; per affitto e/o utenze domestiche',
    'Mancato pagamento di rate del mutuo casa',
    'Avviso di sfratto',
    'Sfratto imminente',
    'Non poter sostenere spese impreviste',
    'Non potersi permettere l\'automobile',
];
const ASSETS = [1 => 'Immobili', 'Veicoli', 'Crediti (Liquidazioni, Assicurazioni, Altro)', 'Altre Voci'];
const LIABILITIES = [
    1 => 'Debiti v/Pubblica Amm.ne e Agenzia delle Entrate',
    'Mutui',
    'Debiti Privati e/o ACER',
    'Altre Voci',
];
const INCOME = [
    1 => 'Stipendi',
    'Pensioni di Anzianit&agrave;',
    'Pensioni di invalidit&agrave;, accompagnamento, cura',
    'Indennit&agrave;: (Disoccupazione, Mobilit&agrave;, Altro)',
    'Contributi: (REI, RES, Reddito Cittadinanza)',
    'Entrate straordinarie (compresi sociali consolidate)',
    'Altre entrate (Ass. Mantenimento, altro)',
];
const EXPENSES = [
    1 => 'Affitto Mensile',
    'Rata mensile di mutui',
    'Utenze medie mensili',
    'Spese medico-sanitarie',
    'Rate mensili di debiti privati:',
    'Altre Uscite:',
];

/* Longest text each column takes */
const LENGTHS = [
    'Cognome' => 30, 'Nome' => 30, 'LuogoNascita' => 30, 'Cittadinanza' => 15, 'Residenza' => 30,
    'Via' => 30, 'DocumentoIdent' => 9, 'DocumentoImmig' => 10, 'Telefono' => 15, 'Mail' => 100,
    'ComeHaConosciutoEmporio' => 30, 'NomeCognome' => 60, 'TitoloDiStudio' => 20, 'Competenze' => 20,
    'ConoscenzaLingua' => 30, 'AttualeAtt' => 15, 'Presso' => 15, 'DocAll' => 30, 'Ambito' => 255,
    'NoteCommissione' => 255,
];

function field(array $form, string $name): string
{
    $value = $form[$name] ?? '';
    return is_scalar($value) ? trim((string) $value) : '';
}

function limit(string $value, string $column): string
{
    return mb_substr($value, 0, LENGTHS[$column], 'UTF-8');
}

/* "mario de rossi" => "Mario De Rossi" */
function titleCase(string $value): string
{
    return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
}

function choice(string $value, array $allowed): string
{
    return in_array($value, $allowed, true) ? $value : '';
}

/* A whole number between $min and $max, or null */
function wholeNumber(string $value, int $min, int $max): ?int
{
    if ($value === '' || !ctype_digit($value) || strlen($value) > 9) {
        return null;
    }
    $number = (int) $value;
    return $number >= $min && $number <= $max ? $number : null;
}

/* An amount of money from 0 up, or null */
function amount(string $value): ?float
{
    $value = str_replace(',', '.', $value);
    return is_numeric($value) && (float) $value >= 0 && (float) $value < 1000000 ? round((float) $value, 2) : null;
}

function dateOrNull(string $value): ?string
{
    $date = databaseDate($value);
    return $date === '' ? null : $date;
}

function householdSize(array $form): int
{
    return wholeNumber(field($form, 'nComponentiNucleoFamiliare'), 1, MAX_MEMBERS) ?? 0;
}

/*
    The application to store. Rows of the tables left empty are left out:
    a member needs a name, an event a year, a money row an amount.
*/
function recordFromForm(array $form): array
{
    $members = [];
    for ($row = 1; $row <= householdSize($form); $row++) {
        $name = limit(titleCase(cell($form, 'Nome', $row)), 'NomeCognome');
        if ($name === '') {
            continue;
        }
        $members[$row] = [
            'NomeCognome' => $name,
            'LuogoNascita' => limit(titleCase(cell($form, 'LuogoNascita', $row)), 'LuogoNascita'),
            'DataNascita' => dateOrNull(cell($form, 'DataNascita', $row)),
            'Parentela' => choice(titleCase(cell($form, 'Parentela', $row)), RELATIONS),
            'Occupazione' => choice(titleCase(cell($form, 'Occupazione', $row)), OCCUPATIONS),
            'TitoloDiStudio' => limit(cell($form, 'TitoloDiStudio', $row), 'TitoloDiStudio'),
            'Competenze' => limit(cell($form, 'Competenze', $row), 'Competenze'),
            'ConoscenzaLingua' => limit(cell($form, 'ConoscenzaLingua', $row), 'ConoscenzaLingua'),
            'Patente' => choice(strtoupper(cell($form, 'Patente', $row)), LICENCES),
            'AttualeAtt' => limit(cell($form, 'AttAtt', $row), 'AttualeAtt'),
            'Presso' => limit(cell($form, 'Presso', $row), 'Presso'),
            'Termine' => dateOrNull(cell($form, 'Termine', $row)),
            'SenzaLavoroDa' => dateOrNull(cell($form, 'SenzaLavoroDa', $row)),
        ];
    }

    $events = [];
    foreach (array_keys(EVENTS) as $row) {
        $year = wholeNumber(cell($form, 'Anno', $row), 1900, 3100);
        if ($year !== null) {
            $events[$row] = ['Anno' => $year, 'DocAll' => limit(cell($form, 'DocAll', $row), 'DocAll')];
        }
    }

    $moneyRows = function (array $labels, string $valueField, ?string $dateField) use ($form): array {
        $rows = [];
        foreach (array_keys($labels) as $row) {
            $value = amount(cell($form, $valueField, $row));
            if ($value !== null) {
                $rows[$row] = ['Valore' => $value]
                    + ($dateField === null ? [] : ['Scadenza' => dateOrNull(cell($form, $dateField, $row))]);
            }
        }
        return $rows;
    };

    return [
        'applicant' => [
            'Cod_Fiscale' => strtoupper(field($form, 'CodiceFiscale')),
            'Cognome' => limit(titleCase(field($form, 'CognomeRichiedente')), 'Cognome'),
            'Nome' => limit(titleCase(field($form, 'NomeRichiedente')), 'Nome'),
            'LuogoNascita' => limit(titleCase(field($form, 'LuogoNascitaRichiedente')), 'LuogoNascita'),
            'DataNascita' => dateOrNull(field($form, 'DataNascitaRichiedente')),
            'Cittadinanza' => limit(titleCase(field($form, 'Cittadinanza')), 'Cittadinanza'),
            'InItaliaDallAnno' => wholeNumber(field($form, 'InItaliaDallAnno'), 1900, 3100),
            'Residenza' => limit(titleCase(field($form, 'Residenza')), 'Residenza'),
            'Via' => limit(titleCase(field($form, 'Via')), 'Via'),
            'TipoAlloggio' => choice(field($form, 'TipoDiAlloggio'), HOUSING),
            'DocumentoIdent' => limit(strtoupper(field($form, 'DocumentoDIdent')), 'DocumentoIdent'),
            'DocumentoImmig' => limit(strtoupper(field($form, 'DocumentoDImmig')), 'DocumentoImmig'),
            'Telefono' => limit(field($form, 'Telefono'), 'Telefono'),
            'Mail' => limit(strtolower(field($form, 'Mail')), 'Mail'),
            'ServiziSociali' => choice(field($form, 'ServiziSociali'), SOCIAL_SERVICES),
            'ComeHaConosciutoEmporio' => limit(field($form, 'ComeHaConosciutoLEmporio'), 'ComeHaConosciutoEmporio'),
            'nMembri' => householdSize($form),
            'ISEE' => wholeNumber(field($form, 'IndiceISEE'), 0, 999999999),
        ],
        'members' => $members,
        'events' => $events,
        'collaboration' => [
            'AmbitoFormativo' => limit(field($form, 'Formativo'), 'Ambito'),
            'AmbitoLavorativo' => limit(field($form, 'Lavorativo'), 'Ambito'),
            'AmbitoSociale' => limit(field($form, 'Sociale'), 'Ambito'),
        ],
        'assets' => $moneyRows(ASSETS, 'ValoreSPatt', null),
        'liabilities' => $moneyRows(LIABILITIES, 'ValoreSPpas', 'ScadenzaSPpas'),
        'income' => $moneyRows(INCOME, 'ValoreENT', 'ScadenzaENT'),
        'expenses' => $moneyRows(EXPENSES, 'ValoreUSC', 'ScadenzaUSC'),
        'assessment' => assessmentFromForm($form)
            + ['NoteCommissione' => limit(field($form, 'ValutCommissione'), 'NoteCommissione')],
    ];
}

/*
    What stops the form from being saved, as messages for the operator.
    $today is 'yyyy-mm-dd'. An empty list means the form can be saved.
*/
function formErrors(array $form, string $today): array
{
    $record = recordFromForm($form);
    $applicant = $record['applicant'];
    $errors = [];

    $required = [
        'Cognome' => 'Cognome', 'Nome' => 'Nome', 'LuogoNascita' => 'Luogo di nascita',
        'Cittadinanza' => 'Cittadinanza', 'Residenza' => 'Residenza', 'Via' => 'Via',
        'TipoAlloggio' => 'Tipo di alloggio', 'Telefono' => 'Telefono',
        'ServiziSociali' => 'In carico ai Servizi Sociali',
    ];
    foreach ($required as $column => $label) {
        if ($applicant[$column] === '') {
            $errors[] = "$label: campo obbligatorio";
        }
    }

    if (!preg_match('/^[A-Z0-9]{16}$/', $applicant['Cod_Fiscale'])) {
        $errors[] = 'Codice fiscale: servono 16 lettere o cifre';
    }
    if (filter_var($applicant['Mail'], FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'E-mail: indirizzo non valido';
    }
    if ($applicant['ISEE'] === null) {
        $errors[] = 'Indice ISEE: serve un numero intero da 0 in su';
    }
    if ($applicant['nMembri'] === 0) {
        $errors[] = 'Numero componenti: serve un numero da 1 a ' . MAX_MEMBERS;
    }

    $birthDate = $applicant['DataNascita'];
    if ($birthDate === null) {
        $errors[] = 'Data di nascita del richiedente: usare il formato gg/mm/aaaa';
    } elseif ($birthDate > $today) {
        $errors[] = 'Data di nascita del richiedente: non può essere nel futuro';
    } elseif (parseDate($birthDate)->diff(parseDate($today))->y < 18) {
        $errors[] = 'Il richiedente deve essere maggiorenne';
    }

    for ($row = 1; $row <= $applicant['nMembri']; $row++) {
        $typed = cell($form, 'DataNascita', $row);
        $member = $record['members'][$row] ?? null;
        if ($member === null) {
            if ($typed !== '' || cell($form, 'Parentela', $row) !== '' || cell($form, 'Occupazione', $row) !== '') {
                $errors[] = "Componente $row: manca il nome";
            }
            continue;
        }
        if ($member['DataNascita'] === null) {
            $errors[] = "Componente $row: data di nascita mancante o non nel formato gg/mm/aaaa";
        } elseif ($member['DataNascita'] > $today) {
            $errors[] = "Componente $row: la data di nascita non può essere nel futuro";
        }
        if ($member['Parentela'] === '') {
            $errors[] = "Componente $row: manca la parentela";
        }
    }
    if ($applicant['nMembri'] > 0 && count($record['members']) === 0) {
        $errors[] = 'Composizione del nucleo: inserire almeno il richiedente';
    }

    return $errors;
}

function formDate(?string $databaseDate): string
{
    $date = parseDate($databaseDate);
    return $date === null ? '' : $date->format('d/m/Y');
}

function formAmount(mixed $value): string
{
    return is_numeric($value) ? rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.') : '';
}

/* The fields that fill the form with a stored application */
function formFromRecord(array $record): array
{
    $applicant = $record['applicant'];
    $form = [
        'CodiceFiscale' => $applicant['Cod_Fiscale'],
        'CognomeRichiedente' => $applicant['Cognome'],
        'NomeRichiedente' => $applicant['Nome'],
        'LuogoNascitaRichiedente' => $applicant['LuogoNascita'],
        'DataNascitaRichiedente' => formDate($applicant['DataNascita']),
        'Cittadinanza' => $applicant['Cittadinanza'],
        'InItaliaDallAnno' => (string) ($applicant['InItaliaDallAnno'] ?? ''),
        'Residenza' => $applicant['Residenza'],
        'Via' => $applicant['Via'],
        'TipoDiAlloggio' => $applicant['TipoAlloggio'],
        'DocumentoDIdent' => (string) $applicant['DocumentoIdent'],
        'DocumentoDImmig' => (string) $applicant['DocumentoImmig'],
        'Telefono' => $applicant['Telefono'],
        'Mail' => $applicant['Mail'],
        'ServiziSociali' => $applicant['ServiziSociali'],
        'ComeHaConosciutoLEmporio' => (string) $applicant['ComeHaConosciutoEmporio'],
        'nComponentiNucleoFamiliare' => (string) $applicant['nMembri'],
        'IndiceISEE' => (string) ($applicant['ISEE'] ?? ''),
        'Formativo' => (string) ($record['collaboration']['AmbitoFormativo'] ?? ''),
        'Lavorativo' => (string) ($record['collaboration']['AmbitoLavorativo'] ?? ''),
        'Sociale' => (string) ($record['collaboration']['AmbitoSociale'] ?? ''),
        'ValutCommissione' => (string) ($record['assessment']['NoteCommissione'] ?? ''),
    ];
    foreach (\IlSole\Form\ASSESSMENT_FIELDS as $name) {
        $form[$name] = (string) ($record['assessment'][$name] ?? 0);
    }

    foreach ($record['members'] as $row => $member) {
        $form['Nome'][$row] = $member['NomeCognome'];
        $form['LuogoNascita'][$row] = (string) $member['LuogoNascita'];
        $form['DataNascita'][$row] = formDate($member['DataNascita']);
        $form['Parentela'][$row] = $member['Parentela'];
        $form['Occupazione'][$row] = (string) $member['Occupazione'];
        $form['TitoloDiStudio'][$row] = (string) $member['TitoloDiStudio'];
        $form['Competenze'][$row] = (string) $member['Competenze'];
        $form['ConoscenzaLingua'][$row] = (string) $member['ConoscenzaLingua'];
        $form['Patente'][$row] = (string) $member['Patente'];
        $form['AttAtt'][$row] = (string) $member['AttualeAtt'];
        $form['Presso'][$row] = (string) $member['Presso'];
        $form['Termine'][$row] = formDate($member['Termine']);
        $form['SenzaLavoroDa'][$row] = formDate($member['SenzaLavoroDa']);
    }
    foreach ($record['events'] as $row => $event) {
        $form['Anno'][$row] = (string) $event['Anno'];
        $form['DocAll'][$row] = (string) $event['DocAll'];
    }
    $tables = [
        'assets' => ['ValoreSPatt', null],
        'liabilities' => ['ValoreSPpas', 'ScadenzaSPpas'],
        'income' => ['ValoreENT', 'ScadenzaENT'],
        'expenses' => ['ValoreUSC', 'ScadenzaUSC'],
    ];
    foreach ($tables as $table => [$valueField, $dateField]) {
        foreach ($record[$table] as $row => $money) {
            $form[$valueField][$row] = formAmount($money['Valore']);
            if ($dateField !== null) {
                $form[$dateField][$row] = formDate($money['Scadenza'] ?? null);
            }
        }
    }

    return $form;
}
