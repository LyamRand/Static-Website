<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Splitz - Profil</title>

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
                            <img v-if="user.photo" :src="user.photo" alt="Avatar" class="w-full h-full object-cover">
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

    <div class="p-10 max-w-[1000px] w-full mx-auto" @click="isProfileMenuOpen = false">

        <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900 mb-8">Profil</h2>

        <div class="flex gap-2 border-b border-slate-200 mb-8">
            <button @click="activeSettingsTab = 'profil'"
                :class="activeSettingsTab === 'profil' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'"
                class="px-6 py-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">person</span> Mon profil
            </button>
            <button @click="activeSettingsTab = 'paiements'"
                :class="activeSettingsTab === 'paiements' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'"
                class="px-6 py-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">account_balance</span> Paiements & IBAN
            </button>
            <button @click="activeSettingsTab = 'securite'"
                :class="activeSettingsTab === 'securite' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'"
                class="px-6 py-4 font-bold text-sm border-b-2 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">lock</span> Sécurité
            </button>
        </div>

        <div v-if="settingsMessage.text"
            :class="settingsMessage.type === 'error' ? 'bg-red-50 text-red-danger border-red-200' : 'bg-green-50 text-green-success border-green-200'"
            class="p-4 rounded-xl border font-bold text-sm mb-6 transition-all flex items-center gap-3">
            <span class="material-symbols-outlined">{{ settingsMessage.type === 'error' ? 'error' :
                'check_circle' }}</span>
            {{ settingsMessage.text }}
        </div>

        <div v-if="activeSettingsTab === 'profil'"
            class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
            <h3 class="text-xl font-bold text-slate-900 mb-6">Informations personnelles</h3>

            <div class="flex items-center gap-6 mb-8">
                <div
                    class="w-24 h-24 rounded-full bg-primary/10 text-primary font-black flex items-center justify-center overflow-hidden border border-primary/20 text-3xl">
                    <span v-if="!user?.photo">{{ userInitials }}</span>
                    <img v-else :src="user.photo" class="w-full h-full object-cover">
                </div>
                <div>
                    <button
                        class="bg-surface hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-sm transition-all mb-2">Changer
                        la photo</button>
                    <p class="text-xs text-slate-400">JPG ou PNG. Max 2MB.</p>
                </div>
            </div>

            <form @submit.prevent="saveProfile" class="space-y-6 max-w-lg">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom complet</label>
                    <input v-model="profileForm.name" type="text"
                        class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold"
                        required />
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Adresse Email</label>
                    <input v-model="profileForm.email" type="email" placeholder="Sera chargée depuis la BDD..."
                        class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold" />
                </div>
                <button type="submit"
                    class="bg-primary hover:bg-[#5044e6] text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 mt-4">Enregistrer
                    les modifications</button>
            </form>
        </div>

        <div v-if="activeSettingsTab === 'paiements'"
            class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
            <h3 class="text-xl font-bold text-slate-900 mb-2">Coordonnées bancaires</h3>
            <p class="text-sm text-slate-500 mb-8 max-w-xl">Renseignez votre IBAN pour que vos amis puissent
                vous rembourser facilement. Ces informations ne sont visibles que par les membres de vos
                groupes.</p>

            <form @submit.prevent="saveBank" class="space-y-6 max-w-lg">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Numéro de compte (IBAN)</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">account_balance</span>
                        <input v-model="bankForm.iban" type="text" placeholder="BE32 1234 5678 9012"
                            class="w-full pl-12 pr-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold uppercase tracking-wider placeholder-slate-400" />
                    </div>
                </div>
                <button type="submit"
                    class="bg-primary hover:bg-[#5044e6] text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-primary/30">Enregistrer
                    l'IBAN</button>
            </form>
        </div>

        <div v-if="activeSettingsTab === 'securite'" class="space-y-6">
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8">
                <h3 class="text-xl font-bold text-slate-900 mb-6">Changer le mot de passe</h3>

                <form @submit.prevent="saveSecurity" class="space-y-6 max-w-lg">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mot de passe actuel</label>
                        <input v-model="securityForm.oldPassword" type="password"
                            class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900"
                            required />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nouveau mot de passe</label>
                        <input v-model="securityForm.newPassword" type="password"
                            class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900"
                            required />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Confirmer le nouveau mot de
                            passe</label>
                        <input v-model="securityForm.confirmPassword" type="password"
                            class="w-full px-5 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900"
                            required />
                    </div>
                    <button type="submit"
                        class="bg-primary hover:bg-[#5044e6] text-white px-8 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 mt-2">Mettre
                        à jour le mot de passe</button>
                </form>
            </div>

            <div class="bg-red-50/50 rounded-[32px] border border-red-100 p-8 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-red-danger mb-1">Supprimer le compte</h3>
                    <p class="text-sm text-slate-500">Cette action est irréversible. Toutes vos données seront
                        effacées.</p>
                </div>
                <button
                    class="bg-white border-2 border-red-danger text-red-danger hover:bg-red-danger hover:text-white px-6 py-3 rounded-xl font-bold transition-all">Supprimer
                    mon compte</button>
            </div>
        </div>

    </div>
    </main>
    </div>

    <script src="../app.js"></script>
</body>

</html>