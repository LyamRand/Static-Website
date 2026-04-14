import { store, userInitials } from '../store.js';
const { ref, onMounted } = Vue;
const { useRouter } = VueRouter;

export default {
    setup() {
        const router = useRouter();
        const isProfileMenuOpen = ref(false);
        const showNotifications = ref(false);
        const hasNewNotifications = ref(true);
        
        const searchQuery = ref('');
        const searchFocused = ref(false);
        const allGroups = ref([]);

        const fetchGroups = async () => {
            try {
                const response = await fetch('./api/get_groupes.php');
                const data = await response.json();
                if (data.success) {
                    allGroups.value = data.groupes;
                }
            } catch (error) { console.error("Erreur groupes :", error); }
        };

        const filteredGroups = Vue.computed(() => {
            if (!searchQuery.value.trim()) return [];
            const query = searchQuery.value.toLowerCase();
            return allGroups.value.filter(g => g.nom.toLowerCase().includes(query));
        });

        const handleSearchFocus = () => {
            searchFocused.value = true;
            fetchGroups();
        };

        const goToGroup = (id) => {
            router.push('/groupe/' + id);
            searchQuery.value = '';
            searchFocused.value = false;
        };

        const handleLogout = async () => {
            await store.logout();
            router.push('/');
        };

        const closeMenu = () => {
            isProfileMenuOpen.value = false;
            showNotifications.value = false;
            setTimeout(() => { searchFocused.value = false; }, 150); // delay pour permettre le clic
        };

        onMounted(() => {
            if (!store.user) {
                store.checkAuth().then(() => {
                    if (!store.user) router.push('/auth');
                });
            }
        });

        return { store, userInitials, isProfileMenuOpen, showNotifications, hasNewNotifications, handleLogout, closeMenu, searchQuery, searchFocused, filteredGroups, handleSearchFocus, goToGroup };
    },
    template: `
    <div class="flex min-h-screen w-full bg-[#F9FAFB] text-slate-900 font-sans" @click="closeMenu">
        <!-- Sidebar -->
        <aside class="w-72 bg-white flex flex-col fixed h-full border-r border-slate-100 z-20">
            <div class="p-8 pb-12">
                <h1 class="text-5xl font-logo text-black tracking-wider cursor-pointer" @click="$router.push('/dashboard')">Splitz</h1>
            </div>
            <nav class="flex-1 px-4 space-y-3">
                <router-link to="/dashboard" custom v-slot="{ isActive, navigate }">
                    <a @click="navigate" :class="isActive ? 'bg-primary/20 text-primary font-bold transition-all' : 'bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all'" class="flex items-center gap-4 px-6 py-4 rounded-2xl cursor-pointer">
                        <span class="material-symbols-outlined text-[26px]">grid_view</span>
                        <span class="text-[15px]">Tableau de bord</span>
                    </a>
                </router-link>
                <router-link to="/groupes" custom v-slot="{ isActive, navigate }">
                    <a @click="navigate" :class="isActive ? 'bg-primary/20 text-primary font-bold transition-all' : 'bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all'" class="flex items-center gap-4 px-6 py-4 rounded-2xl cursor-pointer">
                        <span class="material-symbols-outlined text-[26px]">group</span>
                        <span class="text-[15px]">Mes groupes</span>
                    </a>
                </router-link>
            </nav>
            <div class="p-4 mb-4">
                <router-link to="/settings" custom v-slot="{ isActive, navigate }">
                    <a @click="navigate" :class="isActive ? 'bg-primary/20 text-primary font-bold transition-all' : 'bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all'" class="flex items-center gap-4 px-6 py-4 rounded-2xl cursor-pointer">
                        <span class="material-symbols-outlined text-[26px]">settings</span>
                        <span class="text-[15px]">Paramètres</span>
                    </a>
                </router-link>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="flex-1 ml-72 flex flex-col min-h-screen">
            <!-- Header -->
            <header class="h-[90px] bg-[#F9FAFB] px-10 flex items-center justify-between border-b border-slate-200/60 z-10 sticky top-0">
                <div class="relative w-[450px]">
                    <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-full w-full shadow-sm border border-slate-100">
                        <span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
                        <input type="text" v-model="searchQuery" @focus="handleSearchFocus" placeholder="Rechercher un de vos groupes..." class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-slate-400 outline-none font-medium" />
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div v-if="searchFocused && searchQuery.trim() !== ''" class="absolute left-0 top-full mt-2 w-full bg-white rounded-[24px] shadow-xl border border-slate-100 overflow-hidden z-50 p-2">
                        <div v-if="filteredGroups.length === 0" class="p-4 text-center text-sm font-medium text-slate-400">
                            Aucun groupe trouvé.
                        </div>
                        <div v-else>
                            <div v-for="g in filteredGroups" :key="g.id" @click.stop="goToGroup(g.id)" class="flex items-center gap-3 cursor-pointer p-3 hover:bg-slate-50 rounded-2xl transition-all">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-[18px]">{{ g.icone }}</div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ g.nom }}</p>
                                    <p class="text-[11px] font-medium text-slate-400">{{ g.participants }} participants</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="relative">
                        <button @click.stop="showNotifications = !showNotifications; hasNewNotifications = false; isProfileMenuOpen = false" class="relative text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                            <span class="material-symbols-outlined text-[28px]">notifications</span>
                            <span v-if="hasNewNotifications" class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-danger rounded-full border-2 border-[#F9FAFB]"></span>
                        </button>
                        
                        <!-- Notifications Dropdown -->
                        <div v-if="showNotifications" @click.stop class="absolute right-0 top-full mt-3 w-72 bg-white rounded-[24px] shadow-xl border border-slate-100 overflow-hidden z-50 p-6 text-center">
                            <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-300">
                                <span class="material-symbols-outlined text-[28px]">notifications_off</span>
                            </div>
                            <p class="text-[16px] font-black text-slate-900 mb-1">Aucune notification</p>
                            <p class="text-[13px] font-medium text-slate-400">Vous êtes à jour !</p>
                        </div>
                    </div>
                    
                    <div v-if="store.user" class="relative pl-4 border-l border-slate-200">
                        <button @click.stop="isProfileMenuOpen = !isProfileMenuOpen; showNotifications = false" class="flex items-center gap-3 text-left hover:bg-slate-50 p-2 rounded-xl transition-all outline-none">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-slate-900">{{ store.user.name }}</p>
                                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Compte Personnel</p>
                            </div>
                            <div class="w-11 h-11 rounded-full bg-primary/10 text-primary font-black flex items-center justify-center overflow-hidden border border-primary/20">
                                <img v-if="store.user.photo" :src="store.user.photo" alt="Avatar" class="w-full h-full object-cover">
                                <span v-else class="text-[16px]">{{ userInitials }}</span>
                            </div>
                            <span class="material-symbols-outlined text-slate-400 text-[20px] transition-transform duration-300" :class="{'rotate-180': isProfileMenuOpen}">expand_more</span>
                        </button>
                        
                        <!-- Dropdown -->
                        <div v-if="isProfileMenuOpen" @click.stop class="absolute right-0 top-full mt-2 w-64 bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden z-50">
                            <div class="p-5 border-b border-slate-50 bg-slate-50/50">
                                <p class="text-sm font-bold text-slate-900">{{ store.user.name }}</p>
                                <p class="text-xs font-medium text-slate-400 mt-0.5">Membre de Splitz</p>
                            </div>
                            <div class="p-2">
                                <router-link to="/account" @click="closeMenu" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 hover:text-primary hover:bg-primary/5 rounded-2xl transition-all">
                                    <span class="material-symbols-outlined text-[22px]">person</span> Mon profil
                                </router-link>
                            </div>
                            <div class="p-2 border-t border-slate-50">
                                <button @click="handleLogout" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-danger hover:bg-red-50 rounded-2xl transition-all">
                                    <span class="material-symbols-outlined text-[22px]">logout</span> Déconnexion
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <router-view></router-view>
        </main>
    </div>
    `
};
