<template>
  <q-layout view="lHh Lpr lFf" :class="LayoutClass">
    <q-header
      :elevated="!$q.dark.isActive"
      :class="$q.dark.isActive ? 'bg-dark' : 'bg-white text-primary'"
    >
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />

        <q-toolbar-title class="text-weight-bolder"> KONTENT </q-toolbar-title>

        <q-item
          v-if="isAdmin"
          clickable
          v-ripple
          class="q-ml-sm row items-center"
          @click="routeToAdmin"
        >
          <q-icon
            name="admin_panel_settings"
            :color="$q.dark.isActive ? 'purple-3' : 'purple-7'"
            size="25px"
          />

          <div class="text-weight-medium q-ma-none" style="margin-left: 2px">Admin</div>
        </q-item>

        <div>
          <q-btn
            :color="$q.dark.isActive ? 'red-4' : 'red-6'"
            class="q-mx-sm"
            @click="logout"
            label="Logout"
          />
        </div>

        <q-btn
          flat
          dense
          round
          :icon="$q.dark.isActive ? 'light_mode' : 'dark_mode'"
          @click="toggleDark"
        />
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      :class="$q.dark.isActive ? 'bg-black' : 'bg-grey-1'"
      :width="260"
    >
      <div class="column full-height">
        <div class="q-pa-lg text-center">
          <q-avatar size="60px" color="primary" text-color="white" icon="admin_panel_settings" />
          <div class="q-mt-md text-weight-bold text-uppercase">Management</div>
        </div>

        <q-list padding class="col scroll">
          <q-item
            v-for="link in linksList"
            :key="link.title"
            clickable
            v-ripple
            :to="link.to"
            exact
            active-class="active-link"
            class="q-ma-sm rounded-borders text-weight-medium"
            :class="$q.dark.isActive ? 'text-grey-4' : 'text-grey-8'"
          >
            <q-item-section avatar>
              <q-icon :name="link.icon" />
            </q-item-section>
            <q-item-section>{{ link.title }}</q-item-section>
          </q-item>
        </q-list>

        <q-separator :dark="$q.dark.isActive" />

        <div class="q-pa-sm">
          <q-item class="q-px-sm">
            <q-item-section avatar>
              <q-avatar size="32px" color="primary" text-color="white">{{
                user.name?.[0]?.toUpperCase() || ''
              }}</q-avatar>
            </q-item-section>

            <q-item-section>
              <q-item-label class="text-caption text-weight-bold">{{ user.name }}</q-item-label>
              <q-item-label caption class="ellipsis">{{ user.email }}</q-item-label>
            </q-item-section>

            <q-item-section side>
              <q-btn flat round dense color="grey-8" icon="logout" @click="logout">
                <q-tooltip>Log out</q-tooltip>
              </q-btn>
            </q-item-section>
          </q-item>
        </div>
      </div>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Dark, useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/authStore'
import { useRouter } from 'vue-router'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'

const notify = useQuasaMsgs()
const router = useRouter()
const authStore = useAuthStore()
const loading = ref(false)
const $q = useQuasar()
const LayoutClass = computed(() => {
  return $q.dark.isActive ? 'bg-dark text-white' : 'bg-grey-2 text-dark'
})
// Dark.set(true)

const user = authStore.user

const linksList = [
  {
    title: 'News',
    icon: 'book',
    to: '/',
  },
  {
    title: 'Pages',
    icon: 'pages',
    to: '/',
  },
  {
    title: 'Menu',
    icon: 'menu',
    to: '/',
  },
  {
    title: 'Settings',
    icon: 'settings',
    to: '/',
  },
]

const leftDrawerOpen = ref(false)

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function toggleDark() {
  Dark.toggle()
}

const isAdmin = computed(() => {
  return authStore.user?.roles?.some((role) => role.name === 'admin')
})

function routeToAdmin() {
  router.push('/admin')
}

async function logout() {
  loading.value = true
  try {
    const response = await authStore.logout()
    console.log(response)
    notify.success(response.data.message)
    window.location.href = window.location.origin + '/#/auth/login'
  } catch (e) {
    console.log(e)
  } finally {
    loading.value = false
  }
}
</script>
