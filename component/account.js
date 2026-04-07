import { store, userInitials } from '../store.js';
const { ref, onMounted } = Vue;

export default {
    setup() {
        const activeSettingsTab = ref('profil');
        const profileForm = ref({ name: '', email: '' });
        const bankForm = ref({ iban: '' });
        const securityForm = ref({ oldPassword: '', newPassword: '', confirmPassword: '' });
        const settingsMessage = ref({ text: '', type: '' });

        onMounted(() => {
            if (store.user) {
                profileForm.value.name = store.user.name;
                profileForm.value.email = store.user.email;
            }
        });

        const showMessage = (text, type = 'success') => {
            settingsMessage.value = { text, type };
            setTimeout(() => settingsMessage.value = { text: '', type: '' }, 3000);
        };

        const saveProfile = () => { showMessage("Profil mis à jour !"); if (store.user) store.user.name = profileForm.value.name; };
        const saveBank = () => { showMessage("Coordonnées enregistrées !"); };
        const saveSecurity = () => { showMessage("Mot de passe modifié !"); };

        return { store, userInitials, activeSettingsTab, profileForm, bankForm, securityForm, saveProfile, saveBank, saveSecurity, settingsMessage };
    },
    template: `
    <div class="p-10 max-w-[1000px] w-full mx-auto">
        <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 mb-8">Mon compte</h2>

        <div class="flex gap-2 border-b border-slate-200 mb-8">
            <button @click="activeSettingsTab = 'profil'" :class="activeSettingsTab === 'profil' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'" class="px-6 py-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">person</span> Mon profil
            </button>
            <button @click="activeSettingsTab = 'paiements'" :class="activeSettingsTab === 'paiements' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'" class="px-6 py-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">account_balance</span> Paiements & IBAN
            </button>
            <button @click="activeSettingsTab = 'securite'" :class="activeSettingsTab === 'securite' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'" class="px-6 py-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">lock</span> Sécurité
            </button>
        </div>

        <div v-if="settingsMessage.text" :class="settingsMessage.type === 'error' ? 'bg-red-50 text-red-danger border-red-200' : 'bg-green-50 text-green-success border-green-200'" class="p-4 rounded-xl border font-bold text-sm mb-6 transition-all flex items-center gap-3">
            <span class="material-symbols-outlined">{{ settingsMessage.type === 'error' ? 'error' : 'check_circle' }}</span>
            {{ settingsMessage.text }}
        </div>

        <div v-if="activeSettingsTab === 'profil'" class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
            <h3 class="text-xl font-bold text-slate-900 mb-6">Informations personnelles</h3>

            <div class="flex items-center gap-6 mb-8">
                <div class="w-24 h-24 rounded-full bg-primary/10 text-primary font-black flex items-center justify-center overflow-hidden border border-primary/20 text-3xl">
                    <img v-if="store.user && store.user.photo" :src="store.user.photo" alt="Avatar" class="w-full h-full object-cover">
                    <span v-else-if="userInitials">{{ userInitials }}</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900">{{ store.user?.name }}</h3>
                    <p class="text-sm text-slate-400">Votre avatar est généré automatiquement avec vos initiales.</p>
                </div>
            </div>

            <form @submit.prevent="saveProfile" class="space-y-6 max-w-lg">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom complet</label>
                    <input v-model="profileForm.name" type="text" class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold" required />
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Adresse Email</label>
                    <input v-model="profileForm.email" type="email" placeholder="Sera chargée depuis la BDD..." class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold" readonly/>
                </div>
                <button type="submit" class="bg-primary hover:bg-[#5044e6] text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 mt-4">Enregistrer les modifications</button>
            </form>
        </div>

        <div v-if="activeSettingsTab === 'paiements'" class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
            <h3 class="text-xl font-bold text-slate-900 mb-2">Coordonnées bancaires</h3>
            <p class="text-sm text-slate-500 mb-8 max-w-xl">Renseignez votre IBAN pour que vos amis puissent vous rembourser facilement. Ces informations ne sont visibles que par les membres de vos groupes.</p>

            <form @submit.prevent="saveBank" class="space-y-6 max-w-lg">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Numéro de compte (IBAN)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">account_balance</span>
                        <input v-model="bankForm.iban" type="text" placeholder="BE32 1234 5678 9012" class="w-full pl-12 pr-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold uppercase tracking-wider placeholder-slate-400" />
                    </div>
                </div>
                <button type="submit" class="bg-primary hover:bg-[#5044e6] text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-primary/30">Enregistrer l'IBAN</button>
            </form>
        </div>

        <div v-if="activeSettingsTab === 'securite'" class="space-y-6">
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
                <h3 class="text-xl font-bold text-slate-900 mb-6">Changer le mot de passe</h3>
                <form @submit.prevent="saveSecurity" class="space-y-6 max-w-lg">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mot de passe actuel</label>
                        <input v-model="securityForm.oldPassword" type="password" class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900" required />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nouveau mot de passe</label>
                        <input v-model="securityForm.newPassword" type="password" class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900" required />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Confirmer le nouveau mot de passe</label>
                        <input v-model="securityForm.confirmPassword" type="password" class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900" required />
                    </div>
                    <button type="submit" class="bg-primary hover:bg-[#5044e6] text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 mt-2">Mettre à jour le mot de passe</button>
                </form>
            </div>

            <div class="bg-red-50/50 rounded-[32px] border border-red-100 p-8 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-red-danger mb-1">Supprimer le compte</h3>
                    <p class="text-sm text-slate-500">Cette action est irréversible. Toutes vos données seront effacées.</p>
                </div>
                <button class="bg-white border-2 border-red-danger text-red-danger hover:bg-red-danger hover:text-white px-6 py-3 rounded-xl font-bold transition-all">Supprimer mon compte</button>
            </div>
        </div>
    </div>`
};
