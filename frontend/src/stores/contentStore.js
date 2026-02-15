import { defineStore } from 'pinia'
import { api } from 'src/boot/axios'

export const useContentStore = defineStore('content', {
  state: () => ({
    publishedArticles: [],
    myArticles: [],
    articleTypes: [],
    currentArticle: null,
  }),
  actions: {
    async fetchPublished() {
      const res = await api.get('/api/articles')
      this.publishedArticles = res.data
      return res.data
    },

    async fetchMyArticles() {
      const res = await api.get('/api/my-articles')
      this.myArticles = res.data
      return res.data
    },

    async fetchArticleTypes() {
      const res = await api.get('/api/articles_type')
      this.articleTypes = res.data
      return res.data
    },

    async fetchArticle(id) {
      const res = await api.get(`/api/articles/${id}`)
      this.currentArticle = res.data
      return res.data
    },

    async createArticle(payload) {
      const res = await api.post('/api/articles', {
        title: payload.title,
        body: payload.body,
        type_id: payload.type_id,
      })
      this.myArticles = [res.data, ...(this.myArticles || [])]
      return res.data
    },

    async updateArticle(id, payload) {
      const res = await api.put(`/api/articles/${id}`, payload)
      const idx = this.myArticles?.findIndex((a) => a.id === parseInt(id, 10))
      if (idx !== undefined && idx >= 0) this.myArticles[idx] = res.data
      if (this.currentArticle?.id === parseInt(id, 10)) this.currentArticle = res.data
      return res.data
    },

    async deleteArticle(id) {
      await api.delete(`/api/articles/${id}`)
      this.myArticles = (this.myArticles || []).filter((a) => a.id !== parseInt(id, 10))
      if (this.currentArticle?.id === parseInt(id, 10)) this.currentArticle = null
    },

    async submitArticle(id) {
      await api.post(`/api/articles/${id}/submit`)
      const a = this.myArticles?.find((x) => x.id === parseInt(id, 10))
      if (a) a.status = 'submitted'
      if (this.currentArticle?.id === parseInt(id, 10)) this.currentArticle.status = 'submitted'
    },

    async publishArticle(id) {
      await api.post(`/api/articles/${id}/publish`)
      const a = this.myArticles?.find((x) => x.id === parseInt(id, 10))
      if (a) a.status = 'published'
      if (this.currentArticle?.id === parseInt(id, 10)) this.currentArticle.status = 'published'
    },

    async rejectArticle(id, rejectionReason) {
      await api.post(`/api/articles/${id}/reject`, {
        rejection_reason: rejectionReason || null,
      })
      const a = this.myArticles?.find((x) => x.id === parseInt(id, 10))
      if (a) a.status = 'rejected'
      if (this.currentArticle?.id === parseInt(id, 10)) this.currentArticle.status = 'rejected'
    },
  },
})
