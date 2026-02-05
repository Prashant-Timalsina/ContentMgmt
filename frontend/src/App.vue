<template>
  <div v-if="authStore.isReady">   
    <router-view />
  </div>
  <div v-else class="flex flex-center">
    <q-spinner-dots color="primary" size="40px"/>
  </div>
</template>

<script setup>
//
import { useAuthStore} from '../src/stores/authStore.js'

const authStore = useAuthStore();
</script>
