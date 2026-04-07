const { ref, onMounted } = Vue;

export default {
    setup() {
        const groupes = ref([]);
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

        return { groupes, isAddGroupModalOpen, selectedGroupIcon, newGroupForm, createGroup };
    },
    template: `
    <div class="p-10 max-w-[1100px] w-full mx-auto">
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-5">
                <div class="w-[75px] h-[75px] bg-surface-dark rounded-[24px] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[42px] text-slate-900">group</span>
                </div>
                <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900">Mes groupes</h2>
            </div>
            <button @click.stop="isAddGroupModalOpen = true" class="bg-primary hover:bg-[#5044e6] text-white px-7 py-4 rounded-[16px] font-bold text-lg transition-all shadow-lg shadow-primary/30 flex items-center gap-2">
                <span class="material-symbols-outlined">add_circle</span> Ajouter un groupe
            </button>
        </div>

        <div class="flex flex-col gap-6">
            <template v-if="groupes.length > 0">
                <router-link v-for="groupe in groupes" :key="groupe.id" :to="'/groupe/' + groupe.id" class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md hover:border-primary transition-all cursor-pointer group">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-[20px] bg-slate-100 flex items-center justify-center text-[32px] group-hover:bg-primary/10 transition-colors">{{ groupe.icone }}</div>
                        <div>
                            <h4 class="text-[22px] font-black text-slate-900 leading-tight mb-1 group-hover:text-primary transition-colors">{{ groupe.nom }}</h4>
                            <p class="text-sm font-medium text-slate-400">{{ groupe.participants }} participants</p>
                        </div>
                    </div>
                    <div class="bg-surface rounded-2xl px-6 py-4 flex items-center gap-6 min-w-[250px]">
                        <span class="text-sm font-bold text-slate-500">Solde actuel</span>
                        <span class="text-xl font-black ml-auto" :class="groupe.solde >= 0 ? 'text-green-success' : 'text-red-danger'">
                            {{ groupe.solde > 0 ? '+' : '' }}{{ groupe.solde.toFixed(2).replace('.', ',') }} €
                        </span>
                    </div>
                </router-link>
            </template>
            <template v-else>
                <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm h-[120px] flex items-center justify-center">
                    <p class="text-sm font-bold text-slate-400">Aucun groupe créé ou rejoint.</p>
                </div>
            </template>
        </div>

        <!-- Add Group modal -->
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
