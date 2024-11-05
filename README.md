Gemeinschaftliches Projekt für die Uni

# Best Practises und Entwicklungsabläufe
Im Folgenden werden Entwicklungsabläufe und Best Practises definiert, um einen einheitlichen Code zu schaffen.

## Back-End
Folgende Regeln gelten für Arbeiten im Backend:

### 1. Neue Dateien
Neue Dateien im Backend müssen im richtigen Ordner erstellt werden. Der Name sollte einzigartig und eindeutig sein.
Jede Datei (seit 01.11.2024) besitzt folgenden Header mit angepassten Informationen
```php
/**
 *  =================================================================================
 *  Name        :       name-of-the-file.php
 *  Purpose     :       This files does this
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       dd.MM.yyyy
 *  =================================================================================
 *  
 *  USAGE       :
 *  How do I use the file in code
 *  
 *  EXAMPLE     :
 *  Give some example usages
 */
```

Klassen erhalten immer einen Namespace. Dieser ist die Ordnerstruktur ausgehend vom
Root des Backends.
```php
# File is located in ./includes/
namespace Includes;
class MyIncludedClass
{}
```