<template>
  <div class="fullscreen bg-negative text-white text-center q-pa-md flex flex-center">
    <div>
      <div style="font-size: 20vh">403</div>
      <div class="text-h5" style="opacity: 0.9">Access denied</div>
      <q-btn
        class="q-mt-xl"
        color="white"
        text-color="negative"
        unelevated
        to="/"
        label="Go Home"
        no-caps
      />
    </div>
  </div>
</template>

<script setup>
defineOptions({ name: 'ForbiddenPage' })
</script>
