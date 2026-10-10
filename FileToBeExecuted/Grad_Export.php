<?php
    $Host="localhost";
    $User="root";
    $Password="";
    $DataBaseName="EmporioSolidale";

    $connection=MYSQLI_CONNECT($Host,$User,$Password,$DataBaseName) OR DIE ("Error 104: " . MYSQLI_CONNECT_ERROR($connection));


    $query="SELECT * FROM Export;";
    /* SELECT R.Cod_Fiscale, R.Cognome, R.Nome, R.Cittadinanza, R.LuogoNascita, R.DataNascita, R.TipoAlloggio, R.Residenza, R.Via, R.nMembri, R.Telefono, R.Mail, P.PunteggioISEE, P.PunteggioPresenzaMinori, P.PunteggioDisoccupazione, P.PunteggioInvalidi, P.PunteggioSituazioneDebitoria, P.PunteggioBenefit, P.PunteggioTotale, TR.CodiceTessera, DATE_FORMAT(TR.DataOraRilascioTessera, '%d/%m/%Y') AS DataOraRilascioTessera, DATE_FORMAT(TR.DataOraScadenzaTessera, '%d/%m/%Y') AS DataOraScadenzaTessera, DATE_FORMAT(R.DataOraRitiroTessera, '%d/%m/%Y') AS DataOraRitiroTessera, DATE_FORMAT(R.DataOraStalloTessera, '%d/%m/%Y') AS DataOraStalloTessera 
    FROM Richiedente AS R LEFT OUTER JOIN TesseraRilasciata AS TR 
    ON R.Cod_Fiscale = TR.CodFamiglia 
    LEFT OUTER JOIN  Tessera AS T 
    ON TR.CodiceTessera = T.Codice 
    INNER JOIN Punteggio AS P 
    ON R.Cod_Fiscale = P.CodFamiglia  
    ORDER BY P.PunteggioTotale DESC; */


    /* Esecuzione della select */
        $risultato=mysqli_query($connection,$query) OR DIE(mysqli_error($connection));
    /*————*/

    /* Numero di righe */
        $righe=mysqli_num_rows($risultato);
    /*————*/

    if( $righe == 0 ) {

        ?>
            <script>
                alert("Non Esiste alcun dato da esportare su MicrosoftExcel");
                window.location.replace("List.php");
            </script>
        <?php
        DIE();

    }



    // Filter Data
    function filterData(&$str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

    // File Name & Content Header For Download
    $GiornoSettimana=DATE('D');
    $Mese=DATE('M');
    switch ($GiornoSettimana) {
        case "Mon":
            $GiornoSettimana = "Lun";
        break;
        case "Tue":
            $GiornoSettimana = "Mar";
        break;
        case "Wed":
            $GiornoSettimana = "Mer";
        break;
        case "Thu":
            $GiornoSettimana = "Gio";
        break;
        case "Fri":
            $GiornoSettimana = "Ven";
        break;
        case "Sat":
            $GiornoSettimana = "Sab";
        break;
        case "Sun":
            $GiornoSettimana = "Dom";
        break;
    }
    switch ($Mese) {
        case "Jan":
            $Mese = "Gen";
        break;
        case "Feb":
            $Mese = "Feb";
        break;
        case "Mar":
            $Mese = "Mar";
        break;
        case "Apr":
            $Mese = "Apr";
        break;
        case "May":
            $Mese = "Mag";
        break;
        case "Jun":
            $Mese = "Giu";
        break;
        case "Jul":
            $Mese = "Lug";
        break;
        case "Aug":
            $Mese = "Ago";
        break;
        case "Sep":
            $Mese = "Set";
        break;
        case "Oct":
            $Mese = "Ott";
        break;
        case "Nov":
            $Mese = "Nov";
        break;
        case "Dec":
            $Mese = "Dic";
        break;
    }
    $Oggi=$GiornoSettimana . DATE('d') . $Mese . DATE('Y_H\hi\ms\s');
    $file_name = "Graduatoria_$Oggi.xls";
    header("Content-Disposition: attachment; filename=\"$file_name\"");
    header("Content-Type: application/vnd.ms-excel");

    //To define column name in first row.
    $column_names = false;
    // run loop through each row in $risultato
    foreach ($risultato as $row) {
        if (!$column_names) {
            echo implode("\t", array_keys($row)) . "\n";
            $column_names = true;
        }
        // The array_walk() function runs each array element in a user-defined function.
        array_walk($row, 'filterData');
        echo implode("\t", array_values($row)) . "\n";
    }
    exit;
?>
    <script>
        alert("File Esportato");
        window.location.replace("List.php");
    </script>
<?php
DIE();