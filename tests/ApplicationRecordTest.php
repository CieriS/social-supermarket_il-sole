<?php

declare(strict_types=1);

namespace IlSole\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function IlSole\Html\e;
use function IlSole\Record\amount;
use function IlSole\Record\formErrors;
use function IlSole\Record\formFromRecord;
use function IlSole\Record\recordFromForm;
use function IlSole\Record\wholeNumber;

final class ApplicationRecordTest extends TestCase
{
    private const TODAY = '2026-06-15';

    /* A complete, valid form */
    private static function form(array $changes = []): array
    {
        return array_replace([
            'CodiceFiscale' => 'rssmra80a01a944x',
            'CognomeRichiedente' => 'de rossi',
            'NomeRichiedente' => 'MARIO',
            'LuogoNascitaRichiedente' => 'bologna',
            'DataNascitaRichiedente' => '20/04/1980',
            'Cittadinanza' => 'ita',
            'InItaliaDallAnno' => '',
            'Residenza' => 'casalecchio di reno',
            'Via' => 'via esempio 1',
            'TipoDiAlloggio' => 'Emergenza Abitativa',
            'DocumentoDIdent' => 'ab1234567',
            'DocumentoDImmig' => '',
            'Telefono' => '3330000001',
            'Mail' => 'Mario.Rossi@Example.com',
            'ServiziSociali' => 'No',
            'ComeHaConosciutoLEmporio' => 'Passaparola',
            'nComponentiNucleoFamiliare' => '3',
            'IndiceISEE' => '2000',
            'Nome' => [1 => 'mario de rossi', 2 => 'anna bianchi', 3 => 'luca de rossi'],
            'LuogoNascita' => [1 => 'bologna', 2 => 'modena', 3 => 'bologna'],
            'DataNascita' => [1 => '20/04/1980', 2 => '25/09/1982', 3 => '30/12/2015'],
            'Parentela' => [1 => 'Capofamiglia', 2 => 'Coniuge', 3 => 'Figlio'],
            'Occupazione' => [1 => 'Disoccupato', 2 => 'Altro', 3 => ''],
            'TitoloDiStudio' => [1 => 'Diploma', 2 => 'Laurea'],
            'Competenze' => [1 => 'Muratore'],
            'ConoscenzaLingua' => [1 => 'Inglese'],
            'Patente' => [1 => 'B', 2 => 'x'],
            'AttAtt' => [2 => 'Cassiera'],
            'Presso' => [2 => 'Negozio'],
            'Termine' => [2 => '31/12/2026'],
            'SenzaLavoroDa' => [1 => '15/01/2024'],
            'Anno' => [7 => '2025', 9 => '', 10 => 'abc'],
            'DocAll' => [7 => 'Sollecito', 9 => 'senza anno'],
            'ValoreSPatt' => [2 => '3500'],
            'ValoreSPpas' => [1 => '1200,50'],
            'ScadenzaSPpas' => [1 => '31/01/2027'],
            'ValoreENT' => [1 => '900', 5 => '500', 6 => ''],
            'ScadenzaENT' => [5 => ''],
            'ValoreUSC' => [1 => '450'],
            'ScadenzaUSC' => [1 => 'abc'],
            'Formativo' => 'Corso di italiano',
            'Lavorativo' => '',
            'Sociale' => 'Volontariato',
            'InvaliditaGraveMinori' => '1',
            'AltriDebiti' => '4',
            'ValutCommissione' => 'Da rivedere tra sei mesi',
        ], $changes);
    }

    public function testBuildsTheApplicant(): void
    {
        self::assertSame(
            [
                'Cod_Fiscale' => 'RSSMRA80A01A944X',
                'Cognome' => 'De Rossi',
                'Nome' => 'Mario',
                'LuogoNascita' => 'Bologna',
                'DataNascita' => '1980-04-20',
                'Cittadinanza' => 'Ita',
                'InItaliaDallAnno' => null,
                'Residenza' => 'Casalecchio Di Reno',
                'Via' => 'Via Esempio 1',
                'TipoAlloggio' => 'Emergenza Abitativa',
                'DocumentoIdent' => 'AB1234567',
                'DocumentoImmig' => '',
                'Telefono' => '3330000001',
                'Mail' => 'mario.rossi@example.com',
                'ServiziSociali' => 'No',
                'ComeHaConosciutoEmporio' => 'Passaparola',
                'nMembri' => 3,
                'ISEE' => 2000,
            ],
            recordFromForm(self::form())['applicant']
        );
    }

    public function testBuildsTheMembersWithTheirWorkDetails(): void
    {
        $members = recordFromForm(self::form())['members'];

        self::assertSame([1, 2, 3], array_keys($members));
        self::assertSame(
            [
                'NomeCognome' => 'Anna Bianchi',
                'LuogoNascita' => 'Modena',
                'DataNascita' => '1982-09-25',
                'Parentela' => 'Coniuge',
                'Occupazione' => 'Altro',
                'TitoloDiStudio' => 'Laurea',
                'Competenze' => '',
                'ConoscenzaLingua' => '',
                'Patente' => '',
                'AttualeAtt' => 'Cassiera',
                'Presso' => 'Negozio',
                'Termine' => '2026-12-31',
                'SenzaLavoroDa' => null,
            ],
            $members[2]
        );
        self::assertSame('B', $members[1]['Patente']);
        self::assertSame('2024-01-15', $members[1]['SenzaLavoroDa']);
    }

