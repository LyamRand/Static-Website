import { store } from '../store.js';
const { ref } = Vue;

export default {
    setup() {
        const settingsForm = ref({ ...store.settings });

        const saveSettings = () => {
            store.updateSettings('currency', settingsForm.value.currency);
            window.location.reload();
        };

        return { settingsForm, saveSettings };
    },
    template: `
    <div class="p-10 max-w-[1000px] w-full mx-auto">
        <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 mb-8">Paramètres</h2>
        
        <div class="space-y-8">
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
                <h3 class="text-xl font-bold text-slate-900 mb-2">Préférences générales</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-xl">Configurez l'application selon vos préférences.</p>

                <form @submit.prevent="saveSettings" class="space-y-6 max-w-lg">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Devise par défaut</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">payments</span>
                            <select v-model="settingsForm.currency" class="w-full pl-12 pr-4 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold cursor-pointer">
                                <option value="EUR">Euro (€)</option>
                                <option value="USD">Dollar Américain ($)</option>
                                <option value="CHF">Franc Suisse (CHF)</option>
                                <option value="GBP">Livre Sterling (£)</option>
                                <option value="CAD">Dollar Canadien ($)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="bg-[#6155F5] hover:bg-[#5044e6] text-white px-8 py-4 rounded-xl font-bold text-base transition-all shadow-lg flex items-center justify-center gap-2 w-full mt-4">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Sauvegarder
                    </button>
                </form>
            </div>
        </div>
    </div>`
};
