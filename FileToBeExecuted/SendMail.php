<?php require __DIR__ . '/../src/bootstrap.php'; \IlSole\Auth\requireLogin(); ?>
<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/MailStyle.css" media="" rel="stylesheet" type="text/css">
        <title> Spedisci Mail </title>
        <link rel="icon" type="image/png" href="../FileInclude/immagini/icon.png">
        <script src="../FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body>

        <?php


            /* FILE INCLUSI DI DEFAULT RISPETTIVAMENTE Connessione al Database, Navbar e Footer, Funzioni php */
                require('../FileInclude/FileRequired/Connessione.php');
                require('../FileInclude/FileRequired/NavBars.php');
                require(__DIR__ . '/../src/Mail.php');
            /*————*/

            /* Mailing */

                /* Tutte le Mails */
                    $arrayMail="SELECT Cod_Fiscale, Nome, Cognome, Mail AS Mail FROM Richiedente GROUP BY Mail ORDER BY Mail ASC;";

                    $arrayMailResti="SELECT DISTINCT(R.Mail) AS Mail, CURDATE() AS Oggi, DataOraScadenzaTessera FROM Richiedente AS R INNER JOIN TesseraRilasciata AS TR ON TR.CodFamiglia = R.Cod_Fiscale HAVING (Oggi + INTERVAL 7 DAY) >= DataOraScadenzaTessera ORDER BY Mail ASC;";
                    
                    $arrayMailStallo="SELECT DISTINCT(Mail) AS Mail, CURDATE() AS Oggi, DataOraStalloTessera FROM Richiedente HAVING (Oggi + INTERVAL 7 DAY) >= DataOraStalloTessera ORDER BY Mail ASC;";
                /*————*/

                /* View Competenze */
                    $risultatoMail=mysqli_query($connection,$arrayMail) OR DIE(mysqli_error($connection));
                    
                    $risultatoMailResti=mysqli_query($connection,$arrayMailResti) OR DIE(mysqli_error($connection));

                    $risultatoMailStallo=mysqli_query($connection,$arrayMailStallo) OR DIE(mysqli_error($connection));
                /*————*/

                /* Quantitativo Mail */
                    $nMail=mysqli_num_rows($risultatoMail);

                    $nMailResti=mysqli_num_rows($risultatoMailResti);
                    
                    $nMailStallo=mysqli_num_rows($risultatoMailStallo);
                /*————*/

            /*————*/

            if($_POST){

                /* Un gruppo di famiglie oppure un indirizzo */
                $Destinatario = \IlSole\Input\text($_POST['Destinatario'] ?? '');
                if ( !in_array($Destinatario, ['Tutti', 'TuttiRestituzione', 'TuttiStallo'], true) ) {
                    $Destinatario = \IlSole\Input\email($Destinatario);
                }
                $Oggetto = \IlSole\Input\singleLine($_POST['Oggetto'] ?? '');
                $Messaggio = \IlSole\Input\text($_POST['Messaggio'] ?? '');
                $headers = 'From: noreply@example.com' . "\r\n" . 'Reply-To: noreply@example.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
                

                if ( $Destinatario == 'Tutti' ) {

                    while ( $riga=mysqli_fetch_array($risultatoMail,MYSQLI_ASSOC) ) {
                        \IlSole\Mail\send($riga['Mail'], $Oggetto, $Messaggio, $headers);
                    }

                    ?>
                        <script>
                            alert("<?php echo \IlSole\Config\demoMode() ? "DEMO: nessuna mail viene spedita davvero.\n" : ""; ?>Mail inviata a tutte le famiglie.\n<?php echo "inviata a: " . $nMail . " persone"; ?>");
                            window.location.replace("List.php");
                        </script>
                    <?php

                } else if ( $Destinatario == 'TuttiRestituzione' ) {

                    while ( $riga=mysqli_fetch_array($risultatoMailResti,MYSQLI_ASSOC) ) {
                    \IlSole\Mail\send($riga['Mail'], $Oggetto, $Messaggio, $headers);
                    }

                    ?>
                        <script>
                            alert("<?php echo \IlSole\Config\demoMode() ? "DEMO: nessuna mail viene spedita davvero.\n" : ""; ?>Mail inviata a tutte le famiglie con imminente restituzione di tessera o che devono restituire la tessera.\n<?php echo "inviata a: " . $nMailResti . " persone"; ?>");
                            window.location.replace("List.php");
                        </script>
                    <?php

                } else if ( $Destinatario == 'TuttiStallo' ) {

                    while ( $riga=mysqli_fetch_array($risultatoMailStallo,MYSQLI_ASSOC) ) {
                    \IlSole\Mail\send($riga['Mail'], $Oggetto, $Messaggio, $headers);
                    }

                    ?>
                        <script>
                            alert("<?php echo \IlSole\Config\demoMode() ? "DEMO: nessuna mail viene spedita davvero.\n" : ""; ?>Mail inviata a tutte le famiglie il cui periodo di stallo è terminato o sta per terminare.\n<?php echo "inviata a: " . $nMailStallo . " persone"; ?>");
                            window.location.replace("List.php");
                        </script>
                    <?php

                } else if ( $Destinatario == '' ) {
                    ?>
                        <script>
                            alert("Destinatario non valido: mail non inviata");
                            window.location.replace("SendMail.php");
                        </script>
                    <?php
                } else {

                    \IlSole\Mail\send($Destinatario, $Oggetto, $Messaggio, $headers);

                    ?>
                        <script>
                            alert("<?php echo \IlSole\Config\demoMode() ? "DEMO: nessuna mail viene spedita davvero.\n" : ""; ?>Mail inviata a <?php echo $Destinatario; ?>");
                            window.location.replace("List.php");
                        </script>
                    <?php

                }

                /* Liberazione memoria utilizzata */
                    mysqli_free_result($risultatoMail);
                /*————*/

                /* Rilascio della connessione */
                    mysqli_close($connection);
                /*————*/

            } else {

                $action=$_SERVER['PHP_SELF'];
                echo "<form action=" . $action . " method='POST' autocomplete='Off'>";
                echo \IlSole\Auth\csrfField();

                    ?>
                        <div class='centro'>
                            <table>
                                <tr>
                                    <th class='Comunicazioni Fucsia'>
                                        <span>Comunicazioni</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <input type='text' placeholder='Oggetto' class='Oggetto' name='Oggetto' required/>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        A: <select class='Destinatario' name='Destinatario'>
                                            <option value='Tutti'>Tutti</option>
                                            <option value='TuttiRestituzione'>Richied. che devono restituire tessera che scade fra 7 giorni o &egrave; gi&agrave; scaduta</option>
                                            <option value='TuttiStallo'>Richied. il cui periodo di stallo &egrave; terminato o sta per terminare</option>
                                            <?php
                                                while ( $riga=\IlSole\Html\escapeRow(mysqli_fetch_array($risultatoMail,MYSQLI_ASSOC)) ) {
                                                    echo "<option value='".$riga['Mail']."'>".$riga['Cod_Fiscale']." - ".$riga['Nome']." - ".$riga['Cognome']." - ".$riga['Mail']."</option>";
                                                }
                                            ?>
                                        </select>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <textarea placeholder='Messaggio' name='Messaggio' required></textarea>
                                    </th>
                                </tr>
                                <tr>
                                    <th>
                                        <input type='submit' name='invia' value='invia'/>
                                    </th>
                                </tr>
                            </table>
                        </div>
                    <?php

                    /* Liberazione memoria utilizzata */
                        mysqli_free_result($risultatoMail);
                    /*————*/

                    /* Rilascio della connessione */
                        mysqli_close($connection);
                    /*————*/

                echo "</form>";
            
            }

        ?>

    </body>
</html>