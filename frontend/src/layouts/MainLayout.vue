<template>
  <q-layout view="hHh Lpr lFf" :class="LayoutClass">
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
          class="q-ml-sm row items-center rounded-borders"
          @click="routeToAdmin"
        >
          <q-icon
            name="admin_panel_settings"
            :color="$q.dark.isActive ? 'purple-3' : 'purple-7'"
            size="25px"
          />
          <div class="text-weight-medium q-ml-xs">Admin</div>
        </q-item>

        <q-btn
          :color="$q.dark.isActive ? 'red-4' : 'red-6'"
          class="q-mx-sm"
          @click="logout"
          label="Logout"
          flat
        />

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
      :width="280"
    >
      <div class="column full-height">
        <div class="q-pa-lg text-center">
          <q-avatar size="60px" color="primary" text-color="white" icon="hub" />
          <div class="q-mt-md text-weight-bold text-uppercase tracking-widest">Kontent Hub</div>
        </div>

        <q-list padding class="col scroll">
          <q-item clickable v-ripple to="/" exact class="q-ma-sm rounded-borders nav-item">
            <q-item-section avatar><q-icon name="explore" /></q-item-section>
            <q-item-section>Feed</q-item-section>
          </q-item>

          <q-expansion-item
            icon="category"
            label="Categories"
            header-class="text-weight-bold"
            class="q-ma-sm rounded-borders overflow-hidden"
            default-opened
          >
            <q-list class="q-pl-md">
              <q-item
                clickable
                v-ripple
                :to="{ name: 'home' }"
                exact
                class="q-ma-xs rounded-borders"
                active-class="active-type"
              >
                <q-item-section avatar>
                  <q-icon name="apps" size="18px" color="primary" />
                </q-item-section>
                <q-item-section>All Articles</q-item-section>
              </q-item>

              <q-item
                v-for="type in articleTypes"
                :key="type.id"
                clickable
                v-ripple
                :to="{ name: 'home', query: { type: type.id } }"
                class="q-ma-xs rounded-borders"
                active-class="active-type"
              >
                <q-item-section avatar>
                  <q-icon name="label_important" size="18px" color="grey-5" />
                </q-item-section>
                <q-item-section>{{ type.name }}</q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>

          <q-separator class="q-my-sm" inset />

          <q-item clickable v-ripple to="/user" exact class="q-ma-sm rounded-borders nav-item">
            <q-item-section avatar><q-icon name="person" /></q-item-section>
            <q-item-section>My Profile</q-item-section>
          </q-item>

          <q-expansion-item
            v-if="canEdit"
            icon="edit_note"
            label="Editor Workspace"
            header-class="text-weight-bold"
            class="q-ma-sm rounded-borders overflow-hidden"
            :default-opened="isEditorPath"
          >
            <q-list class="q-pl-md">
              <q-item
                clickable
                v-ripple
                :to="{ name: 'editorsContent' }"
                exact
                class="q-ma-xs rounded-borders"
              >
                <q-item-section avatar><q-icon name="article" size="xs" /></q-item-section>
                <q-item-section>My Content</q-item-section>
              </q-item>
              <q-item
                clickable
                v-ripple
                :to="{ name: 'editorsCreate' }"
                exact
                class="q-ma-xs rounded-borders"
              >
                <q-item-section avatar><q-icon name="add_circle" size="xs" /></q-item-section>
                <q-item-section>Create Article</q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>

          <q-expansion-item
            v-if="isAdmin"
            icon="admin_panel_settings"
            label="Management"
            header-class="text-weight-bold"
            class="q-ma-sm rounded-borders overflow-hidden"
            :default-opened="isAdminPath"
          >
            <q-list class="q-pl-md">
              <q-item
                clickable
                v-ripple
                :to="{ name: 'dashboard' }"
                exact
                class="q-ma-xs rounded-borders"
              >
                <q-item-section avatar><q-icon name="dashboard" size="xs" /></q-item-section>
                <q-item-section>Dashboard</q-item-section>
              </q-item>
              <q-item
                clickable
                v-ripple
                :to="{ name: 'listAll' }"
                exact
                class="q-ma-xs rounded-borders"
              >
                <q-item-section avatar><q-icon name="people" size="xs" /></q-item-section>
                <q-item-section>User Directory</q-item-section>
              </q-item>
              <q-item
                clickable
                v-ripple
                :to="{ name: 'adminRequests' }"
                exact
                class="q-ma-xs rounded-borders"
              >
                <q-item-section avatar><q-icon name="pending_actions" size="xs" /></q-item-section>
                <q-item-section>Requests</q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>
        </q-list>

        <q-separator :dark="$q.dark.isActive" />

        <div class="q-pa-sm">
          <q-item class="q-px-sm">
            <q-item-section avatar>
              <q-avatar size="32px" color="primary" text-color="white">
                {{ user.name?.[0]?.toUpperCase() || 'U' }}
              </q-avatar>
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-caption text-weight-bold">{{ user.name }}</q-item-label>
              <q-item-label caption class="ellipsis">{{ user.email }}</q-item-label>
            </q-item-section>
            <q-item-section side>
              <q-btn flat round dense color="grey-8" icon="logout" @click="logout" />
            </q-item-section>
          </q-item>
        </div>
      </div>
    </q-drawer>

    <q-page-container>
      <router-view v-slot="{ Component }">
        <transition
          enter-active-class="animated fadeIn"
          leave-active-class="animated fadeOut"
          mode="out-in"
        >
          <component :is="Component" />
        </transition>
      </router-view>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { Dark, useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/authStore'
import { useContentStore } from 'src/stores/contentStore' // Added this
import { useRouter, useRoute } from 'vue-router'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'

const notify = useQuasaMsgs()
const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const contentStore = useContentStore() // Initialize store
const $q = useQuasar()

const leftDrawerOpen = ref(false)

const user = computed(() => authStore.user || {})
const articleTypes = computed(() => contentStore.articleTypes || [])

const LayoutClass = computed(() => {
  return $q.dark.isActive ? 'bg-dark text-white' : 'bg-grey-2 text-dark'
})

const isAdmin = computed(() => authStore.user?.roles?.some((role) => role.name === 'admin'))
const canEdit = computed(() => authStore.permission?.includes('create_articles'))

const isEditorPath = computed(() => route.path.startsWith('/editors'))
const isAdminPath = computed(() => route.path.startsWith('/admin'))

function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}
function toggleDark() {
  Dark.toggle()
}
function routeToAdmin() {
  router.push('/admin')
}

async function logout() {
  if ($q.loading) $q.loading.show({ message: 'Logging out...' })
  try {
    await authStore.logout()
    window.location.href = window.location.origin + '/#/auth/login'
    window.location.reload()
  } catch {
    notify.error('Logout failed')
  } finally {
    if ($q.loading) $q.loading.hide()
  }
}

// Fetch categories when the layout mounts so they are available immediately
onMounted(() => {
  contentStore.fetchArticleTypes()
})
</script>

<style scoped>
.rounded-borders {
  border-radius: 8px;
}
.active-type {
  color: var(--q-primary);
  background: rgba(var(--q-primary), 0.05);
  font-weight: 600;
}
.q-router-link--exact-active:not(.active-type) {
  background: rgba(var(--q-primary), 0.1);
  color: var(--q-primary) !important;
  font-weight: bold;
}
.tracking-widest {
  letter-spacing: 0.1em;
}
:deep(.q-expansion-item__container .q-item) {
  border-radius: 8px;
}
</style>
