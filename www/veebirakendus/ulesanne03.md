# Ülesanne 3 – Andmete muutmine ja otsimine

Andmebaas `kapakodu`, tabel `loomad`.

### 1. Kuva kõik loomad

Valib tabelist kõik veerud ja read.

```sql
SELECT * FROM loomad;
```

**Tulemus:**

```
+----+--------+----------+-------+-------+--------------+-----------+------------+------------+-------------------+-------------------------------------------------+------------+-------------+-----------------+-------------+------------+
| id | nimi   | liik     | sugu  | vanus | sunnikuupaev | suurus    | varvus     | asukoht    | luhikirjeldus     | kirjeldus                                       | pilt       | staatus     | steriliseeritud | kiibistatud | lisatud    |
+----+--------+----------+-------+-------+--------------+-----------+------------+------------+-------------------+-------------------------------------------------+------------+-------------+-----------------+-------------+------------+
|  1 | Bella  | Koer     | emane |     5 | 2021-03-01   | keskmine  | Must       | Tallinn    | S├Ábralik koer    | S├Ábralik ja rahulik koer.                      | bella.jpg  | saadaval    |               1 |           1 | 2026-11-07 |
|  2 | Nurr   | Kass     | isane |     1 | 2025-02-01   | v├ñike    | Hall       | Tartu      | Noor kass         | Noor ja m├ñnguhimuline kass.                    | nurr.jpg   | saadaval    |               0 |           1 | 2026-11-07 |
|  3 | Rex    | Koer     | isane |     2 | 2023-05-12   | keskmine  | Kuldne     | Tallinn    | Sõbralik koer     | Energiat täis ja armastab jalutuskäike.         | rex.jpg    | saadaval    |               1 |           1 | 2026-07-01 |
|  4 | Mimi   | Kass     | emane |     6 | 2020-08-14   |           | Hall-valge | Tartu      | Rahulik kass      | Väga sõbralik ning naudib inimese seltskonda.   | mimi.jpg   | saadaval    |               1 |           1 | 2026-07-02 |
|  5 | Bruno  | Koer     | isane |     5 | 2021-01-22   | suur      | Pruun      | Pärnu      | Valvekoer         | Rahulik ja ustav perekoer.                      | bruno.jpg  | broneeritud |               1 |           1 | 2026-07-03 |
|  6 | Bella  | Koer     | emane |     1 | 2025-02-18   | keskmine  | Must       | Haapsalu   | Mänguhimuline     | Noor ja aktiivne koer.                          | bella.jpg  | saadaval    |               0 |           1 | 2026-07-04 |
|  7 | Miisu  | Kass     | emane |     4 | 2022-06-30   |           | Triibuline | Viljandi   | Uudishimulik kass | Armastab ronida ja mängida.                     | miisu.jpg  | broneeritud |               1 |           1 | 2026-07-05 |
|  8 | Nurr   | Kass     | isane |     3 | 2023-03-10   |           | Hall       | Rakvere    | Vaikne kass       | Sobib rahulikku koju.                           | nurr.jpg   | hoolduses   |               1 |           0 | 2026-07-06 |
|  9 | Musti  | Koer     | isane |     3 | 2022-04-01   | keskmine  | Pruun-must | Tallinn    | Rõõmsameelne      | Naudib pikki jalutuskäike.                      | musti.jpg  | saadaval    |               1 |           1 | 2026-07-07 |
| 10 | Lotte  | Koer     | emane |     7 | 2019-09-19   | keskmine  | Beež       | Narva      | Rahulik           | Väga sõbralik vanem koer.                       | lotte.jpg  | saadaval    |               1 |           1 | 2026-07-08 |
| 11 | Pätu   | Kass     | isane |     2 | 2024-02-11   |           | Must       | Jõhvi      | Mänguline         | Armastab pallidega mängida.                     | patu.jpg   | saadaval    |               0 |           1 | 2026-07-09 |
| 12 | Kribu  | Küülik   | emane |     1 | 2025-01-15   |           | Valge      | Tallinn    | Armas küülik      | Harjunud inimestega.                            | kribu.jpg  | saadaval    |               0 |           0 | 2026-07-10 |
| 13 | Roki   | Koer     | isane |     8 | 2018-07-07   | suur      | Must       | Tartu      | Kogenud sõber     | Rahulik ja kuulekas.                            | roki.jpg   | hoolduses   |               1 |           1 | 2026-07-11 |
| 14 | Lumi   | Kass     | emane |     5 | 2021-12-24   |           | Valge      | Pärnu      | Hellik            | Naudib pai tegemist.                            | lumi.jpg   | saadaval    |               1 |           1 | 2026-07-12 |
| 15 | Marta  | Koer     | emane |     4 | 2022-10-09   | keskmine  | Pruun      | Rapla      | Sõbralik          | Sobib lastega perre.                            | marta.jpg  | broneeritud |               1 |           1 | 2026-07-13 |
| 16 | Simba  | Kass     | isane |     2 | 2024-01-08   |           | Oranž      | Valga      | Julge             | Väga uudishimulik kass.                         | simba.jpg  | saadaval    |               1 |           0 | 2026-07-14 |
| 17 | Bobby  | Koer     | isane |     6 | 2020-05-03   | suur      | Must-valge | Võru       | Truu kaaslane     | Armastab õues joosta.                           | bobby.jpg  | hoolduses   |               1 |           1 | 2026-07-15 |
| 18 | Kiisu  | Kass     | emane |     1 | 2025-03-14   |           | Kilpkonn   | Paide      | Noor kass         | Õpib alles maailma tundma.                      | kiisu.jpg  | saadaval    |               0 |           1 | 2026-07-16 |
| 19 | Rufi   | Koer     | isane |     2 | 2023-08-18   | keskmine  | Hall       | Kuressaare | Aktiivne          | Sobib sportlikule perele.                       | rufi.jpg   | saadaval    |               0 |           1 | 2026-07-17 |
| 20 | Muri   | Koer     | emane |     9 | 2017-11-11   | suur      | Must       | Tallinn    | Rahulik           | Vajab rahulikku kodu.                           | muri.jpg   | hoolduses   |               1 |           1 | 2026-07-18 |
| 21 | Triibu | Kass     | isane |     7 | 2019-04-20   |           | Triibuline | Põlva      | Tasane            | Meeldib aknal istuda.                           | triibu.jpg | broneeritud |               1 |           1 | 2026-07-19 |
| 22 | Sipsik | Kass     | emane |     3 | 2023-06-06   |           | Must-valge | Keila      | Seltskondlik      | Naudib mängimist ja tähelepanu.                 | sipsik.jpg | saadaval    |               1 |           1 | 2026-07-20 |
+----+--------+----------+-------+-------+--------------+-----------+------------+------------+-------------------+-------------------------------------------------+------------+-------------+-----------------+-------------+------------+
```

