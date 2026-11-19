# Lõppversiooni ER-diagramm

Diagramm kirjeldab `www/veebirakendus/sql/schema.sql` faili ja andmebaasi `mario_kapakodu`. Olemasolevale paigaldusele lisab uued seosed `sql/migrations/20261119_course_completion.sql`. Mermaid-diagramm kuvatakse GitHubis.

```mermaid
erDiagram
    species ||--o{ breeds : "sisaldab"
    species ||--o{ animals : "liigitab"
    breeds |o--o{ animals : "täpsustab"
    users |o--o{ animals : "lisas"
    animals |o--o{ contact_requests : "käsitleb"

    species {
        int_unsigned id PK
        varchar name UK
    }
    breeds {
        int_unsigned id PK
        int_unsigned species_id FK
        varchar name "UNIQUE koos species_id-ga"
    }
    users {
        int_unsigned id PK
        varchar name
        varchar email UK
        varchar password_hash
        enum role "admin või toimetaja"
        timestamp created_at
    }
    animals {
        int_unsigned id PK
        varchar name
        int_unsigned species_id FK
        int_unsigned breed_id FK "NULL lubatud"
        int_unsigned created_by FK "NULL lubatud"
        enum sex
        date birth_date
        varchar size "NULL lubatud"
        varchar color "NULL lubatud"
        varchar location "NULL lubatud"
        boolean neutered
        boolean chipped
        varchar short_description
        text description "NULL lubatud"
        enum status
        varchar image "NULL lubatud"
        timestamp created_at
    }
    contact_requests {
        int_unsigned id PK
        int_unsigned animal_id FK "NULL lubatud"
        varchar name
        varchar email
        enum topic
        text message
        enum status
        timestamp created_at
    }
    menu {
        int id PK
        varchar nimetus
        varchar url
        varchar ikoon "NULL lubatud"
        int jarjekord
        boolean aktiivne
        timestamp loodud
    }
```

## Seosed ja kustutamine

| Viitav väli | Viidatav väli | ON DELETE | ON UPDATE |
| --- | --- | --- | --- |
| `breeds.species_id` | `species.id` | CASCADE | RESTRICT |
| `animals.species_id` | `species.id` | RESTRICT | RESTRICT |
| `animals.breed_id` | `breeds.id` | SET NULL | RESTRICT |
| `animals.created_by` | `users.id` | SET NULL | CASCADE |
| `contact_requests.animal_id` | `animals.id` | SET NULL | CASCADE |

Kui SQL-is puudub `ON UPDATE` reegel, kasutab MySQL vaikimisi `NO ACTION` käitumist, mis InnoDB korral vastab `RESTRICT`-ile. Liiki ei saa kustutada, kui sellele viitab loom. Looma tõu kustutamisel jääb loom alles ning tõuviide muutub `NULL`-iks. Kasutaja kustutamisel jääb loom alles; looma kustutamisel jääb kontaktipäring alles.

Üks liik võib sisaldada mitut tõugu ja looma; iga loom kuulub ühele liigile. Tõug, lisaja ja kontaktipäringu loomaseos võivad puududa. `menu` on eraldiseisev tabel ega vaja kunstlikku seost teiste tabelitega. Vanade loomade lisaja on migratsiooni järel teadmata (`NULL`); uue looma lisamisel määrab rakendus lisajaks sisseloginud kasutaja. Päring võib olla üldine või puudutada ühte looma.

## Vastavus kursuse varasema etapiga

| Varasema `kapakodu` skeemi tabel | Lõppversiooni tabel |
| --- | --- |
| `liigid` | `species` |
| `toud` | `breeds` |
| `tootajad` | `users` |
| `loomad` | `animals` |
| `paringud` | `contact_requests` |
| `menu` | `menu` |

`database/kapakodu.sql` ja `database/designer.png` säilitavad ülesannete 2–6 ajaloolise lahenduse. Need ei kirjelda käesolevat lõppskeemi. Näiteks loomade vanus arvutatakse lõppversioonis `birth_date` järgi, mitte ei säilitata eraldi vanuseveerus.
