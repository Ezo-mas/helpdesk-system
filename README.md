# HelpDesk Sistema

## Apžvalga
HelpDesk sistema yra techninės pagalbos užklausų valdymo sistema, skirta vartotojams, pagalbos darbuotojams ir administratoriams. Sistema leidžia vartotojams pateikti užklausas, sekti jų būseną, o pagalbos darbuotojams - valdyti ir spręsti problemas.

## Funkcijos
- Vartotojų autentifikacija (prisijungimas ir registracija)
- Rolėmis pagrįsta prieigos kontrolė (Administratoriai, Pagalbos darbuotojai, Registruoti vartotojai)
- Užklausų pateikimas ir valdymas
- Administratoriaus skydelis vartotojų valdymui
- Komentarų sistema užklausoms
- Užklausų būsenų valdymas

## Vartotojų rolės

### Administratorius
- Valdo vartotojų teises
- Mato visas užklausas
- Gali priskirti užklausas darbuotojams

### Pagalbos darbuotojas
- Mato jam priskirtas užklausas
- Gali atsakyti klientui žinute
- Gali keisti užklausos būseną

### Prisiregistravęs vartotojas
- Gali pateikti naują užklausą
- Gali matyti savo pateiktas užklausas ir jų būseną
- Gali komentuoti ar papildyti savo užklausą
- Gali vertinti pagalbos kokybę po užklausos uždarymo

## Technologijos
- PHP 8.1
- MySQL 8.0
- Docker & Docker Compose
- Apache
- HTML/CSS/JavaScript

## Instaliavimo instrukcijos

### Reikalavimai
- Docker ir Docker Compose įdiegti jūsų kompiuteryje

### Diegimas
1. Klonuokite repozitoriją:
   ```bash
   git clone https://github.com/Ezo-mas/helpdesk-system.git
   cd helpdesk-system
   ```

2. Nukopijuokite aplinkos failą:
   ```bash
   copy .env.example .env
   ```

3. Paleiskite Docker konteinerius:
   ```bash
   docker-compose up --build
   ```

4. Pasiekite sistemą naršyklėje:
   - Sistema: `http://localhost`
   - phpMyAdmin: `http://localhost:8080`

## Testiniai prisijungimo duomenys

| Rolė | Vartotojo vardas | Slaptažodis |
|------|------------------|-------------|
| Administratorius | admin | password123 |
| Pagalbos darbuotojas | darbuotojas1 | password123 |
| Pagalbos darbuotojas | darbuotojas2 | password123 |
| Vartotojas | vartotojas1 | password123 |
| Vartotojas | vartotojas2 | password123 |

## Autorius
Studentas: [Jūsų vardas ir pavardė]
Kursas: T120B145 IT projektas
KTU, 2025