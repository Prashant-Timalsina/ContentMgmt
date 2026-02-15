<template>
  <q-page class="q-pa-md">
    <div class="text-h6 text-weight-bold q-mb-md">Published content</div>

    <div v-if="loading" class="flex flex-center q-py-xl">
      <q-spinner-dots color="primary" size="48px" />
    </div>
    <div v-else class="row q-col-gutter-md">
      <div
        v-for="article in publishedList"
        :key="article.id"
        class="col-12 col-sm-6 col-md-4"
      >
        <q-card
          flat
          bordered
          clickable
          :class="$q.dark.isActive ? 'bg-dark border-dark' : 'bg-white border-light'"
          class="full-height"
          @click="openArticle(article)"
        >
          <q-card-section>
            <div class="text-subtitle1 text-weight-medium ellipsis-2-lines">
              {{ article.title }}
            </div>
            <div class="text-caption text-grey q-mt-xs">
              {{ article.type?.name ?? 'Article' }}
              <span v-if="article.author"> · {{ article.author.name }}</span>
            </div>
            <div
              class="text-body2 q-mt-sm text-grey-7 ellipsis-3-lines"
              v-html="stripHtml(article.body)"
            />
          </q-card-section>
        </q-card>
      </div>
    </div>
    <div v-if="!loading && !publishedList.length" class="text-center text-grey q-py-xl">
      No published articles yet.
    </div>

    <q-dialog v-model="articleDialog">
      <q-card v-if="selectedArticle" style="min-width: 320px; max-width: 560px">
        <q-card-section>
          <div class="text-h6">{{ selectedArticle.title }}</div>
          <div class="text-caption text-grey q-mt-xs">
            {{ selectedArticle.type?.name }} · {{ selectedArticle.author?.name }}
          </div>
        </q-card-section>
        <q-separator />
        <q-card-section class="article-body">
          <div v-html="selectedArticle.body" />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Close" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useContentStore } from 'src/stores/contentStore'

const contentStore = useContentStore()
const loading = ref(false)
const articleDialog = ref(false)
const selectedArticle = ref(null)

const publishedList = computed(() => contentStore.publishedArticles ?? [])

function stripHtml(html) {
  if (!html) return ''
  const div = document.createElement('div')
  div.innerHTML = html
  return div.textContent?.slice(0, 150) || ''
}

function openArticle(article) {
  selectedArticle.value = article
  articleDialog.value = true
}

async function load() {
  loading.value = true
  try {
    await contentStore.fetchPublished()
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<style scoped>
.ellipsis-2-lines {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.ellipsis-3-lines {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.article-body :deep(img) {
  max-width: 100%;
}
</style>
