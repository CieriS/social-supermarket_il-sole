<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/ReportStyle.css" media="" rel="stylesheet" type="text/css">
        <title> Assegnazione Tessera </title>
        <link rel="icon" type="image/png" href="../FileInclude/immagini/icon.png">
        <script src="../FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body>

        <?php
            session_start();
            /* FILE INCLUSI DI DEFAULT: Connessione al Database, Navbar e Footer, Funzioni php */
                require('../FileInclude/FileRequired/Connessione.php');
                require('../FileInclude/FileRequired/NavBars.php');
                require('../FileInclude/FileRequired/Cookie.php');
                require('../FileInclude/FileRequired/Functions.php');
            /*————*/

            /* Metodo postback per ottimizzare la quantità di file ottimizzati */
                if($_POST) {

                        /* Se Codice Tessera non è stato inserito */

                            if( isset($_REQUEST['CodiceTessera']) ){

                                $CodiceTessera=$_REQUEST['CodiceTessera'];
                                $_SESSION['CodiceTessera']=$CodiceTessera;

                            } else {
                                ?>
                                    <script>
                                        alert('Selezioni una tessera');
                                        window.location.replace('Pass.php');
                                    </script>         
                                <?php   
                                DIE(); 
                            }

                        /*————*/

                        /* RICEZIONE VARIABILI */
                            $CodiceFiscale=$_REQUEST['Cod_Fiscale'];
                            $_SESSION['CodiceFiscale']=$CodiceFiscale;
                        /*————*/

                        /* SELECT STALLO e Vista Generale */
                            $stallo="SELECT * 
                            FROM Stallo"; 

                            $CalcoloDataStallo="SELECT * 
                            FROM Confirmation 
                            WHERE Cod_Fiscale LIKE '".$CodiceFiscale."';";
                            /* SELECT R.Cod_Fiscale, R.Cognome, R.Nome, R.nMembri, P.PunteggioISEE, P.PunteggioPresenzaMinori, P.PunteggioDisoccupazione, P.PunteggioInvalidi, P.PunteggioSituazioneDebitoria, P.PunteggioBenefit, P.PunteggioTotale, TR.CodiceTessera, DATE_FORMAT(TR.DataOraRilascioTessera, '%Y/%m/%d') AS DataOraRilascioTessera, DATE_FORMAT(TR.DataOraScadenzaTessera, '%Y/%m/%d') AS DataOraScadenzaTessera, DATE_FORMAT(R.DataOraRitiroTessera, '%Y/%m/%d') AS DataOraRitiroTessera, DATE_FORMAT(R.DataOraStalloTessera, '%Y/%m/%d') AS DataOraStalloTessera  
                            FROM Richiedente AS R LEFT OUTER JOIN TesseraRilasciata AS TR 
                            ON R.Cod_Fiscale = TR.CodFamiglia 
                            LEFT OUTER JOIN  Tessera AS T 
                            ON TR.CodiceTessera = T.Codice 
                            INNER JOIN Punteggio AS P 
                            ON R.Cod_Fiscale = P.CodFamiglia 
                            WHERE Cod_Fiscale LIKE '".$CodiceFiscale."'  
                            ORDER BY P.PunteggioTotale DESC; */
                        /*————*/

                        /* Ricalcolo Stallo e setting var */
                            $RicalcoloStallo=mysqli_query($connection,$stallo) OR DIE(mysqli_error($connection));

                            $nMesiStallo=mysqli_fetch_array($RicalcoloStallo,MYSQLI_ASSOC) OR DIE(mysqli_error($connection));
                            
                            $RicalcoloDataStallo=mysqli_query($connection,$CalcoloDataStallo) OR DIE(mysqli_error($connection));

                            $DataStallo=mysqli_fetch_array($RicalcoloDataStallo,MYSQLI_ASSOC) OR DIE(mysqli_error($connection));
                        /*————*/

                        /* Controllo che la tessera non sia già in uso o sia nel periodo si stallo */
                            $controllo="SELECT * FROM Pass";
                            $Ris=mysqli_query($connection,$controllo) OR DIE(mysqli_error($connection));
                            $RisCF=mysqli_query($connection,$CalcoloDataStallo) OR DIE(mysqli_error($connection));
                            while( $riga=mysqli_fetch_array($Ris,MYSQLI_ASSOC) ){
                                if( $riga['CodiceTessera'] == $CodiceTessera ){
                                    ?>
                                        <script>
                                            alert('La tessera selezionata è già stata assegnata!');
                                            window.location.replace('Pass.php');
                                        </script>         
                                    <?php
                                    DIE();
                                }
                            }
                            while( $riga=mysqli_fetch_array($RisCF,MYSQLI_ASSOC) ){
                                    if( $riga['CodiceTessera'] == NULL && $riga['DataOraRilascioTessera'] == NULL && $riga['DataOraScadenzaTessera'] == NULL && $riga['DataOraRitiroTessera'] != NULL && $riga['DataOraStalloTessera'] != NULL ){

                                        $OggiSecondi=DATE('Y/m/d');
                                        $StalloSecondi=$DataStallo['DataOraStalloTessera'];

                                        if( $OggiSecondi < $StalloSecondi ) {
                                            ?>
                                                <script>
                                                    if(confirm("Il richiedente è nel periodo di stallo, è sicuro di voler assegnare comunque la tessera?\nCliccare 'Ok' per assegnare comunque\nCliccare 'Annulla' per non assegnare")) {

                                                        alert("La tessera verrà assegnata comunque\nClicchi 'Chiudi' per procedere");
                                                        window.location.replace("ConfermaAssegnazione.php");

                                                    } else {
                                                        alert("l'azione è stata annullata\nLa pagina verrà ricaricata");
                                                        window.location.replace("Pass.php");
                                                    }
                                                </script> 
                                            <?php
                                            DIE();
                                        }

                                        $inserTessera="UPDATE TesseraRilasciata 
                                        SET CodiceTessera = '".$CodiceTessera."', DataOraRilascioTessera = NOW(), DataOraScadenzaTessera = (NOW()+INTERVAL '".$nMesiStallo['nMesiStallo']."' MONTH), 
                                        WHERE CodFamiglia = '".$CodiceFiscale."';";
            
                                        $inserRich="UPDATE Richiedente 
                                        SET DataOraRitiroTessera = NULL, DataOraStalloTessera = NULL 
                                        WHERE Cod_Fiscale = '".$CodiceFiscale."';";
            
                                        $insertStoricoTessere="INSERT INTO StoricoTessere ( CodiceTessera, CodFamiglia, DataRilascio ) VALUES ('".$CodiceTessera."','".$CodiceFiscale."',NOW());";

                                    }elseif( $riga['CodiceTessera'] == "" && $riga['DataOraRilascioTessera'] == "" && $riga['DataOraScadenzaTessera'] == "" && $riga['DataOraRitiroTessera'] == "" && $riga['DataOraStalloTessera'] == "" ){

                                        $inserTessera="INSERT INTO TesseraRilasciata (CodiceTessera, CodFamiglia, DataOraRilascioTessera, DataOraScadenzaTessera) VALUES 
                                        ('".$CodiceTessera."','".$CodiceFiscale."',NOW(),(NOW()+INTERVAL '".$nMesiStallo['nMesiStallo']."' MONTH));";

                                    }
                            }
                        /*————*/

                        /* Esecuzione Assegnazione */
                            $risultato1=mysqli_query($connection,$inserTessera) OR DIE(mysqli_error($connection));

                            $inserRich="UPDATE Richiedente 
                            SET DataOraRitiroTessera = NULL, DataOraStalloTessera = NULL 
                            WHERE Cod_Fiscale = '".$CodiceFiscale."';";

                            $insertStoricoTessere="INSERT INTO StoricoTessere ( CodiceTessera, CodFamiglia, DataRilascio ) VALUES ('".$CodiceTessera."','".$CodiceFiscale."',NOW());";

                            $risultatoInserRich=mysqli_query($connection,$inserRich) OR DIE($connection);

                            $risultatoStoricoTessere=mysqli_query($connection,$insertStoricoTessere) OR DIE($connection);
                        /*————*/

                        /* Dopo l'esecuzione dell'insert l'utente viene rimandato alla pagina del'assegnazione */
                            ?>
                                <script>
                                    alert("Assegnata Tessera ('<?php echo $CodiceTessera; ?>') a '<?php echo $CodiceFiscale; ?>'\nClicca per procedere");
                                    window.location.replace("Pass.php");
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

                            /* SELECT DELLA GRADUATORIA ORDINATA PER IL PUNTEGGIO TOTALE DAL MAGGIORE AL MINORE (SOLO DI QUELLI CHE NON HANNO LA TESSERA, E SE LA HANNO AVUTA, CHE SIA PASSATO ALMENO UN ANNO) */
                                $query="SELECT * FROM Pass";
                            /*————*/

                            /* SELECT DELLA GRADUATORIA ORDINATA PER IL PUNTEGGIO TOTALE DAL MAGGIORE AL MINORE (SOLO DI QUELLI CHE NON HANNO LA TESSERA, E SE LA HANNO AVUTA, CHE SIA PASSATO ALMENO UN ANNO) */

                                /* $queryCodTessere="SELECT * 
                                FROM Tessera 
                                ORDER BY Codice ASC;"; Con questo si vedono tutte le tessere */

                                /*$queryCodTessere="SELECT * FROM tessera AS T LEFT OUTER JOIN TesseraRilasciata AS TR ON T.Codice = TR.CodiceTessera where TR.CodiceTessera is null;";*/ /* Con questo si vedono solo le tessere non ancora assegnate */

                                $queryCodTessere="SELECT * FROM tessera AS T LEFT OUTER JOIN TesseraRilasciata AS TR ON T.Codice = TR.CodiceTessera;"; /* Con questo si vedono tutte le tessere */
                            /*————*/

                            /* ESECUZIONE DELLE SELECT */
                                $risultato=mysqli_query($connection,$query) OR DIE(mysqli_error($connection));
                                $risultatoTessere=mysqli_query($connection,$queryCodTessere) OR DIE(mysqli_error($connection));
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
                                                        <th class='major' colspan="18">
                                                            <div id='all'>
                                                                <div id="test">loading</div>
                                                            </div>
                                                            Assegna Tessera
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
                                                            &#9835;
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
                                                            Pnt<br/>ISEE
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Pnt<br/>Minori
                                                        </th>

                                                        <th Class="LightBlue">
                                                            Pnt<br/>Disocc
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

                                                            if( $riga['CodiceTessera']=="" ){
                                                                echo "
                                                                <td><input type='radio' name='Cod_Fiscale' value='".$riga['Cod_Fiscale']."' required/></td>";
                                                            }
                                                            else{
                                                                echo "<td><input style='opacity: 44%;' type='radio' disabled/></td>";
                                                            }
                                                            echo "
                                                            <td title='Codice Fiscale Richiedente'>".$riga['Cod_Fiscale']."</td>
                                                            <td title='Cognome Richiedente'>".$riga['Cognome']."</td>
                                                            <td title='Nome Richiedente'>".$riga['Nome']."</td>
                                                            <td title='Membri Nucleo Familiare'>".$riga['nMembri']."</td>
                                                            <td title='Punteggio per ISEE'>".$riga['PunteggioISEE']."</td>
                                                            <td title='Punteggio Per Presenza Di Minori'>".$riga['PunteggioPresenzaMinori']."</td>
                                                            <td title='Punteggio Per Disoccupazione'>".$riga['PunteggioDisoccupazione']."</td>
                                                            <td title='Punteggio Per Invalidit&agrave;'>".$riga['PunteggioInvalidi']."</td>
                                                            <td title='Punteggio Per Situazione Debitoria'>".$riga['PunteggioSituazioneDebitoria']."</td>
                                                            <td title='Punteggio Detratto Per Benefit'>".$riga['PunteggioBenefit']."</td>
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

                                                <!---- input codice tessere ---->
                                                    <tr>
                                                        <th colspan="18">
                                                            <select name="CodiceTessera">
                                                                <optgroup label="Tessere">

                                                                    <option value="" disabled selected>Seleziona</option>
                                                                    <?php
                                                                    
                                                                        while($riga_tes=mysqli_fetch_array($risultatoTessere,MYSQLI_ASSOC)) {
                                                                            if ( $riga_tes['CodiceTessera'] == NULL ) {
                                                                                echo "<option value='".$riga_tes['Codice']."'>".$riga_tes['Codice']."</option>";
                                                                            } else {
                                                                                echo "<option value='".$riga_tes['Codice']."' disabled>".$riga_tes['Codice']."</option>";
                                                                            }
                                                                        }

                                                                    ?>

                                                                </optgroup>
                                                            </select>
                                                        </th>
                                                    </tr>
                                                <!---->

                                                <!---- SUBMIT CHE ELIMINA I RICHIEDENTI SELEZIONATI ---->
                                                    <tr>
                                                        <th class='submit' colspan="18">
                                                            <input type="submit" name="Assegna" value="Assegna" />
                                                            <input type="reset" name="Annulla" value="Annulla" />
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