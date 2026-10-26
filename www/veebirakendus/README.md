# Mario Käpakodu

## Kirjeldus

Mario Käpakodu on loomade varjupaiga veebirakendus. Külastajad saavad tutvuda varjupaigas olevate loomadega ja administraator saab hallata varjupaiga infot.

Käesolev versioon on Ülesanne 01: kasutajaliides on valmis Bootstrapi abil. PHP-d, MySQL-i ega dünaamilist sisu veel ei ole.

## Lehed

| Fail | Vaade |
| --- | --- |
| `index.html` | Avaleht koos loomade kaartidega |
| `all_animals.html` | Kõigi loomade vaade, otsing, filtrid ja lehekülgede vahetus |
| `animal.html` | Looma detailvaade |
| `contact.html` | Kontaktandmed ja kontaktvorm |
| `login.html` | Sisselogimisvorm |
| `admin/index.html` | Administraatori avaleht |
| `admin/animals.html` | Loomade haldamise vaade |
| `admin/animal_add.html` | Looma lisamise ja muutmise vorm |

## Kaustastruktuur

```
veebirakendus/
├── index.html
├── all_animals.html
├── animal.html
├── contact.html
├── login.html
├── admin/
│   ├── index.html
│   ├── animals.html
│   └── animal_add.html
├── assets/
│   ├── css/style.css
│   └── images/
└── README.md
```

## Tehnoloogiad

- HTML5
- Bootstrap 5.3.8
- Font Awesome 7.0.1

## Valideerimine

Kõik lehed on kontrollitud W3C HTML validaatoriga (https://validator.w3.org/) ja vigu ei ole.

## Hindamismudel

### Front-end ehk avalik osa

- [x] Veebirakendusel on avaleht, kus kuvatakse loomade nimekiri koos piltidega
- [ ] Loomi saab otsida nime järgi
- [ ] Loomi saab filtreerida
- [ ] Looma valimisel kuvatakse tema detailvaade
- [ ] Külastaja saab saata päringu kontaktivormi kaudu
- [x] Veebirakendus kohandub erinevate ekraanisuurustega
- [x] Veebivormid sisaldavad kliendipoolset valideerimist

### Back-end ehk haldus

- [ ] Administraator saab sisse logida
- [ ] Administraator saab hallata loomade andmeid (lisada, muuta ja kustutada CRUD)
- [ ] Administraator saab hallata liikide andmeid
- [ ] Administraator saab hallata tõugude andmeid
- [ ] Administraator saab hallata kasutajate andmeid
- [ ] Administraator saab hallata saabunud kontaktipäringuid
- [ ] Administraator näeb veebirakenduse statistikat

### Tehnilised nõuded

- [x] Veebirakendus kasutab HTML-i, CSS-i ja Bootstrapi
- [ ] Veebirakendus kasutab PHP-d andmete töötlemiseks
- [ ] Andmed salvestatakse MySQL andmebaasi
- [x] Veebirakendus kohandub erinevate ekraanisuurustega (responsive disain)
- [x] Veebivormid sisaldavad kliendipoolset valideerimist
- [ ] Veebirakendus järgib kursuse jooksul õpitud turvalise programmeerimise põhimõtteid
- [x] Lähtekood on korrektselt struktureeritud ja loetav
- [ ] Projekt on hallatud Gitiga ja üles laaditud GitHubi
- [x] Projekt sisaldab ajakohast README.md faili
