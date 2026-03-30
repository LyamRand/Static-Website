<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Splitz - Mes groupes</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>

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
                        "surface": "#F3F4F6", // Gris très clair
                        "surface-dark": "#E5E7EB", // Gris pour boutons inactifs
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

    <aside class="w-72 bg-white flex flex-col fixed h-full border-r border-slate-100">

        <div class="p-8 pb-12">
            <h1 class="text-5xl font-logo text-black tracking-wider">Splitz</h1>
        </div>

        <nav class="flex-1 px-4 space-y-3">
            <a href="tableau-bord.php"
                class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold hover:bg-slate-300 transition-all">
                <span class="material-symbols-outlined text-[26px]">grid_view</span>
                <span class="text-[15px]">Tableau de bord</span>
            </a>

            <a href="#"
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
                <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-900">Merwan Abzar</p>
                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">Compte personnel</p>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-slate-200 overflow-hidden border border-slate-200">
                        <img src="https://i.pravatar.cc/150?img=11" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </header>

        <div class="p-10 max-w-[1100px] w-full mx-auto">

            <div class="flex items-center justify-between mb-10">

                <div class="flex items-center gap-5">
                    <div class="w-[75px] h-[75px] bg-surface-dark rounded-[24px] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[42px] text-slate-900">group</span>
                    </div>
                    <h2 class="text-[40px] font-extrabold tracking-tight text-slate-900">Mes groupes</h2>
                </div>

                <button>
                    <a href="newgroup.php"
                        class="bg-primary hover:bg-[#5044e6] text-white px-7 py-4 rounded-[16px] font-bold text-lg transition-all shadow-lg shadow-primary/30 flex items-center gap-2">
                        <span class="material-symbols-outlined">add_circle</span>
                        Ajouter un groupe
                    </a>
                </button>
            </div>

            <div class="flex flex-col gap-6">

                <div
                    class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow cursor-pointer">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-[20px] bg-slate-100 flex items-center justify-center text-[32px]">
                            ⛷️</div>
                        <div>
                            <h4 class="text-[22px] font-black text-slate-900 leading-tight mb-1">Vacances Ski</h4>
                            <p class="text-sm font-medium text-slate-400">4 participants</p>
                        </div>
                    </div>
                    <div class="bg-surface rounded-2xl px-6 py-4 flex items-center gap-6 min-w-[250px]">
                        <span class="text-sm font-bold text-slate-500">Solde actuel</span>
                        <span class="text-xl font-black text-green-success ml-auto">+45,00 €</span>
                    </div>
                </div>

                <div
                    class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow cursor-pointer">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-[20px] bg-slate-100 flex items-center justify-center text-[32px]">
                            ☀️</div>
                        <div>
                            <h4 class="text-[22px] font-black text-slate-900 leading-tight mb-1">Voyage Sud</h4>
                            <p class="text-sm font-medium text-slate-400">12 participants</p>
                        </div>
                    </div>
                    <div class="bg-surface rounded-2xl px-6 py-4 flex items-center gap-6 min-w-[250px]">
                        <span class="text-sm font-bold text-slate-500">Solde actuel</span>
                        <span class="text-xl font-black text-red-danger ml-auto">-22,50 €</span>
                    </div>
                </div>

            </div>

        </div>
    </main>
</body>

</html>