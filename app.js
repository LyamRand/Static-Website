const { createApp, ref, onMounted } = Vue;

const app = createApp({
    setup() {
        // État de l'utilisateur (null si déconnecté)
        const user = ref(null);

        // Vérification de la session côté PHP
        const checkAuth = async () => {
            try {
                const response = await fetch('./api/check_session.php', {
    method: 'GET',
    credentials: 'same-origin' // INDISPENSABLE : Dit au navigateur d'envoyer le cookie de session à PHP
});
                const data = await response.json();

                if (data.isLoggedIn) {
                    user.value = data.user;
                }
            } catch (error) {
                console.error("Erreur lors de la vérification de session :", error);
            }
        };

        // Fonction pour se déconnecter
// Fonction pour se déconnecter
        const handleLogout = async () => {
            try {
                // 1. On appelle le fichier PHP pour détruire la session et le cookie
                // Remplacez './chemin_vers_le_dossier/deconnexion.php' par votre vrai chemin !
                await fetch('./api/deconnexion.php', {
                    method: 'POST', // ou GET, selon comment vous avez l'habitude de faire
                    credentials: 'same-origin' // Indispensable pour que PHP sache quelle session détruire
                });
                
                // 2. On met l'utilisateur à null pour mettre à jour l'interface HTML
                // (Le bouton "Tableau de bord" va disparaître, "Connexion" va revenir)
                user.value = null; 
                
            } catch (error) {
                console.error("Erreur lors de la déconnexion :", error);
            }
        };

        // Se lance au démarrage
        onMounted(() => {
            checkAuth();
        });

        // Données d'affichage
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

        const footerCols = ref([
            { title: "PRODUIT", links: ["Fonctionnalités", "Sécurité", "Prix", "Avis clients"] },
            { title: "RESSOURCES", links: ["Centre d'aide", "Guides & Tutoriels", "Blog", "API"] },
            { title: "SOCIÉTÉ", links: ["À propos", "Carrières", "Contact", "Mentions légales"] }
        ]);

        return {
            user,
            features,
            footerCols,
            handleLogout
        };
    }
});

app.mount('#app');