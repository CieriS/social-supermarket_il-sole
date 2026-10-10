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
                require('../FileInclude/FileRequired/Cookie.php');
                require('../FileInclude/FileRequired/Functions.php');
            /*————*/

            if($_POST){

                /* Ricezione Variabili */

                    $nMesiStallo=$_REQUEST['nMesiStallo'];

                    /* Gestione Credenziali */
                        if( $_REQUEST['Psw'] == $_REQUEST['ConfirmPsw'] ) {
                            if( $_REQUEST['Psw'] != "" && $_REQUEST['ConfirmPsw'] != "" ) {
                                $Username=strtolower($_REQUEST['Username']);
                                $Psw=md5($_REQUEST['Psw']);
                                
                                $UpdateCredenziali="UPDATE Administrator 
                                SET Username = '".$Username."', Password = '".$Psw."'; ";

                                $RisultatoCredenziali=mysqli_query($connection,$UpdateCredenziali) OR DIE(mysqli_error($connection));
                            }
                        } else {
                            ?>
                                <script>
                                    alert("Le password non corrispondono");
                                    window.location.replace("Config.php");
                                </script>
                            <?php
                        }
                    /*————*/

                    $ISEEmin=$_REQUEST['ISEEmin'];
                    $ISEEmax=$_REQUEST['ISEEmax'];
                    $Punti=$_REQUEST['Punteggio'];
                    $nComponenti=$_REQUEST['nComponenti'];
                    $ValoreEuro=$_REQUEST['ValoreEuro'];
                    $ValorePunti=$_REQUEST['ValorePunti'];
                /*————*/

                /* Esecuzione Update */

                    /* Update Mesi di Stallo */
                        $UpdateMesiStallo="UPDATE Stallo 
                        SET nMesiStallo = '".$nMesiStallo."';";

                        $RisultatoStallo=mysqli_query($connection,$UpdateMesiStallo) OR DIE(mysqli_error($connection));
                    /*————*/

                    /* Update IndicatoriISEE (ISEEmin, ISEEmax, Punteggio) */
                        for( $i=1;$i<=count($Punti);$i++ ) {
                            $UpdateIndISEE="UPDATE IndicatoriISEE 
                            SET ISEEmin = '".$ISEEmin[$i]."', ISEEmax = '".$ISEEmax[$i]."', Punteggio = '".$Punti[$i]."' 
                            WHERE N = ".$i.";";

                            $RisultatoIndISEE=mysqli_query($connection,$UpdateIndISEE);
                        }
                    /*————*/

                    /* Update PunteggioMensile (nComponenti, ValoreEuro, ValorePunti) */
                        for( $i=1;$i<=count($nComponenti);$i++ ) {             
                            $UpdatePntMen="UPDATE PunteggioMensile 
                            SET nComponenti = '".$nComponenti[$i]."', ValoreEuro = '".$ValoreEuro[$i]."', ValorePunti = '".$ValorePunti[$i]."' 
                            WHERE N = ".$i.";";

                            $RisultatoPntMen=mysqli_query($connection,$UpdatePntMen) OR DIE(mysqli_error($connection));
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

                    /* SELECT */

                        /*———— Mesi Di Stallo ————*/
                        $MesiStallo="SELECT * 
                        FROM Stallo";

                        $RisultatoStallo=mysqli_query($connection,$MesiStallo) OR DIE(mysqli_error($connection));

                        $nMesiStallo=mysqli_fetch_array($RisultatoStallo,MYSQLI_ASSOC);

                        /*———— Cambia Credenziali ————*/
                        $Credenziali="SELECT * 
                        FROM Administrator";

                        $RisultatoCredenziali=mysqli_query($connection,$Credenziali) OR DIE(mysqli_error($connection));

                        $UsPsw=mysqli_fetch_array($RisultatoCredenziali,MYSQLI_ASSOC);

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
                        echo "<tr><th Class='Orangino' colspan='3'>Nuovo Username e password (se l'username cambia deve essere cambiata anche la password)</th></tr>";

                        echo "<tr title='Se cambia l'username vanno inserite anche le password, altrimenti le credenziali non verranno aggiornate.'><th colspan='3'><input minlength='4' maxlength='15' type='text' name='Username' placeholder='Username' value='".$UsPsw['Username']."' required/></th></tr>
                        <tr title='Se cambia l'username vanno inserite anche le password, altrimenti le credenziali non verranno aggiornate.'><th colspan='3'><input minlength='4' maxlength='15' type='password' name='Psw' placeholder='Password' /><input minlength='4' maxlength='15' type='password' name='ConfirmPsw' placeholder='Conferma Password'/></th></tr>";

                        /*———— Indicatori ISEE ————*/
                        echo "<tr><th Class='LightBlue' colspan='3'>Indicatori ISEE</th></tr>";
                        echo "<tr><th Class='Orangino'>ISEE MIN</th><th Class='Orangino'>ISEE MAX</th><th Class='Orangino'>Punteggio</th></tr>";
                        $i=1;
                        while( $RigaIndISEE=mysqli_fetch_array($RisultatoindISEE,MYSQLI_ASSOC) ) {
                            echo "<tr><th><input type='number' name='ISEEmin[$i]' value='".$RigaIndISEE['ISEEmin']."' required/></th>";
                            echo "<th><input type='number' name='ISEEmax[$i]' value='".$RigaIndISEE['ISEEmax']."' required/></th>";
                            echo "<th><input type='number' name='Punteggio[$i]' value='".$RigaIndISEE['Punteggio']."' required/></th></tr>";
                            $i++;
                        }

                        /*———— Punteggio Mensile ————*/
                        echo "<tr><th Class='LightBlue' colspan='3'>Punteggio Mensile</th></tr>";
                        echo "<tr><th Class='Orangino'>Numero Componenti Famiglia</th><th Class='Orangino'>&euro;</th><th Class='Orangino'>Valore in Punti</th></tr>";
                        $i=1;
                        while( $RigaPntMensile=mysqli_fetch_array($RisultatoPntMensile,MYSQLI_ASSOC) ) {
                            echo "<tr><th><input type='number' name='nComponenti[$i]' value='".$RigaPntMensile['nComponenti']."' required/></th>";
                            echo "<th><input type='number' name='ValoreEuro[$i]' value='".$RigaPntMensile['ValoreEuro']."' required/></th>";
                            echo "<th><input type='number' name='ValorePunti[$i]' value='".$RigaPntMensile['ValorePunti']."' required/></th></tr>";
                            $i++;
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
            
            }

        ?>

    </body>
</html>