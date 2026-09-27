# Anime & Manga Tracker
Creato da Ajinliart, come progetto indipendente e per divertimento.
Sito web in cui un utente si registra, cerca anime e manga nel catalogo, ne vede i dettagli
e li aggiunge alla propria lista personale con uno stato (*Voglio guardarlo/leggerlo*, *In corso*,
*Completato*), un voto da 1 a 10 e il progresso (episodi/capitoli).

**Stack:** HTML, CSS, JavaScript (vanilla) · PHP 8.1+ · Apache · MySQL/MariaDB

## Struttura

```
public/            ← unica cartella esposta dal web server
  index.php        home (top e più recenti)
  catalog.php      catalogo con ricerca, filtro genere, ordinamento, paginazione
  detail.php       dettagli titolo, voto medio, ranking, gestione lista
  register.php / login.php / logout.php
  profile.php      profilo e lista personale filtrabile per stato
  api/search.php   ricerca live (JSON)
  api/list.php     aggiungi / aggiorna / rimuovi dalla lista (JSON o form classico)
  assets/          CSS e JavaScript
src/               codice PHP
  bootstrap.php    include tutto e avvia la sessione
  config.php       configurazione (copia di config.example.php)
  helpers.php      escape, redirect, flash, costanti TYPES/STATUSES
  auth.php csrf.php db.php
  repositories/    query SQL (utenti, catalogo, liste)
  views/           header, footer, partial
database/
  schema.sql       tabelle
  seed.sql         20 anime, 20 manga, 14 generi
```


**Esponi la cartella `public/`.** Aggiungi in fondo a `httpd.conf` di apache:
   ```
   Alias /tracker ".../public"
   <Directory ".../public">
       Options -Indexes +FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```
   Riavvia Apache e apri <http://localhost/tracker/>.

   > Se usi un percorso diverso da `/tracker` (o un VirtualHost dedicato), aggiorna
   > `base_url` in `src/config.php` (`''` per un VirtualHost sulla root).

## Sicurezza

- Password salvate con `password_hash()` / verificate con `password_verify()`.
- Sessione con cookie `HttpOnly` + `SameSite=Lax`, ID rigenerato al login.
- Token CSRF su tutti i form POST (incluso il logout) e sulle chiamate `fetch`.
- Solo prepared statement PDO; i nomi delle tabelle arrivano dalla whitelist `TYPES`.
- Tutto l'output passa da `e()` (`htmlspecialchars`).
- Validazione lato server di tipo, stato, voto (1–10) e progresso.

## Voto medio e ranking

Il voto medio di un titolo è la media dei voti personali degli utenti. Il ranking è la
posizione del titolo ordinando per voto medio (tra i titoli con almeno `min_votes_for_rank`
voti, configurabile). Con il database appena creato nessun titolo ha voti: registra uno o
più utenti e vota qualche titolo per vederli comparire.
