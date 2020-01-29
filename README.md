# iAmPresent
  
## Doel
De code die je hier vindt is bedoeld als basis voor het samen bouwen en namaken van het presentie registratie systeem zoals je die kent van de projecturen op SCN. Deze code is nodig om een server te simuleren met een API en een database. De API gaan we gebruiken in JavaScript om de pagina's na te bouwen.  
  
## Installeren
Om de code hier te kunnen gebruiken dien je de volgende stappen in de gegeven volgorde uit te voeren:  
  
### 0. Wat dien je al geïnstalleerd te hebben?
* XAMPP of WAMP (Windows)  
* MAMP (Mac OSX)  
* Apache, MySQL en PHP 7.3+ (Linux)  
  
  
    

### 1. Tools installeren
Je dient eerst de volgende tools te installeren:
  
* GitBash  
  Met GitBash installeer je niet alleen a) een linux terminal onder windows, maar b) ook de git commandline tools.  
    
  Je vindt GitBash op: [GitBash](https://git-scm.com/downloads)  
      
* Composer  
Composer is DE package-manager voor PHP. Met Composer installeer je kant en klare PHP pakketten voor extra functionaliteit in je applicatie.  
  
  Je vindt Composer op: [Composer](https://getcomposer.org/Composer-Setup.exe)  
    
  
### 2. Klonen van de code (Windows)
Open GitBash (de terminal) en ga naar de map htdocs in XAMPP of www in WAMP.   
In het voorbeeld hieronder gaan we er vanuit dat je XAMPP hebt geïnstalleerd volgens de standaard manier op de C:-schijf.  
  
  Tik daarvoor de volgende command(s) in de terminal in:
```bash
cd /c/xampp/htdocs
```  
  
Nu gaan we de code uit deze repository (repo) klonen op jouw schijf met de volgende command:  
```bash
git clone https://github.com/johanstr/iampresent.git iampresent
```  
  
### 3. Packages in de kloon installeren
Om gebruik te kunnen maken van de code dienen we nu nog alle PHP-packages te installeren, dit doen we met de volgende twee commands:  
  
```bash
cd iampresent
composer install
```  
  ***Houd het terminalvenster open.***  
  

### 4. Configuratie in de code aanpassen
Voer nu de volgende commando uit in de Git Bash terminal (zorg er wel voor dat je in de map iampresent zit):  
  
```bash
mv env.txt .env
```

### 5. Database aanmaken
a) Open PHPMyAdmin in de browser met de volgende URL (er vanuit gaande dat je XAMPP hebt draaien):  http://localhost/phpmyadmin
  

b) Maak nu een database aan met de naam: **iampresent**  
  
c) Ga nu weer naar je GitBash terminal en tik het volgende in:  
```bash
php artisan migrate
```  
  Hiermee maak je alle tabellen in de database aan.  
    
  
d) Nu gaan we de tabellen in de database vullen met test data, tik daarvoor de volgende command in:  
```bash
php artisan db:seed
```  
  

### 6. Testen of alles werkt
Open je browser en tik in de adresbalk het volgende in:  
http://localhost/iampresent/public  
  
Als het goed is zie je nu een standaard Laravel pagina, maar we zijn er wel vanuit gegaan dat je map iampresent direct in de map htdocs (of www bij WAMP) is geplaatst.
  

