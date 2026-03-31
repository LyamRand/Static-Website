const { createApp, ref, computed, onMounted } = Vue;

const app = createApp({
    setup() {
        // Stocke les informations de l'utilisateur connecté
        const user = ref(null);

        // ==========================================
        // GESTION DU PROFIL ET MENU DÉROULANT
        // ==========================================
        const isProfileMenuOpen = ref(false);

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
        // GESTION DES PARAMÈTRES
        // ==========================================
        const activeSettingsTab = ref('profil'); // 'profil', 'paiements', 'securite'

        // Données des formulaires de paramètres
        const profileForm = ref({ name: '', email: '' });
        const bankForm = ref({ iban: '' });
        const securityForm = ref({ oldPassword: '', newPassword: '', confirmPassword: '' });

        // Messages de notification
        const settingsMessage = ref({ text: '', type: '' }); // type: 'success' ou 'error'

        const showMessage = (text, type = 'success') => {
            settingsMessage.value = { text, type };
            setTimeout(() => settingsMessage.value = { text: '', type: '' }, 3000);
        };

        const saveProfile = () => {
            // Plus tard, on fera un vrai fetch vers update_profile.php ici
            showMessage("Profil mis à jour avec succès !");
            user.value.name = profileForm.value.name; // Met à jour le nom visuellement partout
        };

        const saveBank = () => {
            showMessage("Coordonnées bancaires enregistrées !");
        };

        const saveSecurity = () => {
            if (securityForm.value.newPassword !== securityForm.value.confirmPassword) {
                showMessage("Les nouveaux mots de passe ne correspondent pas.", "error");
                return;
            }
            showMessage("Mot de passe modifié avec succès !");
            securityForm.value = { oldPassword: '', newPassword: '', confirmPassword: '' }; // On vide les champs
        };

        // ==========================================
        // GESTION DES GROUPES
        // ==========================================
        const groupes = ref([]);

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

        const isAddGroupModalOpen = ref(false);
        const selectedGroupIcon = ref('home');

        // ==========================================
        // DONNÉES TEMPORAIRES POUR LA PAGE D'UN GROUPE (group_pages.php)
        // ==========================================
        const currentGroup = ref({ nom: 'Vacances Ski', icone: '⛷️', participants: 4 });

        const currentGroupExpenses = ref([
            { id: 1, title: 'Restaurant', payer: 'Lucas', amount: 45.00, owed: -11.25, icon: 'restaurant', colorClass: 'bg-red-50 text-red-danger' },
            { id: 2, title: 'Essence', payer: 'Camille', amount: 68.20, owed: -17.05, icon: 'local_gas_station', colorClass: 'bg-yellow-50 text-yellow-500' },
            { id: 3, title: 'Location appartement', payer: 'Moi', amount: 1200.00, owed: 900.00, icon: 'home', colorClass: 'bg-orange-50 text-orange-500' },
            { id: 4, title: 'Courses', payer: 'Marie', amount: 112.40, owed: -28.10, icon: 'shopping_cart', colorClass: 'bg-green-50 text-green-success' }
        ]);

        const currentGroupStats = ref({
            total: 1425.60,
            unbalanced: 843.60
        });

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
                    // On pré-remplit le formulaire avec le nom actuel et l'email !
                    profileForm.value.name = data.user.name;
                    profileForm.value.email = data.user.email;
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

            // Si on arrive sur la page avec un paramètre d'URL (ex: account.php?tab=securite)
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam && ['profil', 'paiements', 'securite'].includes(tabParam)) {
                activeSettingsTab.value = tabParam;
            }
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
            tuDois,
            // Exports pour les paramètres
            activeSettingsTab,
            profileForm,
            bankForm,
            securityForm,
            saveProfile,
            saveBank,
            saveSecurity,
            settingsMessage,
            // Exports des données temporaires pour la page d'un groupe
            currentGroup,
            currentGroupExpenses,
            currentGroupStats
        };
    }
});

app.mount('#app');