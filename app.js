const { createApp, ref, computed, onMounted } = Vue;

const app = createApp({
    setup() {
        const user = ref(null);

        // ==========================================
        // GESTION DU PROFIL
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
        const activeSettingsTab = ref('profil');
        const profileForm = ref({ name: '', email: '' });
        const bankForm = ref({ iban: '' });
        const securityForm = ref({ oldPassword: '', newPassword: '', confirmPassword: '' });
        const settingsMessage = ref({ text: '', type: '' });

        const showMessage = (text, type = 'success') => {
            settingsMessage.value = { text, type };
            setTimeout(() => settingsMessage.value = { text: '', type: '' }, 3000);
        };

        const saveProfile = () => { showMessage("Profil mis à jour !"); user.value.name = profileForm.value.name; };
        const saveBank = () => { showMessage("Coordonnées enregistrées !"); };
        const saveSecurity = () => { showMessage("Mot de passe modifié !"); };

        // ==========================================
        // GESTION DES GROUPES (Dashboard)
        // ==========================================
        const groupes = ref([]);
        const soldeTotal = computed(() => groupes.value.reduce((total, groupe) => total + groupe.solde, 0));
        const onTeDoit = computed(() => groupes.value.filter(g => g.solde > 0).reduce((total, groupe) => total + groupe.solde, 0));
        const tuDois = computed(() => groupes.value.filter(g => g.solde < 0).reduce((total, groupe) => total + Math.abs(groupe.solde), 0));

        const isAddGroupModalOpen = ref(false);
        const selectedGroupIcon = ref('home');

        // ==========================================
        // DONNÉES DYNAMIQUES POUR LA PAGE D'UN GROUPE (group_pages.php)
        // ==========================================
        // On initialise à vide ou "chargement"
        const currentGroup = ref({ nom: 'Chargement...', icone: '⏳', participants: 0 });
        const currentGroupExpenses = ref([]);
        const currentGroupStats = ref({ total: 0, unbalanced: 0 });

        // ==========================================
        // MÉTHODES ET ACTIONS (API)
        // ==========================================

        const fetchGroupes = async () => {
            try {
                const apiPath = window.location.pathname.includes('/page/') ? '../api' : './api';
                const response = await fetch(`${apiPath}/get_groupes.php`, { credentials: 'same-origin' });
                const data = await response.json();
                if (data.success) { groupes.value = data.groupes; }
            } catch (error) { console.error("Erreur groupes :", error); }
        };

        // NOUVEAU : Récupère les données d'un groupe précis
        const fetchGroupDetails = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const groupId = urlParams.get('id');

            if (!groupId) return;

            try {
                const apiPath = window.location.pathname.includes('/page/') ? '../api' : './api';
                const response = await fetch(`${apiPath}/get_group_details.php?id=${groupId}`, { credentials: 'same-origin' });
                const data = await response.json();

                if (data.success) {
                    currentGroup.value = data.group;
                    currentGroupExpenses.value = data.expenses;
                    currentGroupStats.value = data.stats;
                } else {
                    console.error("Erreur serveur:", data.error);
                }
            } catch (error) {
                console.error("Erreur détails groupe :", error);
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
                    profileForm.value.name = data.user.name;
                    profileForm.value.email = data.user.email;

                    // On charge les bonnes données selon la page où on se trouve !
                    if (window.location.pathname.includes('group_pages.php')) {
                        fetchGroupDetails();
                    } else {
                        fetchGroupes();
                    }
                }
            } catch (error) { console.error("Erreur auth :", error); }
        };

        const handleLogout = async () => {
            try {
                const apiPath = window.location.pathname.includes('/page/') ? '../api' : './api';
                await fetch(`${apiPath}/deconnexion.php`, { method: 'POST', credentials: 'same-origin' });
                window.location.href = window.location.pathname.includes('/page/') ? '../index.html' : 'index.html';
            } catch (error) { console.error("Erreur déconnexion :", error); }
        };

        // ==========================================
        // CYCLE DE VIE
        // ==========================================
        onMounted(() => {
            checkAuth();

            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam && ['profil', 'paiements', 'securite'].includes(tabParam)) {
                activeSettingsTab.value = tabParam;
            }
        });

        return {
            user, isProfileMenuOpen, userInitials,
            handleLogout, isAddGroupModalOpen, selectedGroupIcon, groupes,
            soldeTotal, onTeDoit, tuDois, activeSettingsTab, profileForm,
            bankForm, securityForm, saveProfile, saveBank, saveSecurity, settingsMessage,
            // Exports pour la page du groupe
            currentGroup, currentGroupExpenses, currentGroupStats
        };
    }
});

app.mount('#app');