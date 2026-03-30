const { createApp, ref, onMounted } = Vue;

const app = createApp({
    setup() {

        // Stocke les informations de l'utilisateur connecté (null par défaut s'il est déconnecté)
        const user = ref(null);

        // Données d'affichage : Liste des fonctionnalités affichées sur la page d'accueil
        const features = ref([
            {
                icon: "💰",
                title: "Suivi des dépenses",
                desc: "Ajoutez vos dépenses en quelques clics et gardez une trace de chaque transaction en toute simplicité."
            },
            {
                icon: "🔄",
                title: "Équilibre automatique",
                desc: "Splitz calcule instantanément qui doit combien à qui, en minimisant le nombre de transferts nécessaires."
            },
            {
                icon: "👥",
                title: "Gestion de groupes illimités",
                desc: "Créez des groupes pour vos voyages, colocations ou événements entre amis, sans aucune limite."
            }
        ]);

        // Données d'affichage : Liens affichés dans le pied de page (footer)
        const footerCols = ref([
            { title: "PRODUIT", links: ["Fonctionnalités", "Sécurité", "Prix", "Avis clients"] },
            { title: "RESSOURCES", links: ["Centre d'aide", "Guides & Tutoriels", "Blog", "API"] },
            { title: "SOCIÉTÉ", links: ["À propos", "Carrières", "Contact", "Mentions légales"] }
        ]);

        // État de la modale "Ajouter un groupe"
        const isAddGroupModalOpen = ref(false);
        const selectedGroupIcon = ref('home');

        // ==========================================
        // 2. MÉTHODES ET ACTIONS
        // ==========================================

        /**
         * Vérifie auprès du serveur (PHP) si une session utilisateur est active.
         * Met à jour la variable `user` si l'utilisateur est connecté.
         */
        const checkAuth = async () => {
            try {
                // Determine le bon chemin vers l'API selon le dossier actuel
                const apiPath = window.location.pathname.includes('/page/') ? '../api' : './api';

                const response = await fetch(`${apiPath}/check_session.php`, {
                    method: 'GET',
                    credentials: 'same-origin' // INDISPENSABLE : Envoie le cookie de session à PHP
                });
                const data = await response.json();

                if (data.isLoggedIn) {
                    user.value = data.user;
                }
            } catch (error) {
                console.error("Erreur lors de la vérification de la session :", error);
            }
        };

        /**
         * Gère la déconnexion de l'utilisateur.
         * Détruit la session côté PHP et réinitialise l'état utilisateur côté Vue.js.
         */
        const handleLogout = async () => {
            try {
                const apiPath = window.location.pathname.includes('/page/') ? '../api' : './api';

                // Appel à l'API PHP pour détruire la session et le cookie
                await fetch(`${apiPath}/deconnexion.php`, {
                    method: 'POST',
                    credentials: 'same-origin' // INDISPENSABLE : Indique à PHP quelle session détruire
                });

                // On remet l'utilisateur à null pour mettre à jour l'interface HTML
                // (ex: Le bouton "Tableau de bord" va disparaître, "Connexion" va revenir)
                user.value = null;

            } catch (error) {
                console.error("Erreur lors de la déconnexion :", error);
            }
        };

        // ==========================================
        // 3. CYCLE DE VIE (Lifecycle hooks)
        // ==========================================

        // S'exécute automatiquement une fois que l'application est "montée" (chargée)
        onMounted(() => {
            checkAuth(); // Vérifie si l'utilisateur est connecté dès le chargement de la page
        });

        // ==========================================
        // 4. EXPORT DES VARIABLES ET FONCTIONS
        // ==========================================

        // Tout ce qui est retourné ici sera accessible dans le HTML (le template)
        return {
            user,
            features,
            footerCols,
            handleLogout,
            isAddGroupModalOpen,
            selectedGroupIcon
            // Note : checkAuth n'est pas retourné car il n'est utilisé que dans le setup() 
            // et n'a pas besoin d'être appelé depuis un bouton sur la page HTML.
        };
    }
});

// Monte l'application Vue.js sur l'élément de la page HTML qui a l'ID `#app`
app.mount('#app');