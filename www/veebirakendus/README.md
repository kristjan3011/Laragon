# Mario Käpakodu – kursuse lõppversioon

PHP 8.3, MySQL 8.4 ja Bootstrap 5 abil loodud loomade varjupaiga veebirakendus. See kaust on projekti ainus esitatav lõppversioon. `../mario-kapakodu` on varasema õppeetapi materjal, mille sisenemispunktid suunatakse siia.

## Käivitamine Laragonis

1. Käivita Laragonis Apache ja MySQL (**Start All**).
2. Valmista andmebaas ette allpool kirjeldatud viisil.
3. Ava `http://localhost/veebirakendus/`.

Alternatiiv Apache'ile: käivita projekti kaustas `php -S 127.0.0.1:8000` ja ava `http://127.0.0.1:8000/`. MySQL peab ka sel juhul töötama. Rakenduse ajatsoon on `Europe/Tallinn` ning andmebaasiühenduse märgistik `utf8mb4`.

### Uus tühi andmebaas

Ava Laragoni MySQL konsool ning käivita:

```sql
SOURCE C:/laragon/www/veebirakendus/sql/schema.sql;
```

`schema.sql` loob andmebaasi `mario_kapakodu`, tabelid ja näidisandmed. See ei kustuta olemasolevaid tabeleid. Skript on mõeldud uuele paigaldusele; olemasolevate tabelite korral kasuta migratsiooni. Ära käivita importi valikuga `--force`, mis jätkaks pärast olemasoleva tabeli veateadet.

Näidiskasutaja on `admin@varjupaik.ee`, algne parool `muuda-mind`. Tegelikus kasutuses muuda näidisparool kasutajahalduses.

### Olemasolev andmebaas – andmeid säilitav uuendus

Tee oma andmebaasist varukoopia. Seejärel vali MySQL konsoolis olemasolev andmebaas ja käivita migratsioon:

```sql
USE mario_kapakodu;
SOURCE C:/laragon/www/veebirakendus/sql/migrations/20261119_course_completion.sql;
```

Migratsiooni võib käivitada korduvalt. See lisab puuduvad `animals.created_by` ja `contact_requests.animal_id` väljad koos võõrvõtmetega ega kustuta kirjeid. Varasemate loomade lisajat ega vanade päringute loomaseost ei oletata: need jäävad `NULL`-iks. Uue looma lisaja võetakse sisseloginud kasutajast; olemasoleva looma muutmine algset lisajat ei asenda.

MySQL tabelistruktuuri muutmine teeb automaatse COMMIT-i. Migratsioon ei ole ühe tehinguna tagasipööratav. Kui ühendusseadetes kasutatakse teist andmebaasinime, vali see enne migratsiooni käsuga `USE`.

Ühendusfail `includes/config.php` loeb `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` keskkonnamuutujaid; kohaliku arenduse vaikeandmebaas on `mario_kapakodu` ning vaikeport `3306`.

## Lehed ja teostatud võimalused

| Fail | Võimalused |
| --- | --- |
| `index.php` | Neli viimati lisatud looma ID järgi kahanevalt, ühine päis ja jalus |
| `all_animals.php` | GET-otsing nime järgi, liigi-, soo- ja vanusefiltrid, lehekülgede vahetamine, leivapuru |
| `animal.php?id=N` | Looma detailvaade ja kontaktivormi link; vigane või puuduv loom suunab avalehele |
| `contact.php` | Valideeritud kontaktipäringu salvestamine; päring võib olla seotud loomaga |
| `login.php` | Sisselogimine ja e-posti meelespidamine 30 päevaks |
| `kuepsised.php` | Küpsiste nõusoleku salvestamine ja kustutamine POST-päringuga |
| `admin/index.php` | Statistika ning viimased loomad ja päringud |
| `admin/animals.php` | Loomade loend, filtrid, kustutamine ja kümme kirjet lehel |
| `admin/animal_form.php` | Looma lisamine ja muutmine ning JPG/PNG pildi üleslaadimine |
| `admin/species.php` | Liikide ja tõugude haldus |
| `admin/users.php` | Kasutajahaldus, ligipääs ainult administraatoril |
| `admin/requests.php` | Kontaktipäringute loend, staatuse muutmine ja kustutamine |
| `admin/logout.php` | Sessiooni lõpetamine POST-päringuga |

Peamenüü tuleb tabelist `menu`. Ühine leivapuru on failis `includes/breadcrumbs.php`. Tühja otsingutulemuse kohta kuvatakse teade.

## Andmemudel ja õppefailid

