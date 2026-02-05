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
    <q-page  :class="$q.dark.isActive ? 'dark-gradient' : 'light-gradient'">
        <q-card >
           
            <q-card-section>
                <div class="text-h5 q-mb-md text-weight-bold">
                   Let\'s Begin
                </div>
                <q-form ref="formComponent" @submit.prevent="submitHandler">
                    <q-input v-if="loginState==='register'" label="Name" v-model="signUpForm.name"/>
                    <q-input label="Email" v-model="signUpForm.email" clearable />
                    <q-input type="password" label="Password" v-model="signUpForm.password" clearable >
                        <template v-slot:append>
                            <q-icon 
                            :name="isPassword ? 'visibility_off' : 'visibility'" 
                            @click="isPassword = !isPassword" 
                            class="cursor-pointer" 
                            />
                        </template>
                    </q-input>
                    <q-input type="password" label="Confirm Password" v-model="confirmPassword">
                        <template v-slot:append>
                            <q-icon 
                            :name="isPassword ? 'visibility_off' : 'visibility'" 
                            @click="isPassword = !isPassword" 
                            class="cursor-pointer" 
                            />
                        </template>
                    </q-input>
                    <div class="text-center q-mt-md">
                        <span>Already have an account?</span>
                        <span @click="route.push('login')" class="text-primary text-weight-bold q-ml-sm cursor-pointer">Login Back</span>
                    </div>
                    <q-btn type="submit" label="register" color="primary"/>
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