
### Todolist

 - [ ] unittest abdeckung weiter erhöhen
     - auslesen von daten aus datastore
     - schreiben in datastore
     - serialisieren und deserialisieren von webfiles / attribute auslesen (kann auch über datastore test abgedeckt werden) - darstellung der einzelnen attributtypen nochmal prüfen, beispiel: bool wird zu 1/0, string bleibt string, etc.
     - genereller test der konventionen von webfiles abtetstet
     - conventions werden eingehalten in allen verfügbaren webfile definitions
 - [ ] exceptionhandling verbessern
 - [ ] bestehende datastores weiter ausprogrammieren / fokus: getLatestWebfiles
 - [ ] in eigenes projekt auslagern: webfiles ui components
     - formulare
     - siteelement/wrappedsiteelement
     - sessionDatastore?/ authtentifizierung
 - [x] MItem in MWebfile überführen
 - [ ] webfile links - MWebfileLink / MLocalWebfileLink / MRemoteWebfileLink / MWebfileReferences
 - [ ] MDatastoreTransfer: inkrementeller Transfer ermöglichen (irgendwo speichern was oder bis zu welchem punkt schon transferiert wurde)
 - [ ] Gaufrette nochmal anschauen als abstraktion für directory datastore
 - [ ] directory-datastore: wenn nciht alle webfiles ein timestamp und eine id haben eine exception werden
 - [ ] nur dateiendungen mit .webfile zulassen bei storeWebfile
 - [] bei invalidem payload kontrollierter rausgehen
 - [ ] MDatabaseDatastore: wie kann man überschneidungen in datenbanken vermeiden? z.b. durch gleiches prefix