<template>
  <q-page class="q-pa-md">
    <div v-if="loading" class="flex flex-center" style="min-height: 400px">
      <div class="text-center">
        <q-spinner-dots color="primary" size="48px" />
        <div class="text-grey q-mt-md">Loading article details...</div>
      </div>
    </div>

    <div v-else-if="article">
      <div class="text-h6 text-weight-bold q-mb-md">Edit Article</div>

      <q-form @submit="onSubmit" class="q-gutter-md">
        <q-input
          v-model="form.title"
          label="Title"
          outlined
          dense
          :rules="[(v) => !!v || 'Title is required']"
        />

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

        <div class="q-mt-sm">
          <div class="text-caption q-mb-xs">Body</div>
          <div
            :class="$q.dark.isActive ? 'bg-grey-10' : 'bg-white'"
            class="rounded-borders overflow-hidden border-container"
          >
            <quill-editor
              v-model:content="form.body"
              content-type="html"
              theme="snow"
              :options="editorOptions"
              class="quill-editor"
            />
          </div>
        </div>

        <div class="row q-gutter-sm items-center q-mt-md">
          <q-btn unelevated color="primary" type="submit" label="Save" :loading="saving" />

          <q-btn
            v-if="article.status === 'draft'"
            outline
            color="orange"
            label="Submit for review"
            :loading="submitting"
            @click="submitForReview"
          />

          <q-btn flat label="Back to list" :to="{ name: 'editorsContent' }" />
        </div>
      </q-form>
    </div>

    <div v-else class="text-center text-grey q-py-xl">
      <q-icon name="error_outline" size="lg" />
      <div class="text-h6">Article not found.</div>
      <q-btn flat label="Go Back" :to="{ name: 'editorsContent' }" color="primary" />
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useContentStore } from 'src/stores/contentStore'
import { useQuasaMsgs } from 'src/helper/quasaDialogs'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

const route = useRoute()
const router = useRouter()
const contentStore = useContentStore()
const notify = useQuasaMsgs()
const loading = ref(true)
const saving = ref(false)
const submitting = ref(false)
const article = ref(null)
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

async function load() {
  const id = route.params.id
  if (!id) return
  loading.value = true
  try {
    await contentStore.fetchArticleTypes()
    const a = await contentStore.fetchArticle(id)
    article.value = a
    form.value = {
      title: a.title,
      body: a.body ?? '',
      type_id: a.type_id ?? a.type?.id,
    }
  } catch {
    notify.error('Failed to load article')
    router.replace({ name: 'editorsContent' })
  } finally {
    loading.value = false
  }
}

async function onSubmit() {
  if (!form.value.body || form.value.body === '<p><br></p>') {
    notify.warning('Please add some content')
    return
  }
  saving.value = true
  try {
    await contentStore.updateArticle(article.value.id, {
      title: form.value.title,
      body: form.value.body,
      type_id: form.value.type_id,
    })
    notify.success('Article updated')
    article.value = { ...article.value, ...form.value }
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to update')
  } finally {
    saving.value = false
  }
}

async function submitForReview() {
  submitting.value = true
  try {
    await contentStore.submitArticle(article.value.id)
    notify.success('Submitted for review')
    article.value.status = 'submitted'
  } catch (e) {
    notify.error(e.response?.data?.message ?? 'Failed to submit')
  } finally {
    submitting.value = false
  }
}

onMounted(load)
watch(() => route.params.id, load, { immediate: false })
</script>

<style scoped>
.quill-editor :deep(.ql-container) {
  min-height: 280px;
}
.rounded-borders {
  border-radius: 8px;
}
</style>
