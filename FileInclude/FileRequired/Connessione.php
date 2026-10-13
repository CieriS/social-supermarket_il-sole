<?php

      /* Connection settings: see src/Config.php */
      if( !isset($connection) ) {
            require_once __DIR__ . '/../../src/Config.php';

            $Host=\IlSole\Config\setting('DB_HOST', "localhost");
            $User=\IlSole\Config\setting('DB_USER', "root");
            $Password=\IlSole\Config\setting('DB_PASSWORD');
            $DataBaseName=\IlSole\Config\setting('DB_NAME', "EmporioSolidale");

            try {
                  $connection=mysqli_connect($Host,$User,$Password,$DataBaseName);
            } catch ( mysqli_sql_exception $Errore ) {
                  $connection=false;
            }

            /* Without a database nothing works: say so, and what to look at, instead of an empty page.
               No setting is printed, only whether it is there. */
            if( $connection === false ) {
                  http_response_code(503);
                  header('Content-Type: text/plain; charset=UTF-8');
                  echo "Il Sole: impossibile collegarsi al database (errore MySQL ".mysqli_connect_errno().").\n\n";
                  echo "- config.php accanto a index.php: ".( is_file(__DIR__ . '/../../config.php') ? "presente" : "MANCANTE" )."\n";
                  echo "- password del database impostata: ".( $Password !== '' ? "si" : "NO" )."\n";
                  echo "- 1045 = utente o password sbagliati; 1044 o 1049 = nome del database sbagliato; 2002 = host sbagliato\n";
                  exit;
            }
      }

?>
