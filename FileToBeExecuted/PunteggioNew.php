<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/PunteggioStyle.css" rel="stylesheet" type="text/css">
        <title> Modulo Richiedente </title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="icon" type="image/png" href="../FileInclude/immagini/icon.png">
        <script src="../FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body> <!---- <Body id="container"> ---->

        <script>
            $( function() {
                $( ".datepicker" ).datepicker( { dateFormat: 'dd/mm/yy' } ).val;
            } );

            $( function() {
                $( "#datepicker" ).datepicker( { dateFormat: 'dd/mm/yy' } ).val;
            } );
        </script>

        <Div>
            <?php

                session_start();
            
                /* File Required */
                    require('../FileInclude/FileRequired/Connessione.php');
                    require('../FileInclude/FileRequired/NavBars.php');
                    require('../FileInclude/FileRequired/Functions.php');
                    require(__DIR__ . '/../src/Scoring.php');
                    require(__DIR__ . '/../src/ApplicationForm.php');
                /*————*/

                /* Metodo Postback */
                    if($_POST){ 

                        /* √ Variabili Generalità Richiedente √ */

                            /* Salvataggio di tutte le variabili *Quelle sotto "if(isset) non sono sempre obbligatorie* (Ho Considerato la mail non obbligatoria) */
                            $CognomeRichiedente=ucwords(strtolower($_REQUEST['CognomeRichiedente']));
                            $NomeRichiedente=ucwords(strtolower($_REQUEST['NomeRichiedente']));
                            $LuogoNascitaRichiedente=ucwords(strtolower($_REQUEST['LuogoNascitaRichiedente']));
                            $DataNascitaRichiedente=\IlSole\Form\databaseDate($_REQUEST['DataNascitaRichiedente']);
                            $Cittadinanza=ucwords(strtolower($_REQUEST['Cittadinanza']));
                            if( $_REQUEST['InItaliaDallAnno'] != "" ){
                                $InItaliaDallAnno=$_REQUEST['InItaliaDallAnno'];
                            } else {
                                $InItaliaDallAnno="NULL";
                            }
                            $Residenza=ucwords(strtolower($_REQUEST['Residenza']));
                            $Via=ucwords(strtolower($_REQUEST['Via']));
                            $CodiceFiscale=STRTOUPPER($_REQUEST['CodiceFiscale']);
                            if(isset($_REQUEST['TipoDiAlloggio'])){
                                $Alloggio=$_REQUEST['TipoDiAlloggio'];
                            } else {
                                $Alloggio="";
                            }
                            $DocumentoDIdentita=ucwords(strtolower($_REQUEST['DocumentoDIdent']));
                            if(isset($_REQUEST['DocumentoDImmig'])){
                                $DocumentoDImmigrazione=$_REQUEST['DocumentoDImmig'];
                            } else {
                                $DocumentoDImmigrazione="";
                            }
                            if(isset($_REQUEST['Telefono'])){
                                $Telefono=$_REQUEST['Telefono'];
                            } else {
                                $Telefono="";
                            }
                            if(isset($_REQUEST['Mail'])){
                                $Mail=$_REQUEST['Mail'];
                            } else{
                                $Mail="";
                            }
                            if( isset($_REQUEST['ServiziSociali']) ){
                                $ServiziSociali=$_REQUEST['ServiziSociali'];
                            } else{
                                $ServiziSociali="";
                            }
                            if(isset($_REQUEST['ComeHaConosciutoLEmporio'])){
                                $ComeHaConosciutoEmporio=$_REQUEST['ComeHaConosciutoLEmporio'];
                            } else {
                                $ComeHaConosciutoEmporio="";
                            }
                            if( isset($_REQUEST['TipoDiDomanda']) ){
                                $TipoDiDomanda=$_REQUEST['TipoDiDomanda'];
                            } else{
                                $TipoDiDomanda="";
                            }
                            $nMembri=$_REQUEST['nComponentiNucleoFamiliare'];
                        /*————*/

                        /* Condizione Codice Fiscale non duplicato */
                            $queryCodiciFiscali="SELECT Cod_Fiscale FROM Richiedente;";
                            $risultatoCodiciFiscali=mysqli_query($connection,$queryCodiciFiscali) OR DIE(mysqli_error($connection));
                            while ( $riga=mysqli_fetch_array($risultatoCodiciFiscali,MYSQLI_ASSOC) ) {
                                if ( strtoupper($_REQUEST['CodiceFiscale']) == $riga['Cod_Fiscale'] && $TipoDiDomanda != "Domanda Di Modifica" && $TipoDiDomanda != "Domanda Reiterata" ) {
                                    ?>
                                        <script>
                                            alert("Questo richiedente è già stato registrato in precedenza\nSe la nuova domanda è di modifica o reiterata, selezionare l'apposita opzione");
                                            window.location.replace("Domanda.php");
                                        </script>
                                    <?php
                                    DIE();
                                }
                            }
                        /*————*/

                        /* Patrimonio, Entrate e Uscite */
                            $ValPatrimonioAtt=array_map('ucwords',array_map('strtolower',$_REQUEST['ValoreSPatt']));
                            $ValPatrimonioPas=array_map('ucwords',array_map('strtolower',$_REQUEST['ValoreSPpas']));
                            $ScadPatrimonioPas=$_REQUEST['ScadenzaSPpas'];
                            $ValEntrate=array_map('ucwords',array_map('strtolower',$_REQUEST['ValoreENT']));
                            $ScadEntrate=$_REQUEST['ScadenzaENT'];
                            $ValUscite=array_map('ucwords',array_map('strtolower',$_REQUEST['ValoreUSC']));
                            $ScadUscite=$_REQUEST['ScadenzaUSC'];

                            foreach ( $ScadPatrimonioPas as $ind => $val ) {
                                $ScadPatrimonioPas[$ind]=DATE("Y-m-d",strtotime($val));
                            }

                            foreach ( $ScadEntrate as $ind => $val ) {
                                $ScadEntrate[$ind]=DATE("Y-m-d",strtotime($val));
                            }

                            foreach ( $ScadUscite as $ind => $val ) {
                                $ScadUscite[$ind]=DATE("Y-m-d",strtotime($val));
                            }
                        /*————*/

                        /* √ Varibili Generalità membri nucleo familiare √ */

                            $NomeMembroLavoratore=array_map('ucwords',array_map('strtolower',$_REQUEST['NomeMembroLavoratore']));

                                if( isset($_REQUEST["Nome"]) ) {
                                    $NomeMembro=array_map('ucwords',array_map('strtolower',$_REQUEST['Nome']));
                                }
                                if( isset($_REQUEST["LuogoNascita"]) ) {
                                    $LuogoNascitaMembro=array_map('ucwords',array_map('strtolower',$_REQUEST['LuogoNascita']));
                                }
                                if( isset($_REQUEST["DataNascita"]) ) {
                                    $DataNascitaMembro=$_REQUEST['DataNascita'];

                                    foreach ( $DataNascitaMembro as $ind => $val ) {
                                        $DataNascitaMembro[$ind]=\IlSole\Form\databaseDate($val);
                                    }

                                }
                                if( isset($_REQUEST["Parentela"]) ) {
                                    $ParentelaMembro=array_map('ucwords',array_map('strtolower',$_REQUEST['Parentela']));

                                }
                                if( isset($_REQUEST["Occupazione"]) ) {
                                    $OccupazioneMembro=array_map('ucwords',array_map('strtolower',$_REQUEST['Occupazione']));
                                }
                                if( isset($_REQUEST["TitoloDiStudio"]) ) {
                                    $TitoloDiStudio=array_map('ucwords',array_map('strtolower',$_REQUEST['TitoloDiStudio']));
                                }
                                if( isset($_REQUEST["Competenze"]) ) {
                                    $Competenze=array_map('ucwords',array_map('strtolower',$_REQUEST['Competenze']));
                                }
                                if( isset($_REQUEST["ConoscenzaLingua"]) ) {
                                    $ConoscenzaLingua=array_map('ucwords',array_map('strtolower',$_REQUEST['ConoscenzaLingua']));
                                }
                                if( isset($_REQUEST["Patente"]) ) {
                                    $Patente=array_map('ucwords',array_map('strtolower',$_REQUEST['Patente']));
                                }
                                if( isset($_REQUEST["AttAtt"]) ) {
                                    $AttAtt=array_map('ucwords',array_map('strtolower',$_REQUEST['AttAtt']));
                                }
                                if( isset($_REQUEST["Presso"]) ) {
                                    $Presso=array_map('ucwords',array_map('strtolower',$_REQUEST['Presso']));
                                }
                                if( isset($_REQUEST["Termine"]) ) {
                                    $Termine=array_map('ucwords',array_map('strtolower',$_REQUEST['Termine']));
                                }
                                if( isset($_REQUEST["SenzaLavoroDa"]) ) {
                                    $SenzaLavoroDa=array_map('ucwords',array_map('strtolower',$_REQUEST['SenzaLavoroDa']));
                                }
                                if( isset($_REQUEST["NomeExtraISEE"]) ) {
                                    $NomeMembroExtraISEE=array_map('ucwords',array_map('strtolower',$_REQUEST['NomeExtraISEE']));
                                }
                                if( isset($_REQUEST["LuogoNascitaMembroExtraISEE"]) ) {
                                    $LuogoNascitaMembroExtraISEE=array_map('ucwords',array_map('strtolower',$_REQUEST['LuogoNascitaExtraISEE']));
                                }
                                if( isset($_REQUEST["DataNascitaMembroExtraISEE"]) ) {
                                    $DataNascitaMembroExtraISEE=array_map('ucwords',array_map('strtolower',$_REQUEST['DataNascitaExtraISEE']));
                                }
                                if( isset($_REQUEST["ParentelaMembroExtraISEE"]) ) {
                                    $ParentelaMembroExtraISEE=array_map('ucwords',array_map('strtolower',$_REQUEST['ParentelaExtraISEE']));
                                }
                                if( isset($_REQUEST["OccupazioneMembroExtraISEE"]) ) {
                                    $OccupazioneMembroExtraISEE=array_map('ucwords',array_map('strtolower',$_REQUEST['OccupazioneExtraISEE']));
                                }
                        /*————*/

                        /* Ricezionse SSS */
                                if( isset($_REQUEST["Anno"])  ){
                                    $AnnoSSS=array_map('ucwords',array_map('strtolower',$_REQUEST['Anno']));
                                }
                                if( isset($_REQUEST["DocAll"])  ){
                                    $DocAllSSS=array_map('ucwords',array_map('strtolower',$_REQUEST['DocAll']));
                                }
                        /*————*/

                        /* √ Variabili Punteggi √ */
                            $ISEE=$_REQUEST['IndiceISEE'];
                        /*————*/

                        /* √ Variabili domande finali e valutazione √ */
                            $AmbitoFormativo=$_REQUEST['Formativo'];
                            $AmbitoLavorativo=$_REQUEST['Lavorativo'];
                            $AmbitoSociale=$_REQUEST['Sociale'];
                            $ValutazioneCommissione=$_REQUEST['ValutCommissione'];
                        /*————*/

                        /* Calcolo Punteggi: le regole della griglia sono in src/Scoring.php */
                            $risindISEE=mysqli_query($connection,"SELECT ISEEmin, ISEEmax, Punteggio FROM IndicatoriISEE") OR DIE(mysqli_error($connection));
                            $IndicatoriISEE=mysqli_fetch_all($risindISEE,MYSQLI_ASSOC);

                            try {
                                $Punteggi=\IlSole\Scoring\scoreApplication(
                                    \IlSole\Form\applicationFromForm($_REQUEST),
                                    $IndicatoriISEE,
                                    new DateTimeImmutable('today')
                                );
                            } catch ( InvalidArgumentException $Errore ) {
                                ?>
                                    <script>
                                        alert("I dati inseriti non permettono di calcolare il punteggio\nControllare le date di nascita e l'indicatore ISEE");
                                        window.location.replace("Domanda.php");
                                    </script>
                                <?php
                                DIE();
                            }

                            $PunteggioISEE=$Punteggi['PunteggioISEE'];
                            $PunteggioPresenzaMinori=$Punteggi['PunteggioPresenzaMinori'];
                            $PunteggioDisoccupazione=$Punteggi['PunteggioDisoccupazione'];
                            $PunteggioInvalidi=$Punteggi['PunteggioInvalidi'];
                            $PunteggioSituazioneDebitoria=$Punteggi['PunteggioSituazioneDebitoria'];
                            $PunteggioBenefit=$Punteggi['PunteggioBenefit'];
                            $PunteggioTotale=$Punteggi['PunteggioTotale'];
                        /*————*/

                        /* √ Scrittura √ */
                            echo "<table style='font-size: 1vw;' border='1'><tr><tr><th>PunteggioISEE</th><th>PunteggioPresenzaMinori</th><th>PunteggioDisoccupazione</th><th>PunteggioInvalidi</th><th>PunteggioSituazioneDebitoria</th><th>PunteggioBenefit</th><th>PunteggioTotale</th></tr><th>$PunteggioISEE</th><th>$PunteggioPresenzaMinori</th><th>$PunteggioDisoccupazione</th><th>$PunteggioInvalidi</th><th>$PunteggioSituazioneDebitoria</th><th>$PunteggioBenefit</th><th>$PunteggioTotale</th></tr></table>";
                        /*————*/

                        /* INSERT */
                                        /* Inserimento richiedente */
                                            $insertRichiedente="INSERT INTO Richiedente (Cod_Fiscale, Cognome, Nome, LuogoNascita, DataNascita, Cittadinanza, InItaliaDallAnno, Residenza, Via, TipoAlloggio, DocumentoIdent, DocumentoImmig, Telefono, Mail, ServiziSociali, ComeHaConosciutoEmporio, nMembri, ISEE) VALUES 
                                            ('".$CodiceFiscale."','".$CognomeRichiedente."','".$NomeRichiedente."','".$LuogoNascitaRichiedente."','".$DataNascitaRichiedente."','".$Cittadinanza."',".$InItaliaDallAnno.",'".$Residenza."','".$Via."','".$Alloggio."','".$DocumentoDIdentita."','".$DocumentoDImmigrazione."','".$Telefono."','".$Mail."','".$ServiziSociali."','".$ComeHaConosciutoEmporio."',".$nMembri.",".$ISEE.");";
                                            
                                            echo "$insertRichiedente"."<br/>";
                                            $risultatoRichiedente=mysqli_query($connection,$insertRichiedente) OR DIE(mysqli_error($connection));

                                        /*————*/

                                        /* inserimento Punteggio */
                                            $insertPunteggio="INSERT INTO Punteggio (CodFamiglia, PunteggioISEE, PunteggioPresenzaMinori, PunteggioDisoccupazione, PunteggioInvalidi, PunteggioSituazioneDebitoria, PunteggioBenefit, PunteggioTotale) VALUES ( '".$CodiceFiscale."',".$PunteggioISEE.",".$PunteggioPresenzaMinori.",".$PunteggioDisoccupazione.",".$PunteggioInvalidi.",".$PunteggioSituazioneDebitoria.",".$PunteggioBenefit.",".$PunteggioTotale." );";    

                                            echo $insertPunteggio."<br/>";
                                            $risultatoPunteggio=mysqli_query($connection,$insertPunteggio) OR DIE(mysqli_error($connection));
                                            
                                        /*————*/

                                        /* inserimento Membri */

                                            for ( $i=1;$i<=$nMembri;$i++ ) {
                                                if ( $NomeMembro[$i] != "" && $LuogoNascitaMembro[$i] != "" && $ParentelaMembro[$i] != "" ) {
                                                    $insertMembri="INSERT INTO Membri (nMembro, NomeCognome, LuogoNascita, DataNascita, Parentela, Occupazione, TitoloDiStudio, Competenze, ConoscenzaLingua, Patente, AttualeAtt, Presso, Termine, SenzaLavoroDa, CodFamiglia) 
                                                    VALUES (".$i.",'".$NomeMembro[$i]."','".$LuogoNascitaMembro[$i]."','".$DataNascitaMembro[$i]."','".$ParentelaMembro[$i]."','".$OccupazioneMembro[$i]."','".$TitoloDiStudio[$i]."','".$Competenze[$i]."','".$ConoscenzaLingua[$i]."','".$Patente[$i]."','".$AttAtt[$i]."','".$Presso[$i]."','".$Termine[$i]."','".$SenzaLavoroDa[$i]."','".$CodiceFiscale."');";

                                                    echo "$insertMembri<br/>";
                                                    $risultatoInserimentoMembri=mysqli_query($connection,$insertMembri) OR DIE(mysqli_error($connection));
                                                }

                                            }

                                        /*————*/

                                        /* inserimento Situazione Socio Sanitaria */

                                            $Evento=array(1=>"Mancanza del partner per...","Violenze e maltrattamenti familiari","Presenza invalidt&agrave; media (oltre il 75&#65285;)","Presenza invalidit&agrave; grave (oltre il 100&#65285;)","Restrizioni della libert&agrave;","Pignoramenti in corso","Morosit&agrave; per affitto e/o utenze domestiche","Mancato pagamento di rate del mutuo casa","Avviso di sfratto","Sfratto imminente","Non poter sostenere spese impreviste","Non potersi permettere l'automobile)");

                                            for ( $i=1;$i<=12;$i++ ) {
                                                if ( $AnnoSSS[$i] != "" && $DocAllSSS[$i] != "" ) {
                                                    $insertSSS="INSERT INTO SituazioneSocioSanitaria (N, CodFamiglia, Evento, Anno, DocAll) 
                                                    VALUES (".$i.",'".$CodiceFiscale."','".$Evento[$i]."','".$AnnoSSS[$i]."','".$DocAllSSS[$i]."');";

                                                    echo "$insertSSS<br/>";
                                                    $risultatoSSS=mysqli_query($connection,$insertSSS) OR DIE(mysqli_error($connection));
                                                }
                                            }

                                        /*————*/

                                        /* inserimento Disponibilità Collaborative */

                                            $insertDispColl="INSERT INTO DispCollaborative (CodFamiglia, AmbitoFormativo, AmbitoLavorativo, AmbitoSociale) 
                                            VALUES ('".$CodiceFiscale."','".$AmbitoFormativo."','".$AmbitoLavorativo."','".$AmbitoSociale."');";

                                            echo "$insertDispColl"."<br/>";
                                            $risultatoDispColl=mysqli_query($connection,$insertDispColl) OR DIE(mysqli_error($connection));

                                        /*————*/

                                        /* inserimento Patrimonio Attivo */

                                            $ElementoPatrAtt=array(1=>"Immobili","Veicoli","Crediti (Liquidazioni, Assicurazioni, Altro)","Altre Voci");

                                            for ( $i=1;$i<=count($ElementoPatrAtt);$i++ ) {
                                                if ( $ValPatrimonioAtt[$i] != "" ) {
                                                    $insertPatrAtt="INSERT INTO PatrAtt (N, CodFamiglia, Elemento, Valore) 
                                                    VALUES (".$i.",'".$CodiceFiscale."','".$ElementoPatrAtt[$i]."',".$ValPatrimonioAtt[$i].");";

                                                    echo "$insertPatrAtt"."<br/>";
                                                    $risultatoPatrAtt=mysqli_query($connection,$insertPatrAtt) OR DIE(mysqli_error($connection));
                                                }
                                            }

                                        /*————*/

                                        /* inserimento Patrimonio Passivo */

                                            $ElementoPatrPass=array(1=>"Debiti v/Pubblica Amm.ne e Agenzia delle Entrate","Mutui","Debiti Privati e/o ACER","Altre Voci");

                                            for ( $i=1;$i<=count($ElementoPatrPass);$i++ ) {
                                                if ( $ValPatrimonioPas[$i] != "" ) {
                                                    $insertPatrPass="INSERT INTO PatrPass (N, CodFamiglia, Elemento, Valore, Scadenza) 
                                                    VALUES (".$i.",'".$CodiceFiscale."','".$ElementoPatrPass[$i]."','".$ValPatrimonioPas[$i]."','".$ScadPatrimonioPas[$i]."');";

                                                    echo "$insertPatrPass"."<br/>";
                                                    $risultatoPatrPass=mysqli_query($connection,$insertPatrPass) OR DIE(mysqli_error($connection));
                                                }
                                            }

                                        /*————*/

                                        /* inserimento Entrate */

                                            $ElementoENT=array(1=>"Stipendi","Pensioni di Anzianit&agrave;","Pensioni di invalidit&agrave;, accompagnamento, cura","Indennit&agrave;: (Disoccupazione, Mobilit&agrave;, Altro)","Contributi: (REI, RES, Reddito Cittadinanza)","Entrate straordinarie (compresi sociali consolidate)","Altre entrate (Ass. Mantenimento, altro)");

                                            for ( $i=1;$i<=count($ElementoENT);$i++ ) {
                                                if ( $ValEntrate[$i] != "" ) {
                                                    $insertEntrate="INSERT INTO Entrate (N, CodFamiglia, Elemento, Valore, Scadenza) 
                                                    VALUES (".$i.",'".$CodiceFiscale."','".$ElementoENT[$i]."','".$ValEntrate[$i]."','".$ScadEntrate[$i]."');";

                                                    echo "$insertEntrate"."<br/>";
                                                    $risultatoEntrate=mysqli_query($connection,$insertEntrate) OR DIE(mysqli_error($connection));
                                                }
                                            }

                                        /*————*/

                                        /* inserimento Uscite */
                                        
                                            $ElementoUSC=array(1=>"Affitto Mensile","Rata mensile di mutui","Utenze medie mensili","Spese medico-sanitarie","Rate mensili di debiti privati:","Altre Uscite:");

                                            for ( $i=1;$i<=count($ElementoUSC);$i++ ) {
                                                if ( $ValUscite[$i] != "" ) {
                                                        $insertUscite="INSERT INTO Uscite (N, CodFamiglia, Elemento, Valore, Scadenza) 
                                                        VALUES (".$i.",'".$CodiceFiscale."','".$ElementoUSC[$i]."','".$ValUscite[$i]."','".$ScadUscite[$i]."');";

                                                        echo "$insertUscite"."<br/>";
                                                        $risultatoUscite=mysqli_query($connection,$insertUscite) OR DIE(mysqli_error($connection));
                                                    }
                                                }
                                        /*————*/

                        /*————*/

                        /* Alert Di Conferma */
                            /*
                            ?>

                                <script>

                                    alert("i dati del richiedente e del suo nucleo familiare\nsono stati salvati con successo!");
                                    window.location.replace("Domanda.php");

                                </script>

                            <?php
                            */
                        /*————*/

                    }
                    else{

                        $TipoDomandaSession = $_SESSION['Domanda'];
                        $MembriSession = $_SESSION['nMembri'];

                        /* Action e Form */
                            $action=$_SERVER['PHP_SELF'];
                            echo "<form action=" . $action . " method='POST' autocomplete='Off'>";
                        /*————*/

                        ?>
                        
                            <img ID="container" class="banner" src="../FileInclude/immagini/EmporioSolidale.png" />

                        <h1>DOMANDA DI ACCESSO ALL'EMPORIO SOLIDALE</h1>

                        <!---- Dati Anagrafici e Residenziali del richiedente ---->

                            <h3>DATI ANAGRAFICI E RESIDENZIALI DEL RICHIEDENTE</h3>

                            <table Class="Anag Sep" border="0">

                                <tr>
                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="CognomeRichiedente" maxlength='30' required/><br/><div Class="Weighter">Cognome</div></th>

                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="NomeRichiedente" maxlength='30' required/><br/><div Class="Weighter">Nome</div></th>
                                </tr>

                                <tr>
                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="LuogoNascitaRichiedente" maxlength='30' required/><br/><div Class="Weighter">Nat... a</div></th>

                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type='text' id='datepicker' minlength='10' name="DataNascitaRichiedente" required/><br/><div Class="Weighter">Il</div></th>

                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="Cittadinanza" maxlength='15' required/><br/><div Class="Weighter">Cittadinanza</div></th>

                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type="number" min='1900' max='3100' name="InItaliaDallAnno" /><br/><div Class="Weighter">In Italia Dall'Anno</div></th>
                                </tr>

                                <tr>
                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="Residenza" maxlength='30' required/><br/><div Class="Weighter">Residente a</div></th>

                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="Via" maxlength='30' required/><br/><div Class="Weighter">Via</div></th>

                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="CodiceFiscale" minlength="16" maxlength="16" required/><br/><div Class="Weighter">Codice Fiscale</div></th>
                                </tr>

                                <tr>
                                    <th colspan="4" Class="AlignLeft">
                                        <select Class="DatiAnagrafici" name="TipoDiAlloggio">
                                            <optgroup label="Alloggio:">
                                                <option value="" disabled selected></option>
                                                <option value="Proprieta">Propriet&agrave;</option>
                                                <option value="Affitto Privato">Affitto Privato</option>
                                                <option value="Convenzionato">Convenzionato</option>
                                                <option value="Popolare">Popolare</option>
                                                <option value="Emergenza Abitativa">Emergenza Abitativa</option>
                                            </optgroup>
                                        </select>
                                    <br/><div Class="Weighter">Tipo di Alloggio (Propriet&agrave;, Affitto Privato, Convenzionato, Popolare, Emergenza abitativa)</div></th>
                                </tr>

                                <tr>
                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" minlength="9" maxlength="9" name="DocumentoDIdent" /><br/><div Class="Weighter">Documento D'Identit&agrave;</div></th> 
                                    <!---- 2 lettere 5 numeri 2 lettere ---->

                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="DocumentoDImmig" minlength="10" maxlength="10" /><br/><div Class="Weighter">Documento D'Immigrazione</div></th>
                                </tr>

                                <tr>
                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" maxlength="10" name="Telefono" required/><br/><div Class="Weighter">Telefono</div></th>

                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="email" name="Mail" maxlength='50' required/><br/><div Class="Weighter">E-mail</div></th>
                                </tr>

                                <tr>
                                    <th colspan="4" Class="AlignLeft">
                                        <select Class="DatiAnagrafici" name="ServiziSociali">
                                            <optgroup label="Domanda:">
                                                <option value="" disabled selected></option>
                                                <option value="Si">Si</option>
                                                <option value="No">No</option>
                                                <option value="Area">Area</option>
                                                <option value="Assistenza Sociale">Assistenza Sociale</option>
                                            </optgroup>
                                        </select>
                                    <br/><div Class="Weighter">Gi&agrave; in carico ai Servizi Sociali (SI, NO, AREA, Ass. Sociale)</div></th>
                                </tr>

                                <tr>
                                    <th colspan="4" Class="AlignLeft">
                                    <input Class="DatiAnagrafici" type="text" name="ComeHaConosciutoLEmporio" maxlength='30'/>
                                    <br/><div Class="Weighter">Come ha conosciuto l'Emporio</div></th>
                                </tr>

                                <tr>
                                    <?php
                                    echo "<th colspan='4' Class='AlignLeft'>
                                        <input Class='DatiAnagrafici' type='text' name='TipoDiDomanda' value='".$TipoDomandaSession."' readonly/>
                                    <br/><div Class='Weighter'>Tipo di Domanda (Domanda Nuova - Domanda di modifica - Domanda reiterata)</div></th>";
                                    ?>
                                </tr>

                                <tr>
                                    <?php
                                    echo "<th colspan='4' Class='AlignLeft'>
                                    <input Class='DatiAnagrafici' type='Number' value=".$MembriSession." name='nComponentiNucleoFamiliare' readonly/>
                                    <br/><div Class='Weighter'>Numero Componenti Nucleo Familiare (Incluso Richiedente)</div></th>"
                                    ?>
                                </tr>

                            </table>


                        <!---->


                        <p Class="interruzione">&nbsp;</p>


                        <!---- Composizione e Condizione Lavorativa del Nucleo Familiare ---->

                            <h3><span Class="underline">COMPOSIZIONE NUCLEO FAMILIARE (Come da ISEE)</span></h3>

                            <table Class="test" border='1'>

                                <tr> <!---- Intestazione della tabella: COMPOSIZIONE NUCLEO FAMILIARE (Come da ISEE) "" ---->
                                    <th Class="BGcolorIntestazioneTable">N.</th>
                                    <th Class="BGcolorIntestazioneTable">Nome</th>
                                    <th Class="BGcolorIntestazioneTable">Nat... a</th>
                                    <th Class="BGcolorIntestazioneTable">Il</th>
                                    <th Class="BGcolorIntestazioneTable">Parentela</th>
                                    <th Class="BGcolorIntestazioneTable">Occupazione</th>
                                </tr>

                                <?php

                                    for($i=1;$i<=$MembriSession;$i++){
                                        echo "<tr>";
                                            echo "<td Class='bold'>$i</td>";
                                            echo "<td><input title='Nome e Cognome del componente' maxlength='60' type='text' name='Nome[$i]' /></td>";
                                            echo "<td><input type='text' name='LuogoNascita[$i]' maxlength='30' /></td>";
                                            echo "<td><input type='text' class='datepicker'  minlength='10' name='DataNascita[$i]' /></td>";
                                            echo "<td>
                                            <select style='padding-left:2vw' class='AlignCenter' name='Parentela[$i]' />
                                            <option value=''></option>
                                            <option value='Capofamiglia'>Capofamiglia</option>
                                            <option value='Coniuge'>Coniuge</option>
                                            <option value='Figlio'>Figlio</option>
                                            <option value='Altro'>Altro</option>
                                            </select>
                                            </td>";
                                            echo "<td>
                                            <select style='padding-left:2vw' class='AlignCenter' name='Occupazione[$i]' />
                                            <option value=''></option>
                                            <option value='Disoccupato'>Disoccupato</option>
                                            <option value='Semioccupato'>Semioccupato</option>
                                            <option value='Altro'>Altro</option>
                                            </select>
                                            </td>";
                                        echo "</tr>";
                                    }

                                ?>
                            </table>

                            <h3><span Class="underline">COMPONENTI EXTRA ISEE</span></h3>

                            <table Class="Sep" border='1'>

                                <tr>  <!---- Intestazione della tabella: "Componenti extra ISEE" ---->
                                    <th Class="BGcolorIntestazioneTable">N.</th>
                                    <th Class="BGcolorIntestazioneTable">Nome</th>
                                    <th Class="BGcolorIntestazioneTable">Nat... a</th>
                                    <th Class="BGcolorIntestazioneTable">Il</th>
                                    <th Class="BGcolorIntestazioneTable">Parentela</th>
                                    <th Class="BGcolorIntestazioneTable">Occupazione</th>
                                </tr>
                                <?php

                                    for($i=11;$i<=12;$i++){
                                        echo "<tr>";
                                            echo "<td Class='bold'>$i</td>";
                                            echo "<td><input type='text' name='NomeExtraISEE[$i]' maxlength='60' /></td>";
                                            echo "<td><input type='text' name='LuogoNascitaExtraISEE[$i]' maxlength='30' /></td>";
                                            echo "<td><input type='text' class='datepicker'  minlength='10' name='DataNascitaExtraISEE[$i]' /></td>";
                                            echo "<td>
                                            <select style='padding-left:2vw' name='ParentelaExtraISEE[$i]' />
                                            <option value=''></option>
                                            <option value='Nonno/a'>Nonno/a</option>
                                            <option value='Zio/a'>Zio/a</option>
                                            <option value='Nipote'>Nipote</option>
                                            </select>
                                            </td>";
                                            echo "<td>
                                            <select style='padding-left:2vw' class='AlignCenter' name='Occupazione[$i]' />
                                            <option value=''></option>
                                            <option value='Disoccupato'>Disoccupato</option>
                                            <option value='Semioccupato'>Semioccupato</option>
                                            <option value='Altro'>Altro</option>
                                            </select>
                                            </td>";
                                        echo "</tr>";
                                    }

                                ?>
                            </table>

                            <h3><span Class="underline Sep">SITUAZIONE SOCIO-SANITARIA DEL NUCLEO FAMILIARE</span></h3>

                            <table Class="Sep" border='1'>
                                <tr>  <!---- Intestazione della tabella: "SITUAZIONE SOCIO-SANITARIA DEL NUCLEO FAMILIARE" ---->
                                    <th Class="BGcolorIntestazioneTable">N.</th>
                                    <th Class="BGcolorIntestazioneTable">Evento</th>
                                    <th Class="BGcolorIntestazioneTable">Anno</th>
                                    <th Class="BGcolorIntestazioneTable">Doc. All.</th>
                                </tr>

                                <?php

                                    $Evento1="Mancanza del partner per...";
                                    $Evento2="Violenze e maltrattamenti familiari";
                                    $Evento3="Presenza invalidt&agrave; media (oltre il 75&#65285;)";
                                    $Evento4="Presenza invalidit&agrave; grave (oltre il 100&#65285;)";
                                    $Evento5="Restrizioni della libert&agrave;";
                                    $Evento6="Pignoramenti in corso";
                                    $Evento7="Morosit&agrave; per affitto e/o utenze domestiche";
                                    $Evento8="Mancato pagamento di rate del mutuo casa";
                                    $Evento9="Avviso di sfratto";
                                    $Evento10="Sfratto imminente";
                                    $Evento11="Non poter sostenere spese impreviste";
                                    $Evento12="Non potersi permettere l'automobile";

                                    for($i=1;$i<=12;$i++){
                                        echo "<tr>";
                                            echo "<td Class='bold'>$i</td>";
                                            echo "<td> ${'Evento'.$i} </td>";
                                            echo "<td><input type='number' min='1900' max='3100' name='Anno[$i]' /></td>";
                                            echo "<td><input type='text' name='DocAll[$i]' /></td>";
                                        echo "</tr>";
                                    }

                                ?>
                            </table>

                            <p Class="interruzione">&nbsp;</p>

                            <h3><span Class="underline">CONDIZIONE LAVORATIVA DEL NUCLEO FAMILIARE</span><br/><span Class="TextSmaller">(esclusi i componenti minorenni)</span></h3>

                            <?php

                                for($i=1;$i<=8;$i++){ /*  La variabile $nAdulti ancora non esiste DA RICORDARE */
                                
                                    echo "<table Class='CLNF' border='0'>";
                                        echo "<tr>";
                                            echo "<th valign='top' Class='AlignLeft' rowspan='7'>$i</th>";
                                            echo "<td Class='AlignLeft bold'>Nome</td><th colspan='3'><input type='text' name='NomeMembroLavoratore[$i]' /></th>";
                                        echo "</tr>";
                                        
                                        echo "<tr>";
                                            echo "<td Class='AlignLeft bold'>Titolo Di Studio</td><th colspan='3'><input type='text' name='TitoloDiStudio[$i]' /></th>";
                                        echo "</tr>";

                                        echo "<tr>";
                                            echo "<td Class='AlignLeft bold'>Competenze</td><th colspan='3'><input type='text' name='Competenze[$i]' /></th>";
                                        echo "</tr>";

                                        echo "<tr>";
                                            echo "<td Class='AlignLeft bold'>Conoscenza Lingua</td><th><input Class='smaller' type='text' name='ConoscenzaLingua[$i]' /></th><td Class='AlignRight bold'>Patente</td><th>
                                            <select style='padding-left: 10.5vw;' Class='smaller DatiAnagrafici' name='Patente[$i]'>
                                                <option value=''></option>
                                                <option value='AM'>AM</option>
                                                <option value='A1'>A1</option>
                                                <option value='A2'>A2</option>
                                                <option value='A'>A</option>
                                                <option value='B1'>B1</option>
                                                <option value='B'>B</option>
                                                <option value='C1'>C1</option>
                                                <option value='C'>C</option>
                                                <option value='D1'>D1</option>
                                                <option value='D'>D</option>
                                                <option value='BE'>BE</option>
                                                <option value='C1E'>C1E</option>
                                                <option value='CE'>CE</option>
                                                <option value='D1E'>D1E</option>
                                                <option value='DE'>DE</option>
                                            </select>
                                            </th>";
                                        echo "</tr>";

                                        echo "<tr>";
                                            echo "<td Class='AlignLeft bold'>Attivit&agrave; Attuale</td><th colspan='3'><input type='text' name='AttAtt[$i]' /></th>";
                                        echo "</tr>";

                                        echo "<tr>";
                                            echo "<td Class='AlignLeft bold'>Presso</td><th><input type='text' Class='smaller' name='Presso[$i]' /></th><td Class='AlignRight bold'>Termine</td><th><input Class='smaller datepicker' type='text' name='Termine[$i]' /></th>";
                                        echo "</tr>";

                                        echo "<tr>";
                                            echo "<td Class='AlignLeft bold datepicker'>Senza Lavoro Da</td><th colspan='3'><input type='text' class='datepicker' name='SenzaLavoroDa[$i]'/></th>";
                                        echo "</tr>";

                                    echo "</table>";

                                }

                            ?>

                        <!---->


                        <p Class="interruzione">&nbsp;</p>


                        <!---- Analisi Economico Finanziaria Nucleo Familiare ---->

                            <h3><span Class="underline">ANALISI ECONOMICO/FINANZIARIA DEL NUCLEO FAMILIARE</span></h3>
                            
                            <table Class="Sep">
                                <tr>
                                    <th Class="sot bold AlignRight">INDICE ISEE: <input type="number" name="IndiceISEE" min="0" max="7500" required/></th>
                                </tr>
                            </table>

                            <h3><span Class="underline">PATRIMONIO ATTIVO DEL NUCLEO FAMILIARE</span></h3>

                            <table Class="Sep" border='1'>
                                <tr>
                                    <th Class="BGcolorIntestazioneTable">N.</th>
                                    <th Class="BGcolorIntestazioneTable">Elemento</th>
                                    <th Class="BGcolorIntestazioneTable">Valore</th>
                                </tr>

                                <?php

                                    $ElementoAtt1="Immobili";
                                    $ElementoAtt2="Veicoli";
                                    $ElementoAtt3="Crediti (Liquidazioni, Assicurazioni, Altro)";
                                    $ElementoAtt4="Altre Voci";

                                    for($i=1;$i<=4;$i++){
                                        echo "<tr>";
                                        echo "<td Class='AlignLeft'>$i</td>";
                                        echo "<td Class='AlignLeft'> ${'ElementoAtt'.$i} </td>";
                                        echo "<td><input type='number' name='ValoreSPatt[$i]' /></td>";
                                        echo "</tr>";
                                    }

                                ?>

                            </table>


                            <h3><span Class="underline">PATRIMONIO PASSIVO DEL NUCLEO FAMILIARE</span></h3>

                            <table Class="Sep" border='1'>
                                <tr>
                                    <th Class="BGcolorIntestazioneTable">N.</th>
                                    <th Class="BGcolorIntestazioneTable">Elemento</th>
                                    <th Class="BGcolorIntestazioneTable">Valore</th>
                                    <th Class="BGcolorIntestazioneTable">Scadenza</th>
                                </tr>

                                <?php

                                    $ElementoPas1="Debiti v/Pubblica Amm.ne e Agenzia delle Entrate";
                                    $ElementoPas2="Mutui";
                                    $ElementoPas3="Debiti Privati e/o ACER";
                                    $ElementoPas4="Altre Voci";

                                    for($i=1;$i<=4;$i++){
                                        echo "<tr>";
                                        echo "<td Class='AlignLeft'>$i</td>";
                                        echo "<td Class='AlignLeft'> ${'ElementoPas'.$i} </td>";
                                        echo "<td><input type='number' name='ValoreSPpas[$i]' /></td>";
                                        echo "<td><input type='text' class='datepicker'  minlength='10' name='ScadenzaSPpas[$i]' /></td>";
                                        echo "</tr>";
                                    }

                                ?>
                            </table>


                            <h3><span Class="underline">ENTRATE DEL NUCLEO FAMILIARE</span></h3>

                            <table Class="Sep" border='1'>
                                <tr>
                                    <th Class="BGcolorIntestazioneTable">N.</th>
                                    <th Class="BGcolorIntestazioneTable">Elemento</th>
                                    <th Class="BGcolorIntestazioneTable">Valore</th>
                                    <th Class="BGcolorIntestazioneTable">Scadenza</th>
                                </tr>

                                <?php

                                    $ElementoEnt1="Stipendi";
                                    $ElementoEnt2="Pensioni di Anzianit&agrave;";
                                    $ElementoEnt3="Pensioni di invalidit&agrave;, accompagnamento, cura";
                                    $ElementoEnt4="Indennit&agrave;: (Disoccupazione, Mobilit&agrave;, Altro)";
                                    $ElementoEnt5="Contributi: (REI, RES, Reddito Cittadinanza)";
                                    $ElementoEnt6="Entrate straordinarie (compresi sociali consolidate)";
                                    $ElementoEnt7="Altre entrate (Ass. Mantenimento, altro)";

                                    for($i=1;$i<=7;$i++){
                                        echo "<tr>";
                                            echo "<td Class='AlignLeft'>$i</td>";
                                            echo "<td Class='AlignLeft'> ${'ElementoEnt'.$i} </td>";
                                            echo "<td><input type='number' name='ValoreENT[$i]' /></td>";
                                            echo "<td><input type='text' class='datepicker'  minlength='10' name='ScadenzaENT[$i]' /></td>";
                                        echo "</tr>";
                                    }

                                ?>

                            </table>


                            <h3><span Class="underline">USCITE DEL NUCLEO FAMILIARE</span></h3>
                            
                            <table Class="Sep" border='1'>

                                <tr>
                                    <th Class="BGcolorIntestazioneTable">N.</th>
                                    <th Class="BGcolorIntestazioneTable">Elemento</th>
                                    <th Class="BGcolorIntestazioneTable">Valore</th>
                                    <th Class="BGcolorIntestazioneTable">Scadenza</th>
                                </tr>

                                <?php

                                    $ElementoUsc1="Affitto Mensile";
                                    $ElementoUsc2="Rata mensile di mutui";
                                    $ElementoUsc3="Utenze medie mensili";
                                    $ElementoUsc4="Spese medico-sanitarie";
                                    $ElementoUsc5="Rate mensili di debiti privati:";
                                    $ElementoUsc6="Altre Uscite:";

                                    for($i=1;$i<=6;$i++){
                                        echo "<tr>";
                                            echo "<td Class='AlignLeft'>$i</td>";
                                            echo "<td Class='AlignLeft'> ${'ElementoUsc'.$i} </td>";
                                            echo "<td><input type='number' name='ValoreUSC[$i]' /></td>";
                                            echo "<td><input type='text' class='datepicker'  minlength='10' name='ScadenzaUSC[$i]' /></td>";
                                        echo "</tr>";
                                    }

                                ?>

                            </table>

                        <!---->


                        <p Class="interruzione">&nbsp;</p>


                        <!---- Disponibilità Collaborative e Valutazione Commissione ---->

                            <h3><span Class="underline">DISPONIBILIT&Agrave; COLLABORATIVE</span></h3>

                            <table Class="DomandeFinali" border='1'>

                                <tr>
                                    <th Class="BGchocolate">Ambito Formativo</th>
                                </tr>
                                
                                <tr>
                                    <td><textarea Class="padding" name="Formativo" maxlength="255"></textarea></td>
                                </tr>
                                
                                <tr>
                                    <th Class="BGorange">Ambito Lavorativo</th>
                                </tr>

                                <tr>
                                    <td><textarea Class="padding" name="Lavorativo" maxlength="255"></textarea></td>
                                </tr>
                                
                                <tr>
                                    <th Class="BGyellow">Ambito Sociale</th>
                                </tr>

                                <tr>
                                    <td><textarea Class="padding" name="Sociale" maxlength="255"></textarea></td>
                                </tr>

                            </table> 

                            <h3><span Class="underline">VALUTAZIONI DELLA COMMISSIONE</span></h3>   

                            <Div class="TextAreaDiv">
                                    <textarea name="ValutCommissione" maxlength="255"></textarea>
                            </Div>

                        <!---->


                        <!---- Data Odierna, Immagine footer e footer finale ---->

                            <?php
                                $Odierno=Date('d/m/Y');
                                echo "<table Class='dataodierna'>";
                                    echo "<tr>";
                                        echo "<th Class='AlignLeft'><input type='text' minlength='10' maxlength='10' value='$Odierno'/><br/><span Class='Weighter'>Data</span></th>";
                                    echo "</tr>";
                                echo "</table>";
                            ?>

                            <input Class="invia" type="submit" name="invia" value="invia" />

                            <img ID="container" class="Footer" src="../FileInclude/immagini/Footer.png" />

                            <Div Class="Fin">

                                <Div>

                                    <a href='https://cierisamuele.vercel.app' target="_blank" Class="insta"><!--<img class="instalogo" src="FileInclude/immagini/Logo_3.png" />--></a>

                                </Div>

                                <Div class="Copyright">

                                <Div><span>Copyright &copy; <?php $AnnoCopyright=Date('Y'); echo "2002-".$AnnoCopyright; ?> Samuele Cieri Inc.<br/>Tutti i diritti riservati.</span></Div>

                                </Div>

                                <Div>
                                    <span><a href="https://cierisamuele.vercel.app" target="_blank"><img Class="Signature" src="../FileInclude/immagini/FirmaDigitale_2.png" /></a></span>
                                </Div>

                            </Div>

                        <!---->

                        </form>
                    <?php
                    }
                /*————*/
                session_abort();
            ?>
        </Div>

    </body>
</html>