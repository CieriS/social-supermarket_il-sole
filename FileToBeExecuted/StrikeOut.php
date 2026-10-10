<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/ReportStyle.css" media="" rel="stylesheet" type="text/css">
        <title> Elimina Richiedente </title>
        <link rel="icon" type="image/png" href="../FileInclude/immagini/icon.png">
        <script src="../FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body>

        <?php
        
            /* FILE INCLUSI DI DEFAULT: Connessione al Database, Navbar e Footer, Funzioni php */
                require('../FileInclude/FileRequired/Connessione.php');
                require('../FileInclude/FileRequired/NavBars.php');
                require('../FileInclude/FileRequired/Cookie.php');
                require('../FileInclude/FileRequired/Functions.php');
            /*————*/

            /* Metodo postback per ottimizzare la quantità di file ottimizzati */
                if($_POST) {

                        /* RICEZIONE RADIO BUTTON */
                            $Cod_Fiscale=$_REQUEST['Cod_Fiscale'];
                        /*————*/

                        /* DELETE */
                            $query="DELETE FROM Richiedente WHERE Cod_Fiscale LIKE '".$Cod_Fiscale."';";
                        /*————*/

                        /* ESECUZIONE DELLA DELETE */
                            $risultato=mysqli_query($connection,$query) OR DIE(mysqli_error($connection));
                        /*————*/
                        
                        /* Dopo l'esecuzione del delete l'utente viene rimandato alla pagina dell'eliminazione */
                            ?>
                                <script>
                                    alert("Eliminato '<?php echo $Cod_Fiscale; ?>' dalla graduatoria\nClicca per procedere");window.location.replace("StrikeOut.php");
                                </script>
                            <?php
                        /*————*/

                        /* Rilascio della connessione */
                            mysqli_close($connection);
                        /*————*/

                }
                else {

                    $action=$_SERVER['PHP_SELF'];
                    echo "<form action=" . $action . " method='POST' autocomplete='Off'>";
                    ?>
                        
                        <?php

                            /* SELECT DELLA GRADUATORIA ORDINATA PER IL PUNTEGGIO TOTALE DAL MAGGIORE AL MINORE */
                                $query="SELECT * FROM Graduatoria;";
                                /* SELECT R.Cod_Fiscale, R.Cognome, R.Nome, R.nMembri, R.Telefono, R.Mail, P.PunteggioISEE, P.PunteggioPresenzaMinori, P.PunteggioDisoccupazione, P.PunteggioInvalidi, P.PunteggioSituazioneDebitoria, P.PunteggioBenefit, P.PunteggioTotale, TR.CodiceTessera, DATE_FORMAT(TR.DataOraRilascioTessera, '%d/%m/%Y') AS DataOraRilascioTessera, DATE_FORMAT(TR.DataOraScadenzaTessera, '%d/%m/%Y') AS DataOraScadenzaTessera, DATE_FORMAT(R.DataOraRitiroTessera, '%d/%m/%Y') AS DataOraRitiroTessera, DATE_FORMAT(R.DataOraStalloTessera, '%d/%m/%Y') AS DataOraStalloTessera  
                                FROM Richiedente AS R LEFT OUTER JOIN TesseraRilasciata AS TR 
                                ON R.Cod_Fiscale = TR.CodFamiglia 
                                LEFT OUTER JOIN  Tessera AS T 
                                ON TR.CodiceTessera = T.Codice 
                                INNER JOIN Punteggio AS P 
                                ON R.Cod_Fiscale = P.CodFamiglia  
                                ORDER BY P.PunteggioTotale DESC; */
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

                                        <script src="../FileInclude/Javascript/JavaScriptClock.js"></script>

                                        <!---- TABELLA GRADUATORIA ---->
                                            <div class='centro'>
                                            <table id='TabellaDati' class="Report" border="1">
                                                    
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
                                                            &#9798;
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

                                                        <th Class="LightBlue">
                                                            N&deg;<br/>Membri
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Numero<br/>Telefono
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
                                                            /*echo "<tr>";*/
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
                                                            <input type="submit" name="Elimina" value="Elimina" />

                                                            <input type="reset" name="Annulla" value="Annulla" />
                                                        </th>
                                                    </tr>

                                                    <tr>
                                                        <th Class='submit' colspan="19">
                                                            <img style='width:100%;' src='../FileInclude/immagini/Footer.png' />
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