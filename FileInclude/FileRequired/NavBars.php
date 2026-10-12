<?php /* Header with the menu and footer, shared by the pages behind the login */ ?>
        <Div ID="Header" Class="Header">

            <Div>

                <a href="https://cierisamuele.vercel.app" target="_blank"><img Class="Logo" src="../FileInclude/immagini/Logo_4.png" /></a>

            </Div>

            <Div style="text-align: center;font-family:Brush Script MT, Brush Script Std, cursive;font-size: 4vw;-webkit-text-stroke: 0.4444px black;color: orange;text-shadow: -4px 0 orangered, -4px 0 orangered, -4px 0 orangered, -4px 0 orangered;" Class="Title">

                <span>Il Sole</span>

            </Div>

            <Div id="Heade" Class="Search">

                <input type="text" title="La parola inserita manterr&agrave; nella tabella solo i caratteri inseriti che corrispondono ad una fra le colonne 2&deg;, 3&deg; e 4&deg;" id="InputDaCercare" onkeyup="searchTable('InputDaCercare', 'TabellaDati' , '[3,4,5]')" placeholder="Cerca..." maxlength='16' />

            </Div>

            <!----
            <Div>

                <span style="width:100%;text-align: center;margin: 0 auto;"><input type="search" /></span>

            </Div>
            ---->

            <!--<div class="dropdown">
                <button onclick="DropDown()" class="dropbtn">
                    <Div class="Ham">
                    </Div>
                    <Div class="Ham">
                    </Div>
                    <Div class="Ham">
                    </Div>
                    <!--<img class='hamburger' src="../FileInclude/immagini/Hamburger.png" />-->
                <!--</button>
                <div id="myDropdown" class="dropdown-content">

                    <a href="List.php">&#128203; Graduatoria &#128203;</a>

                    <a href="Punteggio.php">Nuova<br/>&#128271; Richiesta &#128271;</a>

                    <a href="InformazioniRichiedente.php"><span style='color: blue; border: 1px ridge blue; border-radius: 100%; padding: 0 0.4vw;'>&#8505;</span> Informazioni <span style='color: blue; border: 1px ridge blue; border-radius: 100%; padding: 0 0.4vw;'>&#8505;</span></a>

                    <a href="SkillsList.php"><span>&#128160;</span> Competenze <span>&#128160;</span></a>

                    <a href="Pass.php">Assegna<br/>&#128994; Tessera &#128994;</a>

                    <a href="PassRetire.php">Ritira<br/>&#128992; Tessera &#128992;</a>

                    <a href="HandlePass.php">Gestione<br/>&#128179; Tessere &#128179;</a>

                    <a href="SendMail.php">E-Mail</a>

                    <a href="StrikeOut.php"><span style='color:red;'>&#10008;</span> Elimina <span style='color:red;'>&#10008;</span></a>

                    <a href="Grad_Export.php">Esporta<br/><span style='color: navy'>&#x2965;</span> Graduatoria <span style='color: navy'>&#x2965;</span><br/>(Excel)</a>

                    <a href="Config.php">&#9881;&#65039; Configurazione &#9881;&#65039;</a>

                    <!--<a href="Credits.php">Credits</a>-->
                    <!--<a href="../Index.php">&#128682; Esci &#128682;</a>
                    
                </div>
            </div>-->


            <div class="menu">
                <ul>
                    <li>
                        <span class='Hamburger'>&#x2630;</span>
                        <ul>
                            <li class="link">
                                <a href="List.php">Graduatoria</a>
                            </li>

                            <li class="link">
                                <a href="Domanda.php">Domanda</a>
                            </li>
                            
                            <li>
                            Tessere
                                <ul>
                                    <li class="link">
                                        <a href="Pass.php">Assegna</a>
                                    </li>
                                    <li class="link">
                                        <a href="PassRetire.php">Ritira</a>
                                    </li>
                                    <li class="link">
                                        <a href="HandlePass.php">Gestisci</a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                            Richiedente
                                <ul>
                                    <li class="link">
                                        <a href="InformazioniRichiedente.php">informazioni</a>
                                    </li>
                                    <li class="link">
                                        <a href="SkillsList.php">Competenze</a>
                                    </li>
                                    <li class="link">
                                        <a href="StrikeOut.php">Elimina</a>
                                    </li>
                                </ul>
                            </li>


                            <li>
                            Extra
                                <ul>
                                    <li class="link">
                                        <a href="SendMail.php">invia mail<span style='color: lightgreen;padding-left: 0.5vw;'>&#x2963;</span></a>
                                    </li>
                                    <li class="link">
                                        <a href="Grad_Export.php">Esporta <span style='color: cyan;padding-left: 0.5vw;'>&#x2965;</span></a>
                                    </li>
                                    <li class="link">
                                        <a href="Config.php">Config.</a>
                                    </li>
                                </ul>
                                <li class="link">
                                    <a href="../index.php?esci=1">Esci</a>
                                </li>
                            </li>

                        </ul>
                    </li>
                </ul>
            </div>

        </Div>

        <Div Class="Footer">

            <Div>
                <span><a href="https://cierisamuele.vercel.app" target="_blank"><img Class="Signature" src="../FileInclude/immagini/FirmaDigitale_2.png" /></a></span>
            </Div>

        </Div>
