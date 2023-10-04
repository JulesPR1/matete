# Documentation technique

## MATETE site web

### <ins>Versions utlisées</ins>
>#### PHP <ins>7.4.3</ins>
>------
>#### MySQL <ins>8.0.27</ins>
>------
>#### Composer <ins>2.1.14</ins>
>------
>#### Symfony <ins>4.26.11</ins>

### <ins>Installation des dépendances</ins>

> #### <ins>PHP et MySQL</ins>
>```sudo apt install apache2 php libapache2-mod-php mysql-server php-mysql```<br>
>```sudo apt install apache2 php libapache2-mod-php mariadb-server php-mysql```<br>
>```sudo apt install php-curl php-gd php-intl php-json php-mbstring php-xml php-zip```
>____
> #### <ins> Composer </ins>
>```php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"```<br><br>
>```php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"```<br><br>
>```php composer-setup.php```<br>
>```php -r "unlink('composer-setup.php');"```<br>
>```sudo mv composer.phar /usr/local/bin/composer```
>____
> #### <ins> Symfony cli (optionnel)</ins>
>```wget https://get.symfony.com/cli/installer -O - | bash```

### <ins>Utilisation en local</ins>

>Cloner le projet :<br>
>```git clone https://gitlab.com/Jules_PR/matete.git --branch Jules_PASCUAL-RAMON```
>____
>Se deplacer dans le dossier matete récupéré :<br>
>```cd matete``` <br> ```cd website```
>____
>Modifier le fichier <strong>.env (ligne 30)</strong> en fonction de vos paramètres mysql
>____
>Créer la BDD :<br>
>```symfony console doctrine:database:create```
>____
>Générer et envoyer les migrations :<br>
>```symfony console make:migration```<br> ```symfony console doctrine:migrations:migrate```
>____
>Lancer le serveur Symfony :<br>
>```symfony serve```

### <ins>Fonctionnalités</ins>

>* L'utilisateur peut créer un compte et s'y connecter, il peut également choisir de ne pas se connecter mais il ne pourra alors pas poster d'annonces
>* L'utilisateur peut créer, modifier et supprimer ses annonces
>* L'utilisateur peut voir les annonces des autres utilisateurs ansi que leurs pages détails.
>* Le site présente une carte situant l'utilisateur et les annonces des vendeurs.
>* L'utilisateur est géolocalisé.
>* Les annonces sont géolocalisées.
>* L'utilisateur peut rechercher une annonce via la barre de recherche (nom, description, ville, categorie).
>* L'utilisateur peut trier ses recherches par distance ou catégorie.
>* L'utilisateur peut acceder à la page détails d'une annonce via la carte.
>* L'utilisateur peut ajouter ou retirer des annonces à son panier.
>* L'utilisateur a accès à une page "panier" regroupant ses annonces favorites.
>* L'utilisateur peut imprimer un résumé de son panier
>* ADMIN : un admin peut suspendre un utilisateur ou le supprimer, il peut également ajouter ou supprimer des catégories
>* ADMIN : un admin peut accéder à des graph de statistiques

### <ins>Intégration continue</ins>

L'integration continue est mise en place sur ce projet.
![](/img/ci.PNG)
