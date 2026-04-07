const { reactive, computed } = Vue;

export const store = reactive({
    user: null,

    // Actions
    async checkAuth() {
        try {
            const response = await fetch('./api/check_session.php');
            const data = await response.json();
            if (data.isLoggedIn) {
                this.user = data.user;
            } else {
                this.user = null;
            }
        } catch (error) {
            console.error("Auth error:", error);
        }
    },

    async logout() {
        try {
            await fetch('./api/deconnexion.php', { method: 'POST' });
            this.user = null;
        } catch (error) {
            console.error("Logout error:", error);
        }
    }
});

export const userInitials = computed(() => {
    if (!store.user || !store.user.name) return '';
    const names = store.user.name.trim().split(' ');
    if (names.length >= 2) {
        return (names[0][0] + names[names.length - 1][0]).toUpperCase();
    } else if (names.length === 1) {
        return names[0].substring(0, 2).toUpperCase();
    }
    return '';
});
