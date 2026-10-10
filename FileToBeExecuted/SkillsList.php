<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/SkillStyle.css" media="" rel="stylesheet" type="text/css">
        <title> Ricerca Competenze </title>
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


            /* DB */

                /* View Competenze */
                    $queryCompetenze="SELECT * FROM CompetenzeView";
                    /* SELECT nMembro, NomeCognome, LuogoNascita, DATE_FORMAT(DataNascita, '%d/%m/%Y') AS DataNascita, Parentela, Occupazione, TitoloDiStudio, Competenze, ConoscenzaLingua, Patente, AttualeAtt, Presso, Termine, SenzaLavoroDa, CodFamiglia 
                    FROM Membri 
                    ORDER BY CodFamiglia, nMembro ASC; */
                /*————*/
                
                /* Esecuzione della select delle competenze */
                    $risultatoCompetenze=mysqli_query($connection,$queryCompetenze) OR DIE(mysqli_error($connection));
                /*————*/
                
                /* Numero di righe query competenze */
                    $righeCompletenze=mysqli_num_rows($risultatoCompetenze);
                /*————*/
        
                /*————*/

            /* Table competenze */
                echo "<div class='centro'>";

                    ?>

                        <table>

                            <tr>
                                <th class='ViolettoAlto' colspan='14'>

                                    <!--
                                    <input type='text' Class='CercaTable' placeholder='Famiglia/Membro' Id="InputPersFam" onkeyup="searchTable('InputPersFam', 'TableDati' , '[1,2]')" /> 
                                    -->

                                    <input type='text' Class='CercaTable' placeholder='Cerca Competenza...'  id="InputSkills" onkeyup="searchTable('InputSkills', 'TableDati' , '[8]')" /> 

                                </th>
                            </tr>
                            
                        </table>

                    <?php

                    echo "<table id='TableDati' border='0'>";

                        echo "<tr>";
                            echo "<th class='major' colspan='15'>";
                                echo "<div id='all'>";
                                    echo "<div id='test'>loading</div>";
                                echo "</div>";
                                    echo "Ricerca Competenze";
                                echo "<div id='all'>";
                                    echo "<div id='ttt'></div>";
                                echo "</div>";
                            echo "<th>";
                        echo "</tr>";

                        echo "<tr>";
                            echo "<th class='LightBlue'>CodFamiglia</th>";
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

                        $i=0;
                        while ( $riga=mysqli_fetch_array($risultatoCompetenze,MYSQLI_ASSOC) ) {
                            $i++;
                            /*echo "<tr>";*/
                            if ( $i%2==0 ) {
                                echo "<tr class='Darker'>";
                            } else {
                                echo "<tr class='Lighter'>";
                            }
                                echo "<td>".$riga['CodFamiglia']."</td>";
                                echo "<td>".$riga['NomeCognome']."</td>";
                                echo "<td>".$riga['LuogoNascita']."</td>";
                                echo "<td>".$riga['DataNascita']."</td>";
                                echo "<td>".$riga['Parentela']."</td>";

                                if ( $riga['Occupazione'] != "" ) {
                                    echo "<td>".$riga['Occupazione']."</td>";
                                } else {
                                    echo "<td> - </td>";
                                }

                                echo "<td>".$riga['TitoloDiStudio']."</td>";

                                if ( $riga['Competenze'] != "" ) {
                                    echo "<td>".$riga['Competenze']."</td>";
                                } else {
                                    echo "<td> - </td>";
                                }

                                if ( $riga['ConoscenzaLingua'] != "" ) {
                                    echo "<td>".$riga['ConoscenzaLingua']."</td>";
                                } else {
                                    echo "<td> - </td>";
                                }

                                if ( $riga['Patente'] != "" ) {
                                    echo "<td>".$riga['Patente']."</td>";
                                } else {
                                    echo "<td> - </td>";
                                }

                                if ( $riga['AttualeAtt'] != "" ) {
                                    echo "<td>".$riga['AttualeAtt']."</td>";
                                } else {
                                    echo "<td> - </td>";
                                }

                                if ( $riga['Presso'] != "" ) {
                                    echo "<td>".$riga['Presso']."</td>";
                                } else {
                                    echo "<td> - </td>";
                                }

                                if ( $riga['Termine'] != "" ) {
                                    echo "<td>".$riga['Termine']."</td>";
                                } else {
                                    echo "<td> - </td>";
                                }

                                if ( $riga['SenzaLavoroDa'] != "" ) {
                                    echo "<td>".$riga['SenzaLavoroDa']."</td>";
                                } else {
                                    echo "<td> - </td>";
                                }

                            echo "</tr>";
                        }

                        echo "<tr>";
                            echo "<th class='submit' colspan='14'> <input type='submit' style='opacity:0;'/> </th>";
                        echo "</tr>";

                        echo "<tr>";
                            echo "<th class='submit' colspan='14'> <img src='../FileInclude/immagini/Footer.png' width='100%' /> </th>";
                        echo "</tr>";

                    echo "</table>";
                    
            /*————*/

            /* Liberazione memoria utilizzata */
                mysqli_free_result($risultatoCompetenze);
            /*————*/

            /* Rilascio della connessione */
                mysqli_close($connection);
            /*————*/

        ?>

        <table>

        <tr>
            <th class='ViolettoBasso' colspan='14'>

                <!--
                <input type='text' Class='CercaTable' placeholder='Famiglia/Membro' Id="InputPersFam" onkeyup="searchTable('InputPersFam', 'TableDati' , '[1,2]')" /> 
                -->

                <input style='opacity: 0;'/> 

            </th>
        </tr>

        </table>

                    </div>

    </body>
</html>