### 2. Ühe looma kõik andmed nime järgi

Valib ainult looma, kelle nimi on Rex.

```sql
SELECT * FROM loomad WHERE nimi = 'Rex';
```

**Tulemus:**

```
+----+------+------+-------+-------+--------------+----------+--------+---------+----------------+-------------------------------------------+---------+----------+-----------------+-------------+------------+
| id | nimi | liik | sugu  | vanus | sunnikuupaev | suurus   | varvus | asukoht | luhikirjeldus  | kirjeldus                                 | pilt    | staatus  | steriliseeritud | kiibistatud | lisatud    |
+----+------+------+-------+-------+--------------+----------+--------+---------+----------------+-------------------------------------------+---------+----------+-----------------+-------------+------------+
|  3 | Rex  | Koer | isane |     2 | 2023-05-12   | keskmine | Kuldne | Tallinn | Sõbralik koer  | Energiat täis ja armastab jalutuskäike.   | rex.jpg | saadaval |               1 |           1 | 2026-07-01 |
+----+------+------+-------+-------+--------------+----------+--------+---------+----------------+-------------------------------------------+---------+----------+-----------------+-------------+------------+
```

### 3. Kõik vähemalt viieaastased loomad

Filtreerib loomad, kelle vanus on vähemalt 5.

```sql
SELECT * FROM loomad WHERE vanus >= 5;
```

**Tulemus:**

