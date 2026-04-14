import { store } from '../store.js';
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

        // Form state for expense
        const expenseForm = ref({
            montant: '',
            description: '',
            categorie: 'Repas',
            payeur: null,
            partageOption: 'equal', // 'equal' or 'custom'
            customPercentages: {}
        });

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

        const deleteGroup = async () => {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce groupe ? Cette action est irréversible.")) {
                try {
                    const res = await fetch('./api/delete_group.php', {
                        method: 'POST',
                        body: JSON.stringify({ group_id: route.params.id })
                    });
                    const data = await res.json();
                    if (data.success) {
                        router.push('/dashboard');
                    } else {
                        alert(data.error);
                    }
                } catch (e) {
                    console.error(e);
                }
            }
        };

        const saveExpense = async () => {
            if (!expenseForm.value.montant || !expenseForm.value.description || !expenseForm.value.payeur) {
                alert("Veuillez remplir tous les champs.");
                return;
            }
            try {
                const res = await fetch('./api/add_expense.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        group_id: route.params.id,
                        montant: expenseForm.value.montant,
                        description: expenseForm.value.description,
                        categorie: expenseForm.value.categorie,
                        payeur: expenseForm.value.payeur,
                        partageOption: expenseForm.value.partageOption,
                        customPercentages: expenseForm.value.customPercentages
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showExpenseModal.value = false;
                    expenseForm.value.montant = '';
                    expenseForm.value.description = '';
                    expenseForm.value.customPercentages = {};
                    expenseForm.value.partageOption = 'equal';
                    fetchGroupDetails();
                } else {
                    alert(data.error);
                }
            } catch (e) { console.error(e); }
        };

        const deleteExpense = async (id) => {
            if (confirm("Supprimer cette dépense ?")) {
                try {
                    const res = await fetch('./api/delete_expense.php', {
                        method: 'POST',
                        body: JSON.stringify({ expense_id: id })
                    });
                    const data = await res.json();
                    if (data.success) {
                        fetchGroupDetails();
                    } else {
                        alert(data.error);
                    }
                } catch (e) { console.error(e); }
            }
        };

        const openExpenseModal = () => {
            expenseForm.value.payeur = store.user?.id; // default to me
            showExpenseModal.value = true;
        };

        onMounted(() => {
            fetchGroupDetails();
        });

        return { store, currentGroup, currentGroupExpenses, currentGroupStats, showExpenseModal, expenseForm, saveExpense, deleteExpense, deleteGroup, openExpenseModal };
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
                    <div class="flex items-center gap-4 mt-4">
                        <span class="text-sm font-bold text-slate-700 ml-1">Code : <span class="text-[#6155F5] font-black">{{ currentGroup?.code || '...' }}</span></span>

                        <div class="w-[1px] h-4 bg-slate-200 mx-1"></div>
                        <button @click="deleteGroup" class="text-sm font-bold text-red-500 hover:text-red-700 hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px]">delete</span> Supprimer
                        </button>
                    </div>
                </div>
            </div>
            <button @click="openExpenseModal" class="bg-primary hover:bg-[#5044e6] text-white px-8 py-4 rounded-[16px] font-bold text-lg transition-all shadow-lg shadow-primary/30 flex items-center gap-2">
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
                                <span>{{ depense.icon }}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-slate-900 leading-tight">{{ depense.title }}</h4>
                                <p class="text-xs font-medium text-slate-400">Payé par <span class="font-bold text-slate-600">{{ depense.payer }}</span></p>
                            </div>
                        </div>
                        <div class="text-right flex items-center gap-4">
                            <div>
                                <p class="text-xl font-black text-slate-900">{{ store.formatCurrency(depense.amount) }}</p>
                                <p class="text-xs font-medium" :class="depense.owed > 0 ? 'text-green-success font-bold' : 'text-slate-400'">
                                    {{ depense.owed > 0 ? 'On vous doit : ' + store.formatCurrency(Math.abs(depense.owed)) : 'Votre part : ' + store.formatCurrency(Math.abs(depense.owed)) }}
                                </p>
                            </div>
                            <button @click="deleteExpense(depense.id)" class="material-symbols-outlined text-slate-300 hover:text-red-danger transition-colors cursor-pointer">delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <!-- Stats Box -->
                <div class="bg-primary rounded-[32px] p-8 text-white shadow-xl shadow-primary/20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-sm font-bold text-primary-100 uppercase tracking-wider mb-2 opacity-90">Dépenses totales du mois</h3>
                    <p class="text-5xl font-black mb-2">{{ store.formatCurrency(currentGroupStats.total) }}</p>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter une dépense -->
        <div v-if="showExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-[32px] p-8 max-w-[650px] w-full shadow-2xl relative border border-slate-100">
                <h3 class="text-3xl font-extrabold text-slate-900 mb-6">Ajouter une dépense</h3>
                <hr class="border-slate-100 mb-6" />
                
                <div class="space-y-6 text-left">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Montant</label>
                            <input type="number" step="0.01" v-model="expenseForm.montant" placeholder="Montant dépensé" class="w-full bg-slate-100 border-none rounded-2xl px-5 py-4 text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                            <input type="text" v-model="expenseForm.description" placeholder="Qu'avez-vous payé ?" class="w-full bg-slate-100 border-none rounded-2xl px-5 py-4 text-slate-900 font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Catégorie</label>
                        <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                            <label v-for="(cat, idx) in [
                                {id: 'Repas', icon: '🍔'},
                                {id: 'Transport', icon: '🚘'},
                                {id: 'Logement', icon: '🏠'},
                                {id: 'Courses', icon: '🛒'},
                                {id: 'Autres', icon: '💬'}
                            ]" :key="cat.id" class="cursor-pointer flex-shrink-0">
                                <input type="radio" :value="cat.id" v-model="expenseForm.categorie" class="peer hidden">
                                <div class="flex flex-col items-center justify-center w-[85px] h-[85px] gap-1 rounded-2xl transition-all bg-slate-100 border-2 border-transparent text-slate-500 hover:bg-slate-200 peer-checked:bg-white peer-checked:border-[#6155F5] peer-checked:text-[#6155F5] peer-checked:shadow-sm">
                                    <span class="text-[28px]">{{ cat.icon }}</span>
                                    <span class="text-xs font-bold">{{ cat.id }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Payé par</label>
                        <div class="flex flex-wrap gap-3">
                            <label v-for="(p, idx) in currentGroup.participantsInfo" :key="p.id" class="cursor-pointer">
                                <input type="radio" :value="p.id" v-model="expenseForm.payeur" class="peer hidden">
                                <div class="flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 peer-checked:bg-[#6155F5]/20 peer-checked:border-[#6155F5] peer-checked:text-[#6155F5] transition-all">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] bg-slate-200 peer-checked:bg-[#6155F5] peer-checked:text-white"
                                        :class="['bg-blue-100 text-blue-600', 'bg-green-100 text-green-600', 'bg-yellow-100 text-yellow-600', 'bg-red-100 text-red-600'][idx % 4]">
                                        {{ p.name.charAt(0).toUpperCase() }}
                                    </div>
                                    {{ store.user && p.id === store.user.id ? 'Moi' : p.name }}
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Options de partage</label>
                        <div class="flex rounded-2xl bg-slate-100 p-1.5 mb-6">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" value="equal" v-model="expenseForm.partageOption" class="peer hidden">
                                <div class="text-center py-3 rounded-xl font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-slate-900 peer-checked:shadow-sm transition-all">
                                    Partager équitablement
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" value="custom" v-model="expenseForm.partageOption" class="peer hidden">
                                <div class="text-center py-3 rounded-xl font-bold text-sm text-slate-500 peer-checked:bg-white peer-checked:text-slate-900 peer-checked:shadow-sm transition-all">
                                    Montants personnalisés (%)
                                </div>
                            </label>
                        </div>
                        
                        <div v-if="expenseForm.partageOption === 'custom'" class="flex flex-col gap-3">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Répartition personnalisée (%)</label>
                            <div v-for="p in currentGroup.participantsInfo" :key="p.id" class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="text-sm font-bold text-slate-700">{{ store.user && p.id === store.user.id ? 'Moi' : p.name }}</span>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="0" max="100" placeholder="0" v-model.number="expenseForm.customPercentages[p.id]" class="w-16 bg-slate-100 border-none rounded-lg px-2 py-2 text-center font-bold text-slate-900 focus:ring-2 focus:ring-primary outline-none">
                                    <span class="text-sm font-bold text-slate-500">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 pt-4">
                        <button @click="saveExpense" class="flex-[2] bg-[#6155F5] hover:bg-[#5044e6] text-white py-4 rounded-xl font-bold text-base shadow-sm transition-all">
                            Enregistrer la dépense
                        </button>
                        <button @click="showExpenseModal = false" class="flex-[1] bg-slate-100 hover:bg-slate-200 text-slate-700 py-4 rounded-xl font-bold text-base shadow-sm transition-all">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>`
};
