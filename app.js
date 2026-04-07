import { store } from './store.js';

// Components
import Landing from './component/landing.js';
import Auth from './component/auth.js';
import Layout from './component/layout.js';
import Dashboard from './component/dashboard.js';
import Groupes from './component/groupes.js';
import GroupDetail from './component/group_detail.js';
import Account from './component/account.js';

const { createApp, onMounted } = Vue;
const { createRouter, createWebHashHistory } = VueRouter;

// Require authentication guard
const requireAuth = async (to, from, next) => {
    if (!store.user) await store.checkAuth();
    if (!store.user) next('/auth');
    else next();
};

const routes = [
    { path: '/', component: Landing },
    { path: '/auth', component: Auth },
    {
        path: '/',
        component: Layout,
        beforeEnter: requireAuth,
        children: [
            { path: 'dashboard', component: Dashboard },
            { path: 'groupes', component: Groupes },
            { path: 'groupe/:id', component: GroupDetail },
            { path: 'account', component: Account }
        ]
    }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes
});

const app = createApp({
    setup() {
        onMounted(async () => {
            await store.checkAuth();
        });
        return { store };
    }
});

app.use(router);
app.mount('#app');