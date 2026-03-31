const { createApp, ref, computed, onMounted } = Vue;

const app = createApp({
    setup() {
        // Stocke les informations de l'utilisateur connecté
        const user = ref(null);

        // ==========================================
        // GESTION DU PROFIL (NOUVEAUTÉ)
        // ==========================================
        const isProfileMenuOpen = ref(false); // État du menu déroulant (ouvert/fermé)

        // Calcul automatique des initiales (ex: Merwan Abzar -> MA)
        const userInitials = computed(() => {
            if (!user.value || !user.value.name) return '';
            const names = user.value.name.trim().split(' ');
            if (names.length >= 2) {
                return (names[0][0] + names[names.length - 1][0]).toUpperCase();
            } else if (names.length === 1) {
                return names[0].substring(0, 2).toUpperCase();
            }
            return '';
        });

        // ==========================================
        // GESTION DES GROUPES
        // ==========================================
        const groupes = ref([]);

        // Calculs automatiques des soldes basés sur les vrais groupes
        const soldeTotal = computed(() => groupes.value.reduce((total, groupe) => total + groupe.solde, 0));
        const onTeDoit = computed(() => groupes.value.filter(g => g.solde > 0).reduce((total, groupe) => total + groupe.solde, 0));
        const tuDois = computed(() => groupes.value.filter(g => g.solde < 0).reduce((total, groupe) => total + Math.abs(groupe.solde), 0));

        // Données d'affichage statiques (Accueil & Footer)
        const features = ref([
            { icon: "💰", title: "Suivi des dépenses", desc: "Ajoutez vos dépenses en quelques clics et gardez une trace de chaque transaction en toute simplicité." },
            { icon: "🔄", title: "Équilibre automatique", desc: "Splitz calcule instantanément qui doit combien à qui, en minimisant le nombre de transferts nécessaires." },
            { icon: "👥", title: "Gestion de groupes illimités", desc: "Créez des groupes pour vos voyages, colocations ou événements entre amis, sans aucune limite." }
        ]);

        const footerCols = ref([
            { title: "PRODUIT", links: ["Fonctionnalités", "Sécurité", "Prix", "Avis clients"] },
            { title: "RESSOURCES", links: ["Centre d'aide", "Guides & Tutoriels", "Blog", "API"] },
            { title: "SOCIÉTÉ", links: ["À propos", "Carrières", "Contact", "Mentions légales"] }
        ]);

        // État de la modale "Ajouter un groupe"
        const isAddGroupModalOpen = ref(false);
        const selectedGroupIcon = ref('home');

        // ==========================================
        // MÉTHODES ET ACTIONS (API)
        // ==========================================

        const fetchGroupes = async () => {
            try {
                const apiPath = window.location.pathname.includes('/page/') ? '../api' : './api';
                const response = await fetch(`${apiPath}/get_groupes.php`, { credentials: 'same-origin' });
                const data = await response.json();

                if (data.success) {
                    groupes.value = data.groupes;
                }
            } catch (error) {
                console.error("Erreur lors de la récupération des groupes :", error);
            }
        };

        const checkAuth = async () => {
            try {
                const apiPath = window.location.pathname.includes('/page/') ? '../api' : './api';
                const response = await fetch(`${apiPath}/check_session.php`, {
                    method: 'GET',
                    credentials: 'same-origin'
                });
                const data = await response.json();

                if (data.isLoggedIn) {
                    user.value = data.user;
                    fetchGroupes();
                }
            } catch (error) {
                console.error("Erreur lors de la vérification de la session :", error);
            }
        };

        const handleLogout = async () => {
            try {
                const apiPath = window.location.pathname.includes('/page/') ? '../api' : './api';
                await fetch(`${apiPath}/deconnexion.php`, {
                    method: 'POST',
                    credentials: 'same-origin'
                });

                user.value = null;
                groupes.value = [];

                if (window.location.pathname.includes('/page/')) {
                    window.location.href = '../index.html';
                }
            } catch (error) {
                console.error("Erreur lors de la déconnexion :", error);
            }
        };

        // ==========================================
        // CYCLE DE VIE
        // ==========================================
        onMounted(() => {
            checkAuth();
        });

        // ==========================================
        // EXPORT
        // ==========================================
        return {
            user,
            isProfileMenuOpen,
            userInitials,
            features,
            footerCols,
            handleLogout,
            isAddGroupModalOpen,
            selectedGroupIcon,
            groupes,
            soldeTotal,
            onTeDoit,
            tuDois
        };
    }
});

app.mount('#app');