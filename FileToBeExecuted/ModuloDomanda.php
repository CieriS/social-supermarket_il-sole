<?php

    /*
        Application form: new application, update and repeated application.
        Domanda.php chooses the kind, the applicant and the household size and
        leaves them in the session.

        Everything that reads the request or the database happens here, before
        any output; the markup below only prints $Valori and $Errori.
    */

    require(__DIR__ . '/../src/bootstrap.php');
    \IlSole\Auth\requireLogin();

    require(__DIR__ . '/../src/Scoring.php');
    require(__DIR__ . '/../src/ApplicationForm.php');
    require(__DIR__ . '/../src/AssessmentFields.php');
    require(__DIR__ . '/../src/Rules.php');
    require(__DIR__ . '/../src/ApplicationRecord.php');
    require(__DIR__ . '/../src/ApplicationStore.php');

    use function IlSole\Form\cell;
    use function IlSole\Html\e;
    use function IlSole\Html\options;
    use function IlSole\Record\field;

    use const IlSole\Record\KIND_NEW;

    $TipoDomanda = $_SESSION['Domanda'] ?? '';
    if ( !in_array($TipoDomanda, \IlSole\Record\KINDS, true) ) {
        header('Location: Domanda.php');
        exit;
    }
    $DomandaNuova = $TipoDomanda === KIND_NEW;
    $CodiceFiscaleSessione = strtoupper((string) ($_SESSION['CodiceFiscale'] ?? ''));

    $RegolePunteggio = \IlSole\Store\loadRules($connection);
    $Errori = [];
    $Salvata = null;

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

        /* The applicant of an update is the one chosen in Domanda.php, whatever the form says */
        $Valori = $_POST;
        if ( !$DomandaNuova ) {
            $Valori['CodiceFiscale'] = $CodiceFiscaleSessione;
        }

        $Oggi = new DateTimeImmutable('today');
        $Errori = \IlSole\Record\formErrors($Valori, $Oggi->format('Y-m-d'));
        $Domanda = \IlSole\Record\recordFromForm($Valori);
        $Esiste = \IlSole\Store\applicantExists($connection, $Domanda['applicant']['Cod_Fiscale']);

        if ( $DomandaNuova && $Esiste ) {
            $Errori[] = "Questo richiedente è già registrato: per cambiarne i dati usare una domanda di modifica o reiterata";
        }
        if ( !$DomandaNuova && !$Esiste ) {
            $Errori[] = "Richiedente non trovato: ripartire dalla scelta della domanda";
        }

        if ( $Errori === [] ) {
            try {
                $Punteggi = \IlSole\Scoring\scoreApplication(
                    \IlSole\Form\applicationFromForm($Valori),
                    \IlSole\Store\loadIseeBrackets($connection),
                    $Oggi,
                    $RegolePunteggio
                );
                \IlSole\Store\saveRecord($connection, $Domanda, $Punteggi, $DomandaNuova);
                $Salvata = $Punteggi['PunteggioTotale'];
                unset($_SESSION['Domanda'], $_SESSION['nMembri'], $_SESSION['CodiceFiscale']);
            } catch ( InvalidArgumentException $Errore ) {
                $Errori[] = "Griglia di valutazione: i punti scelti non rientrano negli intervalli configurati";
            }
        }

    } elseif ( $DomandaNuova ) {

        $Valori = [];

    } else {

        $Domanda = \IlSole\Store\loadRecord($connection, $CodiceFiscaleSessione);
        if ( $Domanda === null ) {
            header('Location: Domanda.php');
            exit;
        }
        $Valori = \IlSole\Record\formFromRecord($Domanda);

    }

    /* The household size chosen in Domanda.php decides how many rows the form has */
    $nMembri = \IlSole\Record\wholeNumber((string) ($_POST['nComponentiNucleoFamiliare'] ?? $_SESSION['nMembri'] ?? ''), 1, \IlSole\Record\MAX_MEMBERS) ?? 1;
    $Valori['nComponentiNucleoFamiliare'] = (string) $nMembri;

    /* Value of a field of the form, and of a cell of one of its tables, ready for the markup */
    $v = fn (string $Nome) => e(field($Valori, $Nome));
    $c = fn (string $Nome, int $Riga) => e(cell($Valori, $Nome, $Riga));

