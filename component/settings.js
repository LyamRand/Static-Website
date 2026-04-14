import { store } from '../store.js';
const { ref, watch } = Vue;

export default {
    setup() {
        const settingsForm = ref({ ...store.settings });

        watch(() => settingsForm.value.currency, (newVal) => store.updateSettings('currency', newVal));
        watch(() => settingsForm.value.language, (newVal) => store.updateSettings('language', newVal));

        return { settingsForm, store };
    },
    template: `
    <div class="p-10 max-w-[1000px] w-full mx-auto">
        <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 mb-8">Paramètres</h2>
        
        <div class="space-y-8">
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
                <h3 class="text-xl font-bold text-slate-900 mb-2">Préférences générales</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-xl">Configurez l'application selon vos préférences.</p>

                <div class="space-y-6 max-w-lg">
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

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Langue de l'interface</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">language</span>
                            <select v-model="settingsForm.language" class="w-full pl-12 pr-4 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold cursor-pointer">
                                <option value="FR">Français</option>
                                <option value="EN">English</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>`
};
