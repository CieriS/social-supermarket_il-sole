<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
  <head>
    <title> SamueleCieri </title>
  </head>
  <body>

    <?php
      
      require("../FileInclude/FileRequired/Connessione.php");

      $selectCookie = "SELECT Cookie FROM Administrator;";

      $risultatoSelectCookie = mysqli_query($connection, $selectCookie);

      while( $riga=mysqli_fetch_array($risultatoSelectCookie,MYSQLI_ASSOC) ) {
        $CookieDB = $riga['Cookie'];
      }

      if ( isset($_COOKIE['CookieUser']) ) {
        $ActualCookie = $_COOKIE['CookieUser'];

        if ( $ActualCookie != $CookieDB ) {
          ?>
            <script>
              window.location.replace('../index.php');
            </script>
          <?php
          DIE();
        }
      } else {
        ?>
          <script>
            window.location.replace('../index.php');
          </script>
        <?php
        DIE();
      }

    ?>

  </body>
</html>