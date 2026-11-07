# Ülesanne 7 – Tabelite ühendamine ja alampäringud

Andmebaas `kapakodu`: tabelid `loomad`, `liigid`, `toud`, `tootajad`, `paringud`.

### 1. Kõik loomad koos liigi ja tõu nimetusega

INNER JOIN ühendab loomad liigid ja toud tabelitega ning kuvab ainult need loomad, millel on mõlemad vasted.

```sql
SELECT loomad.nimi, liigid.nimetus AS liik, toud.nimetus AS toug
FROM loomad
INNER JOIN liigid ON loomad.liik_id = liigid.id
INNER JOIN toud ON loomad.toug_id = toud.id;
```

**Tulemus:**

```
+--------+----------+----------------------+
| nimi   | liik     | toug                 |
+--------+----------+----------------------+
| Bella  | Koer     | Kuldne retriiver     |
| Nurr   | Kass     | Siiami               |
| Rex    | Koer     | Labrador             |
| Mimi   | Kass     | Briti lühikarvaline  |
| Bruno  | Koer     | Saksa lambakoer      |
| Bella  | Koer     | Kuldne retriiver     |
| Miisu  | Kass     | Maine Coon           |
| Nurr   | Kass     | Siiami               |
| Musti  | Koer     | Labrador             |
| Lotte  | Koer     | Labrador             |
| Pätu   | Kass     | Maine Coon           |
| Kribu  | Küülik   | Tundmatu             |
| Roki   | Koer     | Labrador             |
| Lumi   | Kass     | Maine Coon           |
| Marta  | Koer     | Labrador             |
| Simba  | Kass     | Maine Coon           |
| Bobby  | Koer     | Labrador             |
| Kiisu  | Kass     | Maine Coon           |
| Rufi   | Koer     | Labrador             |
| Muri   | Koer     | Labrador             |
| Triibu | Kass     | Maine Coon           |
+--------+----------+----------------------+
```

### 2. Kõik loomad koos vastutava töötajaga

INNER JOIN ühendab loomad tootajad tabeliga.

```sql
SELECT loomad.nimi, tootajad.nimi AS tootaja
FROM loomad
INNER JOIN tootajad ON loomad.tootaja_id = tootajad.id;
```

**Tulemus:**

```
+--------+---------------+
| nimi   | tootaja       |
+--------+---------------+
| Bella  | Mari Maasikas |
| Bruno  | Mari Maasikas |
| Musti  | Mari Maasikas |
| Roki   | Mari Maasikas |
| Bobby  | Mari Maasikas |
| Triibu | Mari Maasikas |
| Nurr   | Jaan Tamm     |
| Bella  | Jaan Tamm     |
| Lotte  | Jaan Tamm     |
| Lumi   | Jaan Tamm     |
| Kiisu  | Jaan Tamm     |
| Rex    | Kati Kask     |
| Miisu  | Kati Kask     |
| Pätu   | Kati Kask     |
| Marta  | Kati Kask     |
| Rufi   | Kati Kask     |
| Mimi   | Peeter Pihl   |
| Nurr   | Peeter Pihl   |
| Kribu  | Peeter Pihl   |
| Simba  | Peeter Pihl   |
| Muri   | Peeter Pihl   |
+--------+---------------+
```

### 3. Kõik saadaval olevad loomad koos liigi nimetusega

INNER JOIN liigi lisamiseks ja WHERE filtreerib staatuse järgi saadaval loomad.

```sql
SELECT loomad.nimi, liigid.nimetus AS liik
FROM loomad
INNER JOIN liigid ON loomad.liik_id = liigid.id
WHERE loomad.staatus = 'saadaval';
```

**Tulemus:**

```
+-------+----------+
| nimi  | liik     |
+-------+----------+
| Nurr  | Kass     |
| Rex   | Koer     |
| Mimi  | Kass     |
| Bella | Koer     |
| Musti | Koer     |
| Lotte | Koer     |
| Pätu  | Kass     |
| Kribu | Küülik   |
| Lumi  | Kass     |
| Simba | Kass     |
| Kiisu | Kass     |
| Rufi  | Koer     |
+-------+----------+
```

### 4. Kõik loomad tähestikulises järjekorras koos liigiga (tabeli aliased)

Tabelitele antakse aliased l ja li, ORDER BY järjestab loomade nime järgi.

```sql
SELECT l.nimi, li.nimetus AS liik
FROM loomad l
INNER JOIN liigid li ON l.liik_id = li.id
ORDER BY l.nimi;
```

