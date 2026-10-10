<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/ReportStyle.css" media="" rel="stylesheet" type="text/css">
        <title> Graduatoria </title>
        <link rel="icon" type="image/png" href="../FileInclude/immagini/icon.png">
        <script src="../FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body>

        <script src="../FileInclude/Javascript/JavaScriptClock.js"></script>

        <?php

            /* FILE INCLUSI DI DEFAULT RISPETTIVAMENTE Connessione al Database, Navbar e Footer, Funzioni php */
                require('../FileInclude/FileRequired/Connessione.php');
                require('../FileInclude/FileRequired/NavBars.php');
                require('../FileInclude/FileRequired/Cookie.php');
                require('../FileInclude/FileRequired/Functions.php');
            /*————*/

            /* SELECT DELLA GRADUATORIA ORDINATA PER IL PUNTEGGIO TOTALE DAL MAGGIORE AL MINORE */
                $query="SELECT * FROM Graduatoria";
                /* SELECT R.Cod_Fiscale, R.Cognome, R.Nome, R.nMembri, R.Telefono, R.Mail, P.PunteggioISEE, P.PunteggioPresenzaMinori, P.PunteggioDisoccupazione, P.PunteggioInvalidi, P.PunteggioSituazioneDebitoria, P.PunteggioBenefit, P.PunteggioTotale, TR.CodiceTessera, DATE_FORMAT(TR.DataOraRilascioTessera, '%d/%m/%Y') AS DataOraRilascioTessera, DATE_FORMAT(TR.DataOraScadenzaTessera, '%d/%m/%Y') AS DataOraScadenzaTessera, DATE_FORMAT(R.DataOraRitiroTessera, '%d/%m/%Y') AS DataOraRitiroTessera, DATE_FORMAT(R.DataOraStalloTessera, '%d/%m/%Y') AS DataOraStalloTessera  
                FROM Richiedente AS R LEFT OUTER JOIN TesseraRilasciata AS TR 
                ON R.Cod_Fiscale = TR.CodFamiglia 
                LEFT OUTER JOIN  Tessera AS T 
                ON TR.CodiceTessera = T.Codice 
                INNER JOIN Punteggio AS P 
                ON R.Cod_Fiscale = P.CodFamiglia  
                ORDER BY P.PunteggioTotale DESC; */
            /*————*/

            /* Esecuzione della select */
                $risultato=mysqli_query($connection,$query) OR DIE(mysqli_error($connection));
            /*————*/

            /* Numero di righe */
                $righe=mysqli_num_rows($risultato);
            /*————*/

            /* La tabella della graduatoria viene generata SOLAMENTE se ci sono dei richiedenti già iscritti nel database (var $righe != 0). SE LA var $righe == 0 SIGNIFICA CHE NON CI SONO RICHIEDENTI E SI VIENE MANDATI ALLA PAGINA PER LE RICHIESTE DI DOMANDA */
                    ?>

                        <!---- TABELLA GRADUATORIA ---->
                            <div class='centro'>
                                <table class="Report" id="TabellaDati" border="1">
                                    
                                    <!---- GRADUATORIA ---->
                                        <tr>
                                            <th class="major" colspan="20">
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
                                                <span style='color:yellow;font-size:1.2vw;'>   
                                                    &#9728; 
                                                </span>
                                            </th>

                                            <th Class="LightBlue">
                                                Codice<br/>Fiscale
                                            </th>

                                            <th Class="LightBlue">
                                                Cognome
                                            </th>

                                            <th Class="LightBlue">
                                                Nome
                                            </th>

                                            <th Class="LightBlue">
                                                Membri<br/>Nucleo
                                            </th>

                                            <th Class="LightBlue">
                                                Telefono
                                            </th>

                                            <th Class="LightBlue">
                                                Mail
                                            </th>

                                            <th Class="LightBlue">
                                                Punteggio<br/>ISEE
                                            </th>

                                            <th Class="LightBlue">
                                                Punteggio<br/>Minori
                                            </th>

                                            <th Class="LightBlue">
                                                Punteggio<br/>Disoccupazione
                                            </th>

                                            <th Class="LightBlue">
                                                Punteggio<br/>Invalidi
                                            </th>

                                            <th Class="LightBlue">
                                                Punteggio<br/>Debiti
                                            </th>

                                            <th Class="LightBlue">
                                                Punteggio<br/>Benefit
                                            </th>

                                            <th Class="LightBlue">
                                                Punteggio<br/>Totale
                                            </th>

                                            <th Class="LightBlue">
                                                Codice
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
                                            while( $riga=mysqli_fetch_array($risultato,MYSQLI_ASSOC) ) {

                                                /*
                                                $Telefono="";
                                                $Tel=$riga['Telefono'];
                                                for ( $y=0;$y<3;$y++ ) {
                                                    $Telefono=$Telefono . $Tel[$y];
                                                }
                                                $Telefono=$Telefono . " ";
                                                for ( $y=3;$y<6;$y++ ) {
                                                    $Telefono=$Telefono . $Tel[$y];
                                                }
                                                $Telefono=$Telefono . " ";
                                                for ( $y=6;$y<strlen($riga['Telefono']);$y++ ) {
                                                    $Telefono=$Telefono . $Tel[$y];
                                                }
                                                $Telefono=trim($Telefono);
                                                */

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

                                                echo "<td>&nbsp;</td><td title='Codice Fiscale Richiedente'>".$riga['Cod_Fiscale']."</td>
                                                <td title='Cognome Richiedente'>".$riga['Cognome']."</td>
                                                <td title='Nome Richiedente'>".$riga['Nome']."</td>
                                                <td title='Membri Nel Nucleo Familiare'>".$riga['nMembri']."</td>
                                                <td title='Numero Di Telefono'>".$riga['Telefono']./*.$Telefono.*/"</td>
                                                <td title='e-mail'>".$riga['Mail']."</td>
                                                <td title='Punteggio Assegnato su base ISEE'>".$riga['PunteggioISEE']."</td>
                                                <td title='Punteggio Assegnato in base alla presenza di minori'>".$riga['PunteggioPresenzaMinori']."</td>
                                                <td title='Punteggio Assegnato in base alla Disoccupazione nel nucleo familiare'>".$riga['PunteggioDisoccupazione']."</td>
                                                <td title='Punteggio per invalidit&agrave;'>".$riga['PunteggioInvalidi']."</td>
                                                <td title='Punteggio per Situazione Debitoria'>".$riga['PunteggioSituazioneDebitoria']."</td>
                                                <td title='Punteggio detratto per Benefit'>".$riga['PunteggioBenefit']."</td>
                                                <td title='Punteggio Totale'>".$riga['PunteggioTotale']."</td>";
                                                if( $riga['CodiceTessera']!="" ){
                                                    echo "<td title='Codice Della Tessera Assegnata'>in possesso</td>";
                                                }
                                                else{
                                                    echo "<td>-</td>";
                                                }
                                                if( $riga['DataOraRilascioTessera']=="" ){
                                                    echo "<td>-</td>";
                                                }
                                                else{
                                                    echo "<td title='Data in cui la tessera &egrave; stata rilasciata'>".$riga['DataOraRilascioTessera']."</td>
                                                    ";
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

                                    <tr>
                                        <th Class='submit' colspan="20">
                                            <input type='submit' style='opacity:0;'/>
                                        </th>
                                    </tr>

                                    <tr>
                                        <th Class='submit' colspan="20">
                                            <img style='width:100%;' src='../FileInclude/immagini/Footer.png' />
                                        </th>
                                    </tr>

                                </table>
                            </div>
                        <!---->

                    <?php
            /*————*/

            /* Liberazione memoria utilizzata */
                mysqli_free_result($risultato);
            /*————*/

            /* Rilascio della connessione */
                mysqli_close($connection);
            /*————*/

        ?>

    </body>
</html>