?>
<!doctype html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/PunteggioStyle.css" rel="stylesheet" type="text/css">
        <title> Modulo Richiedente </title>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="icon" type="image/png" href="../FileInclude/immagini/icon.png">
        <script src="../FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body>

        <script>
            $( function() {
                $( ".datepicker" ).datepicker( { dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true, yearRange: '1900:+10' } );
            } );
        </script>

        <Div>
            <?php

                require('../FileInclude/FileRequired/NavBars.php');

                if ( $Salvata !== null ) {
                    ?>
                        <script>
                            alert(<?php echo json_encode("Domanda salvata\nPunteggio totale: ".$Salvata, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>);
                            window.location.replace("List.php");
                        </script>
                        </Div></body></html>
                    <?php
                    exit;
                }

            ?>

            <form action="ModuloDomanda.php" method="POST" autocomplete="Off">

                <?php echo \IlSole\Auth\csrfField(); ?>

                <img ID="container" class="banner" src="../FileInclude/immagini/Intestazione.svg" />

                <h1>DOMANDA DI ACCESSO ALL'EMPORIO SOLIDALE</h1>

                <?php if ( $Errori !== [] ) { ?>
                    <Div Class="Errori">
                        <p>La domanda non &egrave; stata salvata:</p>
                        <ul>
                            <?php foreach ( $Errori as $Errore ) { echo "<li>".e($Errore)."</li>"; } ?>
                        </ul>
                    </Div>
                <?php } ?>

                <!---- Dati Anagrafici e Residenziali del richiedente ---->

                <h3>DATI ANAGRAFICI E RESIDENZIALI DEL RICHIEDENTE</h3>

                <table Class="Anag Sep" border="0">

                    <tr>
                        <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="CognomeRichiedente" maxlength='30' value="<?php echo $v('CognomeRichiedente'); ?>" required/><br/><div Class="Weighter">Cognome</div></th>
                        <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="NomeRichiedente" maxlength='30' value="<?php echo $v('NomeRichiedente'); ?>" required/><br/><div Class="Weighter">Nome</div></th>
                    </tr>

                    <tr>
                        <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="LuogoNascitaRichiedente" maxlength='30' value="<?php echo $v('LuogoNascitaRichiedente'); ?>" required/><br/><div Class="Weighter">Nat... a</div></th>
                        <th Class="AlignLeft"><input Class="DatiAnagrafici datepicker" type='text' minlength='10' maxlength='10' name="DataNascitaRichiedente" value="<?php echo $v('DataNascitaRichiedente'); ?>" required/><br/><div Class="Weighter">Il</div></th>
                        <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="Cittadinanza" maxlength='15' value="<?php echo $v('Cittadinanza'); ?>" required/><br/><div Class="Weighter">Cittadinanza</div></th>
                        <th Class="AlignLeft"><input Class="DatiAnagrafici" type="number" min='1900' max='3100' name="InItaliaDallAnno" value="<?php echo $v('InItaliaDallAnno'); ?>" /><br/><div Class="Weighter">In Italia Dall'Anno</div></th>
                    </tr>

                    <tr>
                        <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="Residenza" maxlength='30' value="<?php echo $v('Residenza'); ?>" required/><br/><div Class="Weighter">Residente a</div></th>
                        <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="Via" maxlength='30' value="<?php echo $v('Via'); ?>" required/><br/><div Class="Weighter">Via</div></th>
                        <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="CodiceFiscale" minlength="16" maxlength="16" value="<?php echo $DomandaNuova ? $v('CodiceFiscale') : e($CodiceFiscaleSessione); ?>" <?php echo $DomandaNuova ? '' : 'readonly'; ?> required/><br/><div Class="Weighter">Codice Fiscale</div></th>
                    </tr>

                    <tr>
                        <th colspan="4" Class="AlignLeft">
                            <select Class="DatiAnagrafici" name="TipoDiAlloggio" required>
                                <?php echo options(\IlSole\Record\HOUSING, field($Valori, 'TipoDiAlloggio'), ['Proprieta' => 'Propriet&agrave;']); ?>
                            </select>
                        <br/><div Class="Weighter">Tipo di Alloggio (Propriet&agrave;, Affitto Privato, Convenzionato, Popolare, Emergenza abitativa)</div></th>
                    </tr>

                    <tr>
                        <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" minlength="9" maxlength="9" name="DocumentoDIdent" value="<?php echo $v('DocumentoDIdent'); ?>" /><br/><div Class="Weighter">Documento D'Identit&agrave;</div></th>
                        <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="DocumentoDImmig" minlength="10" maxlength="10" value="<?php echo $v('DocumentoDImmig'); ?>" /><br/><div Class="Weighter">Documento D'Immigrazione</div></th>
                    </tr>

                    <tr>
                        <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" maxlength="15" name="Telefono" value="<?php echo $v('Telefono'); ?>" required/><br/><div Class="Weighter">Telefono</div></th>
                        <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="email" name="Mail" maxlength='100' value="<?php echo $v('Mail'); ?>" required/><br/><div Class="Weighter">E-mail</div></th>
                    </tr>

                    <tr>
                        <th colspan="4" Class="AlignLeft">
                            <select Class="DatiAnagrafici" name="ServiziSociali" required>
                                <?php echo options(\IlSole\Record\SOCIAL_SERVICES, field($Valori, 'ServiziSociali')); ?>
                            </select>
                        <br/><div Class="Weighter">Gi&agrave; in carico ai Servizi Sociali (SI, NO, AREA, Ass. Sociale)</div></th>
                    </tr>

                    <tr>
                        <th colspan="4" Class="AlignLeft">
                            <input Class="DatiAnagrafici" type="text" name="ComeHaConosciutoLEmporio" maxlength='30' value="<?php echo $v('ComeHaConosciutoLEmporio'); ?>" />
                        <br/><div Class="Weighter">Come ha conosciuto l'Emporio</div></th>
                    </tr>

                    <tr>
                        <th colspan='4' Class='AlignLeft'>
                            <input Class='DatiAnagrafici' type='text' name='TipoDiDomanda' value="<?php echo e($TipoDomanda); ?>" readonly/>
                        <br/><div Class='Weighter'>Tipo di Domanda (Domanda Nuova - Domanda di modifica - Domanda reiterata)</div></th>
                    </tr>

                    <tr>
                        <th colspan='4' Class='AlignLeft'>
                            <input Class='DatiAnagrafici' type='number' name='nComponentiNucleoFamiliare' value="<?php echo $nMembri; ?>" readonly/>
                        <br/><div Class='Weighter'>Numero Componenti Nucleo Familiare (Incluso Richiedente)</div></th>
                    </tr>

                </table>

                <p Class="interruzione">&nbsp;</p>

                <!---- Composizione del Nucleo Familiare ---->

                <h3><span Class="underline">COMPOSIZIONE NUCLEO FAMILIARE (Come da ISEE)</span></h3>

                <table Class="test" border='1'>

                    <tr>
                        <th Class="BGcolorIntestazioneTable">N.</th>
                        <th Class="BGcolorIntestazioneTable">Nome</th>
                        <th Class="BGcolorIntestazioneTable">Nat... a</th>
                        <th Class="BGcolorIntestazioneTable">Il</th>
                        <th Class="BGcolorIntestazioneTable">Parentela</th>
                        <th Class="BGcolorIntestazioneTable">Occupazione</th>
                    </tr>

                    <?php for ( $i=1; $i<=$nMembri; $i++ ) { ?>
                        <tr>
                            <td Class='bold'><?php echo $i; ?></td>
                            <td><input title='Nome e Cognome del componente' maxlength='60' type='text' name='Nome[<?php echo $i; ?>]' value="<?php echo $c('Nome', $i); ?>" /></td>
                            <td><input type='text' name='LuogoNascita[<?php echo $i; ?>]' maxlength='30' value="<?php echo $c('LuogoNascita', $i); ?>" /></td>
                            <td><input type='text' class='datepicker' minlength='10' maxlength='10' name='DataNascita[<?php echo $i; ?>]' value="<?php echo $c('DataNascita', $i); ?>" /></td>
                            <td>
                                <select style='padding-left:2vw' class='AlignCenter' name='Parentela[<?php echo $i; ?>]'>
                                    <?php echo options(\IlSole\Record\RELATIONS, cell($Valori, 'Parentela', $i)); ?>
                                </select>
                            </td>
                            <td>
                                <select style='padding-left:2vw' class='AlignCenter' name='Occupazione[<?php echo $i; ?>]'>
                                    <?php echo options(\IlSole\Record\OCCUPATIONS, cell($Valori, 'Occupazione', $i)); ?>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>

                </table>

                <h3><span Class="underline Sep">SITUAZIONE SOCIO-SANITARIA DEL NUCLEO FAMILIARE</span></h3>

                <table Class="Sep" border='1'>

                    <tr>
                        <th Class="BGcolorIntestazioneTable">N.</th>
                        <th Class="BGcolorIntestazioneTable">Evento</th>
                        <th Class="BGcolorIntestazioneTable">Anno</th>
                        <th Class="BGcolorIntestazioneTable">Doc. All.</th>
                    </tr>

                    <?php foreach ( \IlSole\Record\EVENTS as $i => $Evento ) { ?>
                        <tr>
                            <td Class='bold'><?php echo $i; ?></td>
                            <td> <?php echo $Evento; ?> </td>
                            <td><input type='number' min='1900' max='3100' name='Anno[<?php echo $i; ?>]' value="<?php echo $c('Anno', $i); ?>" /></td>
                            <td><input type='text' maxlength='30' name='DocAll[<?php echo $i; ?>]' value="<?php echo $c('DocAll', $i); ?>" /></td>
                        </tr>
                    <?php } ?>

                </table>

                <p Class="interruzione">&nbsp;</p>

                <!---- Condizione Lavorativa del Nucleo Familiare: una scheda per componente ---->

                <h3><span Class="underline">CONDIZIONE LAVORATIVA DEL NUCLEO FAMILIARE</span><br/><span Class="TextSmaller">(esclusi i componenti minorenni; il numero &egrave; quello della composizione del nucleo)</span></h3>

                <?php for ( $i=1; $i<=$nMembri; $i++ ) { ?>
                    <table Class='CLNF' border='0'>
                        <tr>
                            <th valign='top' Class='AlignLeft' rowspan='6'><?php echo $i; ?></th>
                            <td Class='AlignLeft bold'>Titolo Di Studio</td><th colspan='3'><input type='text' maxlength='20' name='TitoloDiStudio[<?php echo $i; ?>]' value="<?php echo $c('TitoloDiStudio', $i); ?>" /></th>
                        </tr>
                        <tr>
                            <td Class='AlignLeft bold'>Competenze</td><th colspan='3'><input type='text' maxlength='20' name='Competenze[<?php echo $i; ?>]' value="<?php echo $c('Competenze', $i); ?>" /></th>
                        </tr>
                        <tr>
                            <td Class='AlignLeft bold'>Conoscenza Lingua</td><th><input Class='smaller' type='text' maxlength='30' name='ConoscenzaLingua[<?php echo $i; ?>]' value="<?php echo $c('ConoscenzaLingua', $i); ?>" /></th><td Class='AlignRight bold'>Patente</td><th>
                                <select style='padding-left: 10.5vw;' Class='smaller DatiAnagrafici' name='Patente[<?php echo $i; ?>]'>
                                    <?php echo options(\IlSole\Record\LICENCES, cell($Valori, 'Patente', $i)); ?>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <td Class='AlignLeft bold'>Attivit&agrave; Attuale</td><th colspan='3'><input type='text' maxlength='15' name='AttAtt[<?php echo $i; ?>]' value="<?php echo $c('AttAtt', $i); ?>" /></th>
                        </tr>
                        <tr>
                            <td Class='AlignLeft bold'>Presso</td><th><input type='text' Class='smaller' maxlength='15' name='Presso[<?php echo $i; ?>]' value="<?php echo $c('Presso', $i); ?>" /></th><td Class='AlignRight bold'>Termine</td><th><input Class='smaller datepicker' type='text' maxlength='10' name='Termine[<?php echo $i; ?>]' value="<?php echo $c('Termine', $i); ?>" /></th>
                        </tr>
                        <tr>
                            <td Class='AlignLeft bold'>Senza Lavoro Da</td><th colspan='3'><input type='text' class='datepicker' maxlength='10' name='SenzaLavoroDa[<?php echo $i; ?>]' value="<?php echo $c('SenzaLavoroDa', $i); ?>" /></th>
                        </tr>
                    </table>
                <?php } ?>

                <p Class="interruzione">&nbsp;</p>

                <!---- Analisi Economico Finanziaria Nucleo Familiare ---->

                <h3><span Class="underline">ANALISI ECONOMICO/FINANZIARIA DEL NUCLEO FAMILIARE</span></h3>

                <table Class="Sep">
                    <tr>
                        <th Class="sot bold AlignRight">INDICE ISEE: <input type="number" name="IndiceISEE" min="0" step="1" value="<?php echo $v('IndiceISEE'); ?>" required/></th>
                    </tr>
                </table>

                <?php
                    /* Titolo => [righe, campo del valore, campo della scadenza] */
                    $Tabelle = [
                        'PATRIMONIO ATTIVO DEL NUCLEO FAMILIARE' => [\IlSole\Record\ASSETS, 'ValoreSPatt', null],
                        'PATRIMONIO PASSIVO DEL NUCLEO FAMILIARE' => [\IlSole\Record\LIABILITIES, 'ValoreSPpas', 'ScadenzaSPpas'],
                        'ENTRATE DEL NUCLEO FAMILIARE' => [\IlSole\Record\INCOME, 'ValoreENT', 'ScadenzaENT'],
                        'USCITE DEL NUCLEO FAMILIARE' => [\IlSole\Record\EXPENSES, 'ValoreUSC', 'ScadenzaUSC'],
                    ];
                    foreach ( $Tabelle as $Titolo => [$Righe, $CampoValore, $CampoScadenza] ) {
                ?>
                    <h3><span Class="underline"><?php echo $Titolo; ?></span></h3>

                    <table Class="Sep" border='1'>
                        <tr>
                            <th Class="BGcolorIntestazioneTable">N.</th>
                            <th Class="BGcolorIntestazioneTable">Elemento</th>
                            <th Class="BGcolorIntestazioneTable">Valore</th>
                            <?php if ( $CampoScadenza !== null ) { ?><th Class="BGcolorIntestazioneTable">Scadenza</th><?php } ?>
                        </tr>
                        <?php foreach ( $Righe as $i => $Elemento ) { ?>
                            <tr>
                                <td Class='AlignLeft'><?php echo $i; ?></td>
                                <td Class='AlignLeft'> <?php echo $Elemento; ?> </td>
                                <td><input type='number' min='0' step='0.01' name='<?php echo $CampoValore; ?>[<?php echo $i; ?>]' value="<?php echo $c($CampoValore, $i); ?>" /></td>
                                <?php if ( $CampoScadenza !== null ) { ?><td><input type='text' class='datepicker' minlength='10' maxlength='10' name='<?php echo $CampoScadenza; ?>[<?php echo $i; ?>]' value="<?php echo $c($CampoScadenza, $i); ?>" /></td><?php } ?>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>

                <p Class="interruzione">&nbsp;</p>

                <!---- Disponibilità Collaborative, Griglia e Valutazione Commissione ---->

                <h3><span Class="underline">DISPONIBILIT&Agrave; COLLABORATIVE</span></h3>

                <table Class="DomandeFinali" border='1'>
                    <tr><th Class="BGchocolate">Ambito Formativo</th></tr>
                    <tr><td><textarea Class="padding" name="Formativo" maxlength="255"><?php echo $v('Formativo'); ?></textarea></td></tr>
                    <tr><th Class="BGorange">Ambito Lavorativo</th></tr>
                    <tr><td><textarea Class="padding" name="Lavorativo" maxlength="255"><?php echo $v('Lavorativo'); ?></textarea></td></tr>
                    <tr><th Class="BGyellow">Ambito Sociale</th></tr>
                    <tr><td><textarea Class="padding" name="Sociale" maxlength="255"><?php echo $v('Sociale'); ?></textarea></td></tr>
                </table>

                <?php echo \IlSole\Form\assessmentFieldsHtml($Valori, $RegolePunteggio); ?>

                <h3><span Class="underline">VALUTAZIONI DELLA COMMISSIONE</span></h3>

                <Div class="TextAreaDiv">
                    <textarea name="ValutCommissione" maxlength="255"><?php echo $v('ValutCommissione'); ?></textarea>
                </Div>

                <!---- Data Odierna, Immagine footer e footer finale ---->

                <table Class='dataodierna'>
                    <tr>
                        <th Class='AlignLeft'><input type='text' value='<?php echo Date('d/m/Y'); ?>' readonly/><br/><span Class='Weighter'>Data</span></th>
                    </tr>
                </table>

                <input Class="invia" type="submit" name="invia" value="invia" />

                <img ID="container" class="Footer" src="../FileInclude/immagini/Footer.png" />

                <Div Class="Fin">
                    <Div class="Copyright">
                        <Div><span>Copyright &copy; <?php echo "2002-".Date('Y'); ?> Samuele Cieri Inc.<br/>Tutti i diritti riservati.</span></Div>
                    </Div>
                    <Div>
                        <span><a href="https://cierisamuele.vercel.app" target="_blank"><img Class="Signature" src="../FileInclude/immagini/FirmaDigitale_2.png" /></a></span>
                    </Div>
                </Div>

            </form>

        </Div>

    </body>
</html>
