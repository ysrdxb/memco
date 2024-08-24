import { createApp } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import Echo from 'laravel-echo';

require('./bootstrap');

// ...

const el = document.getElementById('app');

createInertiaApp({
  resolve: (name) => require(`./Pages/${name}`),
  setup({ el, app, props }) {
    return createApp({ render: () => h(app, props) })
      .use(InertiaApp, { ...props, resolveComponent: name => require(`./Pages/${name}`).default })
      .mount(el);
  },
});

InertiaProgress.init();

window.Pusher = require('pusher-js');

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  cluster: process.env.MIX_PUSHER_APP_CLUSTER,
  encrypted: true,
});

Echo.private(`App.Models.User.${userId}`)
  .notification((notification) => {
    // Handle the received notification here
    alert(notification);
    console.log('Received notification:', notification);
    // You can display the notification to the user using a toast, modal, or other UI component
  });
