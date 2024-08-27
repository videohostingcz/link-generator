# Videohostingcz/link-generator

[![Build Status](https://travis-ci.org/videohostingcz/link-generator.svg?branch=master)](https://travis-ci.org/videohostingcz/link-generator)

Knihovna generující linky pro snadný přístup k souborům uloženým na úložištích VideoHosting.cz.

## Instalace
Nejjednodušší je cesta k instalování knihovny je pomocí utility [Composer](http://getcomposer.org/).

Příklad souboru *composer.json*:

```
{
	...
	"repositories": [
	    {
		"type": "vcs",
		"url": "git@github.com:videohostingcz/link-generator.git"
	    }
	],
	"require": {
        ...
		"videohostingcz/link-generator": "^1.0"
	},
	...
}
```

## Popis rozhraní knihovny
Pro vytvoření URL odkazu k souboru slouží metoda *generate* ze třídy *LinkGenerator*. Tato metodá má tři parametry:
* ***$uri*** - URI k přístup k souboru,
* ***$params*** - pole argumentů (viz sekce Popis dostupných parametrů),
* ***$secretKey*** - hodnota Sdíleného tajemství používaná k určení hodnoty argumentu *signature*. Pro přístup k souborům musí být taho hodnota shodná s hodnotou *Sdíleného tajemství* v nastavení projektu.

### Popis dostupných parametrů 
Popis dostupných klíčů pole argumentů:
* **filename** - Určuje jméno staženého souboru. Používá se pro stahování souboru.

```Příklad: $params['filename'] = 'myvideo.mp4'```
* **expires** - Čas expirace odkazu. Po této době již není přístup k souboru umožněn.

```Příklad: $params['expires'] = 1466436357```
* **limitsize** - Omezení velikosti souboru. Používá se pro přehrání části souboru. Hodnotou je požadovaná velikost v bytech.

```Příklad: $params['limitsize'] = 1024```
* **limitid** - Nastavení vlastního identifikátoru pro počítání naservírovaného množství dat. Použití bez parametru *limitsize* nemá žádný vliv.

```Příklad: $params['limitid'] = 1```
* **rate** - Omezení rychlosti servírování v bytech nebo kilobytech (sufix k) za sekundu.

```Příklad: $params['rate'] = 150k```
* **rateafter** - Nastaví počáteční část v megabytech (sufix m), po níž bude další přenos na klienta omezen. Použití bez parametru *rate* nemá žádný vliv.
Zatím je nastavitelná pouze hodnota 10 MB. Pracujeme na zrušení tohoto omezení.

```Příklad: $params['rateafter'] = 10m```
* **ip** - Omezení přístupu k souboru jen z konkrétní části sítě. Používá se ve spojení s parametrem *ipm*. Hodnotou může být konkrétní IPv4 nebo IPv6.

```Příklad: $params['ip'] = '::1'```
* **ipm** - Specifikuje masku sítě. Používá se ve spojení s parametrem *ip*. Defaultní hodnota pro IPv4 je 24 bitů, pro IPv6 je 64 bitů.

```Příklad: $params['ipm'] = 24```
* **token** - Token je náhodný řetězec vložený do odkazu pro zvýšení bezpečnosti podpisu. Používá se v kombinaci s parametry *signature* a *sparams*.

```Příklad: $params['token'] = '9f4a6a71499c0fc7eca8ea580b1fd44f'```
* **signature** - Podpis slouží k zabezpečení odkazu před jeho modifikací. Parametr je nutné použít společně s parametrem *sparams*. 

```Příklad: $params['signature'] = 'fa6e8177e6c1d83b19c087b8a46b8c5473a8e571'```
* **sparams** - Určuje seznam a pořadí parametrů použitých pro generování podpisu. Povolené parametry jsou *path* (cesta k souboru), *filename*, *token*, *expires*, *limitsize*, *limitid*, *rate*, *ip* a *ipm*. Jednotlivé parametry jsou odděleny čárkou (v URL je čárka nahrazena sekvencí %2C).

```Příklad: $params['sparams'] = 'token,expires'```

### Příklad kódu
Příklad php kódu pro získání URL odkazu k souboru:
```php
    use Videohostingcz\LinkGenerator;

    $generator = new LinkGenerator();

    // view link to public file (no parameters)
    url = $generator->generate('s1.cdn.cz/wq5UXbiW.mp4');

    // download link to public file
    $url = $generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['filename' => 'myvideo.mp4']);

    // view link to private file
    $url = $generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499', 'sparams' => 'token,path'], 'secretKey');

    // preview link to private file
    $url = $generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['limitsize' => 10485760, 'token' => '9f4a6a71499', 'sparams' => 'token,path'], 'secretKey');

    // download link to private file
    $url = $generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['filename' => 'myvideo.mp4', 'token' => '9f4a6a71499', 'sparams' => 'token,path'], 'secretKey');
```


-----
Projekt je uložen na [http://github.com/videohostingcz/link-generator](http://github.com/videohostingcz/link-generator).
