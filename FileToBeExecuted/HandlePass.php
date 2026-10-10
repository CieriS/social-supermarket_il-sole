<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/SettingStyle.css" media="" rel="stylesheet" type="text/css">
        <title> Gestione Tessere </title>
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

                    if ( $_REQUEST['Codice'] == "" && !(isset($_REQUEST['Elimina'])) ) {
                        ?>
                        <script>
                            alert("Non sono stati immessi dati");
                            window.location.replace("HandlePass.php");
                        </script>
                        <?php  
                        DIE();
                    } else {
                        if ( $_REQUEST['Codice'] != "" ) {
                            $Codice=STRTOUPPER($_REQUEST['Codice']);

                            $Tessere="SELECT t.Codice, tr.CodFamiglia FROM tessera as t left join tesserarilasciata as tr on t.codice=tr.codicetessera ORDER BY Codice ASC;";
                            $risultatoTessera=mysqli_query($connection,$Tessere) OR DIE();

                            while ( $riga=mysqli_fetch_array($risultatoTessera,MYSQLI_ASSOC) ) {
                                if ( $Codice == $riga['Codice'] ) {
                                    ?>
                                        <script>
                                            alert("La tessera è già stata inserita in precedenza");
                                            window.location.replace("HandlePass.php");
                                        </script>
                                    <?php 
                                    DIE(); 
                                }
                            }

                            $insertCodice="INSERT INTO Tessera 
                            VALUES ('".$Codice."');";

                            $Risultato=mysqli_query($connection,$insertCodice) OR DIE(mysqli_error($connection));

                            ?>
                                <script>
                                    alert("Tessera (<?php echo $Codice; ?>) inserita");
                                    window.location.replace("HandlePass.php");
                                </script>
                            <?php
                        }

                        if ( isset($_REQUEST['Elimina']) ) {
                            $Delete=$_REQUEST['Elimina'];

                            $deleteTessera="DELETE FROM Tessera WHERE Codice LIKE '".$Delete."';";

                            $Risultato=mysqli_query($connection,$deleteTessera) OR DIE(mysqli_error($connection));

                            ?>
                                <script>
                                    alert("Eliminata la tessera '<?php echo $Delete; ?>'");
                                    window.location.replace("HandlePass.php");
                                </script>
                            <?php
                        }
                    }
                }
                else {

                    $action=$_SERVER['PHP_SELF'];
                    echo "<form action=" . $action . " method='POST' autocomplete='Off'>";
                    ?>

                        <div class="centro">
                            <table border="4" class="TableInserimentoTessera">
                                <tr>
                                    <th colspan="4" class="major">
                                        Gestione Tessere
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="LightBlue">
                                        Nuova Tessera
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="Orangino">
                                        Inserisci Tessera
                                    </th>
                                </tr>
                                <tr>
                                    <th class='newPass' colspan="4">
                                        <input minlength="12" maxlength="12" type="text" name="Codice" placeholder="Codice"/>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="LightBlue">
                                        Elimina Tessera
                                    </th>
                                </tr>
                                <tr>
                                    <th Class='Orangino' COLSPAN='2'>
                                        &nbsp;
                                    </th>
                                    <th Class='Orangino'>
                                        Codice Tessera
                                    </th>
                                    <th Class='Orangino'>
                                        Codice Fiscale
                                    </th>
                                </tr>

                                <?php
                                    $Tessere="SELECT t.Codice, tr.CodFamiglia FROM tessera as t left join tesserarilasciata as tr on t.codice=tr.codicetessera ORDER BY Codice ASC;";
                                    $risultatoTessera=mysqli_query($connection,$Tessere) OR DIE();
                                    $i=0;
                                    while ( $riga=mysqli_fetch_array($risultatoTessera,MYSQLI_ASSOC) ) {
                                        $i++;
                                        /*echo "<tr>";*/
                                        if ( $i%2==0 ) {
                                            echo "<tr class='Darker'>";
                                        } else {
                                            echo "<tr class='Lighter'>";
                                        }
                                        echo "<th>$i</th>";
                                        echo "<th><input type='radio' name='Elimina' value='".$riga['Codice']."'></th>";
                                        echo "<th>".$riga['Codice']."</th>";
                                        echo "<th>".$riga['CodFamiglia']."</th>";
                                        echo "</tr>";
                                    }
                                ?>

                                <tr>
                                    <th colspan="4" class="submit">
                                        <input type="submit" value="Esegui" required/>
                                    </th>
                                </tr>
                                <tr>
                                    <th Class='submit' colspan="4">
                                        <img style='width:100%;' src='../FileInclude/immagini/footer.png' />
                                    </th>
                                </tr>
                            </table>
                        </div>

                        <a class='linkHist' href='HistoricalPass.php'>
                            Storico<br/>&#x21aa;
                        </a>

                    </form>
                    <?php

                }
            /*————*/

        ?>

    </body>
</html>