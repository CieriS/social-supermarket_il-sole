window.onload = function(){
    printTime();
};
        
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