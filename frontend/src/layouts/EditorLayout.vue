<template>
  <q-layout view="hHh Lpr lFf" :class="LayoutClass">
    <q-header
      :elevated="!$q.dark.isActive"
      :class="$q.dark.isActive ? 'bg-dark' : 'bg-white text-primary'"
    >
      <q-toolbar class="q-py-sm">
        <q-btn flat dense round icon="menu_open" @click="toggleLeftDrawer" />
        <q-toolbar-title class="text-weight-bolder">
          KONTENT <span :class="$q.dark.isActive ? 'text-primary' : 'text-dark'">EDITOR</span>
        </q-toolbar-title>
        <q-space />
        <q-btn
          flat
          round
          :icon="$q.dark.isActive ? 'light_mode' : 'dark_mode'"
          @click="toggleDark"
          class="q-mr-sm"
        />
        <q-btn label="Exit" icon-right="home" to="/" />
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
          <q-avatar size="60px" color="orange" text-color="white" icon="edit_note" />
          <div class="q-mt-md text-weight-bold text-uppercase">Editor</div>
        </div>
        <q-list padding class="col scroll">
          <q-item
            v-for="link in editorLinks"
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
              <q-avatar size="32px" color="primary" text-color="white">
                {{ authStore.user?.name?.[0]?.toUpperCase() || 'E' }}
              </q-avatar>
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-caption text-weight-bold">{{
                authStore.user?.name
              }}</q-item-label>
              <q-item-label caption class="ellipsis">{{ authStore.user?.email }}</q-item-label>
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
import { useQuasaMsgs } from 'src/helper/quasaDialogs'

const authStore = useAuthStore()
const notify = useQuasaMsgs()
const loading = ref(false)
const $q = useQuasar()
const LayoutClass = computed(() => {
  return $q.dark.isActive ? 'bg-dark text-white' : 'bg-grey-2 text-dark'
})

const editorLinks = [
  { title: 'My Content', icon: 'article', to: '/editors/content' },
  { title: 'New Article', icon: 'add_circle', to: '/editors/create' },
]

const leftDrawerOpen = ref(false)

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function toggleDark() {
  Dark.toggle()
}

async function logout() {
  loading.value = true
  try {
    const response = await authStore.logout()
    notify.success(response.data.message)
    window.location.href = window.location.origin + '/#/auth/login'
  } catch (e) {
    console.log(e)
  } finally {
    loading.value = false
  }
}
</script>

<style lang="scss" scoped>
.rounded-borders {
  border-radius: 12px;
}
.active-link {
  background: var(--q-primary) !important;
  color: white !important;
}
</style>
