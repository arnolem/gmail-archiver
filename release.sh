#!/bin/bash

# Récupère le nom du projet (nom du répertoire)
project=${PWD##*/}

# Non du dossier d'export
folder="dist"

# Vérifie si un paramètre est passé
if [ -n "$1" ]
  then
    # Si oui, nous le prenons comme tag
    tag="$1"
  else
    # Sinon, on récupère le dernier tag de GIT
    tag="$(git describe --abbrev=0)"
fi

# On affiche le projet et le tag qui va être construit
echo "------"
echo "- $project"
echo "- Tag : $tag"
echo "------"


# Supprime les anciens builds
rm "$folder" -R -f

# Créé un dossier pour le build
mkdir "$folder/$tag" -p

# Récupère le code source sur git
git archive --format tar.gz -o "$folder"/"$tag"/source.tar.gz "$tag"

# Se place dans le répertoire du build
cd "$folder/$tag"

# Extrait le code source de l'archive
tar -xvf source.tar.gz

# Supprime le fichier d'archive
rm source.tar.gz

# Passe en PHP 8.0 pour faire le build
update-alternatives --set php /usr/bin/php8.0

# Autorise l'exécution de composer en root
export COMPOSER_ALLOW_SUPERUSER=1

# Configuration de production pour le build
export APP_ENV=prod

# Build les dépendances PHP
composer install --prefer-dist --no-interaction --no-dev -o

# Créé le fichier de configuration
mv "config.php.dist" "config.php"

# Supprime les fichiers non utiles en ligne
rm -rf "release.sh"
rm -rf "composer.lock"
rm -rf ".gitignore"

# Créé un zip du répertoire pour le déploiement par un tiers
zip -r ../"$project-$tag.zip" ./

echo "Fin"