    public function testLeavesOutMembersWithoutANameAndRowsBeyondTheHousehold(): void
    {
        $form = self::form(['nComponentiNucleoFamiliare' => '2']);
        $form['Nome'][1] = '  ';

        self::assertSame([2], array_keys(recordFromForm($form)['members']));
    }

    public function testKeepsOnlyTheEventsWithAYear(): void
    {
        self::assertSame(
            [7 => ['Anno' => 2025, 'DocAll' => 'Sollecito']],
            recordFromForm(self::form())['events']
        );
    }

    public function testKeepsOnlyTheMoneyRowsWithAnAmount(): void
    {
        $record = recordFromForm(self::form());

        self::assertSame([2 => ['Valore' => 3500.0]], $record['assets']);
        self::assertSame([1 => ['Valore' => 1200.5, 'Scadenza' => '2027-01-31']], $record['liabilities']);
        self::assertSame(
            [1 => ['Valore' => 900.0, 'Scadenza' => null], 5 => ['Valore' => 500.0, 'Scadenza' => null]],
            $record['income']
        );
        self::assertSame([1 => ['Valore' => 450.0, 'Scadenza' => null]], $record['expenses']);
    }

    public function testBuildsCollaborationAndAssessment(): void
    {
        $record = recordFromForm(self::form());

        self::assertSame(
            ['AmbitoFormativo' => 'Corso di italiano', 'AmbitoLavorativo' => '', 'AmbitoSociale' => 'Volontariato'],
            $record['collaboration']
        );
        self::assertSame(
            [
                'InvaliditaTotaleAdulti' => 0,
                'InvaliditaGraveAdulti' => 0,
                'InvaliditaGraveMinori' => 1,
                'AltriDebiti' => 4,
                'AltriContributi' => 0,
                'NoteCommissione' => 'Da rivedere tra sei mesi',
            ],
            $record['assessment']
        );
    }

    public function testCutsTextToTheColumnLength(): void
    {
        $record = recordFromForm(self::form([
            'CognomeRichiedente' => str_repeat('a', 40),
            'Via' => str_repeat('è', 40),
            'ValutCommissione' => str_repeat('x', 300),
        ]));

        self::assertSame(30, mb_strlen($record['applicant']['Cognome']));
        self::assertSame(30, mb_strlen($record['applicant']['Via']));
        self::assertSame(255, strlen($record['assessment']['NoteCommissione']));
    }

    public function testDropsChoicesThatAreNotOffered(): void
    {
        $form = self::form(['TipoDiAlloggio' => 'Castello', 'ServiziSociali' => "No' OR '1'='1"]);
        $form['Parentela'][3] = 'Cugino';
        $form['Occupazione'][1] = 'Mago';
        $record = recordFromForm($form);

        self::assertSame('', $record['applicant']['TipoAlloggio']);
        self::assertSame('', $record['applicant']['ServiziSociali']);
        self::assertSame('', $record['members'][3]['Parentela']);
        self::assertSame('', $record['members'][1]['Occupazione']);
    }

    public function testAnEmptyFormGivesAnEmptyRecord(): void
    {
        $record = recordFromForm([]);

        self::assertSame('', $record['applicant']['Cod_Fiscale']);
        self::assertSame(0, $record['applicant']['nMembri']);
        self::assertNull($record['applicant']['ISEE']);
        self::assertSame([], $record['members']);
        self::assertSame([], $record['events']);
        self::assertSame([], $record['income']);
    }

    public function testSurvivesFieldsOfTheWrongShape(): void
    {
        $record = recordFromForm(self::form([
            'CognomeRichiedente' => ['x'],
            'Nome' => 'mario',
            'ValoreENT' => [1 => ['900']],
            'Anno' => 'x',
        ]));

        self::assertSame('', $record['applicant']['Cognome']);
        self::assertSame([], $record['members']);
        self::assertSame([], $record['income']);
        self::assertSame([], $record['events']);
    }

    #[DataProvider('wholeNumbers')]
    public function testWholeNumber(string $value, ?int $expected): void
    {
        self::assertSame($expected, wholeNumber($value, 1, 22));
    }

    public static function wholeNumbers(): array
    {
        return [
            ['1', 1], ['22', 22], ['0', null], ['23', null], ['', null], ['-1', null],
            ['1.5', null], ['abc', null], ['99999999999999999999', null],
        ];
    }

    #[DataProvider('amounts')]
    public function testAmount(string $value, ?float $expected): void
    {
        self::assertSame($expected, amount($value));
    }

