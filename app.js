// ================================================================
// app.js — Logique Vue.js de l'application Splitz
//
// Ce fichier contient UNIQUEMENT la logique JavaScript.
// Le HTML avec les directives Vue.JS (v-if, v-for, @click...) est dans index.html
//
// Navigation : on change la variable "pageCourante" pour afficher une page différente
// ================================================================

const { createApp, ref, onMounted, watch } = Vue;

createApp({
    setup() {

        // " const maVariable = ref('valeur'); " cette ligne sert à créer une variable connectée en direct avec la page web.
        // Je crée une "variable spéciale" qui est réactive donc si je modifie sa valeur dans mon JS, la page web se met à jour toute seule là où elle est affichée.
        // "ref" veut dire que dès que la valeur change dans mon code, la page se met à jour toute seule, sans avoir besoin de la recharger.
        // ref(0) : pour un nombre (le solde total)
        // ref([]) : pour une liste (un tableau vide) qui sera remplie plus tard par le serveur (liste des dépenses)
        // ref(null) : pour signifier l'absence de donnée (ex: utilisateur = null veut dire que personne n'est connecté)
        // ref('') : pour préparer un texte vide (un champ de formulaire)


        // ---- Pages et navigation ----
        const pageCourante = ref('accueil'); // 'accueil', 'auth', 'dashboard', 'groupes', 'groupe', 'parametres'
        const deviseCourante = ref('EUR'); // Devise affichée partout via formaterSomme()

        // ---- Données utilisateur ----
        const utilisateur = ref(null); // null = non connecté

        // ---- Données des groupes ----
        const groupes = ref([]); // Liste de tous les groupes de l'utilisateur
        const groupeActuel = ref(null); // Groupe que l'on consulte en ce moment
        const depenses = ref([]); // Dépenses du groupe actuel
        const statsGroupe = ref({ total: 0, nb_membres: 0, mon_solde: 0 }); // Stats du groupe affiché
        const activiteRecente = ref([]); // 5 dernières dépenses pour le dashboard

        // ---- Soldes du dashboard (calculés depuis groupes) ----
        const soldeTotal = ref(0); // Solde net de l'utilisateur sur tous ses groupes
        const montantDu = ref(0); // Ce que les autres lui doivent
        const montantDette = ref(0); // Ce qu'il doit aux autres

        // ---- Champs des formulaires (liés au HTML avec v-model) ----
        const ongletAuth = ref('connexion'); // 'connexion' ou 'inscription'
        const champEmail = ref('');
        const champPassword = ref('');
        const champNom = ref(''); // Uniquement pour l'inscription
        const champNomGroupe = ref('');
        const champIconeGroupe = ref('🏠');
        const champCodeGroupe = ref('');
        const champMontant = ref('');
        const champDescription = ref('');
        const champPayeurId = ref(null);

        // ---- Visibilité des modals ----
        const afficherFormGroupe = ref(false); // Modal créer/rejoindre un groupe
        const afficherFormDepense = ref(false); // Modal ajouter une dépense
        const afficherModalMdp = ref(false); // Modal mot de passe oublié (simulation)
        const menuMobileOuvert = ref(false); // Menu affiché en mode "mobile" 

        // ---- Messages de retour utilisateur ----
        const message = ref('');
        const messageErreur = ref(false); // true = fond rouge, false = fond vert


        // Fermer le menu mobile automatiquement quand on change de page
        // " watch " est une fonction qui surveille en direct si " pageCourante " change. Si c'est le cas, elle lance automatiquement le code qui se trouve dans { }
        watch(pageCourante, () => {
            menuMobileOuvert.value = false; // "FALSE" veut dire que le menu est fermé
        });

        // ================================================================
        // FONCTIONS UTILITAIRES
        // ================================================================

        // Formate un nombre en monnaie selon la devise choisie dans les paramètres
        // Exemple : formaterSomme(12.5) → "12,50 €"
        function formaterSomme(nombre) {
            return new Intl.NumberFormat('fr-BE', { style: 'currency', currency: deviseCourante.value }).format(nombre || 0); // Intl.NumberFormat est une fonction JS qui permet de formater des nombres selon la langue et la devise choisie
        }

        // Affiche un message à l'écran (vert = succès, rouge = erreur)
        function afficherMessage(texte, estErreur) { // "texte" est le message à afficher, "estErreur" est une variable qui indique si c'est une erreur ou non
            message.value = texte; // .value = pour dire "je veux modifier la valeur de la variable 'message'"
            messageErreur.value = estErreur;
        }


        // ================================================================
        // FONCTION CENTRALE : chargerDashboard()
        // Recharge les groupes + les 3 soldes + l'activité récente.
        // À appeler après chaque action qui modifie la base de données.
        // ================================================================
        function chargerDashboard() {
            // 1. Groupes + calcul des soldes
            fetch('api/groupes.php')
                .then(function (r) { return r.json(); }) // .json() permet de transformer ce que le serveur envoie en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    groupes.value = data; // On range les informations dans "groupes.value".
                    var total = 0, du = 0, dette = 0; // On crée trois variables pour stocker les soldes.
                    data.forEach(function (g) {
                        total += g.solde; // On ajoute le solde de chaque groupe au total.
                        if (g.solde > 0) du += g.solde; // Si le solde est positif, on l'ajoute à "du".
                        if (g.solde < 0) dette -= g.solde; // Si le solde est négatif, on l'ajoute à "dette".
                    });
                    soldeTotal.value = total; // On stocke le total dans "soldeTotal.value".
                    montantDu.value = du;
                    montantDette.value = dette;
                });

            // 2. Activité récente (5 dernières dépenses)
            fetch('api/activite_recente.php')
                .then(function (r) { return r.json(); }) // .json() permet de transformer ce que le serveur envoie en tableau JS utilisable.
                .then(function (data) { activiteRecente.value = data; }) // "data" = les informations récupérées depuis le serveur.
                .catch(function () { activiteRecente.value = []; }); // En cas d'erreur, on vide le tableau.
        }


        // ================================================================
        // AUTHENTIFICATION
        // ================================================================

        function seConnecter() { // on envoie les informations de connexion à l'API
            fetch('api/connexion.php', {
                method: 'POST', // on envoie les données en POST
                headers: { 'Content-Type': 'application/json' }, // on spécifie que les données sont au format JSON
                body: JSON.stringify({ email: champEmail.value, mot_de_passe: champPassword.value }) // on envoie les données de connexion
            })
                .then(function (r) { return r.json(); }) // .json() permet de transformer ce que le serveur envoie en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    if (data.succes) { // Si "succes" est vrai, on continue.
                        utilisateur.value = data.utilisateur; // On stocke l'utilisateur dans "utilisateur.value".
                        allerAuDashboard(); // On redirige vers le dashboard.
                    } else { // Si "succes" est faux, on affiche un message d'erreur.
                        afficherMessage(data.message || data.erreur || "Une erreur est survenue.", true);
                    }
                })
                .catch(function () { afficherMessage("Erreur de connexion au serveur.", true); }); // On affiche un message d'erreur.
        }

        function sInscrire() {
            fetch('api/inscription.php', { // On envoie les informations d'inscription à l'API.
                method: 'POST', // On envoie les données en POST.
                headers: { 'Content-Type': 'application/json' }, // On spécifie que les données sont au format JSON.
                body: JSON.stringify({ nom: champNom.value, email: champEmail.value, mot_de_passe: champPassword.value }) // On envoie les données d'inscription.
            })
                .then(function (r) { return r.json(); }) // (r) = réponse de l'API et return r.json() = on transforme la réponse en tableau JS utilisable.
                // .json() permet de transformer ce que le serveur envoie en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    if (data.succes) { // Si "succes" est vrai, on continue.
                        // SECURITE : On ne connecte plus directement l'utilisateur.
                        // On affiche le message générique (simulation d'email).
                        afficherMessage(data.message, false); // On affiche un message de succès.
                        if (data.contenu_email) { // Si "contenu_email" existe, on l'affiche.
                            console.log("=== EMAIL SIMULÉ ENVOYÉ ==="); // console.log = afficher dans la console du navigateur
                            console.log(data.contenu_email);
                            console.log("===========================");
                        }
                        ongletAuth.value = 'connexion'; // On bascule sur l'onglet de connexion
                        champPassword.value = ''; // On vide le mot de passe par sécurité
                    } else {
                        afficherMessage(data.message || data.erreur || "Une erreur est survenue.", true);
                    }
                })
                .catch(function () { afficherMessage("Erreur de connexion au serveur.", true); }); // En cas d'erreur, on affiche un message d'erreur.
        }

        function seDeconnecter() {
            fetch('api/deconnexion.php', { method: 'POST' })
                .then(function () {
                    utilisateur.value = null; //null = absence de données.
                    groupes.value = []; // [] = absence de liste.
                    groupeActuel.value = null;// null = absence de données.
                    soldeTotal.value = 0; // 0 = valeur de départ du solde total.
                    montantDu.value = 0; // 0 = valeur de départ du montant dû.
                    montantDette.value = 0; // 0 = valeur de départ du montant de la dette.
                    activiteRecente.value = []; // [] = absence de liste.
                    pageCourante.value = 'accueil'; // 'accueil' = valeur de départ de la page courante.
                });
        }

        // ================================================================
        // NAVIGATION
        // ================================================================

        function allerAuDashboard() { // On redirige vers le dashboard.
            message.value = ''; // On vide le message.
            chargerDashboard(); // On charge le dashboard.
            pageCourante.value = 'dashboard'; // On redirige vers le dashboard.
        }

        // On utilise les données déjà en mémoire pour éviter un fetch inutile.
        // Si vides (connexion fraîche), chargerDashboard() les récupère.
        function allerAuxGroupes() { // On redirige vers la page des groupes.
            message.value = ''; // On vide le message.
            if (groupes.value.length === 0) chargerDashboard(); // Si le tableau est vide, on charge le dashboard.
            pageCourante.value = 'groupes'; // On redirige vers la page des groupes.
        }


        // ================================================================
        // PAGE GROUPES
        // ================================================================

        // Charger la liste des groupes 
        function voirGroupe(idGroupe) { // On charge le groupe.
            message.value = ''; // On vide le message.
            fetch('api/groupe_detail.php?group_id=' + idGroupe) // On charge le groupe.
                .then(function (r) { return r.json(); }) // (r) = réponse de l'API et return r.json() = on transforme la réponse en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    groupeActuel.value = data; // On stocke le groupe dans "groupeActuel.value".
                    pageCourante.value = 'groupe'; // On redirige vers la page du groupe.
                    if (utilisateur.value) champPayeurId.value = utilisateur.value.id; // Si "utilisateur.value" existe, on stocke l'utilisateur dans "champPayeurId.value".
                });
            chargerDepenses(idGroupe); // On charge les dépenses.
        }

        // Charger les dépenses 
        function chargerDepenses(idGroupe) { // On charge les dépenses.
            fetch('api/depenses.php?groupe_id=' + idGroupe) // On charge les dépenses.
                .then(function (r) { return r.json(); }) // (r) = réponse de l'API et return r.json() = on transforme la réponse en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    depenses.value = data.liste; // On stocke les dépenses dans "depenses.value".
                    statsGroupe.value = data.stats; // On stocke les statistiques dans "statsGroupe.value".
                });
        }

        // Créer un groupe
        function creerGroupe() { // On crée le groupe.
            fetch('api/creer_groupe.php', {
                method: 'POST', // On envoie les données.
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nom: champNomGroupe.value, icone: champIconeGroupe.value }) // JSON.stringify = transforme les données en chaîne JSON (texte)
            })
                .then(function (r) { return r.json(); }) // (r) = réponse de l'API et return r.json() = on transforme la réponse en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    if (data.succes) { // Si "succes" existe, on affiche un message.
                        afficherFormGroupe.value = false; // On ferme le formulaire.
                        champNomGroupe.value = ''; // On vide le champ.
                        champIconeGroupe.value = '🏠'; // On vide le champ.
                        afficherMessage("Groupe créé avec succès !", false); // On affiche un message.
                        allerAuxGroupes(); // On redirige vers la page des groupes.
                        chargerDashboard(); // Met à jour les compteurs du dashboard
                    } else {
                        afficherMessage(data.message || data.erreur || "Une erreur est survenue.", true);
                    }
                });
        }

        // Rejoindre un groupe
        function rejoindreGroupe() { // On rejoint le groupe.   
            if (!champCodeGroupe.value) return; // Si le champ est vide, on quitte.
            fetch('api/rejoindre_groupe.php', { // On rejoint le groupe.
                method: 'POST', // On envoie les données.
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ code: champCodeGroupe.value.toUpperCase() }) // JSON.stringify = transforme les données en chaîne JSON (texte)
            }) // .then() = on attend la réponse de l'API. ()
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.succes) {
                        afficherFormGroupe.value = false;
                        champCodeGroupe.value = '';
                        afficherMessage("Vous avez rejoint le groupe !", false);
                        allerAuxGroupes();
                        chargerDashboard(); // Met à jour les compteurs du dashboard
                    } else {
                        afficherMessage(data.message || data.erreur || "Une erreur est survenue.", true);
                    }
                });
        }

        // Quitter un groupe ; si plus personne dedans, le groupe est supprimé de la BDD
        function quitterGroupe() { // On quitte le groupe.
            if (!confirm('Voulez-vous vraiment quitter ce groupe ?')) return;
            fetch('api/quitter_groupe.php', {
                method: 'POST', // On envoie les données.
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ groupe_id: groupeActuel.value.id }) // JSON.stringify = transforme les données en chaîne JSON (texte)
            })
                .then(function (r) { return r.json(); }) // (r) = réponse de l'API et return r.json() = on transforme la réponse en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    if (data.succes) { // Si "succes" est vrai, on continue.
                        groupeActuel.value = null; // Fermer la page de détail
                        chargerDashboard();        // Mettre à jour les soldes + la liste
                        allerAuxGroupes();         // Retourner à la liste des groupes
                    } else {
                        afficherMessage(data.message || data.erreur || "Une erreur est survenue.", true);
                    }
                });
        }


        // ================================================================
        // DÉPENSES
        // ================================================================

        // Ouvre le modal et pré-sélectionne l'utilisateur connecté comme payeur
        function ouvrirModalDepense() {
            champMontant.value = ''; // On vide le champ.
            champDescription.value = ''; // On vide le champ.
            if (utilisateur.value) champPayeurId.value = utilisateur.value.id;
            afficherFormDepense.value = true; // On affiche le formulaire.
        }

        function ajouterDepense() { // On ajoute une dépense.
            fetch('api/ajouter_depense.php', {
                method: 'POST', // On envoie les données.
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ // JSON.stringify = transforme les données en chaîne JSON (texte)
                    groupe_id: groupeActuel.value.id,
                    payeur_id: champPayeurId.value,
                    montant: champMontant.value,
                    description: champDescription.value
                })
            })
                .then(function (r) { return r.json(); }) // (r) = réponse de l'API et return r.json() = on transforme la réponse en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    if (data.succes) { // Si "succes" est vrai, on continue.
                        afficherFormDepense.value = false;
                        chargerDepenses(groupeActuel.value.id); // Rafraîchit la liste dans le groupe
                        chargerDashboard();                     // Rafraîchit les soldes
                    } else {
                        afficherMessage(data.message || data.erreur || "Une erreur est survenue.", true);
                    }
                });
        }

        function supprimerDepense(idDepense) { // On supprime une dépense.
            if (!confirm("Supprimer cette dépense ?")) return;
            fetch('api/supprimer_depense.php', {
                method: 'POST', // On envoie les données.
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: idDepense }) // JSON.stringify = transforme les données en chaîne JSON (texte)
            })
                .then(function (r) { return r.json(); }) // (r) = réponse de l'API et return r.json() = on transforme la réponse en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    if (data.succes) { // Si "succes" est vrai, on continue.
                        chargerDepenses(groupeActuel.value.id); // Rafraîchit la liste
                        chargerDashboard();                     // Rafraîchit les soldes
                    }
                });
        }


        // ================================================================
        // DÉMARRAGE : vérifier la session PHP au chargement de la page
        // Si l'utilisateur avait déjà une session (F5, retour sur l'onglet),
        // on le redirige directement vers le dashboard sans repasser par l'accueil.
        // ================================================================
        onMounted(function () {
            fetch('api/verifier_session.php')
                .then(function (r) { return r.json(); }) // (r) = réponse de l'API et return r.json() = on transforme la réponse en tableau JS utilisable.
                .then(function (data) { // "data" = les informations récupérées depuis le serveur.
                    if (data.connecte) {
                        utilisateur.value = data.utilisateur;
                        allerAuDashboard();
                    }
                });
        });


        // ================================================================
        // RETOUR : toutes les variables et fonctions accessibles dans index.html
        // ================================================================

        // return sert à envoyer les variables et les fonctions vers le code HTML
        // Pourquoi mettre tout ça à la fin ? Parce que pour que le code HTML puisse utiliser les fonctions et les variables, il faut qu'elles soient définies avant d'être utilisées.
        // Mais attention, ça ne veut pas dire qu'elles seront utilisées tout de suite ! Elles ne seront utilisées que lorsque le code HTML en aura besoin.

        return {
            // Navigation
            pageCourante, deviseCourante,
            // Données
            utilisateur, groupes, groupeActuel, depenses, statsGroupe, activiteRecente,
            soldeTotal, montantDu, montantDette,
            // Formulaires
            ongletAuth, champEmail, champPassword, champNom,
            champNomGroupe, champIconeGroupe, champCodeGroupe,
            champMontant, champDescription, champPayeurId,
            // Modals & Menu
            afficherFormGroupe, afficherFormDepense, afficherModalMdp, menuMobileOuvert,
            // Messages
            message, messageErreur,
            // Fonctions
            formaterSomme, afficherMessage,
            seConnecter, sInscrire, seDeconnecter,
            allerAuDashboard, allerAuxGroupes, voirGroupe,
            creerGroupe, rejoindreGroupe, quitterGroupe,
            ouvrirModalDepense, ajouterDepense, supprimerDepense
        };
    }
}).mount('#app');
// Fin du fichier
