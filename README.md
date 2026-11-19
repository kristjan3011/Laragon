# Kristjani veebirakendus – Mario Käpakodu

Loomade varjupaiga PHP ja MySQL kursuseprojekt. Külastaja saab loomi otsida, filtreerida ja vaadata ning saata kontaktipäringu. Administraator ja toimetaja haldavad andmeid kaitstud haldusalas.

## Lõppversioon

**Esitatav ja edasi arendatav projekt on `www/veebirakendus`.** Käivitamise, andmebaasi uuendamise, lehtede ja kontrollimise juhised asuvad [projekti README-s](www/veebirakendus/README.md).

- Avalik rakendus: `http://localhost/veebirakendus/`.
- Andmebaas: `mario_kapakodu`; ühendus on seadistatav keskkonnamuutujatega.
- Lõppversiooni skeem: `www/veebirakendus/sql/schema.sql`.
- Olemasoleva andmebaasi uuendus: `www/veebirakendus/sql/migrations/20261119_course_completion.sql`.
- Lõppversiooni andmemudel: [ER-diagramm](www/veebirakendus/database/er-diagram.md).
- `www/mario-kapakodu` on varasem MySQLi õppeversioon; selle avalikud sisenemispunktid suunatakse lõppversiooni. Seda ei esitata teise valmis rakendusena.
- `www/veebirakendus/database/kapakodu.sql`, `designer.png`, `loomad.csv` ja ülesannete 3, 4 ning 7 failid on kursuse varasemate etappide tõendusmaterjal. Need kirjeldavad ajaloolist `kapakodu` andmebaasi ega ole lõppversiooni paigaldusskript.

## Kursuse seis

Rakenduse funktsioonide olemasolu ja nende kontrollimine on eri asjad. Lõppversiooni README kirjeldab teostatud võimalusi ja kontrollipiire. Kursuse kolme moodulitesti tulemused kuuluvad õppija kontole; nende läbimist ei kinnitata lähtekoodi põhjal.

Kokkuvõttes pakutud ideed, näiteks parooli taastamine, mitme pildi tugi, e-kirjad ja API, on lisaarendused. Need ei ole vaikimisi kohustuslike ülesannete osa.