```
+----+--------+------+-------+-------+--------------+----------+------------+---------+-------------------+-------------------------------------------------+------------+-------------+-----------------+-------------+------------+
| id | nimi   | liik | sugu  | vanus | sunnikuupaev | suurus   | varvus     | asukoht | luhikirjeldus     | kirjeldus                                       | pilt       | staatus     | steriliseeritud | kiibistatud | lisatud    |
+----+--------+------+-------+-------+--------------+----------+------------+---------+-------------------+-------------------------------------------------+------------+-------------+-----------------+-------------+------------+
|  1 | Bella  | Koer | emane |     5 | 2021-03-01   | keskmine | Must       | Tallinn | S├Ábralik koer    | S├Ábralik ja rahulik koer.                      | bella.jpg  | saadaval    |               1 |           1 | 2026-11-07 |
|  4 | Mimi   | Kass | emane |     6 | 2020-08-14   |          | Hall-valge | Tartu   | Rahulik kass      | Väga sõbralik ning naudib inimese seltskonda.   | mimi.jpg   | saadaval    |               1 |           1 | 2026-07-02 |
|  5 | Bruno  | Koer | isane |     5 | 2021-01-22   | suur     | Pruun      | Pärnu   | Valvekoer         | Rahulik ja ustav perekoer.                      | bruno.jpg  | broneeritud |               1 |           1 | 2026-07-03 |
| 10 | Lotte  | Koer | emane |     7 | 2019-09-19   | keskmine | Beež       | Narva   | Rahulik           | Väga sõbralik vanem koer.                       | lotte.jpg  | saadaval    |               1 |           1 | 2026-07-08 |
| 13 | Roki   | Koer | isane |     8 | 2018-07-07   | suur     | Must       | Tartu   | Kogenud sõber     | Rahulik ja kuulekas.                            | roki.jpg   | hoolduses   |               1 |           1 | 2026-07-11 |
| 14 | Lumi   | Kass | emane |     5 | 2021-12-24   |          | Valge      | Pärnu   | Hellik            | Naudib pai tegemist.                            | lumi.jpg   | saadaval    |               1 |           1 | 2026-07-12 |
| 17 | Bobby  | Koer | isane |     6 | 2020-05-03   | suur     | Must-valge | Võru    | Truu kaaslane     | Armastab õues joosta.                           | bobby.jpg  | hoolduses   |               1 |           1 | 2026-07-15 |
| 20 | Muri   | Koer | emane |     9 | 2017-11-11   | suur     | Must       | Tallinn | Rahulik           | Vajab rahulikku kodu.                           | muri.jpg   | hoolduses   |               1 |           1 | 2026-07-18 |
| 21 | Triibu | Kass | isane |     7 | 2019-04-20   |          | Triibuline | Põlva   | Tasane            | Meeldib aknal istuda.                           | triibu.jpg | broneeritud |               1 |           1 | 2026-07-19 |
+----+--------+------+-------+-------+--------------+----------+------------+---------+-------------------+-------------------------------------------------+------------+-------------+-----------------+-------------+------------+
```

### 4. Loomad, kelle nimi algab M-tähega

LIKE koos % märgiga leiab nimed, mis algavad tähega M.

```sql
SELECT * FROM loomad WHERE nimi LIKE 'M%';
```

**Tulemus:**

```
+----+-------+------+-------+-------+--------------+----------+------------+----------+-------------------+-------------------------------------------------+-----------+-------------+-----------------+-------------+------------+
| id | nimi  | liik | sugu  | vanus | sunnikuupaev | suurus   | varvus     | asukoht  | luhikirjeldus     | kirjeldus                                       | pilt      | staatus     | steriliseeritud | kiibistatud | lisatud    |
+----+-------+------+-------+-------+--------------+----------+------------+----------+-------------------+-------------------------------------------------+-----------+-------------+-----------------+-------------+------------+
|  4 | Mimi  | Kass | emane |     6 | 2020-08-14   |          | Hall-valge | Tartu    | Rahulik kass      | Väga sõbralik ning naudib inimese seltskonda.   | mimi.jpg  | saadaval    |               1 |           1 | 2026-07-02 |
|  7 | Miisu | Kass | emane |     4 | 2022-06-30   |          | Triibuline | Viljandi | Uudishimulik kass | Armastab ronida ja mängida.                     | miisu.jpg | broneeritud |               1 |           1 | 2026-07-05 |
|  9 | Musti | Koer | isane |     3 | 2022-04-01   | keskmine | Pruun-must | Tallinn  | Rõõmsameelne      | Naudib pikki jalutuskäike.                      | musti.jpg | saadaval    |               1 |           1 | 2026-07-07 |
| 15 | Marta | Koer | emane |     4 | 2022-10-09   | keskmine | Pruun      | Rapla    | Sõbralik          | Sobib lastega perre.                            | marta.jpg | broneeritud |               1 |           1 | 2026-07-13 |
| 20 | Muri  | Koer | emane |     9 | 2017-11-11   | suur     | Must       | Tallinn  | Rahulik           | Vajab rahulikku kodu.                           | muri.jpg  | hoolduses   |               1 |           1 | 2026-07-18 |
+----+-------+------+-------+-------+--------------+----------+------------+----------+-------------------+-------------------------------------------------+-----------+-------------+-----------------+-------------+------------+
```

