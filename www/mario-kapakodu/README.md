# Mario Käpakodu — varasem õppeversioon

Lõplik ja hooldatav rakendus asub kõrvalkaustas `../veebirakendus` ning avaneb Laragonis aadressil `http://localhost/veebirakendus/`.

See kaust säilitab varasemate MySQLi ülesannete SQL-i, abifailid ja varad. Kõik varasema rakenduse avalikud ja administraatori PHP-sisenemispunktid suunavad HTTP 303 vastusega lõplikku rakendusse. Vana versioon ei töötle enam POST-päringuid ega muuda andmebaasi. Administraatori ligipääsu kontroll toimub lõplikus rakenduses.

Andmebaas `kapakodu` ja lõpliku rakenduse `mario_kapakodu` on erinevad. Vanu loomade ja liikide numbrilisi ID-sid ei edastata, sest sama ID võib tähistada teist kirjet. Vana looma detaili- või muutmislink avab vastava lõpliku loomade nimekirja. Otsingutekst, sugu ja lõpliku rakendusega sobivad vanuse-/leheküljeparameetrid säilitatakse valideeritult. Vanu 1–5 aasta vanusegruppe ei teisendata erineva tähendusega lõpliku rakenduse gruppideks.

Vanu andmeid automaatselt ei kopeerita ega kustutata. Andmete käsitsi ühendamisel tuleb esmalt võrrelda kirjeid ning koostada ID-vastavused. Lõpliku rakenduse seadistus ja paigaldusjuhis on selle README-failis.
