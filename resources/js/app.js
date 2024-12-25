import './bootstrap';
import { createApp } from 'vue';

import CartComponent from './components/CartComponent.vue';

const app = createApp({});

app.component('cart-component', CartComponent);

app.mount('#app');