    public static function amounts(): array
    {
        return [
            ['0', 0.0], ['12', 12.0], ['12.345', 12.35], ['12,5', 12.5], ['', null],
            ['-1', null], ['abc', null], ['1000000', null],
        ];
    }

    public function testAValidFormHasNoErrors(): void
    {
        self::assertSame([], formErrors(self::form(), self::TODAY));
    }

    #[DataProvider('invalidForms')]
    public function testReportsWhatIsWrong(array $changes, string $message): void
    {
        $errors = formErrors(self::form($changes), self::TODAY);

        self::assertCount(1, $errors, implode(' | ', $errors));
        self::assertStringContainsString($message, $errors[0]);
    }

    public static function invalidForms(): array
    {
        $members = fn (string $field, int $row, string $value) => [$field => [$row => $value] + self::form()[$field]];

        return [
            'no surname' => [['CognomeRichiedente' => ' '], 'Cognome'],
            'no housing' => [['TipoDiAlloggio' => ''], 'Tipo di alloggio'],
            'housing not offered' => [['TipoDiAlloggio' => 'Castello'], 'Tipo di alloggio'],
            'no social services' => [['ServiziSociali' => ''], 'Servizi Sociali'],
            'short tax code' => [['CodiceFiscale' => 'RSSMRA80A01'], 'Codice fiscale'],
            'tax code with symbols' => [['CodiceFiscale' => "RSSMRA80A01A94';-"], 'Codice fiscale'],
            'bad e-mail' => [['Mail' => 'mario.example.com'], 'E-mail'],
            'no ISEE' => [['IndiceISEE' => ''], 'ISEE'],
            'negative ISEE' => [['IndiceISEE' => '-5'], 'ISEE'],
            'decimal ISEE' => [['IndiceISEE' => '2000.5'], 'ISEE'],
            'birth date in the wrong format' => [['DataNascitaRichiedente' => '1980/04/20'], 'gg/mm/aaaa'],
            'impossible birth date' => [['DataNascitaRichiedente' => '31/02/1980'], 'gg/mm/aaaa'],
            'birth date in the future' => [['DataNascitaRichiedente' => '16/06/2026'], 'futuro'],
            'applicant under 18' => [['DataNascitaRichiedente' => '16/06/2008'], 'maggiorenne'],
            'member without a birth date' => [$members('DataNascita', 2, ''), 'Componente 2'],
            'member born in the future' => [$members('DataNascita', 3, '01/01/2030'), 'Componente 3'],
            'member without a relation' => [$members('Parentela', 2, ''), 'Componente 2: manca la parentela'],
            'member with data but no name' => [$members('Nome', 3, ''), 'Componente 3: manca il nome'],
        ];
    }

    public function testTheApplicantCanBeExactly18(): void
    {
        self::assertSame([], formErrors(self::form(['DataNascitaRichiedente' => '15/06/2008']), self::TODAY));
    }

    public function testAnUnusableHouseholdSizeIsReportedOnce(): void
    {
        $errors = formErrors(self::form(['nComponentiNucleoFamiliare' => '23']), self::TODAY);

        self::assertSame(['Numero componenti: serve un numero da 1 a 22'], $errors);
    }

    public function testAHouseholdWithoutMembersIsReported(): void
    {
        $errors = formErrors(self::form(['Nome' => [], 'DataNascita' => [], 'Parentela' => [], 'Occupazione' => []]), self::TODAY);

        self::assertSame(['Composizione del nucleo: inserire almeno il richiedente'], $errors);
    }

    public function testAnEmptyFormReportsEveryRequiredField(): void
    {
        self::assertGreaterThanOrEqual(13, count(formErrors([], self::TODAY)));
    }

    public function testTheRecordGoesBackToTheFormUnchanged(): void
    {
        $record = recordFromForm(self::form());

        self::assertSame($record, recordFromForm(formFromRecord($record)));
    }

    public function testFillsTheFormWithDatesAndAmountsAsTheyAreTyped(): void
    {
        $form = formFromRecord(recordFromForm(self::form()));

        self::assertSame('20/04/1980', $form['DataNascitaRichiedente']);
        self::assertSame('30/12/2015', $form['DataNascita'][3]);
        self::assertSame('31/12/2026', $form['Termine'][2]);
        self::assertSame('', $form['Termine'][1]);
        self::assertSame('1200.5', $form['ValoreSPpas'][1]);
        self::assertSame('31/01/2027', $form['ScadenzaSPpas'][1]);
        self::assertSame('900', $form['ValoreENT'][1]);
        self::assertSame('', $form['ScadenzaENT'][1]);
        self::assertSame('2025', $form['Anno'][7]);
        self::assertSame('1', $form['InvaliditaGraveMinori']);
        self::assertSame('', $form['InItaliaDallAnno']);
    }

    public function testEscapesTextForHtml(): void
    {
        self::assertSame('&lt;b&gt; &amp; &quot;x&quot; &#039;y&#039;', e('<b> & "x" \'y\''));
        self::assertSame('12', e(12));
        self::assertSame('', e(null));
        self::assertSame('', e(['x']));
    }
}