### 5. Saadaval loomad nime järgi tähestikus

Filtreerib staatuse järgi saadaval loomad ja järjestab need nime järgi kasvavalt.

```sql
SELECT * FROM loomad WHERE staatus = 'saadaval' ORDER BY nimi ASC;
```

**Tulemus:**

```
+----+--------+----------+-------+-------+--------------+-----------+------------+------------+-------------------+-------------------------------------------------+------------+----------+-----------------+-------------+------------+
| id | nimi   | liik     | sugu  | vanus | sunnikuupaev | suurus    | varvus     | asukoht    | luhikirjeldus     | kirjeldus                                       | pilt       | staatus  | steriliseeritud | kiibistatud | lisatud    |
+----+--------+----------+-------+-------+--------------+-----------+------------+------------+-------------------+-------------------------------------------------+------------+----------+-----------------+-------------+------------+
|  1 | Bella  | Koer     | emane |     5 | 2021-03-01   | keskmine  | Must       | Tallinn    | S├Ábralik koer    | S├Ábralik ja rahulik koer.                      | bella.jpg  | saadaval |               1 |           1 | 2026-11-07 |
|  6 | Bella  | Koer     | emane |     1 | 2025-02-18   | keskmine  | Must       | Haapsalu   | Mänguhimuline     | Noor ja aktiivne koer.                          | bella.jpg  | saadaval |               0 |           1 | 2026-07-04 |
| 18 | Kiisu  | Kass     | emane |     1 | 2025-03-14   |           | Kilpkonn   | Paide      | Noor kass         | Õpib alles maailma tundma.                      | kiisu.jpg  | saadaval |               0 |           1 | 2026-07-16 |
| 12 | Kribu  | Küülik   | emane |     1 | 2025-01-15   |           | Valge      | Tallinn    | Armas küülik      | Harjunud inimestega.                            | kribu.jpg  | saadaval |               0 |           0 | 2026-07-10 |
| 10 | Lotte  | Koer     | emane |     7 | 2019-09-19   | keskmine  | Beež       | Narva      | Rahulik           | Väga sõbralik vanem koer.                       | lotte.jpg  | saadaval |               1 |           1 | 2026-07-08 |
| 14 | Lumi   | Kass     | emane |     5 | 2021-12-24   |           | Valge      | Pärnu      | Hellik            | Naudib pai tegemist.                            | lumi.jpg   | saadaval |               1 |           1 | 2026-07-12 |
|  4 | Mimi   | Kass     | emane |     6 | 2020-08-14   |           | Hall-valge | Tartu      | Rahulik kass      | Väga sõbralik ning naudib inimese seltskonda.   | mimi.jpg   | saadaval |               1 |           1 | 2026-07-02 |
|  9 | Musti  | Koer     | isane |     3 | 2022-04-01   | keskmine  | Pruun-must | Tallinn    | Rõõmsameelne      | Naudib pikki jalutuskäike.                      | musti.jpg  | saadaval |               1 |           1 | 2026-07-07 |
|  2 | Nurr   | Kass     | isane |     1 | 2025-02-01   | v├ñike    | Hall       | Tartu      | Noor kass         | Noor ja m├ñnguhimuline kass.                    | nurr.jpg   | saadaval |               0 |           1 | 2026-11-07 |
| 11 | Pätu   | Kass     | isane |     2 | 2024-02-11   |           | Must       | Jõhvi      | Mänguline         | Armastab pallidega mängida.                     | patu.jpg   | saadaval |               0 |           1 | 2026-07-09 |
|  3 | Rex    | Koer     | isane |     2 | 2023-05-12   | keskmine  | Kuldne     | Tallinn    | Sõbralik koer     | Energiat täis ja armastab jalutuskäike.         | rex.jpg    | saadaval |               1 |           1 | 2026-07-01 |
| 19 | Rufi   | Koer     | isane |     2 | 2023-08-18   | keskmine  | Hall       | Kuressaare | Aktiivne          | Sobib sportlikule perele.                       | rufi.jpg   | saadaval |               0 |           1 | 2026-07-17 |
| 16 | Simba  | Kass     | isane |     2 | 2024-01-08   |           | Oranž      | Valga      | Julge             | Väga uudishimulik kass.                         | simba.jpg  | saadaval |               1 |           0 | 2026-07-14 |
| 22 | Sipsik | Kass     | emane |     3 | 2023-06-06   |           | Must-valge | Keila      | Seltskondlik      | Naudib mängimist ja tähelepanu.                 | sipsik.jpg | saadaval |               1 |           1 | 2026-07-20 |
+----+--------+----------+-------+-------+--------------+-----------+------------+------------+-------------------+-------------------------------------------------+------------+----------+-----------------+-------------+------------+
```

