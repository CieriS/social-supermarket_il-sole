<?php require __DIR__ . '/../src/bootstrap.php'; \IlSole\Auth\requireLogin(); ?>
<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/SettingStyle.css" media="" rel="stylesheet" type="text/css">
        <title> Settings </title>
        <link rel="icon" type="image/png" href="../FileInclude/immagini/icon.png">
        <script src="../FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body>

        <?php


            /* FILE INCLUSI DI DEFAULT RISPETTIVAMENTE Connessione al Database, Navbar e Footer, Funzioni php */
                require('../FileInclude/FileRequired/Connessione.php');
                require('../FileInclude/FileRequired/NavBars.php');
                require(__DIR__ . '/../src/Scoring.php');
                require(__DIR__ . '/../src/Rules.php');
                require(__DIR__ . '/../src/ApplicationForm.php');
                require(__DIR__ . '/../src/ApplicationRecord.php');
                require(__DIR__ . '/../src/ApplicationStore.php');
            /*————*/

            /* Ricalcolo di tutti i punteggi con la configurazione salvata */
            if( isset($_POST['Ricalcola']) ) {
                try {
                    $Ricalcolate=\IlSole\Store\recalculateScores($connection, new DateTimeImmutable('today'));
                    $Esito="Graduatoria ricalcolata\nDomande aggiornate: ".$Ricalcolate;
                } catch ( InvalidArgumentException $Errore ) {
                    $Esito="Graduatoria non ricalcolata\nUna domanda ha dati che non rientrano negli intervalli configurati";
                }
                ?>
                    <script>
                        alert(<?php echo json_encode($Esito, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>);
                        window.location.replace("List.php");
                    </script>
                <?php
                DIE();
            }

            if($_POST){

                /* Ricezione Variabili */

                    $nMesiStallo=\IlSole\Input\wholeNumber($_POST['nMesiStallo'] ?? '', 0, 120) ?? 0;

                    /* Gestione Credenziali: si cambiano solo se viene scritta una nuova password */
                        $NuovaPsw=(string) ($_POST['Psw'] ?? '');
                        $Username=strtolower(trim((string) ($_POST['Username'] ?? '')));
                        $ErroreCredenziali=null;
                        if( \IlSole\Config\demoMode() ) {
                            /* Nella demo le credenziali restano quelle mostrate al login */
                            $NuovaPsw='';
                        } elseif( $NuovaPsw !== '' || ($_POST['ConfirmPsw'] ?? '') !== '' ) {
                            if( $NuovaPsw !== ($_POST['ConfirmPsw'] ?? '') ) {
                                $ErroreCredenziali="Le password non corrispondono";
                            } elseif( strlen($NuovaPsw) < 8 ) {
                                $ErroreCredenziali="La password deve avere almeno 8 caratteri";
                            } elseif( !preg_match('/^[a-z0-9._-]{4,15}$/', $Username) ) {
                                $ErroreCredenziali="L'username deve avere da 4 a 15 caratteri tra lettere, cifre, punto, trattino";
                            }
                        }
                        if( $ErroreCredenziali !== null ) {
                            ?>
                                <script>
                                    alert(<?php echo json_encode("Configurazione non salvata\n".$ErroreCredenziali, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>);
                                    window.location.replace("Config.php");
                                </script>
                            <?php
                            DIE();
                        }
                    /*————*/

                    $ISEEmin=$_REQUEST['ISEEmin'];
                    $ISEEmax=$_REQUEST['ISEEmax'];
                    $Punti=$_REQUEST['Punteggio'];
                    $nComponenti=$_REQUEST['nComponenti'];
                    $ValoreEuro=$_REQUEST['ValoreEuro'];
                    $ValorePunti=$_REQUEST['ValorePunti'];
                /*————*/

                /* Punti della griglia: si salva solo se sono tutti validi */
                    try {
                        $RegolePunteggio=\IlSole\Rules\rulesFromForm($_REQUEST['Regola'] ?? null);
                    } catch ( InvalidArgumentException $Errore ) {
                        ?>
                            <script>
                                alert(<?php echo json_encode("Configurazione non salvata\n".$Errore->getMessage(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>);
                                window.location.replace("Config.php");
                            </script>
                        <?php
                        DIE();
                    }
                /*————*/

                /* Esecuzione Update */

                    /* Update Credenziali */
                        if( $NuovaPsw !== '' ) {
                            mysqli_execute_query($connection,"UPDATE Administrator SET Username = ?, Password = ? WHERE Username = ?",[$Username,password_hash($NuovaPsw,PASSWORD_DEFAULT),$_SESSION['operator']]);
                            $_SESSION['operator']=$Username;
                        }
                    /*————*/

                    /* Update Mesi di Stallo */
                        $UpdateMesiStallo="UPDATE Stallo 
                        SET nMesiStallo = ?;";
                        $UpdateMesiStalloParametri=[$nMesiStallo];

                        $RisultatoStallo=mysqli_execute_query($connection,$UpdateMesiStallo,$UpdateMesiStalloParametri) OR DIE(mysqli_error($connection));
                    /*————*/

                    /* Update IndicatoriISEE (ISEEmin, ISEEmax, Punteggio) */
                        for( $i=1;$i<=count($Punti);$i++ ) {
                            $UpdateIndISEE="UPDATE IndicatoriISEE 
                            SET ISEEmin = ?, ISEEmax = ?, Punteggio = ? 
                            WHERE N = ?;";
                            $UpdateIndISEEParametri=[$ISEEmin[$i],$ISEEmax[$i],$Punti[$i],$i];

                            $RisultatoIndISEE=mysqli_execute_query($connection,$UpdateIndISEE,$UpdateIndISEEParametri);
                        }
                    /*————*/

                    /* Update PunteggioMensile (nComponenti, ValoreEuro, ValorePunti) */
                        for( $i=1;$i<=count($nComponenti);$i++ ) {             
                            $UpdatePntMen="UPDATE PunteggioMensile 
                            SET nComponenti = ?, ValoreEuro = ?, ValorePunti = ? 
                            WHERE N = ?;";
                            $UpdatePntMenParametri=[$nComponenti[$i],$ValoreEuro[$i],$ValorePunti[$i],$i];

                            $RisultatoPntMen=mysqli_execute_query($connection,$UpdatePntMen,$UpdatePntMenParametri) OR DIE(mysqli_error($connection));
                        }
                    /*————*/

                    /* Update RegolePunteggio (punti della griglia) */
                        $salvaRegola=mysqli_prepare($connection,"INSERT INTO RegolePunteggio (Chiave, Punti) VALUES (?, ?) ON DUPLICATE KEY UPDATE Punti = VALUES(Punti)");
                        foreach ( $RegolePunteggio as $Chiave => $PuntiRegola ) {
                            mysqli_stmt_bind_param($salvaRegola,"si",$Chiave,$PuntiRegola);
                            mysqli_stmt_execute($salvaRegola);
                        }
                    /*————*/

                /*————*/

                /* Rendering */
                    ?>
                        <script>
                            alert("Database Aggiornato\n(l'username viene aggiornato solamente se vengono immesse delle password)");
                            window.location.replace("List.php");
                        </script>
                    <?php
                /*————*/

            } else {

                $action=$_SERVER['PHP_SELF'];
                echo "<form action=" . $action . " method='POST' autocomplete='Off'>";
                echo \IlSole\Auth\csrfField();

                    /* SELECT */

                        /*———— Mesi Di Stallo ————*/
                        $MesiStallo="SELECT * 
                        FROM Stallo";

                        $RisultatoStallo=mysqli_query($connection,$MesiStallo) OR DIE(mysqli_error($connection));

                        $nMesiStallo=\IlSole\Html\escapeRow(mysqli_fetch_array($RisultatoStallo,MYSQLI_ASSOC));

                        /*———— Cambia Credenziali ————*/
                        $Credenziali="SELECT * 
                        FROM Administrator";

                        $RisultatoCredenziali=mysqli_query($connection,$Credenziali) OR DIE(mysqli_error($connection));

                        $UsPsw=\IlSole\Html\escapeRow(mysqli_fetch_array($RisultatoCredenziali,MYSQLI_ASSOC));

                        /*———— Punteggio MENSILE ————*/
                        $PntMensile="SELECT * 
                        FROM PunteggioMensile";

                        $RisultatoPntMensile=mysqli_query($connection,$PntMensile) OR DIE(mysqli_error($connection));

                        /*———— Indicatori ISEE ————*/
                        $indISEE="SELECT * 
                        FROM IndicatoriISEE";

                        $RisultatoindISEE=mysqli_query($connection,$indISEE) OR DIE(mysqli_error($connection));

                    /*————*/

                    /* Scrittura */

                        echo "<div class='centro'>";
                        echo "<table class='Set' border=1>";
                        echo "<tr><th class='major' colspan='3'>Configurazione</th></tr>";

                        /*———— Mesi Di Stallo ————*/
                        echo "<tr><th Class='LightBlue' colspan='3'>Numero Mesi Di Stallo</th></tr>";
                        echo "<tr><th Class='Orangino' colspan='3'>Periodo in cui la tessera non sar&agrave; pi&ugrave; accessibile dopo la restituzione (in mesi)</th></tr>";
                        echo "<tr><th colspan='3'><input type='number' name='nMesiStallo' value='".$nMesiStallo['nMesiStallo']."' required/></th></tr>";

                        /*———— Cambia Credenziali ————*/
                        echo "<tr><th Class='LightBlue' colspan='3'>Cambia Username e Password</th></tr>";
                        echo "<tr><th Class='Orangino' colspan='3'>".( \IlSole\Config\demoMode() ? "Nella versione dimostrativa le credenziali non si possono cambiare" : "Nuovo Username e password (se l'username cambia deve essere cambiata anche la password)" )."</th></tr>";

                        echo "<tr title='Se cambia l'username vanno inserite anche le password, altrimenti le credenziali non verranno aggiornate.'><th colspan='3'><input minlength='4' maxlength='15' type='text' name='Username' placeholder='Username' value='".\IlSole\Html\e($UsPsw['Username'])."' required/></th></tr>
                        <tr title='Se cambia l'username vanno inserite anche le password, altrimenti le credenziali non verranno aggiornate.'><th colspan='3'><input minlength='8' maxlength='72' type='password' name='Psw' placeholder='Password' autocomplete='new-password' /><input minlength='8' maxlength='72' type='password' name='ConfirmPsw' placeholder='Conferma Password' autocomplete='new-password'/></th></tr>";

                        /*———— Indicatori ISEE ————*/
                        echo "<tr><th Class='LightBlue' colspan='3'>Indicatori ISEE</th></tr>";
                        echo "<tr><th Class='Orangino'>ISEE MIN</th><th Class='Orangino'>ISEE MAX</th><th Class='Orangino'>Punteggio</th></tr>";
                        $i=1;
                        while( $RigaIndISEE=\IlSole\Html\escapeRow(mysqli_fetch_array($RisultatoindISEE,MYSQLI_ASSOC)) ) {
                            echo "<tr><th><input type='number' name='ISEEmin[$i]' value='".$RigaIndISEE['ISEEmin']."' required/></th>";
                            echo "<th><input type='number' name='ISEEmax[$i]' value='".$RigaIndISEE['ISEEmax']."' required/></th>";
                            echo "<th><input type='number' name='Punteggio[$i]' value='".$RigaIndISEE['Punteggio']."' required/></th></tr>";
                            $i++;
                        }

                        /*———— Punteggio Mensile ————*/
                        echo "<tr><th Class='LightBlue' colspan='3'>Punteggio Mensile</th></tr>";
                        echo "<tr><th Class='Orangino'>Numero Componenti Famiglia</th><th Class='Orangino'>&euro;</th><th Class='Orangino'>Valore in Punti</th></tr>";
                        $i=1;
                        while( $RigaPntMensile=\IlSole\Html\escapeRow(mysqli_fetch_array($RisultatoPntMensile,MYSQLI_ASSOC)) ) {
                            echo "<tr><th><input type='number' name='nComponenti[$i]' value='".$RigaPntMensile['nComponenti']."' required/></th>";
                            echo "<th><input type='number' name='ValoreEuro[$i]' value='".$RigaPntMensile['ValoreEuro']."' required/></th>";
                            echo "<th><input type='number' name='ValorePunti[$i]' value='".$RigaPntMensile['ValorePunti']."' required/></th></tr>";
                            $i++;
                        }

                        /*———— Punti della griglia ————*/
                        $risRegole=mysqli_query($connection,"SELECT Chiave, Punti FROM RegolePunteggio") OR DIE(mysqli_error($connection));
                        $RegolePunteggio=\IlSole\Rules\rulesFromRows(mysqli_fetch_all($risRegole,MYSQLI_ASSOC));

                        echo "<tr><th Class='LightBlue' colspan='3'>Griglia Di Valutazione</th></tr>";
                        echo "<tr><th Class='Orangino' colspan='3'>Punti assegnati ad ogni voce (valgono per le domande inserite o modificate da ora in poi)</th></tr>";
                        foreach ( \IlSole\Rules\ruleLabels() as $Sezione => $Voci ) {
                            echo "<tr><th Class='Orangino' colspan='3'>".htmlspecialchars($Sezione)."</th></tr>";
                            foreach ( $Voci as $Chiave => $Etichetta ) {
                                echo "<tr><th colspan='2'>".htmlspecialchars($Etichetta)."</th>";
                                echo "<th><input type='number' min='0' step='1' name='Regola[$Chiave]' value='".$RegolePunteggio[$Chiave]."' required/></th></tr>";
                            }
                        }

                        /*———— input submit ————*/
                        echo "<tr><th class='submit' colspan='3'><input type='submit' name='Aggiorna' value='Aggiorna' /></th></tr>";

                        echo "</table>";
                        echo "</Div>";

                    /*————*/

                    /* Liberazione memoria utilizzata */
                        mysqli_free_result($RisultatoStallo);
                    /*————*/

                    /* Rilascio della connessione */
                        mysqli_close($connection);
                    /*————*/

                echo "</form>";

                /* Ricalcolo: un form a parte, non tocca la configurazione qui sopra */
                echo "<form action='Config.php' method='POST'>";
                echo \IlSole\Auth\csrfField();
                echo "<div class='centro'><table class='Set' border=1>";
                echo "<tr><th Class='LightBlue'>Ricalcola Graduatoria</th></tr>";
                echo "<tr><th Class='Orangino'>Ricalcola il punteggio di tutte le domande con i punti e gli indicatori ISEE salvati e con le et&agrave; di oggi</th></tr>";
                echo "<tr><th class='submit'><input type='submit' name='Ricalcola' value='Ricalcola' /></th></tr>";
                echo "</table></div>";
                echo "</form>";
            
            }

        ?>

    </body>
</html>