<template>
  <div class="q-pa-md">
    <div class="text-h6 text-weight-bold q-mb-md">Requests</div>

    <div class="row q-gutter-sm q-mb-md">
      <q-btn-toggle
        v-model="filterType"
        toggle-color="primary"
        no-caps
        :options="[
          { label: 'All', value: 'all' },
          { label: 'Roles', value: 'role' },
          { label: 'Permissions', value: 'permission' },
          { label: 'Articles', value: 'article' },
        ]"
      />
      <q-btn-toggle
        v-model="filterStatus"
        toggle-color="secondary"
        no-caps
        dense
        :options="[
          { label: 'All', value: 'all' },
          { label: 'Pending', value: 'pending' },
          { label: 'Approved', value: 'approved' },
          { label: 'Rejected', value: 'rejected' },
        ]"
      />
    </div>

    <!-- Access requests (role + permission) -->
    <q-table
      v-if="filterType === 'all' || filterType === 'role' || filterType === 'permission'"
      :rows="filteredAccessRequests"
      :columns="accessColumns"
      row-key="id"
      :loading="loading"
      flat
      bordered
      :card-class="
        $q.dark.isActive ? 'bg-dark border-dark shadow-2' : 'bg-white shadow-1 border-light'
      "
      class="q-mb-lg"
      :row-class="(row) => requestRowClass(row, 'access')"
    >
      <template #body-cell-type="props">
        <q-td :props="props">
          <q-badge
            outline
            dense
            class="text-body2"
            :label="requestType(props.row.type).label"
            :color="requestType(props.row.type).color"
          />
        </q-td>
      </template>
      <template #body-cell-reason="props">
        <q-td :props="props">
          <span v-if="props.row.reason?.trim()">
            {{ props.row.reason }}
          </span>
          <span v-else class="text-grey">—</span>
        </q-td>
      </template>

      <template #body-cell-status="props">
        <q-td :props="props">
          <q-badge
            outline
            :label="RoleStatus(props.row.status).label"
            :color="RoleStatus(props.row.status).color"
          />
        </q-td>
      </template>
      <template #body-cell-actions="props">
        <q-td :props="props">
          <template v-if="props.row.status === 'pending'">
            <q-btn
              flat
              dense
              size="sm"
              color="positive"
              icon="check"
              @click="approveAccessRequest(props.row)"
            >
              <q-tooltip>Approve</q-tooltip>
            </q-btn>
            <q-btn
              flat
              dense
              size="sm"
              color="negative"
              icon="close"
              @click="openRejectAccess(props.row)"
            >
              <q-tooltip>Reject</q-tooltip>
            </q-btn>
          </template>
          <span v-else class="text-grey">—</span>
        </q-td>
      </template>
    </q-table>

    <!-- Article requests -->
    <div v-if="filterType === 'all' || filterType === 'article'" class="q-mb-md">
      <div class="text-subtitle1 text-weight-medium q-mb-sm">Article requests</div>
      <q-table
        :rows="filteredArticleRequests"
        :columns="articleColumns"
        row-key="id"
        :loading="loading"
        flat
        bordered
        :card-class="
          $q.dark.isActive ? 'bg-dark border-dark shadow-2' : 'bg-white shadow-1 border-light'
        "
        row-class="article-pending-row"
      >
        <template #body-cell-status="props">
          <q-td :props="props">
            <q-badge
              outline
              class="text-body2"
              :label="articleStatus(props.row.status).label"
              :color="articleStatus(props.row.status).color"
            >
              <q-tooltip v-if="props.row.status === 'rejected' && props.row.rejection_reason">
                {{ props.row.rejection_reason }}
              </q-tooltip>
            </q-badge>
          </q-td>
        </template>
        <template #body-cell-actions="props">
          <q-td :props="props">
            <template v-if="props.row.status === 'submitted'">
              <q-btn
                flat
                dense
                size="sm"
                color="positive"
                icon="publish"
                @click="publishArticle(props.row)"
              >
                <q-tooltip>Publish</q-tooltip>
              </q-btn>
              <q-btn
                flat
                dense
                size="sm"
                color="negative"
                icon="close"
                @click="openRejectArticle(props.row)"
              >
                <q-tooltip>Reject</q-tooltip>
              </q-btn>
            </template>
            <span v-else class="text-grey">—</span>
          </q-td>
        </template>
      </q-table>
    </div>

    <q-dialog v-model="rejectAccessDialog">
      <q-card style="min-width: 320px">
        <q-card-section>
          <div class="text-h6">Reject request</div>
          <q-input
            v-model="rejectAdminNote"
            label="Admin note"
            outlined
            dense
            type="textarea"
            class="q-mt-sm"
          />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn unelevated color="negative" label="Reject" @click="doRejectAccess" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="rejectArticleDialog">
      <q-card style="min-width: 320px">
        <q-card-section>
          <div class="text-h6">Reject article</div>
          <q-input
            v-model="rejectArticleReason"
            label="Rejection reason (optional)"
            outlined
            dense
            type="textarea"
            class="q-mt-sm"
          />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn unelevated color="negative" label="Reject" @click="doRejectArticle" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { api } from 'src/boot/axios'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'
