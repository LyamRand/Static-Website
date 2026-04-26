// ================================================================
// app.js — Logique Vue.js de l'application Splitz
//
// Ce fichier contient UNIQUEMENT la logique JavaScript.
// Le HTML avec les directives Vue (v-if, v-for, @click...) est dans index.html.
//
// Navigation : on change la variable "pageCourante" pour afficher
// une page différente. Pas de Vue Router, juste un ref().
// ================================================================

const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {

        // ---- Pages et navigation ----
        const pageCourante = ref('accueil'); // 'accueil', 'auth', 'dashboard', 'groupes', 'groupe', 'parametres'
        const deviseCourante = ref('EUR');   // Devise affichée partout via formaterSomme()

        // ---- Données utilisateur ----
        const utilisateur = ref(null);       // null = non connecté

        // ---- Données des groupes ----
        const groupes = ref([]);             // Liste de tous les groupes de l'utilisateur
        const groupeActuel = ref(null);      // Groupe que l'on consulte en ce moment
        const depenses = ref([]);            // Dépenses du groupe actuel
        const statsGroupe = ref({ total: 0 }); // Total dépensé dans le groupe affiché
        const activiteRecente = ref([]);     // 5 dernières dépenses pour le dashboard

        // ---- Soldes du dashboard (calculés depuis groupes) ----
        const soldeTotal = ref(0);   // Solde net de l'utilisateur sur tous ses groupes
        const montantDu = ref(0);    // Ce que les autres lui doivent
        const montantDette = ref(0); // Ce qu'il doit aux autres

        // ---- Champs des formulaires (liés au HTML avec v-model) ----
        const ongletAuth = ref('connexion'); // 'connexion' ou 'inscription'
        const champEmail = ref('');
        const champPassword = ref('');
        const champNom = ref('');            // Uniquement pour l'inscription
        const champNomGroupe = ref('');
        const champIconeGroupe = ref('🏠');
        const champCodeGroupe = ref('');
        const champMontant = ref('');
        const champDescription = ref('');
        const champPayeurId = ref(null);

        // ---- Visibilité des modals ----
        const afficherFormGroupe = ref(false);  // Modal créer/rejoindre un groupe
        const afficherFormDepense = ref(false); // Modal ajouter une dépense
        const afficherModalMdp = ref(false);    // Modal mot de passe oublié (simulation)

        // ---- Messages de retour utilisateur ----
        const message = ref('');
        const messageErreur = ref(false); // true = fond rouge, false = fond vert


        // ================================================================
        // FONCTIONS UTILITAIRES
        // ================================================================

        // Formate un nombre en monnaie selon la devise choisie dans les paramètres
        // Exemple : formaterSomme(12.5) → "12,50 €"
        function formaterSomme(nombre) {
            return new Intl.NumberFormat('fr-BE', { style: 'currency', currency: deviseCourante.value }).format(nombre || 0);
        }

        // Affiche un message à l'écran (vert = succès, rouge = erreur)
        function afficherMessage(texte, estErreur) {
            message.value = texte;
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
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    groupes.value = data;
                    var total = 0, du = 0, dette = 0;
                    data.forEach(function (g) {
                        total += g.solde;
                        if (g.solde > 0) du += g.solde;
                        if (g.solde < 0) dette -= g.solde;
                    });
                    soldeTotal.value = total;
                    montantDu.value = du;
                    montantDette.value = dette;
                });

            // 2. Activité récente (5 dernières dépenses)
            fetch('api/activite_recente.php')
                .then(function (r) { return r.json(); })
                .then(function (data) { activiteRecente.value = data; })
                .catch(function () { activiteRecente.value = []; });
        }


        // ================================================================
        // AUTHENTIFICATION
        // ================================================================

        function seConnecter() {
            fetch('api/connexion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: champEmail.value, mot_de_passe: champPassword.value })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.succes) {
                        utilisateur.value = data.utilisateur;
                        allerAuDashboard();
                    } else {
                        afficherMessage(data.message, true);
                    }
                })
                .catch(function () { afficherMessage("Erreur de connexion au serveur.", true); });
        }

        function sInscrire() {
            fetch('api/inscription.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nom: champNom.value, email: champEmail.value, mot_de_passe: champPassword.value })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.succes) {
                        utilisateur.value = data.utilisateur;
                        allerAuDashboard();
                    } else {
                        afficherMessage(data.message, true);
                    }
                })
                .catch(function () { afficherMessage("Erreur de connexion au serveur.", true); });
        }

        function seDeconnecter() {
            fetch('api/deconnexion.php', { method: 'POST' })
                .then(function () {
                    // On remet tout à zéro côté Vue
                    utilisateur.value = null;
                    groupes.value = [];
                    groupeActuel.value = null;
                    soldeTotal.value = 0;
                    montantDu.value = 0;
                    montantDette.value = 0;
                    activiteRecente.value = [];
                    pageCourante.value = 'accueil';
                });
        }


        // ================================================================
        // NAVIGATION
        // ================================================================

        function allerAuDashboard() {
            message.value = '';
            chargerDashboard();
            pageCourante.value = 'dashboard';
        }

        // On utilise les données déjà en mémoire pour éviter un fetch inutile.
        // Si vides (connexion fraîche), chargerDashboard() les récupère.
        function allerAuxGroupes() {
            message.value = '';
            if (groupes.value.length === 0) chargerDashboard();
            pageCourante.value = 'groupes';
        }


        // ================================================================
        // GROUPES
        // ================================================================

        function voirGroupe(idGroupe) {
            message.value = '';
            fetch('api/groupe_detail.php?id=' + idGroupe)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    groupeActuel.value = data;
                    pageCourante.value = 'groupe';
                    if (utilisateur.value) champPayeurId.value = utilisateur.value.id;
                });
            chargerDepenses(idGroupe);
        }

        function chargerDepenses(idGroupe) {
            fetch('api/depenses.php?groupe_id=' + idGroupe)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    depenses.value = data.liste;
                    statsGroupe.value = data.stats;
                });
        }

        function creerGroupe() {
            fetch('api/creer_groupe.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nom: champNomGroupe.value, icone: champIconeGroupe.value })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.succes) {
                        afficherFormGroupe.value = false;
                        champNomGroupe.value = '';
                        champIconeGroupe.value = '🏠';
                        afficherMessage("Groupe créé avec succès !", false);
                        allerAuxGroupes();
                        chargerDashboard(); // Met à jour les compteurs du dashboard
                    } else {
                        afficherMessage(data.message, true);
                    }
                });
        }

        function rejoindreGroupe() {
            if (!champCodeGroupe.value) return;
            fetch('api/rejoindre_groupe.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ code: champCodeGroupe.value.toUpperCase() })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.succes) {
                        afficherFormGroupe.value = false;
                        champCodeGroupe.value = '';
                        afficherMessage("Vous avez rejoint le groupe !", false);
                        allerAuxGroupes();
                        chargerDashboard(); // Met à jour les compteurs du dashboard
                    } else {
                        afficherMessage(data.message, true);
                    }
                });
        }

        // Quitter un groupe ; si plus personne dedans, le groupe est supprimé de la BDD
        function quitterGroupe() {
            if (!confirm('Voulez-vous vraiment quitter ce groupe ?')) return;
            fetch('api/quitter_groupe.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ groupe_id: groupeActuel.value.id })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.succes) {
                        groupeActuel.value = null; // Fermer la page de détail
                        chargerDashboard();        // Mettre à jour les soldes + la liste
                        allerAuxGroupes();         // Retourner à la liste des groupes
                    } else {
                        afficherMessage(data.message, true);
                    }
                });
        }


        // ================================================================
        // DÉPENSES
        // ================================================================

        // Ouvre le modal et pré-sélectionne l'utilisateur connecté comme payeur
        function ouvrirModalDepense() {
            champMontant.value = '';
            champDescription.value = '';
            if (utilisateur.value) champPayeurId.value = utilisateur.value.id;
            afficherFormDepense.value = true;
        }

        function ajouterDepense() {
            fetch('api/ajouter_depense.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    groupe_id: groupeActuel.value.id,
                    payeur_id: champPayeurId.value,
                    montant: champMontant.value,
                    description: champDescription.value
                })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.succes) {
                        afficherFormDepense.value = false;
                        chargerDepenses(groupeActuel.value.id); // Rafraîchit la liste dans le groupe
                        chargerDashboard();                     // Rafraîchit les soldes
                    } else {
                        afficherMessage(data.message, true);
                    }
                });
        }

        function supprimerDepense(idDepense) {
            if (!confirm("Supprimer cette dépense ?")) return;
            fetch('api/supprimer_depense.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: idDepense })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.succes) {
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
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.connecte) {
                        utilisateur.value = data.utilisateur;
                        allerAuDashboard();
                    }
                });
        });


        // ================================================================
        // RETOUR : toutes les variables et fonctions accessibles dans index.html
        // ================================================================
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
            // Modals
            afficherFormGroupe, afficherFormDepense, afficherModalMdp,
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
