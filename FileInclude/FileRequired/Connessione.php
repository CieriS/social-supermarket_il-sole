<?php

      /* Connection settings come from the environment (see docker-compose.yml) */
      if( !isset($connection) ) {
            $Host=getenv('DB_HOST') ?: "localhost";
            $User=getenv('DB_USER') ?: "root";
            $Password=getenv('DB_PASSWORD') ?: "";
            $DataBaseName=getenv('DB_NAME') ?: "EmporioSolidale";

            $connection=MYSQLI_CONNECT($Host,$User,$Password,$DataBaseName) OR DIE ("Error 104: " . MYSQLI_CONNECT_ERROR());
      }

?>
