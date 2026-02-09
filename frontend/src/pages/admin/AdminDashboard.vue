<template>
  <div class="q-ma-lg">
    <div class="text-h5 q-mb-md text-weight-bold">Admin Overview</div>

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
    <div class="q-mt-xl">
      <q-table
        title="User Management"
        :rows="rows"
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
          <q-input borderless dense debounce="300" placeholder="Search Users">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>

        <template v-slot:body-cell-role="props">
          <q-td :props="props">
            <q-select
              v-model="props.row.role"
              :options="roleOptions"
              dense
              borderless
              emit-value
              map-options
              options-dense
              :bg-color="$q.dark.isActive ? 'grey-10' : 'grey-2'"
              class="q-px-sm rounded-borders text-weight-bold"
              popup-content-class="text-center"
              style="width: 120px; margin: 0 auto"
              @update:model-value="(val) => updateRole(props.row.id, val)"
            >
              <template v-slot:selected-item="scope">
                <div class="full-width text-center">
                  <span :class="getRoleColor(scope.opt)">
                    {{ scope.opt ? scope.opt.toUpperCase() : '' }}
                  </span>
                </div>
              </template>

              <template v-slot:option="scope">
                <q-item
                  v-bind="scope.itemProps"
                  class="text-center"
                  :class="$q.dark.isActive ? 'bg-grey-10 text-white' : 'bg-white text-black'"
                >
                  <q-item-section>
                    <q-item-label :class="getRoleColor(scope.opt)">
                      {{ scope.opt ? scope.opt.toUpperCase() : '' }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </q-td>
        </template>

        <template v-slot:option="scope">
          <q-item v-bind="scope.itemProps" class="text-center">
            <q-item-section>
              <q-item-label :class="getRoleColor(scope.opt)">
                {{ scope.opt.toUpperCase() }}
              </q-item-label>
            </q-item-section>
          </q-item>
        </template>

        <template v-slot:body-cell-actions="props">
          <q-td :props="props" class="q-gutter-x-sm">
            <q-btn
              flat
              label="Warn"
              icon="warning"
              color="warning"
              size="sm"
              @click="warnUser(props.row)"
            />
            <q-btn
              unelevated
              label="Block"
              icon="block"
              color="negative"
              size="sm"
              @click="blockUser(props.row)"
            />
          </q-td>
        </template>
      </q-table>
    </div>
  </div>
</template>

<script setup>
import { useQuasar } from 'quasar'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'
import { computed, ref } from 'vue'

const $q = useQuasar()
const notify = useQuasaMsgs()
const roleOptions = ['admin', 'editor', 'user']

// Style texts in dropdowns
const getRoleColor = (role) => {
  const isDark = $q.dark.isActive

  if (role === 'admin') return isDark ? 'text-red-4' : 'text-negative'
  if (role === 'editor') return isDark ? 'text-orange-4' : 'text-orange-9'
  return isDark ? 'text-blue-4' : 'text-primary'
}

const stats = ref([
  { label: 'Total Users', value: 10, icon: 'people', color: 'primary' },
  { label: 'Editors', value: 5, icon: 'edit_note', color: 'orange' },
  { label: 'Admins', value: 1, icon: 'security', color: 'negative' },
])

const columns = computed(() => [
  {
    name: 'id',
    label: 'ID',
    field: 'id',
    align: 'left',
    sortable: true,
    classes: 'text-weight-bold',
  },
  {
    name: 'name',
    label: 'Full Name',
    field: 'name',
    align: 'left',
    sortable: true,
  },
  {
    name: 'email',
    label: 'Email Address',
    field: 'email',
    align: 'left',
  },
  {
    name: 'role',
    label: 'Role',
    field: 'role',
    align: 'center',
  },
  {
    name: 'actions',
    label: 'Actions',
    field: 'actions',
    align: 'center',
  },
])

const rows = ref([
  {
    id: 1,
    name: 'God',
    email: 'god@gmail.com',
    role: 'admin',
  },
  {
    id: 2,
    name: 'Prashant',
    email: 'prashant@example.com',
    role: 'editor',
  },
  {
    id: 3,
    name: 'John Doe',
    email: 'john@example.com',
    role: 'user',
  },
  {
    id: 4,
    name: 'Jane Smith',
    email: 'jane@example.com',
    role: 'editor',
  },
  {
    id: 5,
    name: 'Alice Johnson',
    email: 'alice@example.com',
    role: 'user',
  },
])

// Action Handlers
const updateRole = (userId, newRole) => {
  $q.notify({
    message: `User ${userId} promoted to ${newRole}`,
    color: 'positive',
    icon: 'done',
  })
}

const warnUser = (user) => {
  $q.dialog({
    title: 'Send Warning',
    message: `Are you sure you want to send a formal warning to ${user.name}?`,
    cancel: true,
    persistent: true,
  }).onOk(() => {
    notify.warning('Warned User')
  })
}

const blockUser = (user) => {
  $q.dialog({
    title: 'Block User',
    message: `This will prevent ${user.name} from logging in. Continue?`,
    ok: { label: 'Block', color: 'negative' },
    cancel: true,
    persistent: true,
  }).onOk(() => {
    // $q.notify({ message: 'User Blocked', color: 'negative', icon: 'lock' })
    notify.error('User Blocked', { icon: 'lock' })
  })
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
