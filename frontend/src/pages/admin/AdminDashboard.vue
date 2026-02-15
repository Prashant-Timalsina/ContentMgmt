<template>
  <div class="q-ma-lg">
    <div class="text-h5 q-mb-md text-weight-bold">Admin Overview</div>

    <!-- Stats Cards -->
    <div class="q-col-gutter-md row">
      <div v-for="stat in stats" :key="stat.label" class="col-12 col-sm-4">
        <q-card
          flat
          bordered
          class="stats-card"
          :class="
            $q.dark.isActive ? 'bg-transparent border-dark' : 'bg-white shadow-1 border-light'
          "
        >
          <q-card-section class="row items-center no-wrap">
            <div class="col">
              <div
                class="text-overline"
                :class="$q.dark.isActive ? 'text-yellow-1' : 'text-grey-7'"
              >
                {{ stat.label }}
              </div>
              <div
                class="text-h4 text-weight-bolder"
                :class="$q.dark.isActive ? 'text-white' : 'text-grey-9'"
              >
                {{ stat.value }}
              </div>
            </div>

            <div class="col-auto">
              <q-avatar
                size="56px"
                :color="$q.dark.isActive ? 'grey-9' : 'grey-2'"
                :text-color="stat.color"
                class="shadow-1"
              >
                <q-icon :name="stat.icon" />
              </q-avatar>
            </div>
          </q-card-section>
          <q-separator :color="stat.color" size="2px" />
        </q-card>
      </div>
    </div>

    <!-- Users Table -->
    <div class="q-mt-xl">
      <q-table
        title="User Management"
        :rows="mappedUsers"
        :columns="columns"
        row-key="id"
        flat
        bordered
        :card-class="
          $q.dark.isActive ? 'bg-dark border-dark shadow-2' : 'bg-white shadow-1 border-light'
        "
        table-header-class="text-uppercase"
        class="user-table"
      >
        <template v-slot:top-left>
          <div class="text-h6 text-weight-bold">User Management</div>
        </template>

        <template v-slot:top-right>
          <q-input borderless dense debounce="300" placeholder="Search Users" v-model="search">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>

        <!-- ROLE COLUMN -->
        <template #body-cell-role="props">
          <q-td :props="props" class="text-center text-weight-bold">
            <span :class="getRoleColor(props.row.role)">
              {{ props.row.role.toUpperCase() }}
            </span>
          </q-td>
        </template>

        <!-- PERMISSIONS COLUMN -->
        <template #body-cell-permission="props">
          <q-td :props="props" class="text-center">
            <div class="row justify-center q-gutter-xs">
              <q-chip
                dense
                size="sm"
                :class="$q.dark.isActive ? 'bg-grey-9 text-white' : 'bg-teal-1 text-teal-10'"
              >
                {{ props.row.role }} role
              </q-chip>
              ,
              <q-chip
                v-for="perm in props.row.permissions"
                :key="perm"
                dense
                size="sm"
                :class="$q.dark.isActive ? 'bg-grey-9 text-white' : 'bg-teal-1 text-teal-10'"
              >
                {{ perm }}
              </q-chip>
            </div>
          </q-td>
        </template>

        <!-- ACTIONS COLUMN -->
        <template v-slot:body-cell-actions="props">
          <q-td :props="props" class="q-gutter-x-sm text-center">
            <q-btn
              flat
              label="Edit"
              icon="edit"
              color="primary"
              size="sm"
              @click="$router.push({ name: 'listAll', query: { userId: props.row.id } })"
            />
          </q-td>
        </template>
      </q-table>
    </div>
  </div>
</template>

<script setup>
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/authStore'
import { computed, onMounted, ref } from 'vue'

const $q = useQuasar()
const authStore = useAuthStore()

const search = ref('')

const stats = computed(() => {
  const users = authStore.users ?? []
  const total = users.length
  const editors = users.filter((u) => (u.roles?.[0]?.name ?? u.role) === 'editor').length
  const admins = users.filter((u) => (u.roles?.[0]?.name ?? u.role) === 'admin').length
  return [
    { label: 'Total Users', value: total, icon: 'people', color: 'primary' },
    { label: 'Editors', value: editors, icon: 'edit_note', color: 'orange' },
    { label: 'Admins', value: admins, icon: 'security', color: 'negative' },
  ]
})

const columns = computed(() => [
  {
    name: 'id',
    label: 'ID',
    field: 'id',
    align: 'left',
    sortable: true,
    classes: 'text-weight-bold',
  },
  { name: 'name', label: 'Full Name', field: 'name', align: 'left', sortable: true },
  { name: 'email', label: 'Email Address', field: 'email', align: 'left' },
  { name: 'role', label: 'Role', field: 'role', align: 'center' },
  { name: 'permission', label: 'Permission', field: 'permission', align: 'center' },
  { name: 'actions', label: 'Actions', field: 'actions', align: 'center' },
])

const mappedUsers = computed(() =>
  (authStore.users ?? []).map((user) => ({
    ...user,
    role: user.roles?.[0]?.name ?? 'user',
    permissions: user.permissions?.map((p) => p.name ?? p) ?? [],
  })),
)

// Ensure users are loaded for stats
onMounted(() => {
  authStore.fetchUsers()
})

const getRoleColor = (role) => {
  const isDark = $q.dark.isActive
  if (role === 'admin') return isDark ? 'text-red-4' : 'text-negative'
  if (role === 'editor') return isDark ? 'text-orange-4' : 'text-orange-9'
  return isDark ? 'text-blue-4' : 'text-primary'
}
</script>

<style scoped>
.stats-card {
  border-radius: 12px;
  transition: all 0.3s ease;
}

/* Light Mode specific hover */
.border-light {
  border: 1px solid rgba(0, 0, 0, 0.05);
}
.stats-card:hover.border-light {
  background: #fdfdfd !important;
  border-color: var(--q-primary);
  transform: translateY(-5px);
}

/* Dark Mode specific hover */
.border-dark {
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.stats-card:hover.border-dark {
  background: rgba(255, 255, 255, 0.05) !important;
  border-color: rgba(255, 255, 255, 0.3);
  transform: translateY(-5px);
}

/* Table Specific CSS */
.user-table :deep(.q-table__th) {
  font-weight: 700;
  letter-spacing: 0.05em;
  font-size: 0.75rem;
  /* Dynamic header color based on mode */
}

.user-table :deep(.q-table__container) {
  border-radius: 12px;
}

/* Make rows feel interactive */
.user-table :deep(.q-tr) {
  transition: background-color 0.3s ease;
}

/* Light mode row hover */
body.body--light .user-table :deep(.q-tr:hover) {
  background-color: #f5f5f5 !important;
}

/* Dark mode row hover */
body.body--dark .user-table :deep(.q-tr:hover) {
  background-color: rgba(255, 255, 255, 0.03) !important;
}

.rounded-borders {
  border-radius: 8px;
}
</style>
