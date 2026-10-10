<!doctype html>
<html lang="it" ondragstart='return false' onselectstart='return false'>
    <head>
        <title> SamueleCieri </title>
        <meta charset="UTF-8">
    </head>
    <body>

        <?php

            function Maggiorenne($DataNascita,$ContatoreMaggiorenni,$ContatoreMinorenni){
                $ContatoreMaggiorenni=1; /* Parte da uno perchè il richiedente è sicuramente maggiorenne */
                $ContatoreMinorenni=0;
                $c=DATE('d-m-y');

                $cD=DATE('d');
                $cM=DATE('m');
                $cY=DATE('Y');

                $nD=date('d', strtotime($DataNascita));
                $nM=date('m', strtotime($DataNascita));
                $nY=date('Y', strtotime($DataNascita));

                if( ($cY-$nY) >= 18 ){
                    if( ($cY-$nY) > 18 ){
                        $ContatoreMaggiorenni++;
                    }
                    if( ($cY-$nY) == 18 ){
                        if( $nM <= $cM ){
                            if( $nM < $cM ){
                                $ContatoreMaggiorenni++;
                            }
                            if( $nM == $cM ){
                                if( $nD <= $cD ){
                                    $ContatoreMaggiorenni++;
                                }
                                else
                                {
                                    $ContatoreMinorenni++;
                                }
                            }
                        }
                        else
                        {
                            $ContatoreMinorenni++;
                        }
                    }
                }
                else
                {
                    $ContatoreMinorenni++;
                }

            }

            /*
            $ora=Date('H');

            if($ora >= 22 || $ora <= 8 ){
                ?>
                    <script>

                        var div = document.getElementById("container");
                        div.className += " night";

                    </script>

                <?php
            } */

        
        ?>

    </body>
</html>