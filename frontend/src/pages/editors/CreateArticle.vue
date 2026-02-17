<template>
  <q-page class="q-pa-lg q-gutter-x-lg">
    <div class="q-mt-lg">
      <q-card class="q-mb-md q-pa-md" style="max-width: 800px; margin: auto" flat bordered>
        <q-card-section>
          <div class="text-h6 text-weight-bold q-mb-md">Create Article</div>
          <q-form @submit.prevent="onSubmit" class="q-gutter-md">
            <!-- Title -->
            <q-input
              v-model="form.title"
              label="Title"
              outlined
              dense
              :rules="[(v) => !!v || 'Title is required']"
            />

            <!-- Type -->
            <q-select
              v-model="form.type_id"
              :options="typeOptions"
              option-value="id"
              option-label="name"
              emit-value
              map-options
              label="Article type"
              outlined
              dense
              :rules="[(v) => !!v || 'Type is required']"
            />

            <!-- Body -->
            <div class="q-mt-sm">
              <div class="text-caption q-mb-xs">Body</div>
              <div
                :class="$q.dark.isActive ? 'bg-grey-10' : 'bg-white'"
                class="rounded-borders overflow-hidden"
              >
                <quill-editor
                  v-model:content="form.body"
                  content-type="html"
                  :options="editorOptions"
                  class="quill-editor"
                />
              </div>
            </div>

            <!-- Buttons -->
            <div class="row q-gutter-sm justify-end">
              <q-btn
                unelevated
                color="primary"
                type="submit"
                label="Save as draft"
                :loading="saving"
              />
              <q-btn flat type="button" label="Cancel" :to="{ name: 'editorsContent' }" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useContentStore } from 'src/stores/contentStore'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const contentStore = useContentStore()
const notify = useQuasaMsgs()
const router = useRouter()
const saving = ref(false)

const form = ref({
  title: '',
  body: '',
  type_id: null,
})

const editorOptions = {
  placeholder: 'Write your article...',
  modules: {
    toolbar: [
      ['bold', 'italic', 'underline'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['link'],
      ['clean'],
    ],
  },
}

const typeOptions = computed(() => contentStore.articleTypes ?? [])

async function loadTypes() {
  await contentStore.fetchArticleTypes()
}

async function onSubmit() {
  if (!form.value.body || form.value.body === '<p><br></p>') {
    notify.warning('Please add some content')
    return
  }
  saving.value = true
  try {
    const article = await contentStore.createArticle({
      title: form.value.title,
      body: form.value.body,
      type_id: form.value.type_id,
    })
    notify.success('Article saved as draft')
    router.push({ name: 'editorsEdit', params: { id: article.id } })
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to create')
  } finally {
    saving.value = false
  }
}

onMounted(loadTypes)
</script>

<style scoped>
.quill-editor :deep(.ql-container) {
  min-height: 280px;
}

/* Rounded editor */
.rounded-borders {
  border-radius: 8px;
}
</style>