### 6. Kolm kõige vanemat looma

Järjestab vanuse järgi kahanevalt ja piirab tulemuse kolme reaga.

```sql
SELECT * FROM loomad ORDER BY vanus DESC LIMIT 3;
```

**Tulemus:**

```
+----+-------+------+-------+-------+--------------+----------+--------+---------+----------------+-----------------------------+-----------+-----------+-----------------+-------------+------------+
| id | nimi  | liik | sugu  | vanus | sunnikuupaev | suurus   | varvus | asukoht | luhikirjeldus  | kirjeldus                   | pilt      | staatus   | steriliseeritud | kiibistatud | lisatud    |
+----+-------+------+-------+-------+--------------+----------+--------+---------+----------------+-----------------------------+-----------+-----------+-----------------+-------------+------------+
| 20 | Muri  | Koer | emane |     9 | 2017-11-11   | suur     | Must   | Tallinn | Rahulik        | Vajab rahulikku kodu.       | muri.jpg  | hoolduses |               1 |           1 | 2026-07-18 |
| 13 | Roki  | Koer | isane |     8 | 2018-07-07   | suur     | Must   | Tartu   | Kogenud sõber  | Rahulik ja kuulekas.        | roki.jpg  | hoolduses |               1 |           1 | 2026-07-11 |
| 10 | Lotte | Koer | emane |     7 | 2019-09-19   | keskmine | Beež   | Narva   | Rahulik        | Väga sõbralik vanem koer.   | lotte.jpg | saadaval  |               1 |           1 | 2026-07-08 |
+----+-------+------+-------+-------+--------------+----------+--------+---------+----------------+-----------------------------+-----------+-----------+-----------------+-------------+------------+
```

### 7. Ühe looma staatus muudetakse broneeritud-ks

Valitud loom (id = 1, Bella) oli staatusega saadaval, nüüd muudetakse see broneeritud-ks. Kontrolliks näitab päring muutunud rida.

```sql
UPDATE loomad SET staatus = 'broneeritud' WHERE id = 1;
SELECT id, nimi, staatus FROM loomad WHERE id = 1;
```

**Tulemus:**

```
+----+-------+-------------+
| id | nimi  | staatus     |
+----+-------+-------------+
|  1 | Bella | broneeritud |
+----+-------+-------------+
```

### 8. Vabalt valitud IDga looma kustutamine

Kustutatakse loom id = 22 (Sipsik). Kontrolliks näitab päring loomade koguarvu.

```sql
DELETE FROM loomad WHERE id = 22;
SELECT COUNT(*) AS loomi_kokku FROM loomad;
```

**Tulemus:**

```
+-------------+
| loomi_kokku |
+-------------+
|          21 |
+-------------+
```