**Tulemus:**

```
+--------+----------+
| nimi   | liik     |
+--------+----------+
| Bella  | Koer     |
| Bella  | Koer     |
| Bobby  | Koer     |
| Bruno  | Koer     |
| Kiisu  | Kass     |
| Kribu  | Küülik   |
| Lotte  | Koer     |
| Lumi   | Kass     |
| Marta  | Koer     |
| Miisu  | Kass     |
| Mimi   | Kass     |
| Muri   | Koer     |
| Musti  | Koer     |
| Nurr   | Kass     |
| Nurr   | Kass     |
| Pätu   | Kass     |
| Rex    | Koer     |
| Roki   | Koer     |
| Rufi   | Koer     |
| Simba  | Kass     |
| Triibu | Kass     |
+--------+----------+
```

### 5. LEFT JOIN: kõik loomad koos vastutava töötajaga

LEFT JOIN säilitab kõik loomad, ka need, millele töötajat ei leidu (NULL).

```sql
SELECT loomad.nimi AS loom, tootajad.nimi AS tootaja
FROM loomad
LEFT JOIN tootajad ON loomad.tootaja_id = tootajad.id;
```

**Tulemus:**

```
+--------+---------------+
| loom   | tootaja       |
+--------+---------------+
| Bella  | Mari Maasikas |
| Nurr   | Jaan Tamm     |
| Rex    | Kati Kask     |
| Mimi   | Peeter Pihl   |
| Bruno  | Mari Maasikas |
| Bella  | Jaan Tamm     |
| Miisu  | Kati Kask     |
| Nurr   | Peeter Pihl   |
| Musti  | Mari Maasikas |
| Lotte  | Jaan Tamm     |
| Pätu   | Kati Kask     |
| Kribu  | Peeter Pihl   |
| Roki   | Mari Maasikas |
| Lumi   | Jaan Tamm     |
| Marta  | Kati Kask     |
| Simba  | Peeter Pihl   |
| Bobby  | Mari Maasikas |
| Kiisu  | Jaan Tamm     |
| Rufi   | Kati Kask     |
| Muri   | Peeter Pihl   |
| Triibu | Mari Maasikas |
+--------+---------------+
```

### 6. Loomade nimed ja liigid alampäringu abil

Alampäring leiab iga looma kohta liigi nimetuse tabelist liigid.

```sql
SELECT nimi,
(
  SELECT nimetus FROM liigid WHERE liigid.id = loomad.liik_id
) AS liik
FROM loomad;
```

**Tulemus:**

```
+--------+----------+
| nimi   | liik     |
+--------+----------+
| Bella  | Koer     |
| Nurr   | Kass     |
| Rex    | Koer     |
| Mimi   | Kass     |
| Bruno  | Koer     |
| Bella  | Koer     |
| Miisu  | Kass     |
| Nurr   | Kass     |
| Musti  | Koer     |
| Lotte  | Koer     |
| Pätu   | Kass     |
| Kribu  | Küülik   |
| Roki   | Koer     |
| Lumi   | Kass     |
| Marta  | Koer     |
| Simba  | Kass     |
| Bobby  | Koer     |
| Kiisu  | Kass     |
| Rufi   | Koer     |
| Muri   | Koer     |
| Triibu | Kass     |
+--------+----------+
```

### 7. Iga töötaja, tema loomade arv ja loomade nimed ühel real

LEFT JOIN säilitab kõik töötajad, COUNT loeb nende loomad ja GROUP_CONCAT kogub nimed ühte veergu.

```sql
SELECT tootajad.nimi AS tootaja,
COUNT(loomad.id) AS loomade_arv,
GROUP_CONCAT(loomad.nimi SEPARATOR ', ') AS loomad
FROM tootajad
LEFT JOIN loomad ON tootajad.id = loomad.tootaja_id
GROUP BY tootajad.id, tootajad.nimi;
```

**Tulemus:**

```
+---------------+-------------+------------------------------------------+
| tootaja       | loomade_arv | loomad                                   |
+---------------+-------------+------------------------------------------+
| Mari Maasikas |           6 | Bella, Bruno, Musti, Roki, Bobby, Triibu |
| Jaan Tamm     |           5 | Nurr, Bella, Lotte, Lumi, Kiisu          |
| Kati Kask     |           5 | Rex, Miisu, Pätu, Marta, Rufi            |
| Peeter Pihl   |           5 | Mimi, Nurr, Kribu, Simba, Muri           |
+---------------+-------------+------------------------------------------+
```

