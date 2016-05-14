/**
 * Created by emaktse on 14.05.2016.
 */
/* eslint-env browser */
'use strict';

/**
 Seame nupule "Kuva lisamise vorm" sĆ¼ndmuse "click" halduri, mis peidab "kuva-nupp" paragrahvi,
 aga toob nĆ¤htavale "lisa-vorm" form elemendi
 */
document.querySelector('#kuva-nupp button').addEventListener('click',
    /**
     * Funktsioon teeb vormi nĆ¤htavaks ning peidab "peida" nupu
     * @event
     */
    function () {
        document.getElementById('lisa-vorm').style.display = 'block';
        document.getElementById('kuva-nupp').style.display = 'none';
    });

/*
 Seame nupule "Peida lisamise vorm" sĆ¼ndmuse "click" halduri, mis teeb "kuva-nupp"
 paragrahvi nĆ¤htavaks, aga peidab "lisa-vorm" form elemendi
 */
document.querySelector('#peida-nupp button').addEventListener('click',
    /**
     * Funktsioon peidab vormi ning teeb nĆ¤htavaks "peida" nupu
     * @event
     */
    function () {
        document.getElementById('lisa-vorm').style.display = 'none';
        document.getElementById('kuva-nupp').style.display = 'block';
    });

/*
 Lisame vormielemendile "lisa-vorm" sĆ¼ndmuse "submit" halduri, mis ilmeb siis kui kasutaja
 kas klikib vormis asuval submit nupul vĆµi vajutab tekstikastis enter klahvi.
 */
document.getElementById('lisa-vorm').addEventListener('submit',
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

