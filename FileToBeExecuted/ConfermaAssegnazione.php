<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
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
                        require('../FileInclude/FileRequired/Connessione.php');
                        
                            /* Se Codice Tessera non è stato inserito */

                            if( isset($_SESSION['CodiceTessera']) ){

                                $CodiceTessera=$_SESSION['CodiceTessera'];

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
                            $CodiceFiscale=$_SESSION['CodiceFiscale'];
                        /*————*/

                        /* SELECT STALLO e Vista Generale */
                            $stallo="SELECT * 
                            FROM Stallo"; 

                            $CalcoloDataStallo="SELECT * 
                            FROM Pass 
                            WHERE Cod_Fiscale LIKE '".$CodiceFiscale."';";
                            /* SELECT R.Cod_Fiscale, R.Cognome, R.Nome, R.nMembri, P.PunteggioISEE, P.PunteggioPresenzaMinori, P.PunteggioDisoccupazione, P.PunteggioInvalidi, P.PunteggioSituazioneDebitoria, P.PunteggioBenefit, P.PunteggioTotale, TR.CodiceTessera, DATE_FORMAT(TR.DataOraRilascioTessera, '%d/%m/%Y') AS DataOraRilascioTessera, DATE_FORMAT(TR.DataOraScadenzaTessera, '%d/%m/%Y') AS DataOraScadenzaTessera, DATE_FORMAT(R.DataOraRitiroTessera, '%d/%m/%Y') AS DataOraRitiroTessera, DATE_FORMAT(R.DataOraStalloTessera, '%d/%m/%Y') AS DataOraStalloTessera  
                            FROM Richiedente AS R LEFT OUTER JOIN TesseraRilasciata AS TR 
                            ON R.Cod_Fiscale = TR.CodFamiglia 
                            LEFT OUTER JOIN  Tessera AS T 
                            ON TR.CodiceTessera = T.Codice 
                            INNER JOIN Punteggio AS P 
                            ON R.Cod_Fiscale = P.CodFamiglia  
                            ORDER BY P.PunteggioTotale DESC; */
                        /*————*/

                        /* Ricalcolo Stallo e setting var */
                            $RicalcoloStallo=mysqli_query($connection,$stallo) OR DIE(mysqli_error($connection));

                            $nMesiStallo=mysqli_fetch_array($RicalcoloStallo,MYSQLI_ASSOC);
                            
                            $RicalcoloDataStallo=mysqli_query($connection,$CalcoloDataStallo) OR DIE(mysqli_error($connection));

                            $DataStallo=mysqli_fetch_array($RicalcoloDataStallo,MYSQLI_ASSOC);
                        /*————*/

                        $inserTessera="INSERT INTO TesseraRilasciata (CodiceTessera, CodFamiglia, DataOraRilascioTessera, DataOraScadenzaTessera) VALUES 
                        ('".$CodiceTessera."','".$CodiceFiscale."',NOW(),(NOW()+INTERVAL '".$nMesiStallo['nMesiStallo']."' MONTH));";

                        $inserRich="UPDATE Richiedente 
                        SET DataOraRitiroTessera = NULL, DataOraStalloTessera = NULL 
                        WHERE Cod_Fiscale = '".$CodiceFiscale."';";

                        $insertStoricoTessere="INSERT INTO StoricoTessere ( CodiceTessera, CodFamiglia, DataRilascio ) VALUES ('".$CodiceTessera."','".$CodiceFiscale."',NOW());";

                        /*————*/

                        /* Esecuzione Assegnazione */

                            $risultatoinserTessera=mysqli_query($connection,$inserTessera) OR DIE(mysqli_error($connection));
                            
                            $risultatoinserRich=mysqli_query($connection,$inserRich) OR DIE(mysqli_error($connection));
                            
                            $risultatoinsertStoricoTessere=mysqli_query($connection,$insertStoricoTessere) OR DIE(mysqli_error($connection));

                        /*————*/

                        /* Dopo l'esecuzione dell'insert l'utente viene rimandato alla pagina del'assegnazione */
                            ?>
                                <script>
                                    alert("Assegnata Tessera ('<?php echo $CodiceTessera; ?>') a '<?php echo $CodiceFiscale; ?>'\nClicchi 'Chiudi' per procedere");
                                    window.location.replace("Pass.php");
                                </script>
                            <?php
                        /*————*/
                        
                        /* Rilascio della connessione */
                            mysqli_close($connection);
                        /*————*/

                        session_abort();

        ?>
    </body>
</html>