const { ref, onMounted } = Vue;
const { useRoute, useRouter } = VueRouter;

export default {
    setup() {
        const route = useRoute();
        const router = useRouter();
        const currentGroup = ref({ nom: 'Chargement...', icone: '⏳', participants: 0 });
        const showExpenseModal = ref(false);
        const currentGroupExpenses = ref([]);
        const currentGroupStats = ref({ total: 0, unbalanced: 0 });

        const fetchGroupDetails = async () => {
            const groupId = route.params.id;
            if (!groupId) return;

            try {
                const response = await fetch('./api/get_group_details.php?id=' + groupId);
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

        const deleteGroup = () => {
            if(confirm("Êtes-vous sûr de vouloir supprimer ce groupe ? Cette action est irréversible.")) {
                alert("Groupe supprimé avec succès ! (Simulation)");
                router.push('/dashboard');
            }
        };

        const saveExpense = () => {
            alert("Dépense ajoutée avec succès ! (Simulation)");
            showExpenseModal.value = false;
        };

        onMounted(() => {
            fetchGroupDetails();
        });

        return { currentGroup, currentGroupExpenses, currentGroupStats, showExpenseModal, deleteGroup, saveExpense };
    },
    template: `
    <div class="p-10 max-w-[1200px] w-full mx-auto">
        <div class="flex items-center text-sm font-medium text-slate-400 mb-6">
            <router-link to="/groupes" class="hover:text-primary transition-colors">Mes groupes</router-link>
            <span class="material-symbols-outlined text-[18px] mx-2">chevron_right</span>
            <span class="text-slate-700 font-bold">{{ currentGroup?.nom || 'Chargement...' }}</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-slate-100 rounded-[32px] flex items-center justify-center text-[45px] shadow-sm">
                    {{ currentGroup?.icone || '📁' }}
                </div>
                <div>
                    <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 leading-tight mb-2">
                        {{ currentGroup?.nom || 'Nom du groupe' }}
                    </h2>
                    <div class="flex items-center gap-4 mt-3">
                        <button class="text-sm font-bold text-primary hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px]">person_add</span> Inviter
                        </button>
                        <div class="w-[1px] h-4 bg-slate-200"></div>
                        <button @click="deleteGroup" class="text-sm font-bold text-red-500 hover:text-red-700 hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px]">delete</span> Supprimer ce groupe
                        </button>
                    </div>
                </div>
            </div>
            <button @click="showExpenseModal = true" class="bg-primary hover:bg-[#5044e6] text-white px-8 py-4 rounded-[16px] font-bold text-lg transition-all shadow-lg shadow-primary/30 flex items-center gap-2">
                <span class="material-symbols-outlined">add_circle</span> Ajouter une dépense
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-6 pr-2">
                    <h3 class="text-2xl font-extrabold text-slate-900">Dépenses récentes</h3>
                    <a href="#" class="text-primary font-bold hover:underline">Voir tout</a>
                </div>

                <div class="flex flex-col gap-4">
                    <div v-for="depense in currentGroupExpenses" :key="depense.id" class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div :class="\`w-14 h-14 rounded-2xl flex items-center justify-center text-[26px] \${depense.colorClass}\`">
                                <span class="material-symbols-outlined">{{ depense.icon }}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-slate-900 leading-tight">{{ depense.title }}</h4>
                                <p class="text-xs font-medium text-slate-400">Payé par <span class="font-bold text-slate-600">{{ depense.payer }}</span></p>
                            </div>
                        </div>
                        <div class="text-right flex items-center gap-4">
                            <div>
                                <p class="text-xl font-black text-slate-900">{{ depense.amount.toFixed(2).replace('.', ',') }} €</p>
                                <p class="text-xs font-medium" :class="depense.owed > 0 ? 'text-green-success font-bold' : 'text-slate-400'">
                                    {{ depense.owed > 0 ? 'On vous doit : ' + depense.owed.toFixed(2).replace('.', ',') + ' €' : 'Votre part : ' + Math.abs(depense.owed).toFixed(2).replace('.', ',') + ' €' }}
                                </p>
                            </div>
                            <span class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">edit</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <!-- Stats Box -->
                <div class="bg-primary rounded-[32px] p-8 text-white shadow-xl shadow-primary/20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-sm font-bold text-primary-100 uppercase tracking-wider mb-2 opacity-90">Dépenses totales du mois</h3>
                    <p class="text-5xl font-black mb-8">{{ currentGroupStats.total.toFixed(2).replace('.', ',') }} €</p>
                    <div>
                        <div class="flex justify-between text-xs font-bold mb-2">
                            <span class="opacity-90">Reste à équilibrer</span>
                            <span>{{ currentGroupStats.unbalanced.toFixed(2).replace('.', ',') }} €</span>
                        </div>
                        <div class="w-full bg-black/20 rounded-full h-2">
                            <div class="bg-white rounded-full h-2" style="width: 75%"></div>
                        </div>
                    </div>
                </div>

                <!-- Chart Box -->
                <div class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm flex flex-col justify-center min-h-[250px]">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-full bg-indigo-50 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                        </div>
                        <h3 class="text-[17px] font-extrabold text-slate-900">Équilibres individuels</h3>
                    </div>
                    <div class="flex-1 flex items-center justify-center text-center">
                        <p class="text-sm font-bold text-slate-400">Le graphique sera généré ici plus tard.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter une dépense -->
        <div v-if="showExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-[32px] p-8 max-w-lg w-full shadow-2xl relative border border-slate-100">
                <button @click="showExpenseModal = false" class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
                <h3 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">receipt_long</span> Nouvelle dépense
                </h3>
                
                <div class="space-y-5 text-left">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Titre de la dépense</label>
                        <input type="text" placeholder="Ex: Courses, Restaurant..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-medium focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Montant (€)</label>
                        <input type="number" step="0.01" placeholder="0.00" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-bold focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Payé par</label>
                        <select class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 font-medium focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all appearance-none cursor-pointer">
                            <option>Moi-même</option>
                            <option>Autre membre...</option>
                        </select>
                    </div>
                    
                    <button @click="saveExpense" class="w-full bg-primary hover:bg-[#5044e6] text-white py-4 rounded-2xl font-bold text-lg mt-4 shadow-lg shadow-primary/30 transition-all">
                        Enregistrer la dépense
                    </button>
                </div>
            </div>
        </div>
    </div>`
};
