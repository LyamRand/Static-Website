const { createApp, ref } = Vue;
const { createRouter, createWebHistory } = VueRouter;

// Composant Landing (Accueil)
const Landing = {
    template: '#landing-template',
    props: {
        features: {
            type: Array,
            required: true
        },
        footerCols: {
            type: Array,
            required: true
        }
    },
    setup() {
        // Simuler l'état de l'utilisateur (null si non connecté, ou un objet avec 'name' si connecté)
        // Par exemple: const user = ref({ name: 'Lyam' });
        const user = ref(null); 

        const logout = () => {
            user.value = null;
            console.log('Déconnexion réussie');
        };

        return { user, logout };
    }
};

// Configuration du routeur Vue
const routes = [
    { path: '/', component: Landing },
    // Les autres routes (auth, dashboard) pourront être rajoutées ici
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Création de l'application Vue principale
const app = createApp({
    setup() {
        // Données pour la section "Pourquoi choisir Splitz ?"
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

        // Données pour le Footer (pied de page)
        const footerCols = ref([
            {
                title: "PRODUIT",
                links: ["Fonctionnalités", "Sécurité", "Prix", "Avis clients"]
            },
            {
                title: "RESSOURCES",
                links: ["Centre d'aide", "Guides & Tutoriels", "Blog", "API"]
            },
            {
                title: "SOCIÉTÉ",
                links: ["À propos", "Carrières", "Contact", "Mentions légales"]
            }
        ]);

        return {
            features,
            footerCols
        };
    }
});

// Intégration du routeur et montage sur la div #app
app.use(router);
app.mount('#app');
