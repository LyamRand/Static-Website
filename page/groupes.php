<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Splitz - Mes groupes</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;500;600;700;800;900&family=Pacifico&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@300,0..1&display=swap"
        rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#6155F5",
                        "surface": "#F3F4F6",
                        "surface-dark": "#E5E7EB",
                        "green-success": "#22C55E",
                        "red-danger": "#EF4444",
                    },
                    fontFamily: {
                        "sans": ["Fira Sans", "sans-serif"],
                        "logo": ["Pacifico", "cursive"]
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans bg-[#F9FAFB] text-slate-900 flex min-h-screen">
    <div id="app" class="flex min-h-screen w-full">

        <aside class="w-72 bg-white flex flex-col fixed h-full border-r border-slate-100 z-20">
            <div class="p-8 pb-12">
                <h1 class="text-5xl font-logo text-black tracking-wider">Splitz</h1>
            </div>

            <nav class="flex-1 px-4 space-y-3">
                <a href="dashboard.php"
                    class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all">
                    <span class="material-symbols-outlined text-[26px]">grid_view</span>
                    <span class="text-[15px]">Tableau de bord</span>
                </a>

                <a href="groupes.php"
                    class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-primary/20 text-primary font-bold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[26px]">group</span>
                    <span class="text-[15px]">Mes groupes</span>
                </a>

                <a href="#"
                    class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all">
                    <span class="material-symbols-outlined text-[26px]">history</span>
                    <span class="text-[15px]">Activité</span>
                </a>
            </nav>

        </aside>

        <main class="flex-1 ml-72 flex flex-col min-h-screen">
            <header
                class="h-[90px] bg-[#F9FAFB] px-10 flex items-center justify-between border-b border-slate-200/60 z-10 sticky top-0">
                <div
                    class="flex items-center gap-3 bg-white px-5 py-3 rounded-full w-[450px] shadow-sm border border-slate-100">
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">search</span>
                    <input type="text" placeholder="Rechercher une dépense ou un groupe..."
                        class="bg-transparent border-none focus:ring-0 text-sm w-full placeholder:text-slate-400 outline-none font-medium" />
                </div>

                <div class="flex items-center gap-6">
                    <button class="relative text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined text-[28px]">notifications</span>
                        <span
                            class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-danger rounded-full border-2 border-[#F9FAFB]"></span>
                    </button>

                    <div v-if="user" class="relative pl-4 border-l border-slate-200">

                        <button @click="isProfileMenuOpen = !isProfileMenuOpen"
                            class="flex items-center gap-3 text-left hover:bg-slate-50 p-2 rounded-xl transition-all outline-none">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-slate-900">{{ user.name }}</p>
                                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Compte
                                    personnel</p>
                            </div>

                            <div
                                class="w-11 h-11 rounded-full bg-primary/10 text-primary font-black flex items-center justify-center overflow-hidden border border-primary/20">
                                <img v-if="user.photo" :src="user.photo" alt="Avatar"
                                    class="w-full h-full object-cover">
                                <span v-else class="text-[16px]">{{ userInitials }}</span>
                            </div>

                            <span
                                class="material-symbols-outlined text-slate-400 text-[20px] transition-transform duration-300"
                                :class="{'rotate-180': isProfileMenuOpen}">
                                expand_more
                            </span>
                        </button>

                        <div v-if="isProfileMenuOpen"
                            class="absolute right-0 top-full mt-2 w-64 bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden z-50">
                            <div class="p-5 border-b border-slate-50 bg-slate-50/50">
                                <p class="text-sm font-bold text-slate-900">{{ user.name }}</p>
                                <p class="text-xs font-medium text-slate-400 mt-0.5">Membre de Splitz</p>
                            </div>
                            <div class="p-2">
                                <a href="account.php"
                                    class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 hover:text-primary hover:bg-primary/5 rounded-2xl transition-all">
                                    <span class="material-symbols-outlined text-[22px]">person</span>
                                    Mon profil
                                </a>
                                <a href="#.php"
                                    class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 hover:text-primary hover:bg-primary/5 rounded-2xl transition-all">
                                    <span class="material-symbols-outlined text-[22px]">settings</span>
                                    Paramètres
                                </a>
                            </div>
                            <div class="p-2 border-t border-slate-50">
                                <button @click="handleLogout"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-danger hover:bg-red-50 rounded-2xl transition-all">
                                    <span class="material-symbols-outlined text-[22px]">logout</span>
                                    Déconnexion
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </header>

            <div class="p-10 max-w-[1100px] w-full mx-auto" @click="isProfileMenuOpen = false">
                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center gap-5">
                        <div class="w-[75px] h-[75px] bg-surface-dark rounded-[24px] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[42px] text-slate-900">group</span>
                        </div>
                        <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900">Mes groupes</h2>
                    </div>

                    <button @click.stop="isAddGroupModalOpen = true"
                        class="bg-primary hover:bg-[#5044e6] text-white px-7 py-4 rounded-[16px] font-bold text-lg transition-all shadow-lg shadow-primary/30 flex items-center gap-2">
                        <span class="material-symbols-outlined">add_circle</span>
                        Ajouter un groupe
                    </button>
                </div>

                <div class="flex flex-col gap-6">
                    <template v-if="groupes.length > 0">
                        <a v-for="groupe in groupes" :key="groupe.id"
                            :href="'group_pages.php?id=' + groupe.id"
                            class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md hover:border-primary transition-all cursor-pointer group">
                            <div class="flex items-center gap-5">
                                <div
                                    class="w-16 h-16 rounded-[20px] bg-slate-100 flex items-center justify-center text-[32px] group-hover:bg-primary/10 transition-colors">
                                    {{ groupe.icone }}
                                </div>
                                <div>
                                    <h4 class="text-[22px] font-black text-slate-900 leading-tight mb-1 group-hover:text-primary transition-colors">
                                        {{ groupe.nom }}
                                    </h4>
                                    <p class="text-sm font-medium text-slate-400">{{ groupe.participants }} participants
                                    </p>
                                </div>
                            </div>
                            <div class="bg-surface rounded-2xl px-6 py-4 flex items-center gap-6 min-w-[250px]">
                                <span class="text-sm font-bold text-slate-500">Solde actuel</span>
                                <span class="text-xl font-black ml-auto"
                                    :class="groupe.solde >= 0 ? 'text-green-success' : 'text-red-danger'">
                                    {{ groupe.solde > 0 ? '+' : '' }}{{ groupe.solde.toFixed(2).replace('.', ',') }} €
                                </span>
                            </div>
                        </a>
                    </template>

                    <template v-else>
                        <div
                            class="bg-white rounded-[32px] border border-slate-100 shadow-sm h-[120px] flex items-center justify-center">
                            <p class="text-sm font-bold text-slate-400">Aucun groupe créé ou rejoint.</p>
                        </div>
                    </template>
                </div>
            </div>

            <div v-if="isAddGroupModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
                <div
                    class="bg-white w-full max-w-[600px] rounded-[32px] shadow-2xl overflow-hidden flex flex-col p-8 md:p-10">
                    <h2 class="text-3xl font-extrabold text-slate-900 mb-6">Nouveau groupe</h2>
                    <hr class="border-slate-100 mb-8" />
                    <form action="../api/newgroup.php" method="POST" class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nom du groupe</label>
                            <input type="text" id="name" name="name" placeholder="Comment s'appelle le groupe ?"
                                class="w-full px-5 py-4 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold placeholder-slate-400"
                                required />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Description <span
                                    class="text-slate-400 font-normal">(optionnel)</span></label>
                            <textarea id="description" name="description"
                                placeholder="Un petit mot sur l'objectif de ce groupe..." rows="3"
                                class="w-full px-5 py-4 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold placeholder-slate-400 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-3">Icône du groupe</label>
                            <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                                <label v-for="icon in ['home', 'flight', 'landscape', 'sports_bar']" :key="icon"
                                    class="cursor-pointer flex-shrink-0">
                                    <input type="radio" :value="icon" v-model="selectedGroupIcon" name="group_icon"
                                        class="peer hidden">
                                    <div
                                        class="flex items-center justify-center min-w-[75px] h-[75px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-600 hover:bg-slate-200 peer-checked:bg-primary/20 peer-checked:border-primary peer-checked:text-primary">
                                        <span class="material-symbols-outlined text-[32px]">{{ icon }}</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="relative flex items-center py-4">
                            <div class="flex-grow border-t border-slate-200"></div>
                            <span
                                class="flex-shrink-0 mx-4 w-10 h-10 flex items-center justify-center rounded-full border border-slate-200 bg-white text-sm font-bold text-slate-500">OU</span>
                            <div class="flex-grow border-t border-slate-200"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Rejoindre un groupe</label>
                            <input type="text" placeholder="Saisir le code unique pour rejoindre"
                                class="w-full px-5 py-4 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold placeholder-slate-400" />
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4 pt-4 mt-auto">
                            <button type="submit"
                                class="flex-[2] py-4 rounded-xl bg-primary hover:bg-[#5044e6] text-white font-bold text-base transition-all shadow-lg shadow-primary/30">C'est
                                parti !</button>
                            <button type="button" @click="isAddGroupModalOpen = false"
                                class="flex-[1] py-4 rounded-xl bg-surface hover:bg-slate-200 text-slate-600 font-bold text-base transition-all">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="../app.js"></script>
</body>

</html>