Lõppversiooni tabelid on `species`, `breeds`, `users`, `animals`, `contact_requests` ja `menu`. [ER-diagramm](database/er-diagram.md) kirjeldab nende tegelikke välju ja seoseid. Looma lisaja ning päringu loomaseose võõrvõtmed kasutavad `ON DELETE SET NULL`, et kasutaja või looma kustutamine ei kustutaks looma või kontaktipäringut.

`database/kapakodu.sql`, `database/designer.png` ja `database/loomad.csv` on ülesannete 2–6 ajalooline tõendusmaterjal eestikeelsete tabelitega `kapakodu` andmebaasist. Ülesannete päringud, selgitused ja tulemused on failides `ulesanne03.md`, `ulesanne04.md` ja `ylesanne07.md`. Neid ei tohi segi ajada lõppversiooni `sql/schema.sql` failiga.

## Turvalisus ja vormid

- PDO ettevalmistatud päringud ning HTML-väljundi kodeerimine funktsiooniga `htmlspecialchars`.
- Sisselogimise kontroll enne admin-lehtede andmetöötlust; CSRF-token POST-vormidel.
- Paroolid salvestatakse räsina ning kontrollitakse `password_verify()` abil; sisselogimisel vahetub sessiooni ID.
- Sessiooniküpsisel on `HttpOnly` ja `SameSite=Lax`; HTTPS-i puhul ka `Secure`.
- Kasutajahaldus on administraatorile; viimase administraatori eemaldamist piiratakse.
- Loomapildid: JPG/PNG, kuni 5 MB, MIME ja pildisisu kontroll, juhuslik failinimi.
- E-posti meelespidamise küpsis ei sisalda parooli. Küpsiste nõusolekut saab jaluses muuta või kustutada.
- Serveripoolset andmekontrolli täiendavad vormiväljade piirangud ja kliendipoolne valideerimine.

## Kontrollimise seis ja vastuvõtukontroll

19.11.2026 kontrolliti lõppversiooni eraldatud MySQL 8.4.3 ja PHP 8.3.33 testkeskkonnas. Kõik 58 funktsionaalset kontrolli läbisid: avalikud vaated, vigased ID-d, leivapuru, sisselogimine/väljalogimine, õigused ja CSRF, liikide/tõugude muutmine, loomade lisamine/muutmine/kustutamine, lisaja säilimine, kontaktipäringu loomaseos, PNG üleslaadimine ning võltsitud pildi tagasilükkamine, leheküljevahetus, küpsised ja vana versiooni ümbersuunamised. Migratsiooni korduvkäivitus säilitas andmed.

W3C Nu valideeris 11 genereeritud HTML-vaadet (viis avalikku ja kuus administraatori vaadet) ilma vigade või hoiatusteta. Tulemused: `validatsioon/course-completion-20261119.json`. Kitsaste ekraanide visuaalset brauserikontrolli ei tehtud. Varasem `w3c-login-roheline.png` säilib ajaloolise tõendina.

Töötava MySQL-i ja veebiserveriga kontrolli:

1. Avalehel on neli viimast looma; detailvaade avaneb ja vigane ID suunab avalehele.
2. Otsing ja kõik filtrid toimivad eraldi ning koos; leheküljevahetus säilitab filtrid.
3. Leivapuru on loomade loendis, kontaktilehel ja detailvaates.
4. Kontaktivormi vigane sisend lükatakse tagasi; korrektne päring salvestub koos valitud loomaseosega.
5. Admin-lehed suunavad sisselogimata kasutaja login-lehele; pärast väljalogimist jääb ligipääs suletuks.
6. Looma, liigi ja tõu lisamine, muutmine ning kustutamine töötavad; seotud andmete piirangud annavad arusaadava teate.
7. Uue looma lisaja salvestub; looma muutmine säilitab lisaja; looma kustutamine säilitab kontaktipäringu.
8. Pildi üleslaadimine töötab ning keelatud failitüüp lükatakse tagasi.
9. „Jäta mind meelde” täidab e-posti uuesti ja valiku eemaldamine kustutab küpsise.
10. Kontrolli avalikke ja sisselogitud admin-vaateid kitsal ekraanil ning valideeri nende genereeritud HTML.

## Veel õppija teha

Kursuse testide 1, 2 ja 3 isiklikud tulemused ei ole repositooriumis ega avaliku lehe kaudu kontrollitavad. Nende läbimist ei märgita koodi põhjal tehtuks.

Kursuse kokkuvõtte lisaarendused – mitme pildi tugi, iseregistreerimine, parooli taastamine, eraldi profiil, tegevuslogi, e-kirjad, graafikud, API ja automaattestid – ei ole automaatselt kohustuslikud. „Unustasid parooli?” teavitus ei ole parooli taastamise teenus. Täiendavad nõuded sõltuvad juhendaja ülesandest.
