#  API REST Backend - Laboratoire de Validation de Certificats X.509

> **Cadre Académique :** Université de Kinshasa (UNIKIN)  
> **Faculté :** Sciences et Technologies — Département de Mathématiques et Informatique  
> **Niveau :** Master 1 Informatique (Groupe 09)  
> **Étudiante :** MAKOKO MOPELEMA Sylvie

---

##  Description du Projet
Ce dépôt contient le serveur API RESTful développé avec **Laravel**. Il prend en charge l'analyse syntaxique, le décodage cryptographique des certificats X.509 (PEM) reçus du client, le calcul des empreintes (SHA-256) ainsi que l'évaluation des statuts de sécurité (CRL, OCSP et TLS).

##  Technologies & Sécurité
* **Framework :** Laravel 12 (PHP)[cite: 1]
* **Cryptographie :** Fonctions natives OpenSSL de PHP (`openssl_x509_parse`, `openssl_x509_fingerprint`)[cite: 1]
* **Sécurité Réseau :** Gestion des en-têtes CORS, validation rigoureuse des entrées et gestion des erreurs sans fuite d'informations sensibles[cite: 1]

---

##  Installation et Lancement Local

### Prérequis
* PHP 8.2+ et Composer
* Extension OpenSSL activée

### Étapes d'exécution
1. Cloner le dépôt :
   ```bash
   git clone [https://github.com/makokosylvie1-eng/backend-certificat-x509.git](https://github.com/makokosylvie1-eng/backend-certificat-x509.git)
   cd backend-certificat-x509