<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Splitz - Ajouter un groupe</title>

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

<body class="font-sans bg-[#F9FAFB] text-slate-900 flex min-h-screen relative overflow-hidden">

    <aside class="w-72 bg-white flex flex-col fixed h-full border-r border-slate-100">
        <div class="p-8 pb-12">
            <h1 class="text-5xl font-logo text-black tracking-wider">Splitz</h1>
        </div>
        <nav class="flex-1 px-4 space-y-3">
            <div class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-surface-dark text-slate-700 font-bold">
                <span class="material-symbols-outlined text-[26px]">grid_view</span>
                <span class="text-[15px]">Tableau de bord</span>
            </div>
            <div class="flex items-center gap-4 px-6 py-4 rounded-2xl bg-primary/20 text-primary font-bold shadow-sm">
                <span class="material-symbols-outlined text-[26px]">group</span>
                <span class="text-[15px]">Mes groupes</span>
            </div>
        </nav>
    </aside>

    <main class="flex-1 ml-72 flex flex-col min-h-screen">
        <header class="h-[90px] bg-[#F9FAFB] px-10 flex items-center justify-between border-b border-slate-200/60">
        </header>
        <div class="p-10 max-w-[1100px] w-full mx-auto">
            <div class="flex items-center justify-between mb-10">
                <div class="flex items-center gap-5">
                    <div class="w-[75px] h-[75px] bg-surface-dark rounded-[24px] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[42px]">group</span>
                    </div>
                    <h2 class="text-[40px] font-extrabold tracking-tight">Mes groupes</h2>
                </div>
            </div>
            <div class="flex flex-col gap-6">
                <div class="bg-white rounded-[32px] h-[120px] border border-slate-100 shadow-sm"></div>
                <div class="bg-white rounded-[32px] h-[120px] border border-slate-100 shadow-sm"></div>
            </div>
        </div>
    </main>

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">

        <div class="bg-white w-full max-w-[600px] rounded-[32px] shadow-2xl overflow-hidden flex flex-col p-8 md:p-10">
            <h2 class="text-3xl font-extrabold text-slate-900 mb-6">Nouveau groupe</h2>
            <hr class="border-slate-100 mb-8" />

            <form class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom du groupe</label>
                    <input type="text" placeholder="Comment s'appelle le groupe ?"
                        class="w-full px-5 py-4 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold placeholder-slate-400" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3">Icône du groupe</label>
                    <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide" id="icon-container">
                        <button type="button" onclick="selectGroupIcon(this)"
                            class="group-icon-btn flex items-center justify-center min-w-[75px] h-[75px] rounded-2xl transition-all bg-primary/20 border-2 border-primary text-primary">
                            <span class="material-symbols-outlined text-[32px]">home</span>
                        </button>
                        <button type="button" onclick="selectGroupIcon(this)"
                            class="group-icon-btn flex items-center justify-center min-w-[75px] h-[75px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-600 hover:bg-slate-200">
                            <span class="material-symbols-outlined text-[32px]">flight</span>
                        </button>
                        <button type="button" onclick="selectGroupIcon(this)"
                            class="group-icon-btn flex items-center justify-center min-w-[75px] h-[75px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-600 hover:bg-slate-200">
                            <span class="material-symbols-outlined text-[32px]">landscape</span>
                        </button>
                        <button type="button" onclick="selectGroupIcon(this)"
                            class="group-icon-btn flex items-center justify-center min-w-[75px] h-[75px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-600 hover:bg-slate-200">
                            <span class="material-symbols-outlined text-[32px]">sports_bar</span>
                        </button>
                        <button type="button" onclick="selectGroupIcon(this)"
                            class="group-icon-btn flex items-center justify-center min-w-[75px] h-[75px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-600 hover:bg-slate-200">
                            <span class="material-symbols-outlined text-[32px]">more_horiz</span>
                        </button>
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
                    <a href="groupes.html"
                        class="flex-[1] flex items-center justify-center py-4 rounded-xl bg-surface hover:bg-slate-200 text-slate-600 font-bold text-base transition-all">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectGroupIcon(clickedBtn) {
            const allBtns = document.querySelectorAll('.group-icon-btn');
            allBtns.forEach(btn => {
                btn.className = "group-icon-btn flex items-center justify-center min-w-[75px] h-[75px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-600 hover:bg-slate-200";
            });
            clickedBtn.className = "group-icon-btn flex items-center justify-center min-w-[75px] h-[75px] rounded-2xl transition-all bg-primary/20 border-2 border-primary text-primary";
        }
    </script>
</body>

</html>