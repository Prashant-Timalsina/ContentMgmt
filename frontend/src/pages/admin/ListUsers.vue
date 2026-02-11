<template>
  <div class="q-pa-md">
    <q-table
      :rows="usersToDisplay"
      :columns="columns"
      row-key="id"
      v-model:pagination="pagination"
      hide-bottom
      flat
      bordered
      :visible-columns="visibleColumns"
      :card-class="
        $q.dark.isActive ? 'bg-dark border-dark shadow-2' : 'bg-white shadow-1 border-light'
      "
      table-header-class="text-uppercase"
      class="user-table"
    >
      <template #top-left>
        <div class="text-h6 text-weight-bold">User Management</div>
      </template>

      <template #top-right>
        <div class="row q-gutter-sm items-center">
          <q-select
            v-model="visibleColumns"
            multiple
            outlined
            dense
            options-dense
            display-value="Columns"
            emit-value
            map-options
            :options="columns"
            option-value="name"
            style="min-width: 120px"
          />

          <q-input outlined dense debounce="300" v-model="search" placeholder="Name or email..">
            <template #append>
              <q-icon name="search" />
            </template>
          </q-input>

          <q-btn
            color="primary"
            icon="filter_list"
            label="Filters"
            @click="filterDialogOpen = true"
          >
            <q-badge v-if="activeFilterCount > 0" color="red" floating>
              {{ activeFilterCount }}
            </q-badge>
          </q-btn>
        </div>
      </template>

      <!-- ROLE COLUMN -->
      <template #body-cell-role="props">
        <q-td :props="props">
          <q-select
            :model-value="props.row.role"
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
            @update:model-value="(val) => updateRole(props.row, val)"
          >
            <template #selected-item="scope">
              <div class="full-width text-center">
                <span :class="getRoleColor(scope.opt)">
                  {{ scope.opt?.toUpperCase() }}
                </span>
              </div>
            </template>

            <template #option="scope">
              <q-item
                v-bind="scope.itemProps"
                class="text-center"
                :class="$q.dark.isActive ? 'bg-grey-10 text-white' : 'bg-white text-black'"
              >
                <q-item-section>
                  <q-item-label :class="getRoleColor(scope.opt)">
                    {{ scope.opt?.toUpperCase() }}
                  </q-item-label>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </q-td>
      </template>

      <!-- PERMISSIONS COLUMN -->
      <template #body-cell-permissions="props">
        <q-td :props="props">
          <div class="row no-wrap items-center q-gutter-xs justify-center">
            <div class="row q-gutter-xs">
              <q-chip
                v-for="(perm, index) in props.value"
                :key="index"
                dense
                size="sm"
                :class="$q.dark.isActive ? 'bg-grey-10 text-white' : 'bg-teal-1 text-teal-10'"
              >
                {{ perm }}
              </q-chip>
            </div>

            <q-btn
              flat
              round
              dense
              size="sm"
              icon="add"
              color="primary"
              @click="addPermission(props.row)"
            >
              <q-tooltip>Add Permission</q-tooltip>
            </q-btn>
          </div>
        </q-td>
      </template>

      <!-- ACTIONS COLUMN -->
      <template #body-cell-actions="props">
        <q-td :props="props" class="q-gutter-x-sm text-center">
          <q-btn flat round icon="warning" color="warning" size="sm" @click="warnUser(props.row)">
            <q-tooltip>Warn User</q-tooltip>
          </q-btn>
          <q-btn flat round icon="block" color="negative" size="sm" @click="blockUser(props.row)">
            <q-tooltip>Block User</q-tooltip>
          </q-btn>
        </q-td>
      </template>
    </q-table>

    <!-- FILTER DIALOG -->
    <q-dialog v-model="filterDialogOpen" position="right">
      <q-card style="width: 350px; height: 100vh" class="column">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Filter & Sort</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-separator class="q-my-md" />

        <q-card-section class="col scroll q-gutter-md">
          <div>
            <div class="text-caption text-weight-bold q-mb-xs">BY ROLE</div>
            <q-select v-model="filter.role" :options="['All', ...roleOptions]" outlined dense />
          </div>

          <div>
            <div class="text-caption text-weight-bold q-mb-xs">BY PERMISSIONS</div>
            <q-select
              v-model="filter.permissions"
              multiple
              :options="allAvailablePermissions"
              outlined
              dense
              use-chips
            />
          </div>

          <div>
            <div class="text-caption text-weight-bold q-mb-xs">SORT PENDING REQUESTS</div>
            <q-btn-toggle
              v-model="filter.sortPending"
              spread
              no-caps
              toggle-color="primary"
              :options="[
                { label: 'Highest First', value: 'desc' },
                { label: 'Lowest First', value: 'asc' },
              ]"
            />
          </div>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Reset Filters" color="negative" @click="resetFilters" />
          <q-btn unelevated label="Apply" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- FLOATING PAGINATION -->
    <div
      v-if="!filterDialogOpen"
      class="fixed-bottom flex justify-center q-pb-lg pointer-none z-top"
    >
      <q-card
        bordered
        class="floating-pagination row items-center no-wrap q-px-md q-py-xs pointer-all shadow-10"
      >
        <q-btn
          flat
          round
          dense
          icon="chevron_left"
          :disable="pagination.page === 1"
          @click="pagination.page--"
        />

        <div class="q-px-md text-weight-bold">
          PAGE {{ pagination.page }} /
          {{ totalPages }}
        </div>

        <q-btn
          flat
          round
          dense
          icon="chevron_right"
          :disable="pagination.page >= totalPages"
          @click="pagination.page++"
        />
      </q-card>
    </div>
  </div>
