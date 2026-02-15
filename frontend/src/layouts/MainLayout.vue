<template>
  <q-layout view="hHh Lpr lFf" :class="LayoutClass">
    <q-header
      :elevated="!$q.dark.isActive"
      :class="$q.dark.isActive ? 'bg-dark' : 'bg-white text-primary'"
    >
      <q-toolbar class="q-py-sm">
        <q-btn flat dense round icon="menu_open" @click="toggleLeftDrawer" />

        <q-toolbar-title class="text-weight-bolder">
          <span class="text-uppercase">KONTENT</span>
          <span
            v-if="isAdminSection"
            :class="$q.dark.isActive ? 'text-primary' : 'text-primary'"
            class="q-ml-sm"
          >
            ADMIN
          </span>
          <span v-if="isEditorSection" class="text-orange-8 q-ml-sm"> EDITOR </span>
        </q-toolbar-title>

        <q-space />

        <div class="row items-center q-gutter-md">
          <q-btn
            flat
            round
            :icon="$q.dark.isActive ? 'light_mode' : 'dark_mode'"
            @click="toggleDark"
          />

          <q-btn
            v-if="isAdminSection || isEditorSection"
            unelevated
            color="grey-10"
            class="rounded-borders text-weight-bold q-px-md"
            label="EXIT"
            icon-right="logout"
            to="/"
          />

          <q-btn
            v-else
            flat
            no-caps
            label="Logout"
            color="red-5"
            @click="logout"
            class="text-weight-bold"
          />
        </div>
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
          <q-avatar
            size="60px"
            color="primary"
            text-color="white"
            :icon="isAdminSection ? 'admin_panel_settings' : 'hub'"
          />
          <div class="q-mt-md text-weight-bold text-uppercase tracking-widest">
            {{ isAdminSection ? 'Management' : 'Kontent Hub' }}
          </div>
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
          >
            <q-list class="q-pl-md">
              <q-item
                clickable
                v-ripple
                :to="{ name: 'home' }"
                exact
                class="q-ma-xs rounded-borders"
                active-class="active-sub"
              >
                <q-item-section avatar><q-icon name="apps" size="xs" /></q-item-section>
                <q-item-section>All Articles</q-item-section>
              </q-item>
              <q-item
                v-for="type in articleTypes"
                :key="type.id"
                clickable
                v-ripple
                :to="{ name: 'home', query: { type: type.id } }"
                class="q-ma-xs rounded-borders"
                active-class="active-sub"
              >
                <q-item-section avatar><q-icon name="label_important" size="xs" /></q-item-section>
                <q-item-section>{{ type.name }}</q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>

          <q-separator inset class="q-my-sm" />

          <q-expansion-item
            v-if="canEdit"
            icon="edit_note"
            label="Editor Workspace"
            header-class="text-weight-bold"
            class="q-ma-sm rounded-borders overflow-hidden"
            :default-opened="isEditorSection"
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
            :default-opened="isAdminSection"
          >
            <q-list class="q-pl-md">
              <q-item
                clickable
                v-ripple
                :to="{ name: 'dashboard' }"
                exact
                class="q-ma-xs rounded-borders"
              >
                <q-item-section avatar><q-icon name="grid_view" size="xs" /></q-item-section>
                <q-item-section>Dashboard</q-item-section>
              </q-item>
              <q-item
                clickable
                v-ripple
                :to="{ name: 'listAll' }"
                exact
                class="q-ma-xs rounded-borders"
              >
                <q-item-section avatar><q-icon name="group" size="xs" /></q-item-section>
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
                {{ userInitial }}
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
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { Dark, useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/authStore'
import { useContentStore } from 'src/stores/contentStore'
import { useRoute } from 'vue-router'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'

const notify = useQuasaMsgs()
const route = useRoute()
const authStore = useAuthStore()
const contentStore = useContentStore()
const $q = useQuasar()

const leftDrawerOpen = ref(false)

const user = computed(() => authStore.user || {})
const articleTypes = computed(() => contentStore.articleTypes || [])
const LayoutClass = computed(() =>
  $q.dark.isActive ? 'bg-dark text-white' : 'bg-grey-2 text-dark',
)

const isAdmin = computed(() => authStore.user?.roles?.some((r) => r.name === 'admin'))
const canEdit = computed(() => authStore.permission?.includes('create_articles'))

const isAdminSection = computed(() => route.path.startsWith('/admin'))
const isEditorSection = computed(() => route.path.startsWith('/editors'))

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value
}
const toggleDark = () => {
  Dark.toggle()
}

// Add this to your computed section
const userInitial = computed(() => {
  return user.value?.name?.charAt(0).toUpperCase() || 'U'
})

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

onMounted(() => {
  contentStore.fetchArticleTypes()
})
</script>

<style scoped>
.rounded-borders {
  border-radius: 8px;
}
.tracking-widest {
  letter-spacing: 0.1em;
}

/* Active State for Sub-items */
.active-sub {
  color: var(--q-primary);
  background: rgba(var(--q-primary), 0.1);
  font-weight: bold;
}

/* Global Navigation Highlight */
.q-router-link--exact-active:not(.active-sub) {
  background: var(--q-primary) !important;
  color: white !important;
}

:deep(.q-expansion-item__container .q-item) {
  border-radius: 8px;
}
</style>
