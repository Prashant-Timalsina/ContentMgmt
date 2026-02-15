<template>
  <div class="q-pa-md">
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h6 text-weight-bold">My Content</div>
      <q-btn color="primary" icon="add" label="Create Article" :to="{ name: 'editorsCreate' }" />
    </div>

    <q-table
      :rows="contentList"
      :columns="columns"
      row-key="id"
      :loading="loading"
      flat
      bordered
      :card-class="
        $q.dark.isActive ? 'bg-dark border-dark shadow-2' : 'bg-white shadow-1 border-light'
      "
      table-header-class="text-uppercase"
    >
        <template #body-cell-status="props">
          <q-td :props="props">
            <q-chip
              dense
              :color="statusColor(props.row.status)"
              text-color="white"
              size="sm"
            >
              {{ statusLabel(props.row.status) }}
            </q-chip>
          </q-td>
        </template>
        <template #body-cell-type="props">
          <q-td :props="props">
            {{ props.row.type?.name ?? props.row.type_id }}
          </q-td>
        </template>
        <template #body-cell-actions="props">
          <q-td :props="props" class="q-gutter-x-xs">
            <q-btn
              flat
              dense
              round
              size="sm"
              icon="edit"
              color="primary"
              @click="$router.push({ name: 'editorsEdit', params: { id: props.row.id } })"
            >
              <q-tooltip>Edit</q-tooltip>
            </q-btn>
            <q-btn
              v-if="props.row.status === 'draft'"
              flat
              dense
              round
              size="sm"
              icon="send"
              color="orange"
              @click="submitArticle(props.row)"
            >
              <q-tooltip>Submit for review</q-tooltip>
            </q-btn>
            <q-btn
              flat
              dense
              round
              size="sm"
              icon="delete"
              color="negative"
              @click="confirmDelete(props.row)"
            >
              <q-tooltip>Delete</q-tooltip>
            </q-btn>
          </q-td>
        </template>
      </q-table>

    <q-dialog v-model="deleteDialog">
      <q-card>
        <q-card-section>
          <div class="text-h6">Delete article?</div>
          <div class="text-body2 q-mt-sm">"{{ articleToDelete?.title }}" will be removed.</div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn unelevated color="negative" label="Delete" @click="doDelete" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useContentStore } from 'src/stores/contentStore'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'

const contentStore = useContentStore()
const notify = useQuasaMsgs()
const loading = ref(false)
const deleteDialog = ref(false)
const articleToDelete = ref(null)

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', style: 'width: 60px' },
  { name: 'title', label: 'Title', field: 'title', align: 'left' },
  { name: 'type', label: 'Type', field: 'type', align: 'left' },
  { name: 'status', label: 'Status', field: 'status', align: 'center' },
  { name: 'actions', label: 'Actions', align: 'center' },
]

const contentList = computed(() => contentStore.myArticles ?? [])

function statusLabel(status) {
  const map = { draft: 'Draft', submitted: 'Pending', published: 'Published', rejected: 'Rejected' }
  return map[status] ?? status
}

function statusColor(status) {
  const map = {
    draft: 'grey',
    submitted: 'orange',
    published: 'positive',
    rejected: 'negative',
  }
  return map[status] ?? 'grey'
}

async function load() {
  loading.value = true
  try {
    await contentStore.fetchMyArticles()
  } finally {
    loading.value = false
  }
}

async function submitArticle(row) {
  try {
    await contentStore.submitArticle(row.id)
    notify.success('Submitted for review')
    await load()
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to submit')
  }
}

function confirmDelete(row) {
  articleToDelete.value = row
  deleteDialog.value = true
}

async function doDelete() {
  if (!articleToDelete.value) return
  try {
    await contentStore.deleteArticle(articleToDelete.value.id)
    notify.success('Article deleted')
    deleteDialog.value = false
    articleToDelete.value = null
    await load()
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to delete')
  }
}

onMounted(load)
</script>
