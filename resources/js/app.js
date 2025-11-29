import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import EditorPage from './views/EditorPage.vue';

// Root-Element holen und cardId aus dem data-Attribut lesen
const appElement = document.getElementById('app');
const cardId = Number(appElement.dataset.cardId);

// Vue-App erstellen und EditorPage als Root-Komponente mounten
const app = createApp(EditorPage, { cardId });

app.use(createPinia());

app.mount('#app');
