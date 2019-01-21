[![Build Status](https://travis-ci.org/TimurFlush/phalclate.svg?branch=2.x)](https://travis-ci.org/TimurFlush/phalclate)
[![Coverage Status](https://coveralls.io/repos/github/TimurFlush/phalclate/badge.svg?branch=2.x)](https://coveralls.io/github/TimurFlush/phalclate?branch=2.x)

# Phalclate

Phalclate is library developed for internationalization.
*p.s I will make normal documentation when my library will be the focus of people's attention. For other questions, please contact me in Telegram.*

### Installation

```sh
composer require timur-flush/phalclate:^2.0
```

Via Pdo\Postgresql adapter:

Table structure:

| id(serial) | key(varchar255) | language(char2) | dialect(varchar255) | value(text) |
| ------ | ------ | ------ | ------ | ------ |
| 1 | position| en | GB | centre |
| 3 | hello | en | [NULL] | Hello, %name%. |

```php
use TimurFlush\Phalclate\Adapter\Pdo\Postgresql as PHAdapter;

$adapter = new PHAdapter(
    [
        'host' => '127.0.0.1', #Host
        'port' => 5432, #Port
        'dbname' => '', #Database name
        'username' => '', #Username
        //'password' => '', #Password of user (Optional parameter)
        //'schema' => '', # Database schema (Optional parameter, by default: public)
        'tableName' => 'translations' # Name of table
    ]
);
```

```php
use TimurFlush\Phalclate\Manager as PHManager;
use TimurFlush\Phalclate\Entity\{
    Language as PHLanguage,
    Dialect as PHDialect
};

//Initialize of russian language.
$ru = new PHLanguage('ru');

//Setup of dialect (optional)
$ru->addDialect(
    new PHDialect('ru')
);

//Initialize of english language.
$en = new PHLanguage('en');

//Setup of dialects (optional)
$en->setDialects([
    new PHDialect('US'),
    new PHDialect('GB')
]);

$manager = new PHManager(
    $adapter,
    [
        'baseLanguages' => [$ru, $en], //Base languages (Required, Array of PHLanguage objects)
        'currentLanguage' => 'en', //Current language (Required, One of the above else will be throwed exception)
        'currentDialect' => 'US', //Current dialect (Optional, Any, however if the dialect is not found then it will not be setted.)
        'failOverTranslation' => 'lol' //Fail over translation (Optional)
    ]
);


//Example 1
echo $manager->getTranslation(
    'hello', 
    true, //passed boolean argument is first fetch mode. (Optional, Allows the use of translations from other dialects, by default false)
    ['name' => 'John'] //passed array argument is placeholders. (Optional)
    '' //Passed string argument is custom fail over translation. (Optional)
); // Hello, John.
//Please note that the last 3 arguments can be passed in any order.

//Example 2
echo $manager->getTranslation('notFoundable'); // lol

//Example 3
echo $manager->getTranslation('position', true); //centre

//Example 4
echo $manager->getTranslation('notFoundable', 'kek'); //kek

```


### Requirements

| Name | Version |
| ------ | ------ |
| PHP | ^7.2.0 |
| Phalcon framework | ^3.4.0 |

### Author

Timur Flush.
Telegram: @flush02 or https://t.me/flush02

License
----

BSD-3-Clause