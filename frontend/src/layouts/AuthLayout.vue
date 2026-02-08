<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const bgImage = computed(()=> {
    return route.path.includes('signup') ? '/register.jpg' : '/login.jpg'
})
</script>
                
<template>
    <q-layout view="lHh Lpr lFf" >
        <q-page-container >
            <q-page class="row no-wrap items-stretch">
                <div class="col-6 image-section">
                    <q-img :src="bgImage" class="full-height" fit="cover"/>
                </div>

                <div class="col-6 form-section" :class="$q.dark.isActive ? 'bg-dark' : 'bg-white'">
                    <q-btn>
                        
                    </q-btn>
                    <router-view v-slot="{ Component }">
                        <transition name="fade" mode="out-in">
                            <component :is="Component" :key="$route.fullPath"/>
                        </transition>
                    </router-view>
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

.image-section {
    display: flex;
    align-items: center;
    justify-content: center;
}

.form-section {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>