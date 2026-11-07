# Ülesanne 4 – SQL funktsioonid

Andmebaas `kapakodu`, tabel `loomad`. Andmed on pärast Ülesanne 3 muudatusi (21 looma).

### 1. Varjupaigas olevate loomade koguarv

COUNT(*) loeb kõik read.

```sql
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

### 2. Koerte koguarv

Loeb ainult need read, kus liik on Koer.

```sql
SELECT COUNT(*) AS koeri FROM loomad WHERE liik = 'Koer';
```

**Tulemus:**

```
+-------+
| koeri |
+-------+
|    11 |
+-------+
```

### 3. Kõigi loomade vanuste summa

SUM() liidab vanuse väärtused kokku.

```sql
SELECT SUM(vanus) AS vanuste_summa FROM loomad;
```

**Tulemus:**

```
+---------------+
| vanuste_summa |
+---------------+
|            84 |
+---------------+
```

### 4. Loomade keskmine vanus

AVG() arvutab vanuse keskmise.

```sql
SELECT AVG(vanus) AS keskmine_vanus FROM loomad;
```

**Tulemus:**

```
+----------------+
| keskmine_vanus |
+----------------+
|         4.0000 |
+----------------+
```

### 5. Mitu looma on igast liigist

GROUP BY rühmitab read liigi järgi ja COUNT(*) loeb iga rühma read.

```sql
SELECT liik, COUNT(*) AS arv FROM loomad GROUP BY liik;
```

**Tulemus:**

```
+----------+-----+
| liik     | arv |
+----------+-----+
| Koer     |  11 |
| Kass     |   9 |
| Küülik   |   1 |
+----------+-----+
```

### 6. Iga liigi keskmine vanus

GROUP BY liik ja AVG(vanus) arvutab iga liigi keskmise vanuse.

```sql
SELECT liik, AVG(vanus) AS keskmine_vanus FROM loomad GROUP BY liik;
```

**Tulemus:**

```
+----------+----------------+
| liik     | keskmine_vanus |
+----------+----------------+
| Koer     |         4.7273 |
| Kass     |         3.4444 |
| Küülik   |         1.0000 |
+----------+----------------+
```

### 7. Mitu looma on igas staatuses

GROUP BY rühmitab staatuse järgi ja COUNT(*) loeb iga staatuse loomad.

```sql
SELECT staatus, COUNT(*) AS arv FROM loomad GROUP BY staatus;
```

**Tulemus:**

```
+-------------+-----+
| staatus     | arv |
+-------------+-----+
| broneeritud |   5 |
| saadaval    |  12 |
| hoolduses   |   4 |
+-------------+-----+
```

### 8. Ainult staatused, kus on vähemalt kolm looma

HAVING filtreerib GROUP BY grupid, mille COUNT(*) on vähemalt 3.

```sql
SELECT staatus, COUNT(*) AS arv FROM loomad GROUP BY staatus HAVING COUNT(*) >= 3;
```

**Tulemus:**

```
+-------------+-----+
| staatus     | arv |
+-------------+-----+
| broneeritud |   5 |
| saadaval    |  12 |
| hoolduses   |   4 |
+-------------+-----+
```

