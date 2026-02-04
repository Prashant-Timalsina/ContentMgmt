<script setup>
import { ref } from 'vue';
import { reactive } from 'vue';

const loginForm = reactive({
    name:'',
    email:'',
    password:'',
})

const formComponent = ref(null)

const loginState = ref('login');

const toggleState = () => {
    loginState.value = loginState.value === 'register' ? 'login' : 'register';
}


</script>

<template>
    <q-page class="flex flex-center" :class="$q.dark.isActive ? 'dark-gradient' : 'light-gradient'">
        <q-card class="row">
            <q-card-section >
                <img src="~assets/login.png" alt="Login Image" style="width: 400px;">
            </q-card-section>
            <q-card-section>
                <div class="text-h5 q-mb-md text-weight-bold">
                    {{ loginState === 'login' ? 'Welcome Back' : 'Let\'s Begin' }}
                </div>
                <q-form ref="formComponent">
                    <q-input v-if="loginState==='register'" label="Name" v-model="loginForm.name"/>
                    <q-input label="Email" v-model="loginForm.email" clearable />
                    <q-input type="password" label="Password" v-model="loginForm.password" clearable/>
                    <div>
                        <span>{{ loginState === 'login' ? 'Don\'t You have an account?' : 'Already have an account?' }}</span>
                        <span @click="toggleState" class="text-primary text-weight-bold q-ml-sm cursor-pointer">{{ loginState === 'login' ? 'Register Here' : 'Login Back' }}</span>
                    </div>
                </q-form>
            </q-card-section>
        </q-card>
    </q-page>
</template>

<style scoped>
.dark-gradient{
    background: linear-gradient(135deg, #000000 0%, #414141 100%);
    /* background: --var(bg-primary); */
}

.light-gradient{
    background: linear-gradient(135deg, #d4e4f8 0%, #728db9 100%);
    /* background: --var(bg-primary); */
}
</style>