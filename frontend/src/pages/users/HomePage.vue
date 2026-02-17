<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md q-gutter-sm">
      <div class="text-h6 text-weight-bold">Published content</div>
      <q-space />

      <q-input
        v-model="searchQuery"
        dense
        outlined
        placeholder="Search..."
        style="width: 200px"
        :dark="$q.dark.isActive"
      >
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </div>

    <q-tabs
      v-model="selectedType"
      dense
      class="q-mb-lg"
      :class="$q.dark.isActive ? 'text-grey-4' : 'text-grey-7'"
      active-color="primary"
      indicator-color="primary"
      align="left"
      narrow-indicator
    >
      <q-tab :name="0" label="All" />
      <q-tab v-for="type in articleTypes" :key="type.id" :name="type.id" :label="type.name" />
    </q-tabs>

    <div v-if="loading" class="flex flex-center q-py-xl">
      <q-spinner-dots color="primary" size="48px" />
    </div>

    <div v-else>
      <div class="row q-col-gutter-md">
        <div v-for="article in paginatedList" :key="article.id" class="col-12 col-sm-6 col-md-4">
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

      <div v-if="!paginatedList.length" class="text-center text-grey q-py-xl">
        No articles found matching your criteria.
      </div>

      <div v-if="totalPages > 1" class="flex flex-center q-mt-xl">
        <q-pagination
          v-model="currentPage"
          :max="totalPages"
          :max-pages="6"
          boundary-numbers
          direction-links
          color="primary"
        />
      </div>
    </div>

    <q-dialog
      v-model="articleDialog"
      maximized
      transition-show="slide-up"
      transition-hide="slide-down"
    >
      <q-card v-if="selectedArticle" :class="$q.dark.isActive ? 'bg-dark' : 'bg-white'">
        <q-toolbar>
          <q-btn flat round dense icon="close" v-close-popup />
          <q-toolbar-title>{{ selectedArticle.title }}</q-toolbar-title>
        </q-toolbar>

        <q-card-section class="q-pa-lg" style="max-width: 800px; margin: 0 auto">
          <div class="text-h4 text-weight-bold q-mb-md">{{ selectedArticle.title }}</div>
          <div class="text-subtitle1 text-grey q-mb-xl">
            {{ selectedArticle.type?.name }} · {{ selectedArticle.author?.name }}
          </div>
          <q-separator class="q-mb-lg" />
          <div class="article-body text-body1" v-html="selectedArticle.body" />
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useContentStore } from 'src/stores/contentStore'
import { useAuthStore } from 'src/stores/authStore'
import { useRoute } from 'vue-router'

const contentStore = useContentStore()
const authStore = useAuthStore()
const route = useRoute()

const loading = ref(false)
const articleDialog = ref(false)
const selectedArticle = ref(null)

// Filtering & Pagination State
const currentPage = ref(1)
const rowsPerPage = 6
const selectedType = ref(0)
const searchQuery = ref('')

const articleTypes = computed(() => contentStore.articleTypes ?? [])
const publishedArticles = computed(() => contentStore.publishedArticles ?? [])

// Logic: Filter first, then Paginate
const filteredArticles = computed(() => {
  let list = publishedArticles.value

  if (selectedType.value !== 0) {
    list = list.filter((a) => a.type_id === selectedType.value)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter((a) => a.title.toLowerCase().includes(q) || a.body.toLowerCase().includes(q))
  }

  return list
})

const totalPages = computed(() => Math.ceil(filteredArticles.value.length / rowsPerPage))

const paginatedList = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage
  return filteredArticles.value.slice(start, start + rowsPerPage)
})

watch([selectedType, searchQuery, publishedArticles], () => {
  currentPage.value = 1
})

// Listen for category clicks in the sidebar
watch(
  () => route.query.type,
  (newType) => {
    if (newType) {
      selectedType.value = parseInt(newType)
    } else {
      selectedType.value = 0 // "All"
    }
  },
  { immediate: true },
)

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
    // Parallel loading & Auth Init
    await authStore.init()
    await Promise.all([contentStore.fetchPublished(), contentStore.fetchArticleTypes()])
  } catch (e) {
    console.error(e)
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
  height: auto;
  border-radius: 8px;
}
.article-body {
  line-height: 1.7;
}
</style>
