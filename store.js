const { reactive, computed } = Vue;

export const store = reactive({
    user: null,

    settings: {
        currency: localStorage.getItem('splitz_currency') || 'EUR',
        language: localStorage.getItem('splitz_language') || 'FR'
    },

    updateSettings(key, value) {
        this.settings[key] = value;
        localStorage.setItem('splitz_' + key, value);
    },

    formatCurrency(amount) {
        let val = parseFloat(amount) || 0;
        let symbol = '€';
        let rate = 1;

        if (this.settings.currency === 'USD') { symbol = '$'; rate = 1.08; }
        else if (this.settings.currency === 'CHF') { symbol = 'CHF'; rate = 0.98; }
        else if (this.settings.currency === 'GBP') { symbol = '£'; rate = 0.86; }
        else if (this.settings.currency === 'CAD') { symbol = '$'; rate = 1.47; }

        let converted = val * rate;

        // Format basé sur le langue sélectionnée
        if (this.settings.currency === 'CHF') {
            return converted.toFixed(2) + ' ' + symbol;
        } else if (symbol === '$' || symbol === '£') {
            return symbol + converted.toFixed(2);
        }

        return converted.toFixed(2).replace('.', ',') + ' ' + symbol;
    },

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
