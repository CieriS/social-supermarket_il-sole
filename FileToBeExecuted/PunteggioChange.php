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
                    require(__DIR__ . '/../src/Rules.php');
                    require(__DIR__ . '/../src/AssessmentFields.php');
                    /*————*/

                    /* Punti della griglia configurati */
                        $risRegole=mysqli_query($connection,"SELECT Chiave, Punti FROM RegolePunteggio") OR DIE(mysqli_error($connection));
                        $RegolePunteggio=\IlSole\Rules\rulesFromRows(mysqli_fetch_all($risRegole,MYSQLI_ASSOC));
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

                        /* Condizione Codifice Fiscale non duplicato */
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
                                    new DateTimeImmutable('today'),
                                    $RegolePunteggio
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

                        /* UPDATE DB */

                                    /* Update richiedente */

                                        $insertRichiedente="UPDATE Richiedente 
                                        SET Cognome = '".$CognomeRichiedente."', Nome = '".$NomeRichiedente."', LuogoNascita = '".$LuogoNascitaRichiedente."', DataNascita = '".$DataNascitaRichiedente."', Cittadinanza = '".$Cittadinanza."', InItaliaDallAnno = '".$InItaliaDallAnno."', Residenza = '".$Residenza."', Via = '".$Via."', TipoAlloggio = '".$Alloggio."', DocumentoIdent = '".$DocumentoDIdentita."', DocumentoImmig = '".$DocumentoDImmigrazione."', Telefono = '".$Telefono."', Mail = '".$Mail."', ServiziSociali = '".$ServiziSociali."', ComeHaConosciutoEmporio = '".$ComeHaConosciutoEmporio."', nMembri = '".$nMembri."', ISEE = '".$ISEE."' 
                                        WHERE Cod_Fiscale = '".$CodiceFiscale."';";
                                        
                                        echo "$insertRichiedente"."<br/>";
                                        $risultatoRichiedente=mysqli_query($connection,$insertRichiedente) OR DIE(mysqli_error($connection));

                                    /*————*/

                                    /* Update Punteggio */

                                        $insertPunteggio="UPDATE Punteggio 
                                        SET PunteggioISEE = ".$PunteggioISEE.", PunteggioPresenzaMinori = ".$PunteggioPresenzaMinori.", PunteggioDisoccupazione = ".$PunteggioDisoccupazione.", PunteggioInvalidi = ".$PunteggioInvalidi.", PunteggioSituazioneDebitoria = ".$PunteggioSituazioneDebitoria.", PunteggioBenefit = ".$PunteggioBenefit.", PunteggioTotale = ".$PunteggioTotale." 
                                        WHERE CodFamiglia LIKE '".$CodiceFiscale."';";    

                                        echo $insertPunteggio."<br/>";
                                        $risultatoPunteggio=mysqli_query($connection,$insertPunteggio) OR DIE(mysqli_error($connection));

                            /* Voci della griglia valutate dall'operatore */
                                $Valutazione=\IlSole\Form\assessmentFromForm($_REQUEST);
                                $salvaValutazione=mysqli_prepare($connection,"REPLACE INTO ValutazioneGriglia (CodFamiglia, InvaliditaTotaleAdulti, InvaliditaGraveAdulti, InvaliditaGraveMinori, AltriDebiti, AltriContributi) VALUES (?, ?, ?, ?, ?, ?)");
                                mysqli_stmt_bind_param($salvaValutazione,"siiiii",$CodiceFiscale,$Valutazione['InvaliditaTotaleAdulti'],$Valutazione['InvaliditaGraveAdulti'],$Valutazione['InvaliditaGraveMinori'],$Valutazione['AltriDebiti'],$Valutazione['AltriContributi']);
                                mysqli_stmt_execute($salvaValutazione);
                            /*————*/
                                        
                                    /*————*/

                                    /* Update Membri */

                                        for ( $i=1;$i<=$nMembri;$i++ ) {
                                            if ( $NomeMembro[$i] != "" && $LuogoNascitaMembro[$i] != "" && $ParentelaMembro[$i] != "" ) {
                                                $insertMembri="UPDATE 
                                                Membri SET nMembro = '".$i."', NomeCognome = '".$NomeMembro[$i]."', LuogoNascita = '".$LuogoNascitaMembro."', DataNascita = '".$DataNascitaMembro."', Parentela = '".$ParentelaMembro."', Occupazione = '".$OccupazioneMembro."', TitoloDiStudio = '".$TitoloDiStudio."', Competenze = '".$Competenze."', ConoscenzaLingua = '".$ConoscenzaLingua."', Patente = '".$Patente."', AttualeAtt = '".$AttAtt."', Presso = '".$Presso."', Termine = '".$Termine."', SenzaLavoroDa = '".$SenzaLavoroDa."' 
                                                WHERE CodFamiglia LIKE '".$CodiceFiscale."';";

                                                echo "$insertMembri<br/>";
                                                $risultatoInserimentoMembri=mysqli_query($connection,$insertMembri) OR DIE(mysqli_error($connection));
                                            }

                                        }

                                    /*————*/

                                    /* Update Situazione Socio Sanitaria */

                                        $Evento=array(1=>"Mancanza del partner per...","Violenze e maltrattamenti familiari","Presenza invalidt&agrave; media (oltre il 75&#65285;)","Presenza invalidit&agrave; grave (oltre il 100&#65285;)","Restrizioni della libert&agrave;","Pignoramenti in corso","Morosit&agrave; per affitto e/o utenze domestiche","Mancato pagamento di rate del mutuo casa","Avviso di sfratto","Sfratto imminente","Non poter sostenere spese impreviste","Non potersi permettere l'automobile)");

                                        for ( $i=1;$i<=12;$i++ ) {
                                            if ( $AnnoSSS[$i] != "" && $DocAllSSS[$i] != "" ) {
                                                $insertSSS="UPDATE SituazioneSocioSanitaria 
                                                SET N = '".$i."', Evento = '".$Evento[$i]."', Anno = '".$AnnoSSS[$i]."', DocAll = '".$DocAllSSS[$i]."' 
                                                WHERE CodFamiglia LIKE '".$CodiceFiscale."'";

                                                echo "$insertSSS<br/>";
                                                $risultatoSSS=mysqli_query($connection,$insertSSS) OR DIE(mysqli_error($connection));
                                            }
                                        }

                                    /*————*/

                                    /* Update Disponibilità Collaborative */

                                        $insertDispColl="UPDATE DispCollaborative 
                                        SET AmbitoFormativo = '".$AmbitoFormativo."', AmbitoLavorativo = '".$AmbitoLavorativo."', AmbitoSociale = '".$AmbitoSociale."' 
                                        WHERE CodFamiglia LIKE '".$CodiceFiscale."'";

                                        echo "$insertDispColl"."<br/>";
                                        $risultatoDispColl=mysqli_query($connection,$insertDispColl) OR DIE(mysqli_error($connection));

                                    /*————*/

                                    /* Update Patrimonio Attivo */

                                        $ElementoPatrAtt=array(1=>"Immobili","Veicoli","Crediti (Liquidazioni, Assicurazioni, Altro)","Altre Voci");

                                        for ( $i=1;$i<=count($ElementoPatrAtt);$i++ ) {
                                            if ( $ValPatrimonioAtt[$i] != "" ) {
                                                $insertPatrAtt="UPDATE 
                                                PatrAtt SET N = '".$i."', Elemento = '".$ElementoPatrAtt[$i]."', Valore = '".$ValPatrimonioAtt[$i]."' 
                                                WHERE CodFamiglia LIKE '".$CodiceFiscale."'";

                                                echo "$insertPatrAtt"."<br/>";
                                                $risultatoPatrAtt=mysqli_query($connection,$insertPatrAtt) OR DIE(mysqli_error($connection));
                                            }
                                        }

                                    /*————*/

                                    /* Update Patrimonio Passivo */

                                        $ElementoPatrPass=array(1=>"Debiti v/Pubblica Amm.ne e Agenzia delle Entrate","Mutui","Debiti Privati e/o ACER","Altre Voci");

                                        for ( $i=1;$i<=count($ElementoPatrPass);$i++ ) {
                                            if ( $ValPatrimonioPas[$i] != "" ) {
                                                $insertPatrPass="UPDATE 
                                                PatrPass SET N = '".$i."', Elemento, = '".$ElementoPatrPass[$i]."' Valore = '".$ValPatrimonioPas[$i]."', Scadenza = '".$ScadPatrimonioPas[$i]."' 
                                                WHERE CodFamiglia LIKE '".$CodiceFiscale."'";

                                                echo "$insertPatrPass"."<br/>";
                                                $risultatoPatrPass=mysqli_query($connection,$insertPatrPass) OR DIE(mysqli_error($connection));
                                            }
                                        }

                                    /*————*/

                                    /* Update Entrate */

                                        $ElementoENT=array(1=>"Stipendi","Pensioni di Anzianit&agrave;","Pensioni di invalidit&agrave;, accompagnamento, cura","Indennit&agrave;: (Disoccupazione, Mobilit&agrave;, Altro)","Contributi: (REI, RES, Reddito Cittadinanza)","Entrate straordinarie (compresi sociali consolidate)","Altre entrate (Ass. Mantenimento, altro)");

                                        for ( $i=1;$i<=count($ElementoENT);$i++ ) {
                                            if ( $ValEntrate[$i] != "" ) {
                                                $insertEntrate="UPDATE 
                                                Entrate SET N = '".$i."', Elemento = '".$ElementoENT[$i]."', Valore = '".$ValEntrate[$i]."', Scadenza = '".$ScadEntrate[$i]."' 
                                                WHERE CodFamiglia LIKE '".$CodiceFiscale."'";

                                                echo "$insertEntrate"."<br/>";
                                                $risultatoEntrate=mysqli_query($connection,$insertEntrate) OR DIE(mysqli_error($connection));
                                            }
                                        }

                                    /*————*/

                                    /* Update Uscite */
                                    
                                        $ElementoUSC=array(1=>"Affitto Mensile","Rata mensile di mutui","Utenze medie mensili","Spese medico-sanitarie","Rate mensili di debiti privati:","Altre Uscite:");

                                        for ( $i=1;$i<=count($ElementoUSC);$i++ ) {
                                            if ( $ValUscite[$i] != "" ) {
                                                    $insertUscite="UPDATE 
                                                    Uscite SET N = '".$i."', Elemento = '".$ElementoUSC[$i]."', Valore = '".$ValUscite[$i]."', Scadenza = '".$ScadUscite[$i]."' 
                                                    WHERE CodFamiglia LIKE '".$CodiceFiscale."'";

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

                                    alert("i dati del richiedente e del suo nucleo familiare\nsono stati aggiornati con successo!");
                                    window.location.replace("Domanda.php");

                                </script>

                            <?php
                            */
                        /*————*/

                    }
                    else{

                        $TipoDomandaSession = $_SESSION['Domanda'];
                        $MembriSession = $_SESSION['nMembri'];
                        $RichiedenteSession = $_SESSION['CodiceFiscale'];

                        $queryPerSingoloRich = "SELECT Cod_Fiscale, Nome, Cognome, LuogoNascita, DATE_FORMAT(DataNascita, '%d/%m/%Y') AS DataNascita, Cittadinanza, InItaliaDallAnno, Residenza, Via, TipoAlloggio, DocumentoIdent, DocumentoImmig, Telefono, Mail, ServiziSociali, ComeHaConosciutoEmporio, ISEE FROM Richiedente WHERE Cod_Fiscale LIKE '".$RichiedenteSession."';";

                        $risultatoQueryPerSingoloRich = mysqli_query($connection,$queryPerSingoloRich);

                        while ( $riga=mysqli_fetch_array($risultatoQueryPerSingoloRich, MYSQLI_ASSOC) ) {
                            $arrNomeMod = $riga['Nome'];
                            $arrCognomeMod = $riga['Cognome'];
                            $arrLuogoMod = $riga['LuogoNascita'];
                            $arrDataMod = $riga['DataNascita'];
                            $arrCittMod = $riga['Cittadinanza'];
                            $arrItaMod = $riga['InItaliaDallAnno'];
                            $arrResMod = $riga['Residenza'];
                            $arrViaMod = $riga['Via'];
                            $arrAllMod = $riga['TipoAlloggio'];
                            $arrIdeMod = $riga['DocumentoIdent'];
                            $arrImmMod = $riga['DocumentoImmig'];
                            $arrTelMod = $riga['Telefono'];
                            $arrMaiMod = $riga['Mail'];
                            $arrSocMod = $riga['ServiziSociali'];
                            $arrEmpMod = $riga['ComeHaConosciutoEmporio'];
                            $arrISEEMod = $riga['ISEE'];
                        }

                        $queryPerSingoloMembro = "SELECT NomeCognome, LuogoNascita, DATE_FORMAT(DataNascita, '%d/%m/%Y') AS DataNascita, Parentela, Occupazione, TitoloDiStudio, Competenze, ConoscenzaLingua, Patente, AttualeAtt, Presso, Termine, SenzaLavoroDa, CodFamiglia FROM Membri WHERE CodFamiglia LIKE '".$RichiedenteSession."' ORDER BY nMembro ASC;";

                        $risultatoQueryPerSingoloMembro = mysqli_query($connection,$queryPerSingoloMembro);

                        $queryPerSitSocSan = "SELECT * FROM SituazioneSocioSanitaria WHERE CodFamiglia LIKE '".$RichiedenteSession."' ORDER BY N ASC;";

                        $risultatoQueryPerSitSocSan = mysqli_query($connection,$queryPerSitSocSan);


                        $queryPatrAtt = "SELECT * FROM PatrAtt WHERE CodFamiglia LIKE '".$RichiedenteSession."' ORDER BY N ASC;";

                        $risultatoQueryPatrAtt = mysqli_query($connection,$queryPatrAtt);


                        $queryPatrPass = "SELECT * FROM PatrPass WHERE CodFamiglia LIKE '".$RichiedenteSession."' ORDER BY N ASC;";

                        $risultatoQueryPatrPass = mysqli_query($connection,$queryPatrPass);




                        $queryEnt = "SELECT * FROM Entrate WHERE CodFamiglia LIKE '".$RichiedenteSession."' ORDER BY N ASC;";

                        $risultatoQueryEnt = mysqli_query($connection,$queryEnt);



                        $queryUsc = "SELECT * FROM Uscite WHERE CodFamiglia LIKE '".$RichiedenteSession."' ORDER BY N ASC;";

                        $risultatoQueryUsc = mysqli_query($connection,$queryUsc);


                        $queryDispColl = "SELECT * FROM DispCollaborative WHERE CodFamiglia LIKE '".$RichiedenteSession."';";

                        $risultatoQueryDispColl = mysqli_query($connection,$queryDispColl);

                        while ( $riga=mysqli_fetch_array($risultatoQueryDispColl, MYSQLI_ASSOC) ) {
                            $Formativo = $riga['AmbitoFormativo'];
                            $Lavorativo = $riga['AmbitoLavorativo'];
                            $Sociale = $riga['AmbitoSociale'];
                            $ValutazioneCommissione = $riga['ValutazioneCommissione'];
                        }

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
                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="CognomeRichiedente" maxlength='30' <?php echo "value='".$arrCognomeMod."'"; ?> required/><br/><div Class="Weighter">Cognome</div></th>

                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="NomeRichiedente" maxlength='30' <?php echo "value='".$arrNomeMod."'"; ?> required/><br/><div Class="Weighter">Nome</div></th>
                                </tr>

                                <tr>
                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="LuogoNascitaRichiedente" maxlength='30' <?php echo "value='".$arrLuogoMod."'"; ?> required/><br/><div Class="Weighter">Nat... a</div></th>

                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type='text' id='datepicker' minlength='10' <?php echo "value='".$arrDataMod."'"; ?> name="DataNascitaRichiedente" required/><br/><div Class="Weighter">Il</div></th>

                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="Cittadinanza" maxlength='15' <?php echo "value='".$arrCittMod."'"; ?> required/><br/><div Class="Weighter">Cittadinanza</div></th>

                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type="number" min='1900' max='3100' <?php echo "value='".$arrItaMod."'"; ?> name="InItaliaDallAnno" /><br/><div Class="Weighter">In Italia Dall'Anno</div></th>
                                </tr>

                                <tr>
                                    <th Class="AlignLeft"><input Class="DatiAnagrafici" type="text" name="Residenza" maxlength='30' <?php echo "value='".$arrResMod."'"; ?> required/><br/><div Class="Weighter">Residente a</div></th>

                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="Via" maxlength='30' <?php echo "value='".$arrViaMod."'"; ?> required/><br/><div Class="Weighter">Via</div></th>

                                    <?php
                                    echo "<th Class='AlignLeft'><input Class='DatiAnagrafici' type='text' name='CodiceFiscale' value='".$RichiedenteSession."' minlength='16' maxlength='16' required/><br/><div Class='Weighter'>Codice Fiscale</div></th>"
                                    ?>
                                </tr>

                                <tr>
                                    <th colspan="4" Class="AlignLeft">
                                        <select Class="DatiAnagrafici" name="TipoDiAlloggio">
                                            <optgroup label="Alloggio:">
                                                <option  <?php echo "value='".$arrAllMod."'"; ?>> <?php echo "Attuale: " . $arrAllMod; ?></option>
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
                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" minlength="9" maxlength="9" <?php echo "value='".$arrIdeMod."'"; ?> name="DocumentoDIdent" /><br/><div Class="Weighter">Documento D'Identit&agrave;</div></th> 
                                    <!---- 2 lettere 5 numeri 2 lettere ---->

                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" name="DocumentoDImmig" minlength="10" maxlength="10" <?php echo "value='".$arrImmMod."'"; ?> /><br/><div Class="Weighter">Documento D'Immigrazione</div></th>
                                </tr>

                                <tr>
                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="text" maxlength="10" name="Telefono" <?php echo "value='".$arrTelMod."'"; ?> required/><br/><div Class="Weighter">Telefono</div></th>

                                    <th Class="AlignLeft" colspan="2"><input Class="DatiAnagrafici" type="email" name="Mail" maxlength='50' <?php echo "value='".$arrMaiMod."'"; ?> required/><br/><div Class="Weighter">E-mail</div></th>
                                </tr>

                                <tr>
                                    <th colspan="4" Class="AlignLeft">
                                        <select Class="DatiAnagrafici" name="ServiziSociali">
                                            <optgroup label="Domanda:">
                                                <option <?php echo "value='".$arrSocMod."'"; ?>> <?php echo "Attuale: " . $arrSocMod; ?> </option>
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
                                    <input Class="DatiAnagrafici" type="text" name="ComeHaConosciutoLEmporio" maxlength='30' <?php echo "value='".$arrEmpMod."'"; ?>/>
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

                                    $contatorePersoneNucleo = "SELECT COUNT(*) AS MembriNelNucleoFamiliare FROM Membri WHERE CodFamiglia LIKE '".$RichiedenteSession."' ORDER BY nMembro ASC;";

                                    $risultatoContatorePersoneNucleo = mysqli_query($connection,$contatorePersoneNucleo);

                                    while ( $riga=mysqli_fetch_array($risultatoContatorePersoneNucleo, MYSQLI_ASSOC) ) {
                                        $VecchioNumeroPersoneNucleo = $riga['MembriNelNucleoFamiliare'];
                                    }

                                    if ( $VecchioNumeroPersoneNucleo == $MembriSession ) {
                                        $i=0;
                                        while ( $riga=mysqli_fetch_array($risultatoQueryPerSingoloMembro, MYSQLI_ASSOC) ) {
                                            $i++;
                                            echo "<tr>";
                                            echo "<td Class='bold'>$i</td>";
                                            echo "<td><input title='Nome e Cognome del componente' maxlength='60' type='text' value='".$riga['NomeCognome']."' name='Nome[$i]' /></td>";
                                            echo "<td><input type='text' name='LuogoNascita[$i]' maxlength='30' value='".$riga['LuogoNascita']."' /></td>";
                                            echo "<td><input type='text' class='datepicker'  minlength='10' name='DataNascita[$i]' value='".$riga['DataNascita']."' /></td>";
                                            echo "<td>
                                            <select style='padding-left:2vw' class='AlignCenter' name='Parentela[$i]' />
                                            <option value='".$riga['Parentela']."'>".$riga['Parentela']."</option>
                                            <option value='Capofamiglia'>Capofamiglia</option>
                                            <option value='Coniuge'>Coniuge</option>
                                            <option value='Figlio'>Figlio</option>
                                            <option value='Altro'>Altro</option>
                                            </select>
                                            </td>";
                                            echo "<td>
                                            <select style='padding-left:2vw' class='AlignCenter' name='Occupazione[$i]' />
                                            <option value='".$riga['Occupazione']."'>".$riga['Occupazione']."</option>
                                            <option value='Disoccupato'>Disoccupato</option>
                                            <option value='Semioccupato'>Semioccupato</option>
                                            <option value='Altro'>Altro</option>
                                            </select>
                                            </td>";
                                        echo "</tr>";
                                        }
                                    } else {
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
                                    $Evento3="Presenza invalidit&agrave; media (oltre il 75&#65285;)";
                                    $Evento4="Presenza invalidit&agrave; grave (oltre il 100&#65285;)";
                                    $Evento5="Restrizioni della libert&agrave;";
                                    $Evento6="Pignoramenti in corso";
                                    $Evento7="Morosit&agrave; per affitto e/o utenze domestiche";
                                    $Evento8="Mancato pagamento di rate del mutuo casa";
                                    $Evento9="Avviso di sfratto";
                                    $Evento10="Sfratto imminente";
                                    $Evento11="Non poter sostenere spese impreviste";
                                    $Evento12="Non potersi permettere l'automobile";

                                    $i=0;
                                    while ( $riga=mysqli_fetch_array($risultatoQueryPerSitSocSan, MYSQLI_ASSOC) ) {
                                        $i++;
                                        if ( $i == $riga['N'] ) {
                                            echo "<tr>";
                                                echo "<td Class='bold'>$i</td>";
                                                echo "<td> ${'Evento'.$i} </td>";
                                                echo "<td><input type='number' min='1900' max='3100' name='Anno[$i]' value='".$riga['Anno']."'/></td>";
                                                echo "<td><input type='text' name='DocAll[$i]' value='".$riga['DocAll']."'/></td>";
                                            echo "</tr>";
                                        } else {
                                            echo "<tr>";
                                                echo "<td Class='bold'>$i</td>";
                                                echo "<td> ${'Evento'.$i} </td>";
                                                echo "<td><input type='number' min='1900' max='3100' name='Anno[$i]'/></td>";
                                                echo "<td><input type='text' name='DocAll[$i]'/></td>";
                                            echo "</tr>"; 
                                        }
                                    }

                                ?>
                            </table>

                            <p Class="interruzione">&nbsp;</p>

                            <h3><span Class="underline">CONDIZIONE LAVORATIVA DEL NUCLEO FAMILIARE</span><br/><span Class="TextSmaller">(esclusi i componenti minorenni)</span></h3>

                            <?php
                                        /*$i=0;
                                        while ( $riga=mysqli_fetch_array($risultatoQueryPerSingoloMembro, MYSQLI_ASSOC) ) {
                                            $i++;
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
                                                echo "<td Class='AlignLeft bold'>Presso</td><th><input type='text' Class='smaller' name='Presso[$i]' /></th><td Class='AlignRight bold'>Termine</td><th><input Class='smaller' type='text' name='Termine[$i]' /></th>";
                                            echo "</tr>";
    
                                            echo "<tr>";
                                                echo "<td Class='AlignLeft bold'>Senza Lavoro Da</td><th colspan='3'><input type='text' name='SenzaLavoroDa[$i]' /></th>";
                                            echo "</tr>";
    
                                        echo "</table>";
                                        echo "</tr>";
                                        }*/

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
                                            echo "<td Class='AlignLeft bold'>Senza Lavoro Da</td><th colspan='3'><input type='text' name='SenzaLavoroDa[$i]' class='datepicker'/></th>";
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
                                    <th Class="sot bold AlignRight">INDICE ISEE: <input type="number" name="IndiceISEE" min="0" max="7500" <?php echo "value='".$arrISEEMod."'"; ?> required/></th>
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

                                    $i=0;
                                    while ( $riga=mysqli_fetch_array($risultatoQueryPatrAtt, MYSQLI_ASSOC) ) {
                                        $i++;
                                        echo "<tr>";
                                        echo "<td Class='AlignLeft'>$i</td>";
                                        echo "<td Class='AlignLeft'> ${'ElementoAtt'.$i} </td>";
                                        echo "<td><input type='number' name='ValoreSPatt[$i]' value='".$riga['Valore']."'/></td>";
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

                                    $i=0;
                                    while ( $riga=mysqli_fetch_array($risultatoQueryPatrPass, MYSQLI_ASSOC) ) {
                                        $i++;
                                        echo "<tr>";
                                        echo "<td Class='AlignLeft'>$i</td>";
                                        echo "<td Class='AlignLeft'> ${'ElementoPas'.$i} </td>";
                                        echo "<td><input type='number' name='ValoreSPpas[$i]' value='".$riga['Valore']."'/></td>";
                                        echo "<td><input type='text' class='datepicker'  minlength='10' name='ScadenzaSPpas[$i]' value='".$riga['Scadenza']."'/></td>";
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

                                    $i=0;
                                    while ( $riga=mysqli_fetch_array($risultatoQueryEnt, MYSQLI_ASSOC) ) {
                                        $i++;
                                        echo "<tr>";
                                            echo "<td Class='AlignLeft'>$i</td>";
                                            echo "<td Class='AlignLeft'> ${'ElementoEnt'.$i} </td>";
                                            echo "<td><input type='number' name='ValoreENT[$i]' value='".$riga['Valore']."'/></td>";
                                            echo "<td><input type='text' class='datepicker'  minlength='10' name='ScadenzaENT[$i]' value='".$riga['Scadenza']."'/></td>";
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

                                    $i=0;
                                    while ( $riga=mysqli_fetch_array($risultatoQueryUsc, MYSQLI_ASSOC) ) {
                                        $i++;
                                        echo "<tr>";
                                            echo "<td Class='AlignLeft'>$i</td>";
                                            echo "<td Class='AlignLeft'> ${'ElementoUsc'.$i} </td>";
                                            echo "<td><input type='number' name='ValoreUSC[$i]' /value='".$riga['Valore']."'></td>";
                                            echo "<td><input type='text' class='datepicker'  minlength='10' name='ScadenzaUSC[$i]' value='".$riga['Scadenza']."'/></td>";
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
                                    <td><textarea Class="padding" name="Formativo" maxlength="255"><?php echo "$Formativo"; ?></textarea></td>
                                </tr>
                                
                                <tr>
                                    <th Class="BGorange">Ambito Lavorativo</th>
                                </tr>

                                <tr>
                                    <td><textarea Class="padding" name="Lavorativo" maxlength="255"><?php echo "$Lavorativo"; ?></textarea></td>
                                </tr>
                                
                                <tr>
                                    <th Class="BGyellow">Ambito Sociale</th>
                                </tr>

                                <tr>
                                    <td><textarea Class="padding" name="Sociale" maxlength="255"><?php echo "$Sociale"; ?></textarea></td>
                                </tr>

                            </table> 

                            <?php
                                $leggiValutazione=mysqli_prepare($connection,"SELECT * FROM ValutazioneGriglia WHERE CodFamiglia = ?");
                                mysqli_stmt_bind_param($leggiValutazione,"s",$RichiedenteSession);
                                mysqli_stmt_execute($leggiValutazione);
                                $ValutazioneSalvata=mysqli_fetch_assoc(mysqli_stmt_get_result($leggiValutazione)) ?? [];
                                echo \IlSole\Form\assessmentFieldsHtml($ValutazioneSalvata, $RegolePunteggio);
                            ?>

                            <h3><span Class="underline">VALUTAZIONI DELLA COMMISSIONE</span></h3>   

                            <Div class="TextAreaDiv">
                                    <textarea name="ValutCommissione" maxlength="255"><?php echo "$ValutazioneCommissione"; ?></textarea>
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