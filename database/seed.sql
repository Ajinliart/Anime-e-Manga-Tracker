-- Anime & Manga Tracker - dati di esempio
-- Importare dopo schema.sql

USE anime_tracker;

INSERT INTO genres (id, name) VALUES
  (1, 'Azione'),
  (2, 'Avventura'),
  (3, 'Commedia'),
  (4, 'Drammatico'),
  (5, 'Fantasy'),
  (6, 'Horror'),
  (7, 'Mistero'),
  (8, 'Psicologico'),
  (9, 'Romantico'),
  (10, 'Fantascienza'),
  (11, 'Slice of Life'),
  (12, 'Sport'),
  (13, 'Soprannaturale'),
  (14, 'Thriller');

-- ---------------------------------------------------------------
-- Anime
-- ---------------------------------------------------------------
INSERT INTO anime (id, title, synopsis, episodes, year, status_air, studio) VALUES
  (1,  'Fullmetal Alchemist: Brotherhood', 'I fratelli Edward e Alphonse Elric cercano la Pietra Filosofale per recuperare ciò che hanno perso in un esperimento di alchimia proibita.', 64, 2009, 'concluso', 'Bones'),
  (2,  'Steins;Gate', 'Uno scienziato eccentrico scopre per caso un modo per inviare messaggi nel passato, con conseguenze sempre più pericolose.', 24, 2011, 'concluso', 'White Fox'),
  (3,  'Attack on Titan', 'L''umanità vive rinchiusa dietro enormi mura per proteggersi dai Giganti. Eren giura di sterminarli dopo che le mura vengono violate.', 87, 2013, 'concluso', 'Wit Studio / MAPPA'),
  (4,  'Death Note', 'Uno studente trova un quaderno che uccide chiunque il cui nome venga scritto al suo interno e decide di ripulire il mondo dal crimine.', 37, 2006, 'concluso', 'Madhouse'),
  (5,  'Cowboy Bebop', 'Nel 2071 un gruppo di cacciatori di taglie viaggia per il sistema solare a bordo dell''astronave Bebop.', 26, 1998, 'concluso', 'Sunrise'),
  (6,  'Naruto', 'Naruto Uzumaki, giovane ninja emarginato, sogna di diventare Hokage, il capo del suo villaggio.', 220, 2002, 'concluso', 'Studio Pierrot'),
  (7,  'Naruto Shippuden', 'Naruto torna al villaggio dopo anni di allenamento, deciso a riportare a casa l''amico Sasuke.', 500, 2007, 'concluso', 'Studio Pierrot'),
  (8,  'One Piece', 'Monkey D. Luffy e la sua ciurma solcano i mari alla ricerca del leggendario tesoro One Piece.', NULL, 1999, 'in_corso', 'Toei Animation'),
  (9,  'Hunter x Hunter (2011)', 'Gon Freecss parte per diventare Hunter e ritrovare il padre, affrontando esami e avversari sempre più forti.', 148, 2011, 'concluso', 'Madhouse'),
  (10, 'Your Lie in April', 'Un ex prodigio del pianoforte ritrova la passione per la musica grazie a una violinista esuberante.', 22, 2014, 'concluso', 'A-1 Pictures'),
  (11, 'Haikyu!!', 'Shoyo Hinata, piccolo di statura ma dotato di un salto incredibile, entra nella squadra di pallavolo del liceo Karasuno.', 85, 2014, 'concluso', 'Production I.G'),
  (12, 'Demon Slayer', 'Tanjiro diventa cacciatore di demoni per salvare la sorella Nezuko, trasformata in demone.', NULL, 2019, 'in_corso', 'ufotable'),
  (13, 'Jujutsu Kaisen', 'Yuji Itadori ingerisce un dito maledetto e diventa il contenitore di un potente spirito maledetto.', NULL, 2020, 'in_corso', 'MAPPA'),
  (14, 'Spy x Family', 'Una spia, un''assassina e una bambina telepate formano una famiglia finta, ognuno con i propri segreti.', NULL, 2022, 'in_corso', 'Wit Studio / CloverWorks'),
  (15, 'Mob Psycho 100', 'Un ragazzo dai poteri psichici enormi cerca di vivere una vita normale lavorando per un sensitivo truffatore.', 37, 2016, 'concluso', 'Bones'),
  (16, 'Violet Evergarden', 'Un''ex soldatessa lavora come scrittrice di lettere per comprendere il significato della parola "ti amo".', 13, 2018, 'concluso', 'Kyoto Animation'),
  (17, 'Frieren', 'Dopo la sconfitta del Re dei Demoni, l''elfa Frieren intraprende un nuovo viaggio per capire gli esseri umani.', NULL, 2023, 'in_corso', 'Madhouse'),
  (18, 'Neon Genesis Evangelion', 'Adolescenti pilotano giganteschi robot biologici per difendere l''umanità dagli Angeli.', 26, 1995, 'concluso', 'Gainax'),
  (19, 'Clannad: After Story', 'Tomoya e Nagisa affrontano la vita adulta tra lavoro, famiglia e prove difficili.', 24, 2008, 'concluso', 'Kyoto Animation'),
  (20, 'Code Geass', 'Il principe esiliato Lelouch ottiene il potere di comandare chiunque e guida una ribellione contro l''Impero.', 50, 2006, 'concluso', 'Sunrise');