import { useStatusTheme } from '../../components/useStatusTheme'
import { RoleStatusTheme } from '../../components/RoleStatusTheme'
import { useRolesTheme } from '../../components/RolesTheme'

const { requestType } = useRolesTheme()
const { articleStatus } = useStatusTheme()
const { RoleStatus } = RoleStatusTheme()

const notify = useQuasaMsgs()
const loading = ref(false)
const filterType = ref('all')
const filterStatus = ref('all')
const accessRequests = ref([])
const articleRequests = ref([])
const rejectAccessDialog = ref(false)
const rejectArticleDialog = ref(false)
const selectedAccessRequest = ref(null)
const selectedArticle = ref(null)
const rejectAdminNote = ref('')
const rejectArticleReason = ref('')

const accessColumns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' },
  { name: 'type', label: 'Type', field: 'type', align: 'left' },
  { name: 'item_name', label: 'Item', field: 'item_name', align: 'left' },
  { name: 'user', label: 'User', field: (row) => row.user?.name ?? row.user_id, align: 'left' },
  { name: 'reason', label: 'Reason', field: 'reason', align: 'left' },
  { name: 'status', label: 'Status', field: 'status', align: 'center' },
  { name: 'actions', label: 'Actions', align: 'center' },
]

const articleColumns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' },
  { name: 'title', label: 'Title', field: 'title', align: 'left' },
  {
    name: 'author',
    label: 'Author',
    field: (row) => row.author?.name ?? row.author_id,
    align: 'left',
  },
  { name: 'status', label: 'Status', field: 'status', align: 'center' },
  { name: 'actions', label: 'Actions', align: 'center' },
]

const filteredAccessRequests = computed(() => {
  let list = accessRequests.value
  if (filterType.value === 'role') list = list.filter((r) => r.type === 'role')
  else if (filterType.value === 'permission') list = list.filter((r) => r.type === 'permission')
  if (filterStatus.value === 'pending') list = list.filter((r) => r.status === 'pending')
  else if (filterStatus.value === 'approved') list = list.filter((r) => r.status === 'approved')
  else if (filterStatus.value === 'rejected') list = list.filter((r) => r.status === 'rejected')
  return list
})

const filteredArticleRequests = computed(() => {
  let list = articleRequests.value
  if (filterStatus.value === 'pending') list = list.filter((a) => a.status === 'submitted')
  else if (filterStatus.value === 'approved') list = list.filter((a) => a.status === 'published')
  else if (filterStatus.value === 'rejected') list = list.filter((a) => a.status === 'rejected')
  return list
})

function requestRowClass(row, kind) {
  if (kind === 'access') {
    if (row.type === 'role') return 'request-type-role'
    if (row.type === 'permission') return 'request-type-permission'
  }
  return ''
}

async function loadAccessRequests() {
  const res = await api.get('/api/admin/access-requests')
  accessRequests.value = res.data
}

async function loadArticleRequests() {
  const res = await api.get('/api/all-articles')
  articleRequests.value = res.data ?? []
}

async function load() {
  loading.value = true
  try {
    await Promise.all([loadAccessRequests(), loadArticleRequests()])
  } catch {
    notify.error('Failed to load requests')
  } finally {
    loading.value = false
  }
}

async function approveAccessRequest(row) {
  try {
    await api.post(`/api/access-request/${row.id}/approve`, {})
    notify.success('Request approved')
    await loadAccessRequests()
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to approve')
  }
}

function openRejectAccess(row) {
  selectedAccessRequest.value = row
  rejectAdminNote.value = ''
  rejectAccessDialog.value = true
}

async function doRejectAccess() {
  if (!selectedAccessRequest.value) return
  try {
    await api.post(`/api/access-request/${selectedAccessRequest.value.id}/reject`, {
      admin_note: rejectAdminNote.value || 'Rejected by admin',
    })
    notify.success('Request rejected')
    rejectAccessDialog.value = false
    selectedAccessRequest.value = null
    await loadAccessRequests()
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to reject')
  }
}

async function publishArticle(row) {
  try {
    await api.post(`/api/articles/${row.id}/publish`)
    notify.success('Article published')
    await loadArticleRequests()
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to publish')
  }
}

function openRejectArticle(row) {
  selectedArticle.value = row
  rejectArticleReason.value = ''
  rejectArticleDialog.value = true
}

async function doRejectArticle() {
  if (!selectedArticle.value) return
  try {
    await api.post(`/api/articles/${selectedArticle.value.id}/reject`, {
      rejection_reason: rejectArticleReason.value || null,
    })
    notify.success('Article rejected')
    rejectArticleDialog.value = false
    selectedArticle.value = null
    await loadArticleRequests()
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to reject')
  }
}

onMounted(load)
</script>

<style scoped>
.request-type-role :deep(td) {
  border-left: 4px solid var(--q-primary);
}
.request-type-permission :deep(td) {
  border-left: 4px solid #009688;
}
.article-pending-row :deep(td) {
  border-left: 4px solid #ff9800;
}
</style>
