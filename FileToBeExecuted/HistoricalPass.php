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

            /*  */
                $selectStoricoTessere="SELECT * FROM StoricoTes;";
                $risultatoStoricoTessere=mysqli_query($connection,$selectStoricoTessere) OR DIE($connection);
            /*————*/

            /*  */
                echo "<div class='centro'>";
                    echo "<table border='4'>";
                        echo "<tr>";
                            echo "<th class='major' colspan='7'>Storico Tessere</th>";        
                        echo "</tr>";
                        echo "<tr>";
                            echo "<th class='LightBlue' colspan='7'>Ordine Assegnazione Tessere</th>";        
                        echo "</tr>";
                        echo "<tr>";
                            echo "<th class='Orangino'>Ordine</th><th class='Orangino'>Codice Tessera</th><th class='Orangino'>Codice Fiscale</th><th class='Orangino'>Nome</th><th class='Orangino'>Cognome</th><th class='Orangino'>Data Di Rilascio</th><th class='Orangino'>Data Di Ritiro</th>";        
                        echo "</tr>";
                            $i=0;
                            while ( $riga=mysqli_fetch_array($risultatoStoricoTessere,MYSQLI_ASSOC) ) {
                                $i++;
                                /*echo "<tr>";*/
                                if ( $i%2==0 ) {
                                    echo "<tr class='Darker'>";
                                } else {
                                    echo "<tr class='Lighter'>";
                                }
                                    echo "<td>".$riga['OrdineCronologico']."</td><td>".$riga['CodiceTessera']."</td><td>".$riga['CodFamiglia']."</td><td>".$riga['Nome']."</td><td>".$riga['Cognome']."</td><td>".$riga['DataRilascio']."</td>";
                                    if ( $riga['DataRitiro'] == "" ) {
                                        echo "<td>Ancora in possesso</td>";
                                    } else {
                                        echo "<td>".$riga['DataRitiro']."</td>";
                                    }
                                echo "</tr>";
                            }
                        echo "<tr>";
                            echo "<th class='submit' colspan='7'><input style='opacity: 0;' type='submit' /></th>";        
                        echo "</tr>";
                        echo "<tr>";
                            echo "<th Class='submit' colspan='7'><img style='width:100%;' src='../FileInclude/immagini/footer.png' /></th>";
                        echo "</tr>";
                    echo "</table>";
                echo "</div>";
            /*————*/

        ?>

        <a class='linkHist' href='HandlePass.php'>
            Tessere<br/>&#x21a9;
        </a>

    </body>
</html>