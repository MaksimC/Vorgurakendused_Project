'use strict';

/***LOGIN BLOCK SCRIPT */

document.querySelector('#login_form_open button').addEventListener('click',

    function () {
        document.getElementById('login_form').style.display = 'block';
        document.getElementById('login_form_open').style.display = 'none';
    });



document.querySelector('#login_form_close button').addEventListener('click',

    function () {
        document.getElementById('login_form').style.display = 'none';
        document.getElementById('login_form_open').style.display = 'block';
    });

/*** REGISTER BLOCK SCRIPT */

document.querySelector('#register_form_open button').addEventListener('click',

    function () {
        document.getElementById('register_form').style.display = 'block';
        document.getElementById('register_form_open').style.display = 'none';
    });



document.querySelector('#register_form_close button').addEventListener('click',

    function () {
        document.getElementById('register_form').style.display = 'none';
        document.getElementById('register_form_open').style.display = 'block';
    });





/*
 Lisame vormielemendile "login_form" sĆ¼ndmuse "submit" halduri, mis ilmeb siis kui kasutaja
 kas klikib vormis asuval submit nupul vĆµi vajutab tekstikastis enter klahvi.
 */
document.getElementById('login_form').addEventListener('submit',
    /**
     * KĆ¤ivitatakse vormi postitamisel. Kontrollib vormis olevaid vĆ¤Ć¤rtusi ja lisab
     * laotabelisse uue rea valitud vĆ¤Ć¤rtusega
     * @event
     * @param  {Event} event SĆ¼ndmuse info
     */
    function (event) {
        // loeme tekstikastidest kasutaja sisestatud andmed
        var nimetus = document.getElementById('nimetus').value;
        var kogus = Number(document.getElementById('kogus').value);

        // kontrollime vĆ¤Ć¤rtuseid
        if (!nimetus || kogus <= 0) {
            alert('Vigased vĆ¤Ć¤rtused!');
            // Katkestame tavalise submit tegevuse, vastasel korral navigeeriks brauser mujale
            event.preventDefault();
            return;
        }
    });

