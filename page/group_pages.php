<?php
// On récupère l'ID via l'URL pour la logique PHP future
$groupId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$groupId) {
    header('Location: groupes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Splitz - Détails du groupe</title>

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
            <div class="p-4 mb-4">
                <a href="account.php"
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

                    <div v-if="user" class="relative pl-4 border-l border-slate-200">
                        <button @click.stop="isProfileMenuOpen = !isProfileMenuOpen"
                            class="flex items-center gap-3 text-left hover:bg-slate-50 p-2 rounded-xl transition-all outline-none">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-slate-900">{{ user.name }}</p>
                                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Compte
                                    personnel</p>
                            </div>
                            <div
                                class="w-11 h-11 rounded-full bg-primary/10 text-primary font-black flex items-center justify-center overflow-hidden border border-primary/20">
                                <span class="text-[16px]">{{ userInitials }}</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-slate-400 text-[20px] transition-transform duration-300"
                                :class="{'rotate-180': isProfileMenuOpen}">expand_more</span>
                        </button>

                        <div v-if="isProfileMenuOpen"
                            class="absolute right-0 top-full mt-2 w-64 bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden z-50">
                            <div class="p-5 border-b border-slate-50 bg-slate-50/50">
                                <p class="text-sm font-bold text-slate-900">{{ user.name }}</p>
                                <p class="text-xs font-medium text-slate-400 mt-0.5">Membre de Splitz</p>
                            </div>
                            <div class="p-2">
                                <a href="account.php?tab=profil"
                                    class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 hover:text-primary hover:bg-primary/5 rounded-2xl transition-all">
                                    <span class="material-symbols-outlined text-[22px]">person</span> Mon profil
                                </a>
                                <a href="account.php?tab=securite"
                                    class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 hover:text-primary hover:bg-primary/5 rounded-2xl transition-all">
                                    <span class="material-symbols-outlined text-[22px]">settings</span> Paramètres
                                </a>
                            </div>
                            <div class="p-2 border-t border-slate-50">
                                <button @click="handleLogout"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-danger hover:bg-red-50 rounded-2xl transition-all">
                                    <span class="material-symbols-outlined text-[22px]">logout</span> Déconnexion
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-10 max-w-[1200px] w-full mx-auto" @click="isProfileMenuOpen = false">

                <div class="flex items-center text-sm font-medium text-slate-400 mb-6">
                    <a href="groupes.php" class="hover:text-primary transition-colors">Mes groupes</a>
                    <span class="material-symbols-outlined text-[18px] mx-2">chevron_right</span>
                    <span class="text-slate-700 font-bold">{{ currentGroup?.nom || 'Chargement...' }}</span>
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                    <div class="flex items-center gap-6">
                        <div
                            class="w-24 h-24 bg-slate-100 rounded-[32px] flex items-center justify-center text-[45px] shadow-sm">
                            {{ currentGroup?.icone || '📁' }}
                        </div>
                        <div>
                            <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 leading-tight mb-2">
                                {{ currentGroup?.nom || 'Nom du groupe' }}
                            </h2>
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center text-xs font-bold text-blue-600 z-30">
                                        M</div>
                                    <div
                                        class="w-8 h-8 rounded-full bg-green-100 border-2 border-white flex items-center justify-center text-xs font-bold text-green-600 z-20">
                                        L</div>
                                    <div
                                        class="w-8 h-8 rounded-full bg-yellow-100 border-2 border-white flex items-center justify-center text-xs font-bold text-yellow-600 z-10">
                                        C</div>
                                </div>
                                <button
                                    class="text-sm font-bold text-primary hover:underline ml-2 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[18px]">person_add</span> Inviter
                                </button>
                            </div>
                        </div>
                    </div>

                    <button
                        class="bg-primary hover:bg-[#5044e6] text-white px-8 py-4 rounded-[16px] font-bold text-lg transition-all shadow-lg shadow-primary/30 flex items-center gap-2">
                        <span class="material-symbols-outlined">add_circle</span>
                        Ajouter une dépense
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                    <div class="lg:col-span-2">
                        <div class="flex items-center justify-between mb-6 pr-2">
                            <h3 class="text-2xl font-extrabold text-slate-900">Dépenses récentes</h3>
                            <a href="#" class="text-primary font-bold hover:underline">Voir tout</a>
                        </div>

                        <div class="flex flex-col gap-4">
                            <div v-for="depense in currentGroupExpenses" :key="depense.id"
                                class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all cursor-pointer group">
                                <div class="flex items-center gap-4">
                                    <div
                                        :class="`w-14 h-14 rounded-2xl flex items-center justify-center text-[26px] ${depense.colorClass}`">
                                        <span class="material-symbols-outlined">{{ depense.icon }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-black text-slate-900 leading-tight">{{ depense.title }}
                                        </h4>
                                        <p class="text-xs font-medium text-slate-400">Payé par <span
                                                class="font-bold text-slate-600">{{ depense.payer }}</span></p>
                                    </div>
                                </div>
                                <div class="text-right flex items-center gap-4">
                                    <div>
                                        <p class="text-xl font-black text-slate-900">{{
                                            depense.amount.toFixed(2).replace('.', ',') }} €</p>
                                        <p class="text-xs font-medium"
                                            :class="depense.owed > 0 ? 'text-green-success font-bold' : 'text-slate-400'">
                                            {{ depense.owed > 0 ? 'On vous doit : ' +
                                            depense.owed.toFixed(2).replace('.', ',') + ' €' : 'Votre part : ' +
                                            Math.abs(depense.owed).toFixed(2).replace('.', ',') + ' €' }}
                                        </p>
                                    </div>
                                    <span
                                        class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">edit</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-6">

                        <div
                            class="bg-primary rounded-[32px] p-8 text-white shadow-xl shadow-primary/20 relative overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2">
                            </div>

                            <h3 class="text-sm font-bold text-primary-100 uppercase tracking-wider mb-2 opacity-90">
                                Dépenses totales du mois</h3>
                            <p class="text-5xl font-black mb-8">{{ currentGroupStats.total.toFixed(2).replace('.', ',')
                                }} €</p>

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

                        <div
                            class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-sm flex flex-col justify-center min-h-[250px]">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-50 text-primary flex items-center justify-center">
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

            </div>
        </main>
    </div>

    <script src="../app.js"></script>
</body>

</html>