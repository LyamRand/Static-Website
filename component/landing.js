import { store, userInitials } from '../store.js';

export default {
    setup() {
        const features = [
            { icon: "💰", title: "Suivi des dépenses", desc: "Ajoutez vos dépenses en quelques clics et gardez une trace de chaque transaction en toute simplicité." },
            { icon: "🔄", title: "Équilibre automatique", desc: "Splitz calcule instantanément qui doit combien à qui, en minimisant le nombre de transferts nécessaires." },
            { icon: "👥", title: "Gestion de groupes illimités", desc: "Créez des groupes pour vos voyages, colocations ou événements entre amis, sans aucune limite." }
        ];

        const footerCols = [
            { title: "PRODUIT", links: ["Fonctionnalités", "Sécurité", "Prix", "Avis clients"] },
            { title: "RESSOURCES", links: ["Centre d'aide", "Guides & Tutoriels", "Blog", "API"] },
            { title: "SOCIÉTÉ", links: ["À propos", "Carrières", "Contact", "Mentions légales"] }
        ];

        return { store, userInitials, features, footerCols };
    },
    template: `
    <div class="bg-white min-h-screen font-sans">
        <nav class="bg-white border-b border-gray-200">
            <div class="w-full mx-auto px-4 sm:px-6 lg:px-16 py-4 flex items-center justify-between">
                <router-link to="/" class="font-logo text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Splitz</router-link>
                
                <div v-if="store.user" class="flex gap-4 items-center">
                    <span class="text-sm font-bold text-slate-700 hidden sm:block">Salut, {{ store.user.name }}</span>
                    <router-link to="/dashboard" class="bg-black text-white text-sm sm:text-base font-semibold px-4 sm:px-6 py-2 rounded-full hover:bg-gray-800 transition shadow-sm">
                        Tableau de bord
                    </router-link>
                    <button @click="store.logout()" class="bg-red-50 text-red-600 border border-red-200 text-sm sm:text-base font-semibold px-4 sm:px-6 py-2 rounded-full hover:bg-red-100 transition shadow-sm">
                        Déconnexion
                    </button>
                </div>
                <router-link v-else to="/auth" class="bg-primary text-white text-sm sm:text-base font-semibold px-4 sm:px-6 py-2 rounded-full hover:opacity-90 transition shadow-sm">
                    Connexion
                </router-link>
            </div>
        </nav>

        <section class="w-full mx-auto px-4 sm:px-6 lg:px-16 py-12 sm:py-20 flex flex-col md:flex-row items-center gap-10 lg:gap-16 text-center md:text-left">
            <div class="flex-1 w-full">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black leading-tight mb-4">
                    Partagez vos dépenses en toute
                    <span class="text-primary"> simplicité</span>
                </h1>
                <p class="text-gray-500 text-base sm:text-lg mb-8">
                    Gérez vos comptes entre amis sans stress.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <router-link to="/auth" class="block w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-[10px] transition text-center font-bold">
                        Commencer gratuitement
                    </router-link>
                    <a href="#scroll-down" class="w-full sm:w-auto border-2 border-gray-300 hover:border-primary px-6 py-3 rounded-[10px] transition text-center text-slate-700 font-bold">
                        Splitz, c'est quoi ?
                    </a>
                </div>
            </div>
            <div class="flex-1 flex justify-center items-center w-full mt-10 md:mt-0">
                <img src="./style/img/header.png" alt="Aperçu de l'application Splitz" class="w-full max-w-sm lg:max-w-xl rounded-2xl shadow-xl object-contain" />
            </div>
        </section>

        <section id="scroll-down" class="w-full bg-surface py-12 md:py-20 px-4 sm:px-6 lg:px-16 text-center">
            <div class="w-full mx-auto">
                <h2 class="text-2xl sm:text-4xl font-black mb-3 md:mb-4">Pourquoi choisir Splitz ?</h2>
                <p class="text-gray-500 text-base sm:text-lg mb-10 md:mb-16 max-w-2xl mx-auto">
                    La solution complète pour gérer vos finances partagées sans le moindre effort.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-16 md:gap-8 hover:cursor-default">
                    <div v-for="f in features" :key="f.title" class="bg-white rounded-2xl p-6 md:p-8 text-left shadow-md">
                        <div class="w-12 h-12 md:w-14 md:h-14 bg-primary/10 rounded-xl flex items-center justify-center text-2xl md:text-3xl mb-4 md:mb-5">
                            {{ f.icon }}
                        </div>
                        <h3 class="font-bold text-lg md:text-xl mb-2">{{ f.title }}</h3>
                        <p class="text-gray-500 text-sm md:text-base leading-relaxed">{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-6 py-20">
            <div class="bg-primary rounded-3xl p-14 flex flex-col md:flex-row items-center justify-between gap-12 overflow-hidden shadow-xl shadow-primary/20">
                <div class="flex-1 text-white">
                    <h2 class="text-4xl mb-4 font-black leading-tight">Prêt à simplifier<br>vos paiements ?</h2>
                    <p class="text-white/80 text-sm leading-relaxed mb-8 max-w-md">
                        Rejoignez les milliers d'utilisateurs qui ne s'inquiètent plus jamais de la note à la fin de la soirée.
                    </p>
                    <div class="flex gap-4 flex-wrap">
                        <a href="#" class="block hover:scale-105 transition-transform duration-300">
                            <img src="./style/img/apple-store-v2.png" alt="Télécharger sur l'App Store" class="h-12 sm:h-14 w-auto object-contain">
                        </a>
                        <a href="#" class="block hover:scale-105 transition-transform duration-300">
                            <img src="./style/img/google-play-v2.png" alt="Disponible sur Google Play" class="h-12 sm:h-14 w-auto object-contain">
                        </a>
                    </div>
                </div>
                <div class="flex-shrink-0 flex justify-center">
                    <img src="./style/img/cta.png" alt="Smartphone" class="w-48 md:w-56 h-auto object-contain drop-shadow-2xl">
                </div>
            </div>
        </section>

        <footer class="bg-white border-t border-gray-200 px-6 pt-12 pb-6">
            <div class="max-w-7xl mx-auto flex flex-wrap gap-12 mb-10">
                <div class="flex-1 min-w-[200px]">
                    <span class="text-xl font-black font-logo">Splitz</span>
                    <p class="text-gray-400 text-sm mt-2">Partagez. Réglez. Profitez.</p>
                </div>
                <div class="flex gap-12 flex-wrap">
                    <div v-for="col in footerCols" :key="col.title" class="flex flex-col gap-2">
                        <h4 class="text-xs font-bold text-gray-600 tracking-wider">{{ col.title }}</h4>
                        <a v-for="link in col.links" :key="link" href="#" class="text-sm text-gray-400 hover:text-primary transition">{{ link }}</a>
                    </div>
                </div>
            </div>
            <div class="max-w-6xl mx-auto border-t border-gray-100 pt-6 text-xs text-gray-400 text-center sm:text-left">
                © 2026 Splitz. Tous droits réservés.
            </div>
        </footer>
    </div>`
};
