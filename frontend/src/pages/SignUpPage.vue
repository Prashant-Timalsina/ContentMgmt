<script setup>
import { ref } from 'vue';
import { reactive } from 'vue';
import { useRouter } from 'vue-router';

const route = useRouter();
const signUpForm = reactive({
    name:'',
    email:'',
    password:'',
})
const confirmPassword = ref('');

const formComponent = ref(null)


const submitHandler=(e) =>{
    e.preventDefault();

    signUpForm.value={
        name:'',
        email:'',
        password:''
    }
}


</script>

<template>
    <div class="signUp-box q-pa-lg" >
        <q-card flat class="bg-transparent" >
           
            <q-card-section>
                <div class="text-h5 q-mb-md text-weight-bold">
                   Let's Begin
                </div>
                <q-form ref="formComponent" @submit.prevent="submitHandler" class="q-gutter-y-md">
                    <q-input label="Name" v-model="signUpForm.name" outlined/>
                    <q-input label="Email" v-model="signUpForm.email" outlined />
                    <q-input type="password" label="Password" v-model="signUpForm.password" outlined >
                        <template v-slot:append>
                            <q-icon 
                            :name="isPassword ? 'visibility_off' : 'visibility'" 
                            @click="isPassword = !isPassword" 
                            class="cursor-pointer" 
                            />
                        </template>
                    </q-input>
                    <q-input type="password" label="Confirm Password" v-model="confirmPassword" outlined>
                        <template v-slot:append>
                            <q-icon 
                            :name="isPassword ? 'visibility_off' : 'visibility'" 
                            @click="isPassword = !isPassword" 
                            class="cursor-pointer" 
                            
                            />
                        </template>
                    </q-input>
                    
                    <q-btn type="submit" label="register" color="primary"/>
                    
                    <div class="text-center q-mt-md">
                        <span>Already have an account?</span>
                        <span @click="route.push('login')" class="text-primary text-weight-bold q-ml-sm cursor-pointer">Login Back</span>
                    </div>
                </q-form>
            </q-card-section>
        </q-card>
    </div >
</template>

<style scoped>

</style>