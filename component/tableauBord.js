const { ref, onMounted } = Vue;

export default {
    setup() {
        const user = ref(null);

        onMounted(() => {
            fetch('api/check_session.php')
                .then(res => res.json())
                .then(data => user.value = data.connected ? data.user : null)
                .catch(console.error);
        });

        return { user };
    },
    template: `
    <div class="font-sans bg-[#F9FAFB] text-slate-900 flex min-h-screen w-full text-left">
        <aside class="w-72 bg-white flex flex-col fixed h-full border-r border-slate-100">
            <div class="p-8 pb-12">
                <router-link to="/" class="text-5xl font-logo text-black tracking-wider">Splitz</router-link>
            </div>

            <nav class="flex-1 px-4 space-y-3">
                <a href="#" class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-primary/20 text-primary font-bold transition-all">
                    <span class="material-symbols-outlined text-[26px]">grid_view</span>
                    <span class="text-[15px]">Tableau de bord</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold hover:bg-slate-300">
                    <span class="material-symbols-outlined text-[26px]">group</span>
                    <span class="text-[15px]">Mes groupes</span>
                </a>
                <a href="#" class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all">
                    <span class="material-symbols-outlined text-[26px]">history</span>
                    <span class="text-[15px]">Activité</span>
                </a>
            </nav>

            <div class="p-4 mb-4">
                <a href="#" class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all">
                    <span class="material-symbols-outlined text-[26px]">settings</span>
                    <span class="text-[15px]">Paramètres</span>
                </a>
            </div>
        </aside>

        <main class="flex-1 ml-72 flex flex-col min-h-screen">
            <header class="h-[90px] bg-[#F9FAFB] px-10 flex items-center justify-between border-b border-slate-200/60 z-10 sticky top-0">
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-full w-[450px] shadow-sm border border-slate-100">
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
                    <input type="text" placeholder="Rechercher une dépense ou un groupe..." class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-slate-400 outline-none font-medium" />
                </div>

                <div class="flex items-center gap-6">
                    <button class="relative text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined text-[28px]">notifications</span>
                        <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-danger rounded-full border-2 border-[#F9FAFB]"></span>
                    </button>

                    <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                        <div class="text-right">
                            <p class="text-sm font-bold text-slate-900">{{ user?.name || 'Chargement...' }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-slate-200 overflow-hidden border border-slate-200">
                            <img src="https://i.pravatar.cc/150?img=11" alt="Avatar" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-10 max-w-[1400px]">
                <div class="mb-10">
                    <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 mb-1 flex items-center gap-3">
                        Salut, {{ user?.name || '' }} <span class="text-[35px]">👋</span>
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
                        <p class="text-[34px] font-black text-slate-900">+22,50 €</p>
                    </div>

                    <div class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-center h-[160px]">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-success flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">arrow_circle_right</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500">On te doit</span>
                        </div>
                        <p class="text-[34px] font-black text-green-success">45,00 €</p>
                        <p class="text-xs font-semibold text-slate-400 mt-1">Recevoir 3 paiements</p>
                    </div>

                    <div class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-center h-[160px]">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-red-100 text-red-danger flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">arrow_circle_left</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500">Tu dois</span>
                        </div>
                        <p class="text-[34px] font-black text-red-danger">22,50 €</p>
                        <p class="text-xs font-semibold text-slate-400 mt-1">Régler 2 dettes</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div>
                        <div class="flex items-center justify-between mb-6 pr-2">
                            <h3 class="text-2xl font-extrabold text-slate-900">Mes groupes</h3>
                            <a href="#" class="text-primary font-bold hover:underline">Voir tout</a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm flex flex-col h-[220px]">
                                <div class="flex items-center gap-4 mb-auto">
                                    <div class="w-14 h-14 rounded-[20px] bg-slate-100 flex items-center justify-center text-[28px]">⛷️</div>
                                    <div>
                                        <h4 class="text-[19px] font-black text-slate-900 leading-tight mb-0.5">Vacances Ski</h4>
                                        <p class="text-xs font-medium text-slate-400">4 participants</p>
                                    </div>
                                </div>
                                <div class="bg-surface rounded-2xl p-4 flex justify-between items-center mt-6">
                                    <span class="text-xs font-bold text-slate-500">Solde actuel</span>
                                    <span class="text-sm font-black text-green-success">+45,00 €</span>
                                </div>
                            </div>

                            <div class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm flex flex-col h-[220px]">
                                <div class="flex items-center gap-4 mb-auto">
                                    <div class="w-14 h-14 rounded-[20px] bg-slate-100 flex items-center justify-center text-[28px]">☀️</div>
                                    <div>
                                        <h4 class="text-[19px] font-black text-slate-900 leading-tight mb-0.5">Voyage Sud</h4>
                                        <p class="text-xs font-medium text-slate-400">12 participants</p>
                                    </div>
                                </div>
                                <div class="bg-surface rounded-2xl p-4 flex justify-between items-center mt-6">
                                    <span class="text-xs font-bold text-slate-500">Solde actuel</span>
                                    <span class="text-sm font-black text-red-danger">-22,50 €</span>
                                </div>
                            </div>

                            <button class="bg-transparent rounded-[32px] p-6 border-2 border-dashed border-slate-300 hover:border-primary hover:bg-primary/5 transition-all flex flex-col items-center justify-center h-[220px] text-slate-400 hover:text-primary">
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
            </div>
        </main>
    </div>
    `
}
