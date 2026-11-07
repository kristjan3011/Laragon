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
