# Gmail Archiver

L'application Gmail archiver permet de déplacer automatiquement tous les vieux mails d'une boite Gmail vers une autre boite Gmail (ou équivalent en IMAP).
Le but est de gagner de la place dans la boite principale tout en ayant toujours accès aux emails en ligne.

# Pré-requis

 - Activer IMAP sur chaque compte GMAIL
 - [Créer un mot de passe d'application](https://myaccount.google.com/security?gar=1) pour chaque compte GMAIL
 - PHP 8.0 ```update-alternatives --set php /usr/bin/php8.0```
 - Extensions Imap, MbString, Iconv ```aptitude install php8.0-imap php8.0-iconv php8.0-mbstring```

# Installation

 - ```composer install```

# Configuration

Modifier le fichier config.php

```php
<?php
return [
    'from' => [
        'host'     => 'imap.gmail.com',
        'login'    => 'login@gmail.com',
        'password' => '****************'
    ],
    'to'   => [
        'host'     => 'imap.gmail.com',
        'login'    => 'login_archive@gmail.com',
        'password' => '****************'
    ],
    'before' => '2012-01-01',
    'since'  => '2010-01-01',
];
```


# Lancer le transfert

- ``` php run.php```

# Builder un release distribuable

Créer un TAG dans GIT (Ex : 1.0.2) puis lancer la commande, elle va créer un zip dans le dossier build
 - ``` ./release.sh```

# FAQ

 - [Impossible de créer un mot de passe d'application ?](https://support.google.com/accounts/answer/185833)
 - Autoriser le Firewall : ``ufw allow out 993/tcp``

# Licence

[MIT](LICENSE) - Arnaud Lemercier <arnaud@wixiweb.fr>