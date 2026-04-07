<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Splitz - Connexion & Inscription</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>

    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;500;600;700&family=Pacifico&display=swap"
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

<body class="font-sans bg-gray-50 min-h-screen flex items-center justify-center p-4 text-slate-900">

    <div
        class="max-w-[1000px] w-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[650px]">

        <div class="hidden md:flex md:w-1/2 bg-[#f2f2f2] flex-col p-12 relative overflow-hidden">
            <div class="mb-12">
                <h1 class="text-5xl font-logo text-black mb-1 tracking-wider">Splitz</h1>
            </div>

            <div class="z-10 relative">
                <h2 class="text-4xl font-extrabold leading-[1.1] mb-6">
                    Partagez vos dépenses en toute <span class="text-primary">simplicité</span>
                </h2>
                <p class="text-gray-600 text-lg pr-4">
                    Rejoignez les milliers d'utilisateurs qui ne s'inquiètent plus jamais de la note à la fin de la
                    soirée.
                </p>
            </div>

            <div class="mt-12 w-full flex justify-center relative z-10">
                <img src="../style/img/header.png" alt="Aperçu de l'app"
                    class="max-w-[80%] h-auto drop-shadow-2xl rounded-xl object-contain" />
            </div>
        </div>

        <div class="w-full md:w-1/2 p-8 md:p-14 flex flex-col justify-center bg-white relative">

            <div id="alert-container"></div>

            <div class="flex bg-surface p-1 rounded-lg mb-8 w-full relative z-10">
                <button id="btn-login" onclick="switchTab('login')"
                    class="flex-1 py-2 text-sm font-semibold rounded-md bg-white shadow-sm text-slate-900 transition-all">Connexion</button>
                <button id="btn-register" onclick="switchTab('register')"
                    class="flex-1 py-2 text-sm font-semibold text-slate-500 hover:text-slate-800 transition-all">Inscription</button>
            </div>

            <div class="mb-8 h-[60px]">
                <h3 id="form-title" class="text-2xl font-extrabold text-slate-900 mb-2">Bienvenue !</h3>
                <p id="form-subtitle" class="text-slate-500 text-sm">Veuillez entrer vos informations pour continuer.
                </p>
            </div>

            <div class="min-h-[400px] w-full">

                <form action="../api/connexion.php" method="POST" id="form-login" class="space-y-6 block">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">person</span>
                            <input type="email" name="email" placeholder="exemple@mail.com"
                                class="w-full pl-10 pr-4 py-3 bg-surface border-none rounded-lg focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400"
                                required />
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-slate-700">Mot de passe</label>
                            <a href="#" class="text-xs font-bold text-primary hover:underline">Mot de passe oublié ?</a>
                        </div>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">lock</span>
                            <input type="password" name="password" placeholder="••••••••••••"
                                class="w-full pl-10 pr-4 py-3 bg-surface border-none rounded-lg focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400"
                                required />
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="remember" name="remember"
                            class="w-4 h-4 rounded text-primary border-slate-300 bg-surface focus:ring-primary cursor-pointer" />
                        <label for="remember" class="text-sm text-slate-500 font-medium cursor-pointer">Se souvenir de
                            moi</label>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary hover:bg-[#5044e6] text-white font-bold py-3.5 rounded-lg transition-all flex items-center justify-center gap-2 mt-2">
                        <span>Se connecter</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </form>

                <form action="../api/inscription.php" method="POST" id="form-register" class="space-y-4 hidden">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nom complet</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">badge</span>
                            <input type="text" name="name" placeholder="Merwan Abzar"
                                class="w-full pl-10 pr-4 py-2.5 bg-surface border-none rounded-lg focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400"
                                required />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">mail</span>
                            <input type="email" name="email" placeholder="merwan@exemple.com"
                                class="w-full pl-10 pr-4 py-2.5 bg-surface border-none rounded-lg focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400"
                                required />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">IBAN <span
                                class="text-slate-400 font-normal">(Optionnel)</span></label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">account_balance</span>
                            <input type="text" name="iban" placeholder="BE32 1234..."
                                class="w-full pl-10 pr-4 py-2.5 bg-surface border-none rounded-lg focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Mot de passe</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">lock</span>
                            <input type="password" name="password" placeholder="••••••••••••"
                                class="w-full pl-10 pr-4 py-2.5 bg-surface border-none rounded-lg focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400"
                                required />
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary hover:bg-[#5044e6] text-white font-bold py-3.5 rounded-lg transition-all flex items-center justify-center gap-2 mt-4">
                        <span>Créer mon compte</span>
                        <span class="material-symbols-outlined text-[18px]">person_add</span>
                    </button>
                </form>
            </div>

            <p class="mt-4 text-center text-xs text-slate-400 leading-relaxed">
                En continuant, vous acceptez nos <a href="#" class="text-primary font-bold hover:underline">Conditions
                    d'utilisation</a> et<br /> notre <a href="#"
                    class="text-primary font-bold hover:underline">Politique de confidentialité</a>.
            </p>

        </div>
    </div>

    <script>
        function switchTab(tab) {
            const formLogin = document.getElementById('form-login');
            const formRegister = document.getElementById('form-register');
            const btnLogin = document.getElementById('btn-login');
            const btnRegister = document.getElementById('btn-register');
            const title = document.getElementById('form-title');
            const subtitle = document.getElementById('form-subtitle');

            const activeClass = "flex-1 py-2 text-sm font-semibold rounded-md bg-white shadow-sm text-slate-900 transition-all";
            const inactiveClass = "flex-1 py-2 text-sm font-semibold text-slate-500 hover:text-slate-800 transition-all";

            if (tab === 'login') {
                formLogin.classList.remove('hidden'); formLogin.classList.add('block');
                formRegister.classList.remove('block'); formRegister.classList.add('hidden');
                btnLogin.className = activeClass; btnRegister.className = inactiveClass;
                title.innerText = "Bienvenue !"; subtitle.innerText = "Veuillez entrer vos informations pour continuer.";
            } else {
                formRegister.classList.remove('hidden'); formRegister.classList.add('block');
                formLogin.classList.remove('block'); formLogin.classList.add('hidden');
                btnRegister.className = activeClass; btnLogin.className = inactiveClass;
                title.innerText = "Rejoignez Splitz"; subtitle.innerText = "Créez votre compte pour commencer à partager.";
            }
        }

        // Script pour lire les erreurs dans l'URL et afficher l'alerte
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        const tab = urlParams.get('tab');

        if (tab === 'register') {
            switchTab('register');
        }

        if (error) {
            const alertDiv = document.createElement('div');
            alertDiv.className = "mb-4 p-4 rounded-lg text-sm font-bold text-center bg-red-50 text-red-danger border border-red-200";
            if (error === 'invalid') alertDiv.innerText = "Email ou mot de passe incorrect.";
            else if (error === 'exists') alertDiv.innerText = "Cette adresse email est déjà utilisée.";
            else if (error === 'empty') alertDiv.innerText = "Veuillez remplir tous les champs.";
            else alertDiv.innerText = "Une erreur est survenue.";
            document.getElementById('alert-container').appendChild(alertDiv);
        }
    </script>
</body>

</html>