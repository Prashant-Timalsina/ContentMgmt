<script setup>
import { useQuasaMsgs } from 'src/helper/quasaDialogs';
import { useAuthStore } from 'src/stores/authStore';
import { ref } from 'vue';
import { reactive } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const notify = useQuasaMsgs();
const authStore = useAuthStore()
const signUpForm = reactive({
    name:'',
    email:'',
    password:'',
    password_confirmation:''
})

const isPassword= ref(true)
const isPasswordC= ref(true)
const isLoading = ref(false)


async function submitHandler(){
    isLoading.value = true
    try{
        const response = await authStore.register(signUpForm)
        console.log(response.data)
        notify.success(`Hi, ${response.data.user.name}`)
        router.push('/')
        resetForm()
    }
    catch {
        notify.error('error message')
    }
    finally{
        isLoading.value = false
    }
    
}
function resetForm() {
    Object.assign(
        signUpForm,{
            name:'',
            email:'',
            password:'',
            password_confirmation:''
        }
    )
}
</script>

<template>
    <div class="signUp-box q-pa-lg" >
        <q-card flat class="bg-transparent" >
           
            <q-card-section>
                <div class="text-h5 q-mb-md text-weight-bold">
                   Let's Begin
                </div>
                <q-form @submit.prevent="submitHandler" class="q-gutter-y-md">
                    <q-input label="Name" v-model="signUpForm.name" outlined/>
                    <q-input label="Email" v-model="signUpForm.email" outlined />
                    <q-input :type="isPassword ? 'password' : 'text'" label="Password" v-model="signUpForm.password" outlined >
                        <template v-slot:append>
                            <q-icon 
                            :name="isPassword ? 'visibility_off' : 'visibility'" 
                            @click="isPassword = !isPassword" 
                            class="cursor-pointer" 
                            />
                        </template>
                    </q-input>
                    <q-input 
                        :type="isPasswordC ? 'password' : 'text'" 
                        label="Confirm Password" 
                        v-model="signUpForm.password_confirmation" 
                        outlined
                        :rules="[
                            val => !!val || 'Please confirm your password',
                            val => val === signUpForm.password || 'Passwords do not match'
                        ]"
                    >
                        <template v-slot:append>
                            <q-icon 
                            :name="isPasswordC ? 'visibility_off' : 'visibility'" 
                            @click="isPasswordC = !isPasswordC" 
                            class="cursor-pointer" 
                            
                            />
                        </template>
                    </q-input>
                    
                    <q-btn type="submit" label="register" color="primary"/>
                    
                    <div class="text-center q-mt-md">
                        <span>Already have an account?</span>
                        <span @click="router.push('login')" class="text-primary text-weight-bold q-ml-sm cursor-pointer">Login Back</span>
                    </div>
                </q-form>
            </q-card-section>
        </q-card>
    </div >
</template>

<style scoped>
.signUp-box{
    width:100%;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100%;
}
</style>