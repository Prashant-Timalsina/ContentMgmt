<script setup>
import { ref } from 'vue';
import { reactive } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const loginForm = reactive({
    email:'',
    password:'',
})

const formComponent = ref(null)

const isPassword = ref(true)

const submitHandler=(e) =>{
    e.preventDefault();

    loginForm.value={
        email:'',
        password:''
    }
}


</script>

<template>
    <q-page :class="$q.dark.isActive ? 'dark-gradient' : 'light-gradient'">
        <q-card >
            
            <q-card-section>
                <div class="text-h5 q-mb-md text-weight-bold">
                    Welcome Back
                </div>
                <q-form ref="formComponent" @submit.prevent="submitHandler">
                    <q-input v-if="loginState==='register'" label="Name" v-model="loginForm.name"/>
                    <q-input label="Email" v-model="loginForm.email" clearable />
                    <q-input :type="isPassword ? 'password' : 'text'" label="Password" v-model="loginForm.password" clearable>
                        <template #append>
                            <q-icon :name="isPassword ? 'visibility' : 'visibility_off'" @click="isPassword = !isPassword" />
                        </template>
                    </q-input>
                    <div class="text-center q-mt-md">
                        <span>Don't you have an account?</span>
                        <span @click="router.push('signup')" class="text-primary text-weight-bold q-ml-sm cursor-pointer">Register Here</span>
                    </div>
                    <q-btn  type="submit" label="register" color="primary"/>
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