INSERT INTO anime_genres (anime_id, genre_id) VALUES
  (1,1),(1,2),(1,4),(1,5),
  (2,4),(2,8),(2,10),(2,14),
  (3,1),(3,4),(3,5),(3,7),
  (4,7),(4,8),(4,13),(4,14),
  (5,1),(5,2),(5,4),(5,10),
  (6,1),(6,2),(6,5),
  (7,1),(7,2),(7,5),
  (8,1),(8,2),(8,3),(8,5),
  (9,1),(9,2),(9,5),
  (10,4),(10,9),(10,11),
  (11,3),(11,4),(11,12),
  (12,1),(12,5),(12,13),
  (13,1),(13,5),(13,13),
  (14,1),(14,3),(14,11),
  (15,1),(15,3),(15,13),
  (16,4),(16,5),(16,11),
  (17,2),(17,4),(17,5),
  (18,1),(18,4),(18,8),(18,10),
  (19,4),(19,9),(19,11),(19,13),
  (20,1),(20,4),(20,10),(20,14);

-- ---------------------------------------------------------------
-- Manga
-- ---------------------------------------------------------------
INSERT INTO manga (id, title, synopsis, chapters, volumes, year, status_pub, author) VALUES
  (1,  'Berserk', 'Guts, mercenario solitario armato di un''enorme spada, combatte demoni e il proprio passato in un mondo dark fantasy.', NULL, NULL, 1989, 'in_corso', 'Kentaro Miura'),
  (2,  'One Piece', 'Monkey D. Luffy e la sua ciurma solcano i mari alla ricerca del leggendario tesoro One Piece.', NULL, NULL, 1997, 'in_corso', 'Eiichiro Oda'),
  (3,  'Vagabond', 'La vita romanzata del leggendario spadaccino Miyamoto Musashi, dalla giovinezza violenta alla ricerca dell''illuminazione.', 327, 37, 1998, 'in_pausa', 'Takehiko Inoue'),
  (4,  'Monster', 'Un neurochirurgo giapponese in Germania dà la caccia a un ex paziente che si è rivelato un serial killer.', 162, 18, 1994, 'concluso', 'Naoki Urasawa'),
  (5,  'Fullmetal Alchemist', 'I fratelli Elric cercano la Pietra Filosofale dopo un esperimento di alchimia finito tragicamente.', 108, 27, 2001, 'concluso', 'Hiromu Arakawa'),
  (6,  'Naruto', 'Il giovane ninja Naruto Uzumaki insegue il sogno di diventare Hokage.', 700, 72, 1999, 'concluso', 'Masashi Kishimoto'),
  (7,  'Death Note', 'Uno studente brillante entra in possesso di un quaderno capace di uccidere e sfida il più grande detective del mondo.', 108, 12, 2003, 'concluso', 'Tsugumi Ohba / Takeshi Obata'),
  (8,  'Attack on Titan', 'L''umanità combatte per la sopravvivenza contro i Giganti, mentre emergono verità nascoste sul mondo.', 139, 34, 2009, 'concluso', 'Hajime Isayama'),
  (9,  'Slam Dunk', 'Il teppista Hanamichi Sakuragi entra nella squadra di basket del liceo per fare colpo su una ragazza.', 276, 31, 1990, 'concluso', 'Takehiko Inoue'),
  (10, 'Chainsaw Man', 'Denji, fuso con il suo cane-demone motosega, lavora come cacciatore di demoni per ripagare i debiti.', NULL, NULL, 2018, 'in_corso', 'Tatsuki Fujimoto'),
  (11, 'Jujutsu Kaisen', 'Yuji Itadori entra nel mondo degli stregoni jujutsu dopo aver ingerito un dito maledetto.', 271, 30, 2018, 'concluso', 'Gege Akutami'),
  (12, 'Oyasumi Punpun', 'La crescita difficile di Punpun, dall''infanzia all''età adulta, tra famiglia, amore e depressione.', 147, 13, 2007, 'concluso', 'Inio Asano'),
  (13, 'Nana', 'Due ragazze con lo stesso nome ma caratteri opposti si incontrano per caso e finiscono a vivere insieme a Tokyo.', 84, 21, 2000, 'in_pausa', 'Ai Yazawa'),
  (14, 'Dragon Ball', 'Le avventure di Son Goku, dalla ricerca delle Sfere del Drago fino alle battaglie per salvare la Terra.', 519, 42, 1984, 'concluso', 'Akira Toriyama'),
  (15, 'Tokyo Ghoul', 'Kaneki, sopravvissuto all''attacco di un ghoul, diventa un mezzo-ghoul e deve nascondersi in due mondi.', 143, 14, 2011, 'concluso', 'Sui Ishida'),
  (16, 'Haikyu!!', 'Shoyo Hinata e la squadra di pallavolo del Karasuno puntano ai campionati nazionali.', 402, 45, 2012, 'concluso', 'Haruichi Furudate'),
  (17, 'Yotsuba&!', 'La vita quotidiana della curiosissima bambina Yotsuba, che scopre il mondo con entusiasmo.', NULL, NULL, 2003, 'in_corso', 'Kiyohiko Azuma'),
  (18, '20th Century Boys', 'Un gruppo di amici d''infanzia scopre che un misterioso culto sta realizzando le profezie che avevano inventato da bambini.', 249, 22, 1999, 'concluso', 'Naoki Urasawa'),
  (19, 'Spy x Family', 'Una spia deve formare una famiglia per una missione, senza sapere che moglie e figlia hanno i loro segreti.', NULL, NULL, 2019, 'in_corso', 'Tatsuya Endo'),
  (20, 'Frieren', 'L''elfa maga Frieren viaggia dopo la fine dell''avventura del suo gruppo di eroi, riflettendo sul tempo e sui legami.', NULL, NULL, 2020, 'in_corso', 'Kanehito Yamada / Tsukasa Abe');

INSERT INTO manga_genres (manga_id, genre_id) VALUES
  (1,1),(1,2),(1,4),(1,5),(1,6),
  (2,1),(2,2),(2,3),(2,5),
  (3,1),(3,2),(3,4),
  (4,4),(4,7),(4,8),(4,14),
  (5,1),(5,2),(5,4),(5,5),
  (6,1),(6,2),(6,5),
  (7,7),(7,8),(7,13),(7,14),
  (8,1),(8,4),(8,5),(8,7),
  (9,3),(9,4),(9,12),
  (10,1),(10,6),(10,13),
  (11,1),(11,5),(11,13),
  (12,4),(12,8),(12,11),
  (13,4),(13,9),(13,11),
  (14,1),(14,2),(14,3),
  (15,1),(15,6),(15,8),(15,13),
  (16,3),(16,4),(16,12),
  (17,3),(17,11),
  (18,7),(18,10),(18,14),
  (19,1),(19,3),(19,11),
  (20,2),(20,4),(20,5);
