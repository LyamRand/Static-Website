import LandingPage from './component/landing.js'
import Auth from './component/auth.js'

const { createApp, ref } = Vue
const { createRouter, createWebHashHistory } = VueRouter

const routes = [
    { path: '/', component: LandingPage },
    { path: '/auth', component: Auth }
]

const router = createRouter({
    history: createWebHashHistory(),
    routes,
})

const app = createApp({
    setup() {
        const features = ref([
            {
                icon: '👥',
                title: 'Gestion de groupe',
                desc: "Créez des groupes illimités pour chaque occasion : coloc, vacances, ou simples sorties entre amis."
            },
            {
                icon: '📊',
                title: 'Suivi des dépenses',
                desc: "Visualisez qui doit quoi en un coup d'œil avec des graphiques clairs et des historiques détaillés."
            },
            {
                icon: '⚡',
                title: 'Paiements instantanés',
                desc: "Réglez vos dettes en un clic via nos intégrations de paiement sécurisées et directes."
            }
        ])
        const footerCols = ref([
            { title: 'TITRE COLONNE', links: ['Page', 'Page', 'Page'] },
            { title: 'TITRE COLONNE', links: ['Page', 'Page', 'Page'] },
            { title: 'TITRE COLONNE', links: ['Page', 'Page', 'Page'] },
        ])

        return { features, footerCols }
    },
})

app.use(router)
app.mount('#app')