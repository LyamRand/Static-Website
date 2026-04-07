import { store } from '../store.js';
const { ref, reactive } = Vue;
const { useRouter } = VueRouter;

export default {
  setup() {
    const router = useRouter();
    const activeTab = ref('login');
    const status = reactive({ message: '', error: false });
    const form = reactive({ email: '', password: '', nom: '' });

    const handleAuth = async () => {
      status.message = "Chargement...";
      status.error = false;

      if (activeTab.value === 'register') {
        try {
          const res = await fetch('./api/inscription.php', {
            method: 'POST',
            body: JSON.stringify({
              nom: form.nom,
              email: form.email,
              password: form.password
            })
          });
          const data = await res.json();
          if (data.success) {
            status.message = "Inscription réussie ! Connexion en cours...";
            status.error = false;
            await store.checkAuth();
            setTimeout(() => { router.push('/dashboard'); }, 800);
          } else {
            status.message = data.error || "Erreur lors de l'inscription.";
            status.error = true;
          }
        } catch (e) {
          status.message = "Erreur de connexion au serveur.";
          status.error = true;
        }
      } else if (activeTab.value === 'login') {
        try {
          const res = await fetch('./api/connexion.php', {
            method: 'POST',
            body: JSON.stringify({
              email: form.email,
              password: form.password
            })
          });
          const data = await res.json();

          if (data.success) {
            status.message = "Connexion réussie ! Redirection en cours...";
            status.error = false;
            await store.checkAuth();
            setTimeout(() => { router.push('/dashboard'); }, 800);
          } else {
            status.message = "Identifiants incorrects.";
            status.error = true;
          }
        } catch (e) {
          status.message = "Erreur de connexion au serveur.";
          status.error = true;
        }
      }
    };

    return { activeTab, status, form, handleAuth };
  },
  template: `
    <div class="font-sans bg-[#F9FAFB] min-h-screen flex items-center justify-center p-4 text-slate-900 w-full">
      <div class="max-w-[1000px] w-full bg-white rounded-[32px] shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[650px] border border-slate-100">

        <div class="hidden md:flex md:w-1/2 bg-[#f2f2f2] flex-col p-12 relative overflow-hidden">
            <div class="mb-12 cursor-pointer" @click="$router.push('/')">
                <h1 class="text-5xl font-logo text-black mb-1 tracking-wider">Splitz</h1>
                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Partagez. Réglez. Profitez.</p>
            </div>

            <div class="z-10 relative">
                <h2 class="text-4xl font-extrabold leading-[1.1] mb-6">
                    Partagez vos dépenses en toute <span class="text-primary">simplicité</span>
                </h2>
                <p class="text-gray-600 text-lg pr-4">
                    Rejoignez les milliers d'utilisateurs qui ne s'inquiètent plus jamais de la note à la fin de la soirée.
                </p>
            </div>

            <div class="mt-12 w-full flex justify-center relative z-10">
                <img src="./style/img/header.png" alt="Aperçu de l'app" class="max-w-[80%] h-auto drop-shadow-2xl rounded-xl object-contain" />
            </div>
        </div>

        <div class="w-full md:w-1/2 p-8 md:p-14 flex flex-col justify-center bg-white relative">

          <div v-if="status.message" :class="['mb-4 p-4 rounded-xl text-sm font-bold text-center border', status.error ? 'bg-red-50 text-red-danger border-red-200' : 'bg-green-50 text-green-success border-green-200']">
            {{ status.message }}
          </div>

          <div class="flex bg-surface p-1 rounded-xl mb-8 w-full border border-slate-100">
            <button @click="activeTab = 'login'; status.message = ''"
              :class="[activeTab === 'login' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500 hover:text-slate-800']"
              class="flex-1 py-3 text-sm font-semibold rounded-lg transition-all">
              Connexion
            </button>
            <button @click="activeTab = 'register'; status.message = ''"
              :class="[activeTab === 'register' ? 'bg-white shadow-sm text-slate-900' : 'text-slate-500 hover:text-slate-800']"
              class="flex-1 py-3 text-sm font-semibold rounded-lg transition-all">
              Inscription
            </button>
          </div>

          <div class="mb-8 h-[60px]">
            <h3 class="text-2xl font-extrabold text-slate-900 mb-2">
              {{ activeTab === 'login' ? 'Bienvenue !' : 'Rejoignez Splitz' }}
            </h3>
            <p class="text-slate-500 text-sm font-medium">
              {{ activeTab === 'login' ? 'Veuillez entrer vos informations pour continuer.' : 'Créez votre compte pour commencer à partager.' }}
            </p>
          </div>

          <form v-if="activeTab === 'login'" class="space-y-6 min-h-[400px]" @submit.prevent="handleAuth">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">person</span>
                <input v-model="form.email" type="email" placeholder="exemple@mail.com"
                  class="w-full pl-12 pr-4 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400 font-semibold"
                  required />
              </div>
            </div>

            <div>
              <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-bold text-slate-700">Mot de passe</label>
                <a href="#" class="text-xs font-bold text-primary hover:underline">Mot de passe oublié ?</a>
              </div>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">lock</span>
                <input v-model="form.password" type="password" placeholder="••••••••••••"
                  class="w-full pl-12 pr-4 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400 font-semibold"
                  required />
              </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
              <input type="checkbox" id="remember" class="w-4 h-4 rounded text-primary border-slate-300 bg-surface focus:ring-primary cursor-pointer" />
              <label for="remember" class="text-sm text-slate-500 font-bold cursor-pointer">Se souvenir de moi</label>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-[#5044e6] text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/30 flex items-center justify-center gap-2 mt-2">
              <span>Se connecter</span>
              <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </button>
          </form>

          <form v-if="activeTab === 'register'" class="space-y-4 min-h-[400px]" @submit.prevent="handleAuth">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Nom complet</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">badge</span>
                <input v-model="form.nom" type="text" placeholder="Merwan Abzar"
                  class="w-full pl-12 pr-4 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400 font-semibold"
                  required />
              </div>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">mail</span>
                <input v-model="form.email" type="email" placeholder="merwan@exemple.com"
                  class="w-full pl-12 pr-4 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400 font-semibold"
                  required />
              </div>
            </div>

            <div>
              <label class="block text-sm font-bold text-slate-700 mb-2">Mot de passe</label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">lock</span>
                <input v-model="form.password" type="password" placeholder="••••••••••••"
                  class="w-full pl-12 pr-4 py-3.5 bg-surface border-none rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all text-slate-900 placeholder-slate-400 font-semibold"
                  required />
              </div>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-[#5044e6] text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/30 flex items-center justify-center gap-2 mt-4">
              <span>Créer mon compte</span>
              <span class="material-symbols-outlined text-[18px]">person_add</span>
            </button>
          </form>

          <p class="mt-4 text-center text-xs text-slate-400 font-medium leading-relaxed">
            En continuant, vous acceptez nos
            <a href="#" class="text-primary font-bold hover:underline">Conditions d'utilisation</a> et<br/>
            notre <a href="#" class="text-primary font-bold hover:underline">Politique de confidentialité</a>.
          </p>
        </div>
      </div>
    </div>
    `
}