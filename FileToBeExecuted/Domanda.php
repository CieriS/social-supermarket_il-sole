<?php require __DIR__ . '/../src/bootstrap.php'; \IlSole\Auth\requireLogin(); ?>
<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../FileInclude/CascadingStyleSheet/PunteggioStyle.css" rel="stylesheet" type="text/css">
        <title> Modulo Richiedente </title>
        <link rel="icon" type="image/png" href="../FileInclude/immagini/icon.png">
        <script src="../FileInclude/Javascript/JavaScript.js"></script>
    </head>
    <body> <!---- <Body id="container"> ---->

        <Div>
            <?php
            

                /* File Required */
                    require('../FileInclude/FileRequired/Connessione.php');
                    require('../FileInclude/FileRequired/NavBars.php');
                /*————*/

                /* Lista Richiedenti */
                    $selectRichiedenti = "SELECT Cod_Fiscale, Nome, Cognome FROM ".\IlSole\Views\from('Graduatoria')." ORDER BY Cognome, Nome ASC;";

                    $risultatoRichiedenti = mysqli_query($connection,$selectRichiedenti) OR DIE(mysqli_error($connection));
                /*————*/

                if($_POST)
                            {

                                if ( !isset($_REQUEST['TipoDiDomanda']) ) {
                                    ?>
                                        <script>
                                            window.location.replace('Domanda.php');
                                        </script>
                                    <?php
                                    DIE();
                                }

                                $Domanda = \IlSole\Input\text($_POST['TipoDiDomanda']);
                                $nMembri = \IlSole\Input\wholeNumber($_POST['nComponentiNucleoFamiliare'] ?? '', 1, 22) ?? 1;
                                $CodiceFiscale = \IlSole\Input\taxCode($_POST['CodiceFiscale'] ?? '');

                                $_SESSION['Domanda'] = $Domanda;
                                $_SESSION['nMembri'] = $nMembri;
                                $_SESSION['CodiceFiscale'] = $CodiceFiscale;

                                if( $Domanda == 'Domanda Nuova' ) {
                                    ?>
                                        <script>
                                            window.location.replace('ModuloDomanda.php');
                                        </script>
                                    <?php
                                }

                                if( $Domanda == 'Domanda Di Modifica' || $Domanda == 'Domanda Reiterata' ) {
                                    ?>
                                        <script>
                                            window.location.replace('ModuloDomanda.php');
                                        </script>
                                    <?php
                                }

                            } else {

                                $action=$_SERVER['PHP_SELF'];
                                echo "<form action=" . $action . " method='POST' autocomplete='Off'>";
                                echo \IlSole\Auth\csrfField();
                                ?>
                                    <img ID="container" class="banner" src="../FileInclude/immagini/Intestazione.svg" />

                                    <h1>DOMANDA DI ACCESSO ALL'EMPORIO SOLIDALE</h1>

                                    <h3>Tipo Di Domanda e Membri</h3>

                                    <table Class="Anag Sep" border="0">
                                       
                                        <script>
                                            function NascondiRichiedente() {
                                                var TipoDomanda = document.getElementById("TipoDomanda").value;
                                                if ( TipoDomanda == "Domanda Reiterata" || TipoDomanda == "Domanda Di Modifica" ) {
                                                    document.getElementById("RichiedenteCF").style.display = 'contents';
                                                } else {
                                                    document.getElementById("RichiedenteCF").style.display = 'none';
                                                }
                                            }
                                        </script>

                                        <tr>
                                            <th Class="AlignLeft">
                                                <select onchange="NascondiRichiedente()" id='TipoDomanda' Class="DatiAnagrafici" name="TipoDiDomanda">
                                                    <optgroup label="Tipo di Domanda:">
                                                        <option value="" disabled selected></option>
                                                        <option value="Domanda Nuova">Domanda Nuova</option>
                                                        <option value="Domanda Di Modifica">Domanda Di Modifica</option>
                                                        <option value="Domanda Reiterata">Domanda Reiterata</option>
                                                    </optgroup>
                                                </select>
                                            <br/><div Class="Weighter">Tipo di Domanda (Domanda Nuova - Domanda di modifica - Domanda reiterata)</div></th>
                                        </tr>

                                        <tr id="RichiedenteCF">
                                            <th Class="AlignLeft">
                                                <select Class="DatiAnagrafici AlignLeft" name="CodiceFiscale">
                                                    <optgroup label='Richiedente'>
                                                    <option></option>
                                                        <?php
                                                            $i=0;
                                                            while( $riga=\IlSole\Html\escapeRow(mysqli_fetch_array($risultatoRichiedenti,MYSQLI_ASSOC)) ) {
                                                                $i++;
                                                                echo "<option value='".$riga['Cod_Fiscale']."'>". "$i: " .$riga['Cognome']. " " .$riga['Nome']. " -" .$riga['Cod_Fiscale']. "- " ."</option>";
                                                            }
                                                        ?>
                                                    </optgroup>
                                                </select>
                                            <br/><div Class="Weighter">Richiedente</div></th>
                                        </tr>

                                        <tr>
                                            <th Class="AlignLeft">
                                            <input Class="DatiAnagrafici AlignLeft" type="Number" min="1" max="22" name="nComponentiNucleoFamiliare" required/>
                                            <br/><div Class="Weighter">Numero Componenti Nucleo Familiare (Incluso Richiedente)</div></th>
                                        </tr>

                                    </table>

                                    <Div Class="Fin">

                                        <Div>

                                            <a href='https://cierisamuele.vercel.app' target="_blank" Class="insta"><!--<img class="instalogo" src="FileInclude/immagini/Logo_3.png" />--></a>

                                        </Div>

                                        <Div class="Copyright">

                                            <Div><span>Copyright &copy; <?php $AnnoCopyright=Date('Y'); echo "2002-".$AnnoCopyright; ?> Samuele Cieri Inc.<br/>Tutti i diritti riservati.</span></Div>

                                        </Div>

                                        <Div>
                                            <span><a href="https://cierisamuele.vercel.app" target="_blank"><img Class="Signature" src="../FileInclude/immagini/FirmaDigitale_2.png" /></a></span>
                                        </Div>

                                    </Div>

                                </form>
                                <?php

                            }
                        ?>

        </Div>

    </body>
</html>