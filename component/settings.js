const { ref } = Vue;

export default {
    setup() {
        const settingsForm = ref({
            currency: 'EUR',
            language: 'FR',
            emailNotifications: true,
            pushNotifications: false,
            weeklyReport: true
        });

        const settingsMessage = ref({ text: '', type: '' });

        const showMessage = (text, type = 'success') => {
            settingsMessage.value = { text, type };
            setTimeout(() => settingsMessage.value = { text: '', type: '' }, 3000);
        };

        const saveSettings = () => {
            // Simulation API Call
            showMessage("Préférences sauvegardées !");
        };

        const exportData = () => {
            showMessage("Exportation de vos données en cours...", "success");
            // Simulation Export
            setTimeout(() => {
                showMessage("Un email contenant vos données a été envoyé !");
            }, 3000);
        };

        return { settingsForm, settingsMessage, saveSettings, exportData };
    },
    template: `
    <div class="p-10 max-w-[1000px] w-full mx-auto">
        <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 mb-8">Paramètres</h2>
        
        <div v-if="settingsMessage.text" :class="settingsMessage.type === 'error' ? 'bg-red-50 text-red-danger border-red-200' : 'bg-green-50 text-green-success border-green-200'" class="p-4 rounded-xl border font-bold text-sm mb-6 transition-all flex items-center gap-3">
            <span class="material-symbols-outlined">{{ settingsMessage.type === 'error' ? 'error' : 'check_circle' }}</span>
            {{ settingsMessage.text }}
        </div>

        <form @submit.prevent="saveSettings" class="space-y-8">
            <!-- Préférences d'affichage -->
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
                <h3 class="text-xl font-bold text-slate-900 mb-2">Préférences générales</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-xl">Configurez l'application selon vos préférences.</p>

                <div class="space-y-6 max-w-lg">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Devise par défaut</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">payments</span>
                            <select v-model="settingsForm.currency" class="w-full pl-12 pr-10 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold cursor-pointer appearance-none">
                                <option value="EUR">Euro (€)</option>
                                <option value="USD">Dollar Américain ($)</option>
                                <option value="CHF">Franc Suisse (CHF)</option>
                                <option value="GBP">Livre Sterling (£)</option>
                                <option value="CAD">Dollar Canadien ($)</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Langue de l'interface</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">language</span>
                            <select v-model="settingsForm.language" class="w-full pl-12 pr-10 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold cursor-pointer appearance-none">
                                <option value="FR">Français</option>
                                <option value="EN">English</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
                <h3 class="text-xl font-bold text-slate-900 mb-2">Notifications</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-xl">Choisissez comment vous souhaitez être informé de l'activité sur vos comptes.</p>

                <div class="space-y-4 max-w-xl">
                    <label class="flex items-center justify-between p-5 border border-slate-100 rounded-2xl cursor-pointer hover:bg-slate-50 transition-colors">
                        <div class="pr-4">
                            <p class="text-[15px] font-bold text-slate-900">Activité des groupes</p>
                            <p class="text-sm text-slate-500 leading-tight mt-1">M'alerter par email lors d'une nouvelle dépense ou la modification d'un compte.</p>
                        </div>
                        <div class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" v-model="settingsForm.emailNotifications" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </div>
                    </label>

                    <label class="flex items-center justify-between p-5 border border-slate-100 rounded-2xl cursor-pointer hover:bg-slate-50 transition-colors">
                        <div class="pr-4">
                            <p class="text-[15px] font-bold text-slate-900">Rapport hebdomadaire</p>
                            <p class="text-sm text-slate-500 leading-tight mt-1">Recevoir un email récapitulatif de mes soldes chaque semaine.</p>
                        </div>
                        <div class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" v-model="settingsForm.weeklyReport" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </div>
                    </label>

                    <label class="flex items-center justify-between p-5 border border-slate-100 rounded-2xl cursor-pointer hover:bg-slate-50 transition-colors">
                        <div class="pr-4">
                            <p class="text-[15px] font-bold text-slate-900">Notifications navigateur (Push)</p>
                            <p class="text-sm text-slate-500 leading-tight mt-1">Recevoir des alertes en temps réel même lorsque l'application est en arrière-plan.</p>
                        </div>
                        <div class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" v-model="settingsForm.pushNotifications" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Actions de données -->
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 mb-1">Exporter mes données</h3>
                    <p class="text-sm text-slate-500 max-w-lg">Téléchargez une archive contenant l'intégralité de votre historique de dépenses et événements au format CSV.</p>
                </div>
                <button type="button" @click="exportData" class="flex items-center justify-center w-full sm:w-auto gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3.5 rounded-xl font-bold transition-all shrink-0">
                    <span class="material-symbols-outlined text-[20px]">download</span> Exporter
                </button>
            </div>

            <div class="pt-4 pb-12 flex justify-end">
                <button type="submit" class="bg-primary hover:bg-[#5044e6] text-white px-10 py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 flex items-center justify-center w-full sm:w-auto gap-2">
                    <span class="material-symbols-outlined text-[20px]">save</span> Enregistrer les préférences
                </button>
            </div>
        </form>
    </div>`
};
