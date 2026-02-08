<template>
  <q-layout view="hHh Lpr fFf">
    <q-header :elevated="!$q.dark.isActive" :class="$q.dark.isActive ? 'bg-dark' : 'bg-primary'">
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />

        <q-toolbar-title> News Heram </q-toolbar-title>

        <q-item v-if="isAdmin" clickable v-ripple class="bg-grey-9 text-white q-ms-sm rounded-borders" @click="routeToAdmin">
          <q-item-section avatar>
            <q-icon name="admin_panel_settings" color="accent"/>
          </q-item-section>
          <q-item-section>
            Admin Panel
          </q-item-section>
        </q-item>

        <div>
          <q-btn :color="$q.dark.isActive ? 'negative' : 'red'" class="q-mx-sm" @click="logout" label="logout"/>
        </div>
        <q-btn flat  dense round :icon="$q.dark.isActive ? 'light_mode' : 'dark_mode'" @click="toggleDark"/>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <q-list>
        <q-item-label header> Menu </q-item-label>

        <EssentialLink v-for="link in linksList" :key="link.title" v-bind="link" />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { computed, ref } from 'vue'
import EssentialLink from 'components/EssentialLink.vue'
import { Dark } from 'quasar'
import { useAuthStore } from 'src/stores/authStore'
import { useRouter } from 'vue-router';
import { useQuasaMsgs } from 'src/helper/quasaDialogs';

const notify = useQuasaMsgs();
const router = useRouter()
const authStore = useAuthStore();
const loading = ref(false)

Dark.set(true)

const linksList = [
  {
    title: 'Docs',
    caption: 'quasar.dev',
    icon: 'school',
    link: 'https://quasar.dev',
  },
  {
    title: 'Github',
    caption: 'github.com/quasarframework',
    icon: 'code',
    link: 'https://github.com/quasarframework',
  },
  {
    title: 'Discord Chat Channel',
    caption: 'chat.quasar.dev',
    icon: 'chat',
    link: 'https://chat.quasar.dev',
  },
  {
    title: 'Forum',
    caption: 'forum.quasar.dev',
    icon: 'record_voice_over',
    link: 'https://forum.quasar.dev',
  },
  {
    title: 'Twitter',
    caption: '@quasarframework',
    icon: 'rss_feed',
    link: 'https://twitter.quasar.dev',
  },
  {
    title: 'Facebook',
    caption: '@QuasarFramework',
    icon: 'public',
    link: 'https://facebook.quasar.dev',
  },
  {
    title: 'Quasar Awesome',
    caption: 'Community Quasar projects',
    icon: 'favorite',
    link: 'https://awesome.quasar.dev',
  },
]

const leftDrawerOpen = ref(false)

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function toggleDark(){
  Dark.toggle()
}

const isAdmin = computed(()=> {
  return authStore.user?.roles?.some(role => role.name === 'admin')
})

function routeToAdmin() {
  router.push('/admin')
}

async function logout  () {
  loading.value = true;
  try{
    const response = await authStore.logout();
    console.log(response);
    router.push('/auth/login')
    notify.success(response.data.message)
  }
  catch (e){
    console.log(e)
  }
  finally {
    loading.value = false
  }
}

</script>
