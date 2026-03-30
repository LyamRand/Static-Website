<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Splitz - Ajouter une dépense</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>

    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@300,0..1&display=swap"
        rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#6155F5",
                        "surface": "#F3F4F6", // Gris clair pour les inputs et boutons inactifs
                    },
                    fontFamily: {
                        "sans": ["Fira Sans", "sans-serif"]
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans bg-slate-500 min-h-screen flex items-center justify-center p-4 text-slate-900">

    <div class="bg-white w-full max-w-[650px] rounded-[32px] shadow-2xl overflow-hidden flex flex-col p-8 md:p-10">

        <h2 class="text-3xl font-extrabold text-slate-900 mb-6">Ajouter une dépense</h2>
        <hr class="border-slate-100 mb-8" />

        <form class="space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Montant (€)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-lg">€</span>
                        <input type="text" placeholder="0,00"
                            class="w-full pl-10 pr-4 py-4 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold placeholder-slate-400" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                    <input type="text" placeholder="Qu'avez-vous payé ?"
                        class="w-full px-4 py-4 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 font-semibold placeholder-slate-400" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Catégorie</label>
                <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide" id="category-container">
                    <button type="button" onclick="selectCategory(this)"
                        class="category-btn flex flex-col items-center justify-center min-w-[85px] h-[85px] rounded-2xl transition-all bg-primary/20 border-2 border-primary text-primary">
                        <span class="material-symbols-outlined text-[32px] mb-1">restaurant</span>
                        <span class="text-sm font-bold">Repas</span>
                    </button>
                    <button type="button" onclick="selectCategory(this)"
                        class="category-btn flex flex-col items-center justify-center min-w-[85px] h-[85px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-400 hover:bg-slate-200 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[32px] mb-1">directions_car</span>
                        <span class="text-sm font-bold">Transport</span>
                    </button>
                    <button type="button" onclick="selectCategory(this)"
                        class="category-btn flex flex-col items-center justify-center min-w-[85px] h-[85px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-400 hover:bg-slate-200 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[32px] mb-1">home</span>
                        <span class="text-sm font-bold">Logement</span>
                    </button>
                    <button type="button" onclick="selectCategory(this)"
                        class="category-btn flex flex-col items-center justify-center min-w-[85px] h-[85px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-400 hover:bg-slate-200 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[32px] mb-1">shopping_cart</span>
                        <span class="text-sm font-bold">Courses</span>
                    </button>
                    <button type="button" onclick="selectCategory(this)"
                        class="category-btn flex flex-col items-center justify-center min-w-[85px] h-[85px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-400 hover:bg-slate-200 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[32px] mb-1">more_horiz</span>
                        <span class="text-sm font-bold">Autres</span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Payé par</label>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" onclick="togglePayer(this)"
                        class="payer-btn flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm transition-all bg-primary/20 border border-primary text-primary">
                        <div class="w-7 h-7 rounded-full bg-slate-300 overflow-hidden flex items-center justify-center">
                            <span class="material-symbols-outlined text-slate-500 text-[18px]">person</span>
                        </div>
                        Moi
                    </button>
                    <button type="button" onclick="togglePayer(this)"
                        class="payer-btn flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm transition-all bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">
                        <div
                            class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-black">
                            L</div>
                        Lucas
                    </button>
                    <button type="button" onclick="togglePayer(this)"
                        class="payer-btn flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm transition-all bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">
                        <div
                            class="w-7 h-7 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-black">
                            C</div>
                        Camille
                    </button>
                    <button type="button" onclick="togglePayer(this)"
                        class="payer-btn flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm transition-all bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">
                        <div
                            class="w-7 h-7 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs font-black">
                            M</div>
                        Marie
                    </button>

                    <button type="button"
                        class="flex items-center justify-center w-[40px] h-[40px] rounded-full bg-white border border-slate-200 text-slate-400 hover:bg-slate-50 transition-all">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Options de partage</label>
                <div class="flex bg-surface p-1.5 rounded-xl w-full">
                    <button type="button" id="btn-share-equal" onclick="switchShareOption('equal')"
                        class="flex-1 py-3.5 text-sm font-bold rounded-lg bg-white shadow-sm text-slate-900 transition-all">
                        Partager équitablement
                    </button>
                    <button type="button" id="btn-share-custom" onclick="switchShareOption('custom')"
                        class="flex-1 py-3.5 text-sm font-bold text-slate-400 hover:text-slate-700 transition-all">
                        Montants personnalisés (%)
                    </button>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit"
                    class="flex-[2] py-4 rounded-xl bg-primary hover:bg-[#5044e6] text-white font-bold text-base transition-all shadow-lg shadow-primary/30">
                    Enregistrer la dépense
                </button>
                <button type="button"
                    class="flex-[1] py-4 rounded-xl bg-surface hover:bg-slate-200 text-slate-600 font-bold text-base transition-all">
                    Annuler
                </button>
            </div>

        </form>
    </div>

    <script>
        // 1. Sélection unique pour les catégories
        function selectCategory(clickedBtn) {
            const allBtns = document.querySelectorAll('.category-btn');

            // On remet tous les boutons en gris (inactifs)
            allBtns.forEach(btn => {
                btn.className = "category-btn flex flex-col items-center justify-center min-w-[85px] h-[85px] rounded-2xl transition-all bg-surface border-2 border-transparent text-slate-400 hover:bg-slate-200 hover:text-slate-600";
            });

            // On met le bouton cliqué en mauve (actif)
            clickedBtn.className = "category-btn flex flex-col items-center justify-center min-w-[85px] h-[85px] rounded-2xl transition-all bg-primary/20 border-2 border-primary text-primary";
        }

        // 2. Sélection multiple pour "Payé par"
        function togglePayer(btn) {
            // Si le bouton contient déjà la classe bg-primary/20, c'est qu'il est actif
            const isActive = btn.classList.contains('bg-primary/20');

            if (isActive) {
                // S'il est actif, on le désactive (gris)
                btn.className = "payer-btn flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm transition-all bg-white border border-slate-200 text-slate-600 hover:bg-slate-50";
            } else {
                // S'il est inactif, on l'active (mauve)
                btn.className = "payer-btn flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm transition-all bg-primary/20 border border-primary text-primary";
            }
        }

        // 3. Bascule pour les options de partage
        function switchShareOption(option) {
            const btnEqual = document.getElementById('btn-share-equal');
            const btnCustom = document.getElementById('btn-share-custom');

            // Les classes CSS à appliquer
            const activeClass = "flex-1 py-3.5 text-sm font-bold rounded-lg bg-white shadow-sm text-slate-900 transition-all";
            const inactiveClass = "flex-1 py-3.5 text-sm font-bold text-slate-400 hover:text-slate-700 transition-all";

            if (option === 'equal') {
                btnEqual.className = activeClass;
                btnCustom.className = inactiveClass;
            } else {
                btnCustom.className = activeClass;
                btnEqual.className = inactiveClass;
            }
        }
    </script>
</body>

</html>