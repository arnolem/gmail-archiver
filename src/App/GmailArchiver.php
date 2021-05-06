<?php

declare(strict_types=1);

namespace App;

use Ddeboer\Imap\ConnectionInterface;
use Ddeboer\Imap\Message;
use Ddeboer\Imap\SearchExpression;

class GmailArchiver
{
    protected ConnectionInterface $fromConnexion;
    protected ConnectionInterface $toConnexion;

    public function __construct(ConnectionInterface $fromConnexion, ConnectionInterface $toConnexion)
    {
        $this->fromConnexion = $fromConnexion;
        $this->toConnexion = $toConnexion;
    }

    /**
     * Lance l'archivage des emails correspondants à la recherche.
     */
    public function archive(SearchExpression $search): void
    {
        $trash = $this->fromConnexion->getMailbox('[Gmail]/Corbeille');

        echo 'LIST : FROM_MAILBOXES'.PHP_EOL;
        $mailboxes = $this->fromConnexion->getMailboxes();
        foreach ($mailboxes as $mailBoxFrom) {
            // Skip container-only mailboxes
            // @see https://secure.php.net/manual/fr/function.imap-getmailboxes.php
            if ($mailBoxFrom->getAttributes() & \LATT_NOSELECT) {
                continue;
            }

            if ($mailBoxFrom->getFullEncodedName() === $trash->getFullEncodedName()) {
                echo 'SKIP : On passe la corbeille pour marqué comme supprimé dans la destination ceux qui le sont dans la source'.\PHP_EOL;
                continue;
            }

            echo 'FROM : '.$mailBoxFrom->getName().PHP_EOL;
            $folder = $mailBoxFrom->getName();

            if (!$this->toConnexion->hasMailbox($folder)) {
                // Création du dossier
                try {
                    echo 'CREATE : '.$folder.PHP_EOL;
                    $this->toConnexion->createMailbox($folder);
                } catch (\Throwable $exception) {
                    echo $exception->getMessage().PHP_EOL;
                    echo sprintf("Un dossier ne peut pas finir par un espace. Les caractères / permettent de créer des sous-dossier mais ne doivent pas être entouré d'espaces. Renommer le dossier '%s' avant de relancer l'application.",
                            $folder).PHP_EOL;
                    exit;
                }
            }

            // Connexion à la boite de destination
            echo 'TO : '.$folder.PHP_EOL;
            $mailBoxTo = $this->toConnexion->getMailbox($folder);

            echo 'LIST : FROM_MAIL_MESSAGES'.PHP_EOL;
            $messageIterator = $mailBoxFrom->getMessages($search, \SORTDATE, false);

            /** @var Message $message */
            foreach ($messageIterator as $message) {
                // Récupère la date originale du message
                $internalDate = \DateTimeImmutable::createFromFormat('U', $message->getHeaders()['udate']);

                // Ajoute les marqueurs (Lu, A suivre, Brouillon, ...)
                $options = '';
                $options .= $message->isFlagged() ? '\\Flagged ' : null;
                $options .= $message->isDeleted() ? '\\Deleted ' : null;
                $options .= $message->isSeen() ? '\\Seen ' : null;
                $options .= $message->isDraft() ? '\\Draft ' : null;
                $options .= $message->isAnswered() ? '\\Answered ' : null;
                $options = \trim($options);

                echo 'COPY : ['.$folder.'] '.$message->getSubject().PHP_EOL;
                $success = $mailBoxTo->addMessage($message->getRawMessage(), $options, $internalDate);

                if ($success) {
                    echo 'TRASH : ['.$folder.'] '.$message->getSubject().PHP_EOL;
                    $message->move($trash);
                } else {
                    // On a choisi de déplacer dans la corbeille même en cas d'echec (mais c'est très personnel)
                    echo '* TRASH : ['.$folder.'] '.$message->getSubject().PHP_EOL;
                    $message->move($trash);
                }
            }
        }
    }
}
