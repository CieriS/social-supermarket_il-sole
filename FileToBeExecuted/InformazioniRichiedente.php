<?php require __DIR__ . '/../src/bootstrap.php'; \IlSole\Auth\requireLogin(); ?>
<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/infoStyle.css" media="" rel="stylesheet" type="text/css">
        <title> Maggiori Informazioni Richiedente </title>
        <link rel="icon" type="image/png" href="../FileInclude/immagini/icon.png">
        <script src="../FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body class="info">

        <?php

            /* Metodo postback per ottimizzare la quantità di file ottimizzati */
                if($_POST) {
                        /* FILE INCLUSI DI DEFAULT: Connessione al Database, Navbar e Footer, Funzioni php */
                            require('../FileInclude/FileRequired/Connessione.php');
                            require('../FileInclude/FileRequired/NavBars.php');
                            require('../FileInclude/FileRequired/Functions.php');
                            ?>
                                <style>
                                    .Search{
                                        display: none;
                                    }
                                </style>
                            <?php
                        /*————*/

                        /* RICEZIONE RADIO BUTTON */
                            $Cod_Fiscale=$_REQUEST['Cod_Fiscale'];
                        /*————*/

                        /* Select */

                            /* Select e Esecuzione Select Richiedente, Punteggio e tessere */
                                $queryRichiedente="SELECT * FROM informazioniRichiedente WHERE Cod_Fiscale LIKE '".$Cod_Fiscale."'";
                                /* SELECT R.Cod_Fiscale, R.Cognome, R.Nome, R.LuogoNascita, DATE_FORMAT(R.DataNascita, '%d/%m/%Y') AS DataNascita, R.Cittadinanza, R.InItaliaDallAnno, R.Residenza, R.Via, R.TipoAlloggio, R.DocumentoIdent, R.DocumentoImmig, R.Telefono, R.Mail, R.ServiziSociali, R.ComeHaConosciutoEmporio, R.nMembri, R.ISEE, TR.CodiceTessera, DATE_FORMAT(TR.DataOraRilascioTessera, '%d/%m/%Y') AS DataOraRilascioTessera, DATE_FORMAT(TR.DataOraScadenzaTessera, '%d/%m/%Y') AS DataOraScadenzaTessera, DATE_FORMAT(R.DataOraRitiroTessera, '%d/%m/%Y') AS DataOraRitiroTessera, DATE_FORMAT(R.DataOraStalloTessera, '%d/%m/%Y') AS DataOraStalloTessera, P.PunteggioISEE, P.PunteggioPresenzaMinori, P.PunteggioDisoccupazione, P.PunteggioInvalidi, P.PunteggioSituazioneDebitoria, P.PunteggioBenefit, P.PunteggioTotale 
                                FROM Richiedente AS R LEFT OUTER JOIN TesseraRilasciata AS TR 
                                ON R.Cod_Fiscale = TR.CodFamiglia 
                                LEFT OUTER JOIN  Tessera AS T 
                                ON TR.CodiceTessera = T.Codice 
                                INNER JOIN Punteggio AS P 
                                ON R.Cod_Fiscale = P.CodFamiglia 
                                ORDER BY P.PunteggioTotale DESC; */

                                $risultatoRichiedente=mysqli_query($connection,$queryRichiedente) OR DIE(mysqli_error($connection));
                            /*————*/

                            /* Select e Esecuzione Select Membri */
                                $queryMembri="SELECT * FROM informazioniMembri 
                                WHERE CodFamiglia LIKE '".$Cod_Fiscale."';";
                                /* SELECT nMembro, NomeCognome, LuogoNascita, DATE_FORMAT(DataNascita, '%d/%m/%Y') AS DataNascita, Parentela, Occupazione, TitoloDiStudio, Competenze, ConoscenzaLingua, Patente, AttualeAtt, Presso, Termine, SenzaLavoroDa, CodFamiglia 
                                FROM Membri; */

                                $risultatoMembri=mysqli_query($connection,$queryMembri) OR DIE(mysqli_error($connection));
                            /*————*/

                            /* Select e Esecuzione Select Disponibilità Collaborative */
                                $queryDisp="SELECT * FROM informazioniDispColl 
                                WHERE CodFamiglia LIKE '".$Cod_Fiscale."';";
                                /* SELECT AmbitoFormativo, AmbitoLavorativo, AmbitoSociale, CodFamiglia 
                                FROM DispCollaborative; */

                                $risultatoDisp=mysqli_query($connection,$queryDisp) OR DIE(mysqli_error($connection));
                            /*————*/

                            /* Select e Esecuzione Select Situazione Socio Sanitaria */
                                $querySSS="SELECT * FROM informazioniSSS  
                                WHERE CodFamiglia LIKE '".$Cod_Fiscale."';";
                                /* SELECT N, Evento, Anno, DocAll, CodFamiglia 
                                FROM SituazioneSocioSanitaria; */

                                $risultatoSSS=mysqli_query($connection,$querySSS) OR DIE(mysqli_error($connection));
                            /*————*/

                            /* Select e Esecuzione Select Patrimonio Attivo */
                                $queryPatrAtt="SELECT * FROM PatrAtt 
                                WHERE CodFamiglia LIKE '".$Cod_Fiscale."';";
                                /* SELECT N, Elemento, Valore, CodFamiglia 
                                FROM PatrAtt; */

                                $risultatoPatrAtt=mysqli_query($connection,$queryPatrAtt) OR DIE(mysqli_error($connection));
                            /*————*/

                            /* Select e Esecuzione Select Patrimonio Passivo */
                                $queryPatrPass="SELECT * FROM PatrPass 
                                WHERE CodFamiglia LIKE '".$Cod_Fiscale."';";
                                /* SELECT N, Elemento, Valore, DATE_FORMAT(Scadenza, '%d/%m/%Y') AS Scadenza, CodFamiglia 
                                FROM PatrPass; */

                                $risultatoPatrPass=mysqli_query($connection,$queryPatrPass) OR DIE(mysqli_error($connection));
                            /*————*/

                            /* Select e Esecuzione Select Entrate */
                                $queryEnt="SELECT * FROM Entrate 
                                WHERE CodFamiglia LIKE '".$Cod_Fiscale."';";
                                /* SELECT N, Elemento, Valore, DATE_FORMAT(Scadenza, '%d/%m/%Y') AS Scadenza, CodFamiglia 
                                FROM Entrate; */

                                $risultatoEnt=mysqli_query($connection,$queryEnt) OR DIE(mysqli_error($connection));
                            /*————*/

                            /* Select e Esecuzione Select Uscite */
                                $queryUsc="SELECT * FROM Uscite 
                                WHERE CodFamiglia LIKE '".$Cod_Fiscale."';";
                                /* SELECT N, Elemento, Valore, DATE_FORMAT(Scadenza, '%d/%m/%Y') AS Scadenza, CodFamiglia 
                                FROM Uscite; */

                                $risultatoUsc=mysqli_query($connection,$queryUsc) OR DIE(mysqli_error($connection));
                            /*————*/

                        /*————*/

                        /* Numero di righe */
                            $righeRichiedente=mysqli_num_rows($risultatoRichiedente);
                            $righeMembri=mysqli_num_rows($risultatoMembri);
                            $righeSSS=mysqli_num_rows($risultatoSSS);
                            $righeDisp=mysqli_num_rows($risultatoDisp);
                            $righePatrAtt=mysqli_num_rows($risultatoPatrAtt);
                            $righePatrPass=mysqli_num_rows($risultatoPatrPass);
                            $righeEnt=mysqli_num_rows($risultatoEnt);
                            $righeUsc=mysqli_num_rows($risultatoUsc);
                        /*————*/

                        /* Table Richiedente */
                            if ( $righeRichiedente > 0 ) {
                                echo "<div class='centro'>";
                                    echo "<table border='4'>";
                                        echo "<tr><th class='major' colspan='17'>Generalit&agrave;</th></tr>";
                                        echo "<tr>";
                                            echo "<th class='LightBlue'>Codice Fiscale</th>";
                                            echo "<th class='LightBlue'>Cognome</th>";
                                            echo "<th class='LightBlue'>Nome</th>";
                                            echo "<th class='LightBlue'>Luogo Nascita</th>";
                                            echo "<th class='LightBlue'>Data Nascita</th>";
                                            echo "<th class='LightBlue'>Cittadinanaza</th>";
                                            echo "<th class='LightBlue'>In Italia Dall'Anno</th>";
                                            echo "<th class='LightBlue'>Residenza</th>";
                                            echo "<th class='LightBlue'>Via</th>";
                                            echo "<th class='LightBlue'>Tipo Alloggio</th>";
                                            echo "<th class='LightBlue'>Documento d'identit&agrave;</th>";
                                            echo "<th class='LightBlue'>Doc Immigrazione</th>";
                                            echo "<th class='LightBlue'>Telefono</th>";
                                            echo "<th class='LightBlue'>Mail</th>";
                                            echo "<th class='LightBlue'>Servizi Sociali</th>";
                                            echo "<th class='LightBlue'>Numero Membri</th>";
                                            echo "<th class='LightBlue'>ISEE</th>";
                                        echo "</tr>";
                                        while( $Richiedente=mysqli_fetch_array($risultatoRichiedente,MYSQLI_ASSOC) ) {
                                            echo "<tr>";
                                                echo "<th>".$Richiedente['Cod_Fiscale']."</th>";
                                                echo "<th>".$Richiedente['Cognome']."</th>";
                                                echo "<th>".$Richiedente['Nome']."</th>";
                                                echo "<th>".$Richiedente['LuogoNascita']."</th>";
                                                echo "<th>".$Richiedente['DataNascita']."</th>";
                                                echo "<th>".$Richiedente['Cittadinanza']."</th>";
                                                echo "<th>".$Richiedente['InItaliaDallAnno']."</th>";
                                                echo "<th>".$Richiedente['Residenza']."</th>";
                                                echo "<th>".$Richiedente['Via']."</th>";
                                                echo "<th>".$Richiedente['TipoAlloggio']."</th>";
                                                echo "<th>".$Richiedente['DocumentoIdent']."</th>";
                                                echo "<th>".$Richiedente['DocumentoImmig']."</th>";
                                                echo "<th>".$Richiedente['Telefono']."</th>";
                                                echo "<th>".$Richiedente['Mail']."</th>";
                                                echo "<th>".$Richiedente['ServiziSociali']."</th>";
                                                echo "<th>".$Richiedente['nMembri']."</th>";
                                                echo "<th>".$Richiedente['ISEE']."</th>";
                                            echo "</tr>";
                                            $ConEmp=$Richiedente['ComeHaConosciutoEmporio'];
                                        }
                                        echo "<th colspan='17'>$ConEmp</th>";
                                    echo "</table>";
                                echo "</div>";
                            }
                        /*————*/

                        /* Table Membri */
                            if ( $righeMembri > 0 ) {
                                echo "<div class='centro'>";
                                    echo "<table class='' border='4'>";
                                        echo "<tr><th class='major' colspan='14'>Membri Nucleo Familiare</th></tr>";
                                        echo "<tr>";
                                            echo "<th class='LightBlue'>nMembro</th>";
                                            echo "<th class='LightBlue'>NomeCognome</th>";
                                            echo "<th class='LightBlue'>LuogoNascita</th>";
                                            echo "<th class='LightBlue'>DataNascita</th>";
                                            echo "<th class='LightBlue'>Parentela</th>";
                                            echo "<th class='LightBlue'>Occupazione</th>";
                                            echo "<th class='LightBlue'>TitoloDiStudio</th>";
                                            echo "<th class='LightBlue'>Competenze</th>";
                                            echo "<th class='LightBlue'>ConoscenzaLingua</th>";
                                            echo "<th class='LightBlue'>Patente</th>";
                                            echo "<th class='LightBlue'>AttualeAtt</th>";
                                            echo "<th class='LightBlue'>Presso</th>";
                                            echo "<th class='LightBlue'>Termine</th>";
                                            echo "<th class='LightBlue'>SenzaLavoroDa</th>";
                                        echo "</tr>";
                                            while( $Membri=mysqli_fetch_array($risultatoMembri,MYSQLI_ASSOC) ) {
                                                echo "<tr>";
                                                    echo "<th>".$Membri['nMembro']."</th>";
                                                    echo "<th>".$Membri['NomeCognome']."</th>";
                                                    echo "<th>".$Membri['LuogoNascita']."</th>";
                                                    echo "<th>".$Membri['DataNascita']."</th>";
                                                    echo "<th>".$Membri['Parentela']."</th>";
                                                    echo "<th>".$Membri['Occupazione']."</th>";
                                                    echo "<th>".$Membri['TitoloDiStudio']."</th>";
                                                    echo "<th>".$Membri['Competenze']."</th>";
                                                    echo "<th>".$Membri['ConoscenzaLingua']."</th>";
                                                    echo "<th>".$Membri['Patente']."</th>";
                                                    echo "<th>".$Membri['AttualeAtt']."</th>";
                                                    echo "<th>".$Membri['Presso']."</th>";
                                                    echo "<th>".$Membri['Termine']."</th>";
                                                    echo "<th>".$Membri['SenzaLavoroDa']."</th>";
                                                echo "</tr>";
                                            }
                                    echo "</table>";
                                echo "</div>";
                            }
                        /*————*/

                        /* Table Situazione Socio Sanitaria */
                            if ( $righeSSS > 0 ) {
                                echo "<div class='centro'>";
                                    echo "<table class='' border='4'>";
                                        echo "<tr><th class='major' colspan='4'>Situazione Socio Sanitaria</th></tr>";
                                        echo "<tr>";
                                            echo "<th class='LightBlue'>N&deg;</th>";
                                            echo "<th class='LightBlue'>Evento</th>";
                                            echo "<th class='LightBlue'>Anno</th>";
                                            echo "<th class='LightBlue'>DocAll</th>";
                                        echo "</tr>";
                                        while( $SSS=mysqli_fetch_array($risultatoSSS,MYSQLI_ASSOC) ) {
                                            echo "<tr>";
                                                echo "<th>".$SSS['N']."</th>";
                                                echo "<th>".$SSS['Evento']."</th>";
                                                echo "<th>".$SSS['Anno']."</th>";
                                                echo "<th>".$SSS['DocAll']."</th>";
                                            echo "</tr>";
                                        }
                                    echo "</table>";
                                echo "</div>";
                            }
                        /*————*/

                        /* Table Disponibilità Collaborative */
                            if ( $righeDisp > 0 ) {
                                echo "<div class='centro'>";
                                    echo "<table class='' border='4'>";
                                        echo "<tr><th class='major' colspan='4'>Disponibilit&agrave; Collaborative</th></tr>";
                                        echo "<tr>";
                                            echo "<th class='LightBlue'>Formativo</th>";
                                            echo "<th class='LightBlue'>Lavorativo</th>";
                                            echo "<th class='LightBlue'>Sociale</th>";
                                        echo "</tr>";
                                        while( $DispColl=mysqli_fetch_array($risultatoDisp,MYSQLI_ASSOC) ) {
                                            echo "<tr>";
                                                echo "<th>".$DispColl['AmbitoFormativo']."</th>";
                                                echo "<th>".$DispColl['AmbitoLavorativo']."</th>";
                                                echo "<th>".$DispColl['AmbitoSociale']."</th>";
                                            echo "</tr>";
                                        }
                                    echo "</table>";
                                    /*echo "<table class='' border='4'>";
                                        echo "<tr><th class='major' colspan='4'>Disponibilit&agrave; Collaborative</th></tr>";
                                        while( $DispColl=mysqli_fetch_array($risultatoDisp,MYSQLI_ASSOC) ) {
                                            echo "<tr>";
                                                echo "<th class='LightBlue'>Formativo</th><th>".$DispColl['AmbitoFormativo']."</th>";
                                            echo "</tr>";
                                            echo "<tr>";
                                                echo "<th class='LightBlue'>Lavorativo</th><th>".$DispColl['AmbitoLavorativo']."</th>";
                                            echo "</tr>";
                                            echo "<tr>";
                                                echo "<th class='LightBlue'>Sociale</th><th>".$DispColl['AmbitoSociale']."</th>";
                                            echo "</tr>";
                                        }
                                    echo "</table>";*/
                                echo "</div>";
                            }
                        /*————*/

                        /* Table Patrimonio Attivo */
                            if ( $righePatrAtt > 0 ) {
                                echo "<div class='centro'>";
                                    echo "<table class='' border='4'>";
                                        echo "<tr><th class='major' colspan='3'>Patrimonio Attivo</th></tr>";
                                        echo "<tr>";
                                            echo "<th class='LightBlue'>N&deg;</th>";
                                            echo "<th class='LightBlue'>Elemento</th>";
                                            echo "<th class='LightBlue'>Valore</th>";
                                        echo "</tr>";
                                        while( $PatrAtt=mysqli_fetch_array($risultatoPatrAtt,MYSQLI_ASSOC) ) {
                                            echo "<tr>";
                                                echo "<th>".$PatrAtt['N']."</th>";
                                                echo "<th>".$PatrAtt['Elemento']."</th>";
                                                echo "<th>".$PatrAtt['Valore']."</th>";
                                            echo "</tr>";
                                        }
                                    echo "</table>";
                                echo "</div>";
                            }
                        /*————*/

                        /* Table Patrimonio Passivo */
                            if ( $righePatrPass > 0 ) {
                                echo "<div class='centro'>";
                                    echo "<table class='' border='4'>";
                                        echo "<tr><th class='major' colspan='4'>Patrimonio Passivo</th></tr>";
                                        echo "<tr>";
                                            echo "<th class='LightBlue'>N&deg;</th>";
                                            echo "<th class='LightBlue'>Elemento</th>";
                                            echo "<th class='LightBlue'>Valore</th>";
                                            echo "<th class='LightBlue'>Scadenza</th>";
                                        echo "</tr>";
                                        while( $PatrPass=mysqli_fetch_array($risultatoPatrPass,MYSQLI_ASSOC) ) {
                                            echo "<tr>";
                                                echo "<th>".$PatrPass['N']."</th>";
                                                echo "<th>".$PatrPass['Elemento']."</th>";
                                                echo "<th>".$PatrPass['Valore']."</th>";
                                                echo "<th>".$PatrPass['Scadenza']."</th>";
                                            echo "</tr>";
                                        }
                                    echo "</table>";
                                echo "</div>";
                            }
                        /*————*/

                        /* Table Entrate */
                            if ( $righeEnt > 0 ) {
                                echo "<div class='centro'>";
                                    echo "<table class='' border='4'>";
                                        echo "<tr><th class='major' colspan='4'>Entrate</th></tr>";
                                        echo "<tr>";
                                            echo "<th class='LightBlue'>N&deg;</th>";
                                            echo "<th class='LightBlue'>Elemento</th>";
                                            echo "<th class='LightBlue'>Valore</th>";
                                            echo "<th class='LightBlue'>Scadenza</th>";
                                        echo "</tr>";
                                        while( $Ent=mysqli_fetch_array($risultatoEnt,MYSQLI_ASSOC) ) {
                                            echo "<tr>";
                                                echo "<th>".$Ent['N']."</th>";
                                                echo "<th>".$Ent['Elemento']."</th>";
                                                echo "<th>".$Ent['Valore']."</th>";
                                                echo "<th>".$Ent['Scadenza']."</th>";
                                            echo "</tr>";
                                        }
                                    echo "</table>";
                                echo "</div>";
                            }
                        /*————*/

                        /* Table Uscite */
                            if ( $righeUsc > 0 ) {
                                echo "<div class='centro'>";
                                    echo "<table class='' border='4'>";
                                        echo "<tr><th class='major' colspan='4'>Uscite</th></tr>";
                                        echo "<tr>";
                                            echo "<th class='LightBlue'>N&deg;</th>";
                                            echo "<th class='LightBlue'>Elemento</th>";
                                            echo "<th class='LightBlue'>Valore</th>";
                                            echo "<th class='LightBlue'>Scadenza</th>";
                                        echo "</tr>";
                                        while( $Usc=mysqli_fetch_array($risultatoUsc,MYSQLI_ASSOC) ) {
                                            echo "<tr>";
                                                echo "<th>".$Usc['N']."</th>";
                                                echo "<th>".$Usc['Elemento']."</th>";
                                                echo "<th>".$Usc['Valore']."</th>";
                                                echo "<th>".$Usc['Scadenza']."</th>";
                                            echo "</tr>";
                                        }
                                    echo "</table>";
                                echo "</div>";
                            }
                        /*————*/

                        ?>
                            <!---- Footer ---->
                                <Div Class="Fin">

                                    <Div>
                                        <a href='https://cierisamuele.vercel.app' target="_blank" Class="insta"><!--<img class="instalogo" src="FileInclude/immagini/Logo_3.png" />--></a>
                                    </Div>

                                    <Div class="Copyright">

                                    <Div><span>Copyright &copy; <?php $AnnoCopyright=Date('Y'); echo "2002-".$AnnoCopyright; ?> Samuele Cieri Inc.<br/>Tutti i diritti riservati.</span></Div>

                                    </Div>

                                    <!--    <Div>

                                        <a href="https://cierisamuele.vercel.app"><Div class="imgFin"><img Class="imgFin" src="../FileInclude/immagini/s4msong.png" /></Div></a>

                                    </Div>  -->

                                    <Div>
                                        <span><a href="https://cierisamuele.vercel.app" target="_blank"><img Class="Signature" src="../FileInclude/immagini/FirmaDigitale_2.png" /></a></span>
                                    </Div>

                                </Div>
                            <!---->
                        <?php

                        /* Rilascio della connessione */
                            mysqli_close($connection);
                        /*————*/

                }
                else {

                    /* FILE INCLUSI DI DEFAULT: Connessione al Database, Navbar e Footer, Funzioni php */
                        require('../FileInclude/FileRequired/Connessione.php');
                        require('../FileInclude/FileRequired/NavBars.php');
                        require('../FileInclude/FileRequired/Functions.php');
                    /*————*/

                    $action=$_SERVER['PHP_SELF'];
                    echo "<form action=" . $action . " method='POST' autocomplete='Off'>";
                    echo \IlSole\Auth\csrfField();

                    ?>
                        
                        <script src="../FileInclude/Javascript/JavaScriptClock.js"></script>

                        <?php

                            /* SELECT DELLA GRADUATORIA ORDINATA PER IL PUNTEGGIO TOTALE DAL MAGGIORE AL MINORE */
                                $query="SELECT * FROM Graduatoria";
                            /*————*/

                            /* ESECUZIONE DELLA SELECT */
                                $risultato=mysqli_query($connection,$query) OR DIE(mysqli_error($connection));
                            /*————*/

                            /* Numero di righe */
                                $righe=mysqli_num_rows($risultato);
                            /*————*/

                            /* La tabella della graduatoria viene generata SOLAMENTE se ci sono dei richiedenti già iscritti nel database (var $righe != 0). SE LA var $righe == 0 SIGNIFICA CHE NON CI SONO RICHIEDENTI E SI VIENE MANDATI ALLA PAGINA PER LE RICHIESTE DI DOMANDA */
                                if( $righe > 0 ) {
                                    ?>

                                        <!---- TABELLA GRADUATORIA ---->
                                            <div class='centro'>
                                            <table id="TabellaDati" class="Report" border="1">
                                                    
                                                <!---- TITOLO TABELLA ---->
                                                    <tr>
                                                        <th class="major" colspan="19">
                                                            <div id='all'>
                                                                <div id="test">loading</div>
                                                            </div>
                                                            Graduatoria
                                                            <div id='all'>
                                                                <div id="ttt"></div>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                <!---->

                                                <!---- INTESTAZIONE TABELLA ---->
                                                    <tr>
                                                        <th Class="LightBlue">
                                                            N&deg;
                                                        </th>

                                                        <th Class="LightBlue">
                                                            &#9775;
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Codice Fiscale
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Cognome
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Nome
                                                        </th>

                                                        <th title="Membri Nucleo Familiare" Class="LightBlue">
                                                            &#127968;
                                                        </th>

                                                        <th Class="LightBlue">
                                                            &#9742;&#65039;
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Pnt<br/>ISEE
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Pnt<br/>Minori
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Pnt<br/>Disocc.
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Pnt<br/>Invalidi
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Pnt<br/>Debiti
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Pnt<br/>Benefit
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Pnt<br/>Totale
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Codice<br/>Tessera
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Data<br/>Rilascio
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Data<br/>Scadenza
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Data<br/>Ritiro
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Termine<br/>Stallo<br/>
                                                        </th>

                                                    </tr>
                                                <!---->

                                                <!---- CORPO TABELLA ---->
                                                    <?php

                                                        $i=0;
                                                        while($riga=mysqli_fetch_array($risultato,MYSQLI_ASSOC)) {

                                                            $i++;
                                                            /* echo "<tr>"; */
                                                            if ( $i%2==0 ) {
                                                                echo "<tr class='Darker'>";
                                                            } else {
                                                                echo "<tr class='Lighter'>";
                                                            }

                                                            switch ($i) {
                                                                case ($i == 1):
                                                                    echo "<td Class='Gold'>".$i."</td>";
                                                                break;
                                                                case ($i == 2):
                                                                    echo "<td Class='Silver'>".$i."</td>";
                                                                break;
                                                                case ($i == 3):
                                                                    echo "<td Class='Bronze'>".$i."</td>";
                                                                break;
                                                                default:
                                                                    echo "<td Class='Green'>".$i."</td>";
                                                            }

                                                            echo"
                                                            <td><input type='radio' name='Cod_Fiscale' value='".$riga['Cod_Fiscale']."' required/></td>
                                                            <td title='Codice Fiscale Richiedente'>".$riga['Cod_Fiscale']."</td>
                                                            <td title='Cognome Richiedente'>".$riga['Cognome']."</td>
                                                            <td title='Nome Richiedente'>".$riga['Nome']."</td>
                                                            <td title='Membri Nucleo Familiare'>".$riga['nMembri']."</td>
                                                            <td title='Numero Di Telefono'>".$riga['Telefono']."</td>
                                                            <td title='Punteggio per ISEE'>".$riga['PunteggioISEE']."</td>
                                                            <td title='Punteggio Per Presenza Di Minori'>".$riga['PunteggioPresenzaMinori']."</td>
                                                            <td title='Punteggio Per Disoccupazione'>".$riga['PunteggioDisoccupazione']."</td>
                                                            <td title='Punteggio Per Invalidit&agrave;'>".$riga['PunteggioInvalidi']."</td>
                                                            <td title='Punteggio Per Situazione Debitoria'>".$riga['PunteggioSituazioneDebitoria']."</td>
                                                            <td title='Punteggio Detratto Per Benefit'>".$riga['PunteggioBenefit']."</td>
                                                            <td title='Punteggio Totale'>".$riga['PunteggioTotale']."</td>";
                                                            if( $riga['CodiceTessera']!="" ){
                                                                echo "<td title='Codice Della Tessera Assegnata'>".$riga['CodiceTessera']."</td>";
                                                            }
                                                            else{
                                                                echo "<td>-</td>";
                                                            }
                                                            if( $riga['DataOraRilascioTessera']=="" ){
                                                                echo "<td>-</td>";
                                                            }
                                                            else{
                                                                echo "<td title='Data in cui la tessera &egrave; stata rilasciata'>".$riga['DataOraRilascioTessera']."</td>";
                                                            }

                                                            if( $riga['DataOraScadenzaTessera']=="" ){
                                                                echo "<td>-</td>";
                                                            }
                                                            else{
                                                                echo "<td title='Data in cui la tessera assegnata scadr&agrave;'>".$riga['DataOraScadenzaTessera']."</td>
                                                                ";
                                                            }
                                                            if( $riga['DataOraRitiroTessera']=="" ){
                                                                echo "<td>-</td>";
                                                            }
                                                            else{
                                                                echo "<td title='Data in cui la tessera viene restituita o ritirata'>".$riga['DataOraRitiroTessera']."</td>
                                                                ";
                                                            }
                                                            if( $riga['DataOraStalloTessera']=="" ){
                                                                echo "<td>-</td></tr>";
                                                            }
                                                            else{
                                                                echo "<td title='Data fino alla quale non sar&agrave; concesso riottenere la tessera'>".$riga['DataOraStalloTessera']."</td>
                                                                </tr>";
                                                            }

                                                        };

                                                    ?>
                                                <!---->

                                                <!---- SUBMIT CHE ELIMINA I RICHIEDENTI SELEZIONATI ---->
                                                    <tr>
                                                        <th class='submit' colspan="19">
                                                            <input type="submit" name="Visualizza" value="Visualizza" />

                                                            <input type="reset" name="Annulla" value="Annulla" />
                                                        </th>
                                                    </tr>

                                                    <tr>
                                                        <th Class='submit' colspan="19">
                                                            <img style='width:100%;' src='../FileInclude/immagini/footer.png' />
                                                        </th>
                                                    </tr>
                                                <!---->

                                            </table>
                                            </div>
                                        <!---->

                                    <?php
                                }
                                else {
                                    ?>
                                        <script>
                                            alert("Non ci sono richiedenti:\nverrai renderizzato alla pagina di richiesta di domanda");
                                            window.location.replace("Punteggio.php");
                                        </script>
                                    <?php
                                }
                            /*————*/

                            /* Liberazione memoria utilizzata */
                                mysqli_free_result($risultato);
                            /*————*/

                            /* Rilascio della connessione */
                                mysqli_close($connection);
                            /*————*/

                        ?>

                    </form>
                    <?php

                }
            /*————*/

        ?>

    </body>
</html>