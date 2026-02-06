<script setup>
import { useAuthStore } from 'src/stores/authStore';
import { ref } from 'vue';
import { reactive } from 'vue';
import { useRouter } from 'vue-router';

const authStore = useAuthStore()
const router = useRouter();
const loginForm = reactive({
    email:'',
    password:'',
    remember: false
})

const isPassword = ref(true)
const loading = ref(false)

const submitHandler= async () =>{
    loading.value = true
    try {
        await authStore.login(loginForm)
        router.push('/')
    }
    catch(err) {
        console.log(err)
    }
    finally{
        loading.value = false
    }

    Object.assign(loginForm,{ email: '' , password:''})
}


</script>

<template>
    <div class="login-box q-pa-lg" >
        <q-card flat class="bg-transparent" style="min-width: 320px; max-width: 400px;" >
            
            <q-card-section>
                <div class="text-h5 q-mb-md text-weight-bold">
                    Welcome Back
                </div>
                <q-form @submit.prevent="submitHandler" class="q-gutter-y-md">
                    <q-input label="Email" v-model="loginForm.email" outlined />
                    <q-input :type="isPassword ? 'password' : 'text'" label="Password" v-model="loginForm.password" outlined>
                        <template #append>
                            <q-icon :name="isPassword ? 'visibility' : 'visibility_off'" class="cursor-pointer" @click="isPassword = !isPassword" />
                        </template>
                    </q-input>
                    
                    <q-btn  type="submit" label="Login" color="primary"/>
                    
                    <div class="text-center q-mt-md">
                        <span>Don't you have an account?</span>
                        <span @click="router.push('signup')" class="text-primary text-weight-bold q-ml-sm cursor-pointer">Register Here</span>
                    </div>
                </q-form>
            </q-card-section>
        </q-card>
    </div>
</template>

<style scoped>
.login-box{
    width:100%;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100%;
}
</style>