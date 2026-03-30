<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Splitz - Tableau de bord</title>

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
                        "surface": "#F3F4F6", // background
                        "surface-dark": "#E5E7EB", // boutons inactifs
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
        <aside class="w-72 bg-white flex flex-col fixed h-full border-r border-slate-100">

            <div class="p-8 pb-12">
                <h1 class="text-5xl font-logo text-black tracking-wider">Splitz</h1>
            </div>

            <nav class="flex-1 px-4 space-y-3">
                <a href="dashboard.php"
                    class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-primary/20 text-primary font-bold transition-all">
                    <span class="material-symbols-outlined text-[26px]">grid_view</span>
                    <span class="text-[15px]">Tableau de bord</span>
                </a>
                <a href="groupes.php"
                    class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all">
                    <span class="material-symbols-outlined text-[26px]">group</span>
                    <span class="text-[15px]">Mes groupes</span>
                </a>
                <a href="#"
                    class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all">
                    <span class="material-symbols-outlined text-[26px]">history</span>
                    <span class="text-[15px]">Activité</span>
                </a>
            </nav>

            <div class="p-4 mb-4">
                <a href="#"
                    class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all">
                    <span class="material-symbols-outlined text-[26px]">settings</span>
                    <span class="text-[15px]">Paramètres</span>
                </a>
            </div>
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

                    <div v-if="user" class="flex items-center gap-3 pl-4 border-l border-slate-200">
                        <div class="text-right">
                            <p class="text-sm font-bold text-slate-900">{{ user.name }}</p>
                            <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Compte personnel
                            </p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-slate-200 overflow-hidden border border-slate-200">
                            <img src="https://i.pravatar.cc/150?img=11" alt="Avatar" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-10 max-w-[1400px]">

                <div v-if="user" class="mb-10">
                    <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 mb-1 flex items-center gap-3">
                        Salut, {{ user.name }} <span class="text-[35px]">👋</span>
                    </h2>
                    <p class="text-slate-500 text-lg font-medium">Voici le récapitulatif de tes comptes partagés ce
                        mois-ci.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

                    <div
                        class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-center h-[160px]">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-500 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">account_balance</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500">Solde total</span>
                        </div>
                        <p class="text-[34px] font-black text-slate-900">
                            {{ soldeTotal > 0 ? '+' : '' }}{{ soldeTotal.toFixed(2).replace('.', ',') }} €
                        </p>
                    </div>

                    <div
                        class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-center h-[160px]">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-8 h-8 rounded-full bg-green-100 text-green-success flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">arrow_circle_right</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500">On te doit</span>
                        </div>
                        <p class="text-[34px] font-black text-green-success">{{ onTeDoit.toFixed(2).replace('.', ',') }}
                            €</p>
                    </div>

                    <div
                        class="bg-white p-7 rounded-[32px] border border-slate-100 shadow-sm flex flex-col justify-center h-[160px]">
                        <div class="flex items-center gap-3 mb-3">
                            <div
                                class="w-8 h-8 rounded-full bg-red-100 text-red-danger flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">arrow_circle_left</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500">Tu dois</span>
                        </div>
                        <p class="text-[34px] font-black text-red-danger">{{ tuDois.toFixed(2).replace('.', ',') }} €
                        </p>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

                    <div>
                        <div class="flex items-center justify-between mb-6 pr-2">
                            <h3 class="text-2xl font-extrabold text-slate-900">Mes groupes</h3>
                            <a href="groupes.php" class="text-primary font-bold hover:underline">Voir tout</a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <template v-if="groupes.length > 0">
                                <div v-for="groupe in groupes" :key="groupe.id"
                                    class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm flex flex-col h-[220px]">
                                    <div class="flex items-center gap-4 mb-auto">
                                        <div
                                            class="w-14 h-14 rounded-[20px] bg-slate-100 flex items-center justify-center text-[28px]">
                                            {{ groupe.icone }}</div>
                                        <div>
                                            <h4 class="text-[19px] font-black text-slate-900 leading-tight mb-0.5">{{
                                                groupe.nom }}
                                            </h4>
                                            <p class="text-xs font-medium text-slate-400">{{ groupe.participants }}
                                                participants</p>
                                        </div>
                                    </div>
                                    <div class="bg-surface rounded-2xl p-4 flex justify-between items-center mt-6">
                                        <span class="text-xs font-bold text-slate-500">Solde actuel</span>
                                        <span class="text-sm font-black"
                                            :class="groupe.solde >= 0 ? 'text-green-success' : 'text-red-danger'">
                                            {{ groupe.solde > 0 ? '+' : '' }}{{ groupe.solde.toFixed(2).replace('.',
                                            ',') }} €
                                        </span>
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div
                                    class="bg-white rounded-[32px] border border-slate-100 shadow-sm h-[220px] flex items-center justify-center col-span-1 sm:col-span-2">
                                    <p class="text-sm font-bold text-slate-400">Aucun groupe créé ou rejoint.</p>
                                </div>
                            </template>

                            <button @click="isAddGroupModalOpen = true"
                                :class="['bg-transparent rounded-[32px] p-6 border-2 border-dashed border-slate-300 hover:border-primary hover:bg-primary/5 transition-all flex flex-col items-center justify-center h-[220px] text-slate-400 hover:text-primary', groupes.length === 0 ? 'col-span-1 sm:col-span-2 mt-4' : '']">
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

                        <div
                            class="bg-white rounded-[32px] border border-slate-100 shadow-sm h-[220px] flex items-center justify-center">
                            <p class="text-sm font-bold text-slate-400">Aucune activité récente</p>
                        </div>
                    </div>

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
                                class="w-full px-5 py-4 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold placeholder-slate-400" />
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
                                class="flex-[2] py-4 rounded-xl bg-primary hover:bg-[#5044e6] text-white font-bold text-base transition-all shadow-lg shadow-primary/30">
                                C'est parti !
                            </button>
                            <button type="button" @click="isAddGroupModalOpen = false"
                                class="flex-[1] py-4 rounded-xl bg-surface hover:bg-slate-200 text-slate-600 font-bold text-base transition-all">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>
    <script src="../app.js"></script>
</body>

</html>