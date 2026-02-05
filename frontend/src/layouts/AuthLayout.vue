<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const bgImage = computed(()=> {
    return route.path.includes('signup') ? '/signup.png' : '/login.png'
})
</script>
                
<template>
    <q-layout view="ppp ppp ppp">
        <q-page-container>
            <q-page class="row items-stretch">
                <div class="gt-sm col-12 col-md-6">
                    <q-img :src="bgImage" class="full-height"/>
                </div>
                <div class="flex flex-center col-12 col-md-6">
                    <router-view v-slot="{Component}">
                        <transition name="fade" mode="out-in">
                            <component :is="Component"/>
                        </transition>
                    </router-view >
                </div>
            </q-page>
        </q-page-container>
    </q-layout>
</template>

<style scoped>
.q-page{
    height:100vh;
    overflow: hidden;
}
</style>