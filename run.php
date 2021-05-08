<?php

declare(strict_types=1);

use App\GmailArchiver;
use Ddeboer\Imap\Search\Date\Before;
use Ddeboer\Imap\Search\Date\Since;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Server;

chdir(__DIR__);
require 'vendor/autoload.php';

// Chargement de la configuration
try {
    $arrayConfig = require 'config.php';
    $config = json_decode(json_encode($arrayConfig, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
} catch (Exception $e) {
    exit('Impossible de charger la configuration');
}

// Connexion au serveur source
$fromServer = new Server($config->from->host);
$fromConnexion = $fromServer->authenticate($config->from->login, $config->from->password);

// Connexion au serveur de destination
$toServer = new Server($config->to->host);
$toConnexion = $toServer->authenticate($config->to->login, $config->to->password);

// Prépare GmailArchiver
$gmailArchiver = new GmailArchiver($fromConnexion, $toConnexion);

// Critères de recherche (mails inférieurs à ..) // before:2010/01/01
$search = new SearchExpression();
$search->addCondition(new Before(new \DateTimeImmutable($config->before)));
$search->addCondition(new Since(new \DateTimeImmutable($config->since)));

$gmailArchiver->archive($search);
