<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="FileInclude/CascadingStyleSheet/LoginStyle.css" rel="stylesheet" type="text/css">
        <title> Login </title>
        <link rel="icon" type="image/png" href="FileInclude/immagini/icon.png">
        <script src="FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body>

        <?php
            
                if($_POST) /* Metodo Postback */ {

                    /* Inclusione del file per connettersi al DBMS */
                        require('FileInclude/FileRequired/Connessione.php');
                    /*————*/

                    /* Ricevo username e password */
                        $username=strtolower($_REQUEST['username']);
                        $password=md5($_REQUEST['psw']);
                    /*————*/

                    /* Select */
                        $query="SELECT * FROM Administrator WHERE Username='".$username."';";
                    /*————*/

                    /* Esecuzione query */
                        $risultato=mysqli_query($connection,$query) or die("Fatal Error" .mysqli_error($connection));
                    /*————*/

                    /* Array associativo */
                        $valore=mysqli_fetch_array($risultato,MYSQLI_ASSOC);
                    /*————*/

                    /* Numero di righe */
                        $righe=mysqli_num_rows($risultato);
                    /*————*/

                    /* Se il numero di righe risultante dalla select è 0, significa che l'username non esiste, quindi non ti permette di accedere e ti riporta all'inizio di questa pagina */
                        if($righe == 0){
                            ?>
                                <script>
                                    alert("Username inesistente!\nRiprova, potresti aver sbagliato...");
                                    window.location.replace("index.php");
                                </script>
                            <?php
                        }
                        else
                        if( $password == $valore['Password']  && $username == $valore['Username']) {

                            $hash = MD5(rand(100000000,999999999));

                            setcookie( 'CookieUser', $hash, strtotime("+30 minute") );

                            $updateCookie="UPDATE Administrator SET Cookie = '".$hash."' WHERE Username = '".$username."';";

                            $risultatoUpdateCookie=mysqli_query($connection,$updateCookie) or die(mysqli_error($connection));

                                ?>
                                    <script>
                                        window.location.replace("FileToBeExecuted/List.php");
                                    </script>
                                <?php
                                DIE();

                            /*
                                ?>

                                    <table Class="WindowLogin">

                                        <tr>
                                            <th><a Class="Link" href="List.php">Visualizza Graduatoria</a></th>
                                        </tr>

                                        <tr>
                                            <th><a Class="Link" href="Pass.php">Assegna Tessera</a></th>
                                        </tr>

                                        <tr>
                                            <th><a Class="Link" href="Pass.php">Ritira Tessera</a></th>
                                        </tr>

                                        <tr>
                                            <th><a Class="Link" href="Punteggio.php">Nuova Richiesta</a></th>
                                        </tr>

                                        <tr>
                                            <th><a Class="Link" href="StrikeOut.php">Elimina Richiedente</a></th>
                                        </tr>

                                    </table>

                                <?php
                            */
                        }
                        else{
                            ?>
                            <script>
                                alert("Ops...!\nPassword sbagliata =·(");
                                window.location.replace("index.php");
                            </script>
                            <?php
                        }
                    /*————*/

                    /* Libera la memoria utilizzata */
                        mysqli_free_result($risultato);
                    /*————*/

                    /* Rilascio della connessione */
                        mysqli_close($connection);
                    /*————*/

                }
                else{
                    /* Finestra Login */
                        $action=$_SERVER['PHP_SELF'];
                        echo "<form action=" . $action . " method='POST' autocomplete='OFF'>";
                        ?>
                        
                            <canvas id="cnv"></canvas>
                            <script src="FileInclude/Javascript/JavaScriptChemistry.js"></script>
                            <script>
                                function printTime() {
                                    var d = new Date();
                                    var hours = d.getHours();
                                    var mins = d.getMinutes();
                                    var secs = d.getSeconds();
                                    var day = d.getDay();
                                    var date = d.getDate();
                                    var month = d.getMonth();
                                    var year = d.getFullYear();
                                            
                                    switch ( day ) { 
                                        case 0:
                                            day = "Domenica";
                                            break;
                                        case 1:
                                            day = "Luned&igrave;";
                                            break;
                                        case 2:
                                            day = "Marted&igrave;";
                                            break;
                                        case 3:
                                            day = "Mercoled&igrave;";
                                            break;
                                        case 4:
                                            day = "Gioved&igrave;";
                                            break;
                                        case 5:
                                            day = "Venerd&igrave;";
                                            break;
                                        case 6:
                                            day = "Sabato";
                                            break;
                                    }

                                    if ( hours < 10 ) {
                                        hours = "0" + hours;
                                    }
                                    if ( mins < 10 ) {
                                        mins = "0" + mins;
                                    }
                                    if ( secs < 10 ) { 
                                        secs = "0" + secs;
                                    }
                                    if ( date < 10 ) { 
                                        date = "0" + date;
                                    }
                                    if ( month < 10 ) { 
                                        month = "0" + (month+1);
                                    }
                                    
                                    document.getElementById("test").innerHTML = hours+":"+mins+":"+secs;
                                    document.getElementById("ttt").innerHTML = day + ", " + date + "/" + month + "/" + year;
                                }
                                setInterval(printTime, 1000);
                            </script>

                            <div id='all'>
                                <div id="test">Samu</div>
                                <div id="ttt"></div>
                            </div>

                            <Div Class="increase">

                                <table Class="WindowLogin">
                                
                                    <tr>
                                        <th Class="IntesTable">
                                            <h1>Accesso</h1>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th Class="Credenziali">
                                            <input type="text" name="username" placeholder="Username" title="Inserisci username e password in qualità di amministratore" maxlength="15" value="EmporioSolidale" required/>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th Class="Credenziali">   
                                            <input type="password" name="psw" placeholder="Password" maxlength="15" title="Inserisci Password" id="myInput" value="IlSole" required/>
                                        </th>
                                    </tr>
                                        <th Class="accedi">
                                            <input type="submit" name="accedi" value="Entra"/>
                                        </th>
                                    </tr>


                                </table>

                                <Div>
                                    <a href='https://cierisamuele.vercel.app' target="_blank" Class="insta fa fa-instagram"></a>
                                </Div>

                                <!--    <Div>
                                    <img Class="Sign" src="FileInclude/immagini/FirmaDigitale_2.png" />
                                </Div>    -->

                                <Div>
                                    <a href='https://prezi.com/view/CxUIXDLdNGksqDvTeW9X/' target="_blank">
                                        <img Class="Sign" src="FileInclude/immagini/FirmaDigitale_2inv.png" />
                                    </a>
                                </Div>

                            </Div>

                        </form>
                        <?php
                    /*————*/
                }

        ?>

    </body>
</html>