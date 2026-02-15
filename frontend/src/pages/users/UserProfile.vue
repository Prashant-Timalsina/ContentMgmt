<template>
  <div class="q-pa-md">
    <div class="text-h6 text-weight-bold q-mb-md">My Profile</div>

    <q-card
      flat
      bordered
      :class="$q.dark.isActive ? 'bg-dark border-dark' : 'bg-white border-light'"
      class="q-pa-md q-mb-lg"
    >
      <q-card-section>
        <div class="text-subtitle1 text-weight-medium q-mb-sm">Details</div>
        <div class="row q-col-gutter-md">
          <div class="col-12 col-sm-6">
            <div class="text-caption text-grey">Name</div>
            <div>{{ authStore.user?.name }}</div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="text-caption text-grey">Email</div>
            <div>{{ authStore.user?.email }}</div>
          </div>
        </div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="text-subtitle1 text-weight-medium q-mb-sm">Roles</div>
        <div class="q-gutter-xs">
          <q-chip
            v-for="r in rolesList"
            :key="r"
            dense
            color="primary"
            text-color="white"
          >
            {{ r }}
          </q-chip>
          <span v-if="!rolesList.length" class="text-grey">No roles</span>
        </div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="text-subtitle1 text-weight-medium q-mb-sm">Permissions</div>
        <div class="q-gutter-xs">
          <q-chip
            v-for="p in permissionsList"
            :key="p"
            dense
            :class="$q.dark.isActive ? 'bg-grey-9' : 'bg-teal-1 text-teal-10'"
          >
            {{ p }}
          </q-chip>
          <span v-if="!permissionsList.length" class="text-grey">No extra permissions</span>
        </div>
      </q-card-section>
    </q-card>

    <q-card
      flat
      bordered
      :class="$q.dark.isActive ? 'bg-dark border-dark' : 'bg-white border-light'"
      class="q-pa-md"
    >
      <div class="text-subtitle1 text-weight-medium q-mb-md">Request role or permission</div>
      <q-form @submit="onSubmitRequest" class="q-gutter-md">
        <q-select
          v-model="requestForm.type"
          :options="requestTypeOptions"
          option-value="value"
          option-label="label"
          emit-value
          map-options
          label="Request type"
          outlined
          dense
        />
        <q-input
          v-model="requestForm.item_name"
          :label="requestForm.type === 'role' ? 'Role name' : 'Permission name'"
          outlined
          dense
          placeholder="e.g. editor or create_articles"
        />
        <q-input
          v-model="requestForm.reason"
          label="Reason (optional)"
          outlined
          dense
          type="textarea"
        />
        <q-btn unelevated color="primary" type="submit" label="Submit request" :loading="sending" />
      </q-form>
    </q-card>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useAuthStore } from 'src/stores/authStore'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'
import { api } from 'src/boot/axios'

const authStore = useAuthStore()
const notify = useQuasaMsgs()
const sending = ref(false)
const requestForm = ref({
  type: 'permission',
  item_name: '',
  reason: '',
})

const requestTypeOptions = [
  { label: 'Role', value: 'role' },
  { label: 'Permission', value: 'permission' },
]

const rolesList = computed(() => authStore.roles ?? [])
const permissionsList = computed(() => authStore.permission ?? [])

async function onSubmitRequest() {
  if (!requestForm.value.item_name?.trim()) {
    notify.warning('Enter role or permission name')
    return
  }
  sending.value = true
  try {
    await api.post('/api/access-request', {
      type: requestForm.value.type,
      item_name: requestForm.value.item_name.trim(),
      reason: requestForm.value.reason || null,
    })
    notify.success('Request submitted for review')
    requestForm.value = { type: requestForm.value.type, item_name: '', reason: '' }
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to submit request')
  } finally {
    sending.value = false
  }
}
</script>
