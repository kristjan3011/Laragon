# Mario Käpakodu

## Kirjeldus

Mario Käpakodu on loomade varjupaiga veebirakendus. Külastajad saavad tutvuda varjupaigas olevate loomadega ja saata kontaktipäringu. Administraator ja toimetaja haldavad varjupaiga infot admin-paneelis.

Rakendus on kirjutatud PHP-ga, andmed on MySQL andmebaasis ja kasutajaliides põhineb Bootstrapil.

## Käivitamine (Laragon)

1. Käivita Laragonis **Start All** (Apache ja MySQL). Kui MySQL-is on juba andmebaas `mario_kapakodu`, jäta järgmine samm vahele.
2. Impordi andmebaas (loob andmebaasi `mario_kapakodu` koos näidisandmetega). Ava Laragoni terminal (Menu → Terminal), kus `mysql` käsk töötab:

   ```
   mysql -uroot < C:\laragon\www\veebirakendus\sql\schema.sql
   ```

   Tähelepanu: skript kustutab olemasolevad tabelid ja loob need uuesti, seega ära käivita seda andmetega andmebaasil.
3. Ava brauseris `http://localhost/veebirakendus/`. Alternatiiv ilma Apache'ita: `php -S 127.0.0.1:8000` projekti kaustas ja `http://127.0.0.1:8000/`.
4. Andmebaasi ühendus tuleb failist `includes/config.php`. Seda saab muuta keskkonnamuutujatega `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

Näidiskasutaja: `admin@varjupaik.ee`, parool `muuda-mind`. Vaheta see parool enne igasugust päris kasutust.

## Lehed

### Avalik osa

| Fail | Vaade |
| --- | --- |
| `index.php` | Avaleht koos viimaste loomade kaartidega |
| `all_animals.php` | Kõik loomad: otsing nime järgi, filtrid (liik, vanus, sugu) ja lehekülgede vahetus |
| `animal.php?id=N` | Looma detailvaade |
| `contact.php` | Kontaktandmed ja kontaktivorm (salvestub andmebaasi) |
| `login.php` | Sisselogimine |

### Admin-paneel

| Fail | Vaade |
| --- | --- |
| `admin/index.php` | Statistika ning viimased loomad ja päringud |
| `admin/animals.php` | Loomade nimekiri, filtrid, kustutamine |
| `admin/animal_form.php` | Looma lisamine ja muutmine, pildi üleslaadimine |
| `admin/species.php` | Liikide ja tõugude haldus |
| `admin/users.php` | Kasutajate haldus (ainult administraator) |
| `admin/requests.php` | Kontaktipäringute haldus |
| `admin/logout.php` | Väljalogimine (ainult POST) |

## Kaustastruktuur

```
veebirakendus/
├── index.php, all_animals.php, animal.php, contact.php, login.php
├── admin/            admin-paneeli lehed
├── includes/         config.php, functions.php, päised ja jalused
├── sql/schema.sql    andmebaasi skeem ja näidisandmed
├── assets/
│   ├── css/style.css
│   ├── images/
│   └── uploads/      üleslaaditud loomapildid (tekib esimesel üleslaadimisel, ei lähe Giti)
└── README.md
```

## Tehnoloogiad

- HTML5, CSS3, Bootstrap 5.3.8, Font Awesome 7.0.1
- PHP 8.3 (PDO)
- MySQL 8.4

## Turvalisus

- PDO ettevalmistatud päringud (SQL-süstimise vastu)
- Kogu väljund läbib `htmlspecialchars` (XSS-i vastu)
- CSRF-token kõigil POST-vormidel, väljalogimine on samuti POST
- Paroolid `password_hash` / `password_verify`; üldine veateade ja katsete piirang sisselogimisel
- Sessiooniküpsis `HttpOnly` ja `SameSite=Lax`; sessiooni ID vahetub sisselogimisel
- Rollikontroll: kasutajaid haldab ainult administraator; viimast administraatorit ega iseennast kustutada ei saa
- Pildi üleslaadimine: ainult JPG/PNG, kuni 5 MB, MIME ja `getimagesize` kontroll, juhuslik failinimi

## Valideerimine ja testimine

- W3C HTML validaator: `index.php`, `animal.php` ja `contact.php` on ilma vigade ja hoiatusteta; `all_animals.php` leht 1 oli ilma vigadeta ning ainus hoiatus (`aria-disabled` lingil) on pärast seda parandatud. `login.php` ja admin-lehed on käsitsi veel valideerimata, sest validaator nõudis automaatsetele päringutele CAPTCHA-t.
- Kliendipoolne valideerimine: kontaktivorm, sisselogimine, loomavorm ja kasutajavorm (Bootstrap `was-validated`).
- Käsitsi (curl) testitud: sisselogimine ja väljalogimine, loomade, liikide, tõugude, kasutajate ja päringute lisamine, muutmine ja kustutamine, pildi üleslaadimine ja valesti tüübi tagasilükkamine, rollikontroll, CSRF.

## Hindamismudel

### Front-end ehk avalik osa

- [x] Veebirakendusel on avaleht, kus kuvatakse loomade nimekiri koos piltidega
- [x] Loomi saab otsida nime järgi
- [x] Loomi saab filtreerida
- [x] Looma valimisel kuvatakse tema detailvaade
- [x] Külastaja saab saata päringu kontaktivormi kaudu
- [x] Veebirakendus kohandub erinevate ekraanisuurustega
- [x] Veebivormid sisaldavad kliendipoolset valideerimist

### Back-end ehk haldus

- [x] Administraator saab sisse logida
- [x] Administraator saab hallata loomade andmeid (lisada, muuta ja kustutada CRUD)
- [x] Administraator saab hallata liikide andmeid
- [x] Administraator saab hallata tõugude andmeid
- [x] Administraator saab hallata kasutajate andmeid
- [x] Administraator saab hallata saabunud kontaktipäringuid
- [x] Administraator näeb veebirakenduse statistikat

### Tehnilised nõuded

- [x] Veebirakendus kasutab HTML-i, CSS-i ja Bootstrapi
- [x] Veebirakendus kasutab PHP-d andmete töötlemiseks
- [x] Andmed salvestatakse MySQL andmebaasi
- [x] Veebirakendus kohandub erinevate ekraanisuurustega (responsive disain)
- [x] Veebivormid sisaldavad kliendipoolset valideerimist
- [x] Veebirakendus järgib kursuse jooksul õpitud turvalise programmeerimise põhimõtteid
- [x] Lähtekood on korrektselt struktureeritud ja loetav
- [x] Projekt on hallatud Gitiga ja üles laaditud GitHubi
- [x] Projekt sisaldab ajakohast README.md faili
