<?php

    /* Login: everything that reads the request happens here, before any output */

    require __DIR__ . '/src/bootstrap.php';

    use function IlSole\Html\e;

    if ( isset($_GET['esci']) ) {
        \IlSole\Auth\logOut();
        header('Location: index.php');
        exit;
    }

    /* Demo: whoever opens the login page finds the data as it was, at most once per interval */
    if ( \IlSole\Config\demoMode() && $_SERVER['REQUEST_METHOD'] === 'GET' ) {
        require __DIR__ . '/src/Demo.php';
        $Minuti = max(1, (int) \IlSole\Config\setting('DEMO_RESET_MINUTES', '60'));
        try {
            \IlSole\Demo\resetIfDue($connection, __DIR__ . '/db', time(), $Minuti * 60);
        } catch ( Throwable $ErroreRipristino ) {
            /* The demo stays usable with the data it has; the reason is shown under the form */
            $AvvisoRipristino = "Ripristino dei dati dimostrativi non riuscito: ".$ErroreRipristino->getMessage();
        }
    }

    $Errore = null;
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        $Indirizzo = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        if ( !\IlSole\Auth\csrfMatches($_SESSION, $_POST['csrf'] ?? null) ) {
            /* The page was opened before the session it belongs to ended (a long wait, a
               restart of the server): the form below carries the token of the new one */
            $Errore = "La pagina era scaduta: inserire di nuovo username e password";
        } elseif ( \IlSole\Auth\tooManyFailures($connection, $Indirizzo, time()) ) {
            $Errore = "Troppi tentativi sbagliati: riprovare tra qualche minuto";
        } elseif ( \IlSole\Auth\logIn($connection, (string) ($_POST['username'] ?? ''), (string) ($_POST['psw'] ?? '')) ) {
            \IlSole\Auth\clearFailures($connection, $Indirizzo);
            header('Location: FileToBeExecuted/List.php');
            exit;
        } else {
            \IlSole\Auth\recordFailure($connection, $Indirizzo, time());
            $Errore = "Username o password sbagliati";
        }
    }

    /* The demo account, "username / password": when set, a button under the form fills it in */
    $Demo = array_map('trim', explode('/', \IlSole\Config\setting('DEMO_LOGIN_HINT'), 2));
    $Demo = count($Demo) === 2 && $Demo[0] !== '' && $Demo[1] !== '' ? $Demo : null;

?>
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

        <form action="index.php" method="POST" autocomplete="OFF">

            <?php echo \IlSole\Auth\csrfField(); ?>

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
                                            <input type="text" name="username" placeholder="Username" title="Inserisci username e password in qualità di amministratore" maxlength="15" autocomplete="username" required/>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th Class="Credenziali">   
                                            <input type="password" name="psw" placeholder="Password" maxlength="72" title="Inserisci Password" id="myInput" autocomplete="current-password" required/>
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


            <?php if ( isset($AvvisoRipristino) ) { ?>
                <p Class="Avviso Errore" style="bottom: 14vh;"><?php echo e($AvvisoRipristino); ?></p>
            <?php } ?>
            <?php if ( $Errore !== null ) { ?>
                <p Class="Avviso Errore"><?php echo e($Errore); ?></p>
            <?php } ?>
            <?php if ( $Demo !== null ) { ?>
                <p Class="Avviso">
                    Account dimostrativo: <code><?php echo e($Demo[0]); ?></code> / <code><?php echo e($Demo[1]); ?></code>
                    <button type="button" id="UsaDemo" data-username="<?php echo e($Demo[0]); ?>" data-password="<?php echo e($Demo[1]); ?>">Compila</button>
                </p>
                <script>
                    document.getElementById('UsaDemo').addEventListener('click', function () {
                        document.querySelector('input[name="username"]').value = this.dataset.username;
                        document.querySelector('input[name="psw"]').value = this.dataset.password;
                    });
                </script>
            <?php } ?>

        </form>

    </body>
</html>
