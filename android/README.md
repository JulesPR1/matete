# Documentation technique

## MATETE application mobile

### <ins>Versions utlisées</ins>
>#### Android Studio Artic Fox (Java) <ins>2020.3.1</ins>

### <ins>Utilisation en local</ins>
>Pour une utilisation en local, veuillez mettre en place le serveur BDD et l'API, remplacez par la suite les adresses IPv4 des variables urlAnonnces et urlCategories dans le fichier **/app/src/main/java/MapsActivity.java** par votre adresse IPv4.
>Il Faut ensuite mettre en place l'API et la BDD.
>Il est recommandé d'utiliser un émulateur de Pixel 2 pour un meilleur rendu.

### <ins>Contexte</ins>
>C'est une application mobile Android permettant à ses utilisateurs de consulter et filtrer les annonces de vendeurs de fruits et légumes.

### <ins>Fonctionnalités</ins>

>* L'application présente une carte situant l'utilisateur et les annonces des vendeurs.
>* L'utilisateur est géolocalisé.
>* Les annonces sont géolocalisées.
>* L'utilisateur peut rechercher une annonce via la barre de recherche (nom, description, ville, categorie).
>* L'utilisateur peut trier ses recherches par distance ou catégorie.
>* L'utilisateur peut acceder à la page détails d'une annonce en la touchant sur la carte.
>* L'utilisateur peut ajouter ou retirer des annonces à ses favoris.
>* L'utilisateur a accès à une page "favoris" regroupant ses annonces favorites.

### <ins>Fonctionnement</ins>

>* L'application récupère les annonces via l'**[API](/API)**.
>* L'application géolocalise les annonces (avec l'**[api-gouv](https://adresse.data.gouv.fr/api-doc/adresse)**) et les places sur la map.
>* L'application géolocalise l'utilisateur.

### <ins>Aperçus</ins>

>##### Map :
>![](/img/map1.PNG)
>![](/img/map2.PNG)
>
>#### Page Détail :
>![](/img/detail.PNG)
>
>#### Page Favoris :
>![](/img/favoris.png)