<?php

      /* Connection settings: see src/Config.php */
      if( !isset($connection) ) {
            require_once __DIR__ . '/../../src/Config.php';

            $Host=\IlSole\Config\setting('DB_HOST', "localhost");
            $User=\IlSole\Config\setting('DB_USER', "root");
            $Password=\IlSole\Config\setting('DB_PASSWORD');
            $DataBaseName=\IlSole\Config\setting('DB_NAME', "EmporioSolidale");

            $connection=MYSQLI_CONNECT($Host,$User,$Password,$DataBaseName) OR DIE ("Error 104: " . MYSQLI_CONNECT_ERROR());
      }

?>
