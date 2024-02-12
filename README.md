# Application Web MySpots

## Description
Réalisation d’une application web complète permettant la consultation de lieux d’intérêt pour l’univers du skateboard.

## Développement de la partie Front-End
- **Maquettage de l’application** 
- **Interface statique en HTML/CSS** *(voir Annexe n°3)*
- **Interface adaptable aux différents écrans** grâce à l’utilisation de media queries et de breakpoints *(voir Annexe n°4)*
- **Menu burger adapté à la vue mobile** *(voir Annexe n°4)*
- **Mode clair / mode sombre** permettant une personnalisation de l’interface visuelle par l’utilisateur *(voir Annexes n°3 et 4)*
- **Utilisation de Twig pour un système de templating** et création de composants pour la réutilisation de code
- **Interface utilisateur dynamique** avec l’utilisation de Javascript
- **Éléments graphiques particuliers en CSS** (effets de hover, animations du logo, intégration de vidéos)
- **Éléments interactifs en JavaScript** (modales pour afficher les photos en grands, carrousel de photos)
- **Consommation d’API** pour une expérience utilisateur optimale (carte Leaflet, API Geolocation pour exploiter la position, etc.)
- **AJAX pour la fonction de renseignement de champs automatique** avec l’API Nominatim
- **Règle REGEX recommandée par la CNIL** pour la complexité du mot de passe
- **Contraintes de validation des entrées de formulaire**
- **Filtrage des entrées de formulaire**
- **Échappement des caractères contenus dans les variables lors du rendu**
- **Optimisation de l’accessibilité et du référencement** avec l’emploi d’attributs HTML de description (alt, aria-label)
- **Optimisation du référencement** avec l’emploi de meta-description et d’un design responsive

## Développement de la partie Back-End

- **Réalisation d’un modèle conceptuel de données

- **Réalisation d’un modèle logique de données

- **Utilisation de Symfony et du design pattern MVC
- **Emploi d’un système de gestion de base de données relationnel (MySQL)
- **Utilisation de Doctrine pour générer des requêtes SQL en employant des concepts de programmation objet

- **Protection contre la faille XSS en filtrant les entrées de formulaire et en échappant les variables rendues en front.
- **Protection contre la faille CSRF via l’emploi d’un token CSRF permettant de vérifier l’origine des formulaires
- **Protection contre la faille SQL par l’usage de requêtes préparées

Fonctionnalités
- **Réalisation d’une interface d’administration personnalisée (gestion des utilisateurs, modération des contenus)
- **Développement de fonctions de CRUD
- **Réalisation de méthodes personnalisées pour interroger la base de données *(voir Annexe n°12)*
- **Sécurisation des routes (vérification de rôle, conditions multiples)

Sécurité des Informations Sensibles
- **Sécurisation des informations sensibles avec algorithme de hachage du mot de passe (bcrypt) et restrictions d’accès aux données (respect des RGPD)
