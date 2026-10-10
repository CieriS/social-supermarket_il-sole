function DropDown() {
    document.getElementById("myDropdown").classList.toggle("show");
}

/*function ConvertDate() {
    var date = new Date(document.getElementById("myDate").value)
}*/

// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {

        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

function blocco_mousedx() {
    return(false); 
}
document.oncontextmenu = blocco_mousedx;

document.onkeydown = function(blocco_tasti){
    if(event.keyCode == 123) { 
        return false; 
    }
    if(blocco_tasti.ctrlKey && blocco_tasti.shiftKey && blocco_tasti.keyCode == 'I'.charCodeAt(0)) { 
        return false; 
    }
    if(blocco_tasti.ctrlKey && blocco_tasti.shiftKey && blocco_tasti.keyCode == 'C'.charCodeAt(0)) { 
        return false; 
    }
    if(blocco_tasti.ctrlKey && blocco_tasti.shiftKey && blocco_tasti.keyCode == 'J'.charCodeAt(0)) { 
        return false; 
    }
    if(blocco_tasti.ctrlKey && blocco_tasti.keyCode == 'U'.charCodeAt(0)) { 
        return false; 
    } 
}

window.onscroll = function() {scrollFunction()};
function scrollFunction() {
    if(document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
        document.getElementById("Header").style.top = "-5%";
    }
    else {
        document.getElementById("Header").style.top = "0%";
    }
}

/*
document.addEventListener("DOMContentLoaded", function() {
    var elements = document.getElementsByTagName("INPUT");
    for (var i = 0; i < elements.length; i++) {
        elements[i].oninvalid = function(e) {
            e.target.setCustomValidity("");
            if (!e.target.validity.valid) {
                e.target.setCustomValidity("Campo da invalidare obbligatoriamente ~Samuele Cieri");
            }
        };
        elements[i].oninput = function(e) {
            e.target.setCustomValidity("");
        };
    }
})
*/

function MCounter(){
    var x = document.getElementById("MCounter");
    if (x.value > 0 ){
        counter = x.value;
    }
}

/*
var currentTime = new
Date().getHours();


function timeCheck() {
    if( currentTime > 18 || currentTime < 8 ){
        document.documentElement.classList.add('night');
    }
}
timeCheck();
*/

function searchTable(inputId, tableId, tds) {// eslint-disable-line no-unused-vars
        // it represents h
        if (typeof inputId !== 'undefined' && typeof tableId !== 'undefined' && typeof tds !== 'undefined') {
            inputId = $.trim(inputId); // the id of the searchField
            tableId = $.trim(tableId); // the id of the table in which the typed value is to be searched
            if (tds != 'all') {
                tds = $.parseJSON(tds); // json_array of td numbers e.g: [1,3,6] or the string 'all'
                // all means search in all tds/columns
            }
            if (inputId != '' && tableId != '' && tds.length >= 1) {
        
                var input, filter, table, tr, i, search_matched, current_td_value;
                input = document.getElementById(inputId);
                filter = input.value.toUpperCase();
                table = document.getElementById(tableId);
                tr = table.getElementsByTagName("tr");
                for (i = 2; i < (tr.length-2); i++) {
        
                    if ($('#' + tableId).find('tr:eq(' + i + ')').hasClass('tableFloatingHeaderOriginal') || $('#' + tableId).find('tr:eq(' + i + ')').hasClass('tableFloatingHeader')) {
                        continue;
                    }
        
                    search_matched = false;
                    current_td_value = '';
                    if (tds == 'all') {
        
                        for (var j = 0; j < tr[i].getElementsByTagName("td").length; j++) {
                            current_td_value = tr[i].getElementsByTagName("td")[j];
                            if (current_td_value && current_td_value.innerHTML.toUpperCase().indexOf(filter) > -1) {
                                search_matched = true;
                                break;
                            }
                        }
                    } else {
        
                        for (var j = 0; j < tds.length; j++) {
        
                            current_td_value = tr[i].getElementsByTagName("td")[(tds[j] - 1)];
                            if (current_td_value && current_td_value.innerHTML.toUpperCase().indexOf(filter) > -1) {
                                search_matched = true;
                                break;
                            }
                        }
                    }
        
                    if (search_matched) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            } else {
                console.log('error in parameters');
            }
        } else {
            console.log('error in parameters');
        }
    }