</template>

<script setup>
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'
import { useAuthStore } from 'src/stores/authStore'
import { computed, onMounted, ref, watch } from 'vue'

const $q = useQuasar()
const notify = useQuasaMsgs()
const authStore = useAuthStore()

onMounted(() => authStore.fetchUsers())

const search = ref('')
const roleOptions = ['admin', 'editor', 'user']
const visibleColumns = ref(['id', 'name', 'email', 'role', 'pending', 'permissions', 'actions'])
const pagination = ref({ page: 1, rowsPerPage: 15 })
const allAvailablePermissions = ['view content', 'edit content', 'delete content']

const filterDialogOpen = ref(false)
const filter = ref({
  role: 'All',
  permissions: [],
  sortPending: 'desc',
})

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' },
  { name: 'name', label: 'Full Name', field: 'name', align: 'left' },
  { name: 'email', label: 'Email', field: 'email', align: 'left' },
  { name: 'role', label: 'Role', field: 'role', align: 'center' },
  { name: 'pending', label: 'Pending Requests', field: 'pending', align: 'center' },
  { name: 'permissions', label: 'Permissions', field: 'permissions', align: 'center' },
  { name: 'actions', label: 'Actions', align: 'center' },
]

const mappedUsers = computed(() =>
  (authStore.users ?? []).map((user) => ({
    ...user,
    role: user.roles?.[0]?.name ?? 'user',
    permissions: user.permissions?.map((p) => p.name ?? p) ?? [],
    pending: user.pending ?? 0,
  })),
)

const filterAndSort = computed(() => {
  let result = [...mappedUsers.value]

  if (search.value) {
    const s = search.value.toLowerCase()
    result = result.filter(
      (r) => r.name.toLowerCase().includes(s) || r.email.toLowerCase().includes(s),
    )
  }

  if (filter.value.role !== 'All') {
    result = result.filter((r) => r.role === filter.value.role)
  }

  if (filter.value.permissions.length) {
    result = result.filter((r) => filter.value.permissions.every((p) => r.permissions.includes(p)))
  }

  result.sort((a, b) =>
    filter.value.sortPending === 'desc' ? b.pending - a.pending : a.pending - b.pending,
  )

  return result
})

const activeFilterCount = computed(() => {
  let count = 0

  if (filter.value.role !== 'All') count++
  if (filter.value.permissions.length > 0) count++
  if (filter.value.sortPending !== 'desc') count++

  return count
})

const resetFilters = () => {
  filter.value.role = 'All'
  filter.value.permissions = []
  filter.value.sortPending = 'desc'

  search.value = ''
  pagination.value.page = 1
}

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filterAndSort.value.length / pagination.value.rowsPerPage)),
)

const usersToDisplay = computed(() => {
  const start = (pagination.value.page - 1) * pagination.value.rowsPerPage
  return filterAndSort.value.slice(start, start + pagination.value.rowsPerPage)
})

watch(
  [search, filter],
  () => {
    pagination.value.page = 1
  },
  { deep: true },
)

const getRoleColor = (role) => {
  if (role === 'admin') return $q.dark.isActive ? 'text-red-4' : 'text-negative'
  if (role === 'editor') return $q.dark.isActive ? 'text-orange-4' : 'text-orange-9'
  return $q.dark.isActive ? 'text-blue-4' : 'text-primary'
}

const updateRole = async (user, newRole) => {
  if (user.role === newRole) return
  try {
    await api.post(`/api/${user.id}/roles`, { role: newRole })
    await authStore.fetchUsers()
    notify.success(`Updated ${user.name} to ${newRole}`)
  } catch {
    notify.error('Failed to update role')
  }
}

const warnUser = (user) => {
  $q.dialog({
    title: 'Send Warning',
    message: `Send a formal warning to ${user.name}?`,
    cancel: true,
  }).onOk(() => notify.warning('Warning Sent'))
}

const blockUser = (user) => {
  $q.dialog({
    title: 'Block User',
    message: `Confirm blocking ${user.name}?`,
    ok: { label: 'Block', color: 'negative' },
    cancel: true,
  }).onOk(() => notify.error('User Blocked', { icon: 'lock' }))
}

const addPermission = (user) => {
  $q.dialog({
    title: 'Add Permission',
    prompt: { model: '', type: 'text' },
    cancel: true,
  }).onOk((data) => {
    if (data) {
      user.permissions.push(data)
      notify.success('Permission Added')
    }
  })
}
</script>
