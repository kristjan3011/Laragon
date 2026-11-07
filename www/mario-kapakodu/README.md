# Mario Käpakodu (moodul 3, mysqli)

Eraldi PHP + MySQLi projekt (Ülesanded 8-11). Andmebaas: `kapakodu`.

## Lehed
- `index.php` - 4 viimast looma (Ülesanne 8)
- `animal.php?id=` - looma detailvaade, vigane/puuduv id suunab avalehele (Ülesanne 8)
- `all_animals.php` - otsing nime järgi, liigi, soo ja vanuse filter (Ülesanne 9)
- `contact.php` - kontaktivorm, salvestab tabelisse `paringud` (Ülesanne 11)
- `includes/` - `config.php`, `header.php` (menüü andmebaasist), `footer.php`, `breadcrumbs.php`, `card.php`

Pildid: `assets/images/<pilt>`; puuduva faili korral proovitakse `assets/images/default.png` (pole loodud).

## Käivitamine
1. Käivita MySQL (Laragon).
2. Loo menüü ja andmebaasi kasutaja (üks kord): `mysql -uroot kapakodu < sql/module3.sql`
3. `php -S 127.0.0.1:8100 -t C:\laragon\www\mario-kapakodu` ja ava http://127.0.0.1:8100/

Ühendus: `includes/config.php` loeb `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` keskkonnamuutujatest (vaikimisi kasutaja `kapakodu_app`).

## Loodud andmebaasi objektid
- tabel `menu` (3 rida: Avaleht, Kõik loomad, Kontakt)
- MySQL kasutaja `kapakodu_app`@`localhost` õigustega SELECT, INSERT, UPDATE, DELETE andmebaasil `kapakodu.*`
- Tabelid `loomad`, `liigid`, `toud`, `tootajad`, `paringud` olid juba olemas (skeem: `liik_id`/`toug_id`/`tootaja_id`).

## Moodul 4 (Ülesanded 12-15): admin
Admin-ala asub kaustas `admin/` (sisselogimist veel pole, see tuleb moodulis 5). Ühised osad: `includes/admin_header.php`, `admin_footer.php`, `animal_helpers.php` (valideerimine, pildi salvestus), `animal_form.php` (ühine vorm).
- `admin/index.php` - statistika (kokku, saadaval, broneeritud, hoolduses, päringud), 4 viimast looma ja 4 viimast sõnumit (Ülesanne 12)
- `admin/animals.php` - loomade tabel (Vaata / Muuda / Kustuta), kustutamine POST + `confirm()` + ettevalmistatud päring (Ülesanne 12); 10 looma lehel, `?page=N`, Bootstrapi lehekülgede nupud, liiga suur lehekülg -> viimane (Ülesanne 15)
- `admin/animal_add.php` - uue looma vorm: liigid/tõud/töötajad andmebaasist, serveripoolne valideerimine, sisestatud väärtused säilivad vea korral, JPG/PNG üleslaadimine kausta `assets/images/` (`aeg_failinimi`), PRG suunamine nimekirja (Ülesanne 13)
- `admin/animal_edit.php?id=N` - vorm täidetakse andmebaasist, UPDATE ... WHERE id = ?, tundmatu id suunab nimekirja (Ülesanne 14)

Testimine: käivita server (vt ülal), ava http://127.0.0.1:8100/admin/. Lisa loom, muuda seda, kustuta see; kontrolli `SELECT COUNT(*) FROM loomad`. Puuduvad pildid (nt `default.png`) on oodatud - pildi asemel kuvatakse tühi kast.

Märkus: `loomad.suurus` ENUM-i väärtus `väike` on andmebaasis vigase kodeeringuga. Vormi lubatud väärtused loetakse ENUM-i definitsioonist, seega vorm töötab, aga see valik näeb kentsakas välja. Parandus: `ALTER TABLE loomad MODIFY suurus ENUM('väike','keskmine','suur') NOT NULL;` (UTF-8 kliendiga).
