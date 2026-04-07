import { store } from '../store.js';
const { ref, onMounted, computed } = Vue;

export default {
    setup() {
        const groupes = ref([]);
        const soldeTotal = computed(() => groupes.value.reduce((total, groupe) => total + groupe.solde, 0));
        const onTeDoit = computed(() => groupes.value.filter(g => g.solde > 0).reduce((total, groupe) => total + groupe.solde, 0));
        const tuDois = computed(() => groupes.value.filter(g => g.solde < 0).reduce((total, groupe) => total + Math.abs(groupe.solde), 0));
        const isAddGroupModalOpen = ref(false);
        const selectedGroupIcon = ref('home');
        const newGroupForm = ref({ name: '', description: '' });

        const fetchGroupes = async () => {
            try {
                const response = await fetch('./api/get_groupes.php');
                const data = await response.json();
                if (data.success) { groupes.value = data.groupes; }
            } catch (error) { console.error("Erreur groupes :", error); }
        };

        const createGroup = async () => {
            try {
                const res = await fetch('./api/newgroup.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        name: newGroupForm.value.name,
                        description: newGroupForm.value.description,
                        group_icon: selectedGroupIcon.value
                    })
                });
                const data = await res.json();
                if (data.success) {
                    isAddGroupModalOpen.value = false;
                    fetchGroupes();
                }
            } catch (e) {
                console.error(e);
            }
        };

        onMounted(() => {
            fetchGroupes();
        });

        return { store, groupes, soldeTotal, onTeDoit, tuDois, isAddGroupModalOpen, selectedGroupIcon, newGroupForm, createGroup };
    },
    template: `
    <div class="p-10 max-w-[1400px]">
        <div v-if="store.user" class="mb-10">
            <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 mb-1 flex items-center gap-3">
                Salut, {{ store.user.name }} <span class="text-[35px]">👋</span>
            </h2>
            <p class="text-slate-500 text-lg font-medium">Voici le récapitulatif de tes comptes partagés ce mois-ci.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-center h-[160px]">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">account_balance</span>
                    </div>
                    <span class="text-sm font-bold text-slate-500">Solde total</span>
                </div>
                <p class="text-[34px] font-black text-slate-900">{{ soldeTotal > 0 ? '+' : '' }}{{ soldeTotal.toFixed(2).replace('.', ',') }} €</p>
            </div>
            
            <div class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-center h-[160px]">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 text-green-success flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">arrow_circle_right</span>
                    </div>
                    <span class="text-sm font-bold text-slate-500">On te doit</span>
                </div>
                <p class="text-[34px] font-black text-green-success">{{ onTeDoit.toFixed(2).replace('.', ',') }} €</p>
            </div>

            <div class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-center h-[160px]">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-danger flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">arrow_circle_left</span>
                    </div>
                    <span class="text-sm font-bold text-slate-500">Tu dois</span>
                </div>
                <p class="text-[34px] font-black text-red-danger">{{ tuDois.toFixed(2).replace('.', ',') }} €</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <div class="flex items-center justify-between mb-6 pr-2">
                    <h3 class="text-2xl font-extrabold text-slate-900">Mes groupes</h3>
                    <router-link to="/groupes" class="text-primary font-bold hover:underline">Voir tout</router-link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <template v-if="groupes.length > 0">
                        <div v-for="groupe in groupes" :key="groupe.id" class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm flex flex-col h-[220px] cursor-pointer hover:border-primary transition-all" @click="$router.push('/groupe/' + groupe.id)">
                            <div class="flex items-center gap-4 mb-auto">
                                <div class="w-14 h-14 rounded-[20px] bg-slate-100 flex items-center justify-center text-[28px]">{{ groupe.icone }}</div>
                                <div>
                                    <h4 class="text-[19px] font-black text-slate-900 leading-tight mb-0.5">{{ groupe.nom }}</h4>
                                    <p class="text-xs font-medium text-slate-400">{{ groupe.participants }} participants</p>
                                </div>
                            </div>
                            <div class="bg-surface rounded-2xl p-4 flex justify-between items-center mt-6">
                                <span class="text-xs font-bold text-slate-500">Solde actuel</span>
                                <span class="text-sm font-black" :class="groupe.solde >= 0 ? 'text-green-success' : 'text-red-danger'">
                                    {{ groupe.solde > 0 ? '+' : '' }}{{ groupe.solde.toFixed(2).replace('.', ',') }} €
                                </span>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm h-[220px] flex items-center justify-center col-span-1 sm:col-span-2">
                            <p class="text-sm font-bold text-slate-400">Aucun groupe créé ou rejoint.</p>
                        </div>
                    </template>

                    <button @click.stop="isAddGroupModalOpen = true" :class="['bg-transparent rounded-[32px] p-6 border-2 border-dashed border-slate-300 hover:border-primary hover:bg-primary/5 transition-all flex flex-col items-center justify-center h-[220px] text-slate-400 hover:text-primary', groupes.length === 0 ? 'col-span-1 sm:col-span-2 mt-4' : '']">
                        <span class="material-symbols-outlined text-[28px] mb-2">add_circle</span>
                        <span class="text-[13px] font-black tracking-wide">CRÉER UN GROUPE</span>
                    </button>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-6 pr-2">
                    <h3 class="text-2xl font-extrabold text-slate-900">Activité récente</h3>
                    <a href="#" class="text-primary font-bold hover:underline">Voir tout</a>
                </div>
                <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm h-[220px] flex items-center justify-center">
                    <p class="text-sm font-bold text-slate-400">Aucune activité récente</p>
                </div>
            </div>
        </div>

        <div v-if="isAddGroupModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="bg-white w-full max-w-[600px] rounded-[32px] shadow-2xl overflow-hidden flex flex-col p-8 md:p-10">
                <h2 class="text-3xl font-extrabold text-slate-900 mb-6">Nouveau groupe</h2>
                <hr class="border-slate-100 mb-8" />
                <form @submit.prevent="createGroup" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nom du groupe</label>
                        <input type="text" v-model="newGroupForm.name" placeholder="Comment s'appelle le groupe ?" class="w-full px-5 py-4 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold placeholder-slate-400" required />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Description <span class="text-slate-400 font-normal">(optionnel)</span></label>
                        <textarea v-model="newGroupForm.description" placeholder="Un petit mot sur l'objectif de ce groupe..." rows="3" class="w-full px-5 py-4 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold placeholder-slate-400 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Icône du groupe</label>
                        <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                            <label v-for="icon in ['home', 'flight', 'landscape', 'sports_bar']" :key="icon" class="cursor-pointer flex-shrink-0">
                                <input type="radio" :value="icon" v-model="selectedGroupIcon" class="peer hidden">
                                <div class="flex items-center justify-center min-w-[75px] h-[75px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-600 hover:bg-slate-200 peer-checked:bg-primary/20 peer-checked:border-primary peer-checked:text-primary">
                                    <span class="material-symbols-outlined text-[32px]">{{ icon }}</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4 pt-4 mt-auto">
                        <button type="submit" class="flex-[2] py-4 rounded-xl bg-primary hover:bg-[#5044e6] text-white font-bold text-base transition-all shadow-lg shadow-primary/30">C'est parti !</button>
                        <button type="button" @click="isAddGroupModalOpen = false" class="flex-[1] py-4 rounded-xl bg-surface hover:bg-slate-200 text-slate-600 font-bold text-base transition-all">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>`
};
