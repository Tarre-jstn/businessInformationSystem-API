<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const business_Facebook = ref('');
const business_X = ref('');
const business_Instagram = ref('');
const business_Tiktok = ref('');
const business_image = ref('');
const business_Name = ref('');

const fetchsocialmediaLinks = async() =>{
    try {   const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });
        console.log(getBusinessInfo.data);
        const businessId = getBusinessInfo.data.business_id;

        business_Name.value = getBusinessInfo.data.business_Name;
        business_Facebook.value = getBusinessInfo.data.business_Facebook;
        business_X.value = getBusinessInfo.data.business_X;
        business_Instagram.value = getBusinessInfo.data.business_Instagram;
        business_Tiktok.value = getBusinessInfo.data.business_Tiktok;
        business_image.value = getBusinessInfo.data.business_image;

        console.log("Social Media Links:", business_Facebook.value, business_X.value, business_Instagram.value, business_Tiktok.value, business_image.value);
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}

fetchsocialmediaLinks();

</script>

<template>
    <div style="background-color: #0F2C4A;"><br><br>
    </div>
    <div class="min-h-screen flex flex-col justify-center sm:justify-center items-center pt-6 sm:pt-0 md:flex-row shadow-2xl" style="background: rgba(15,44,74,1); background: linear-gradient(90deg, rgba(15,44,74,1) 0%, rgba(32,75,120,1) 50%, rgba(13,76,89,1) 100%); justify-content: space-evenly;">
        <div class="max-w-lg h-auto flex flex-col items-center">
            <div class="text-white font-bold text-5xl mb-6">
                {{ business_Name }} 
            </div>
            <img :src="business_image" class="hidden md:block rounded-full shadow-2xl shadow-slate-950" alt="Logo" style="border-color: #081626; border-width: 10px; background-color: #FFFFFF;" />
        </div>
        <div
            class="w-full sm:max-w-md mt-6 px-6 py-20 max-w-xl shadow-2xl shadow-slate-950 overflow-hidden sm:rounded-3xl" style="background-color: #FFFFFF; max-width: 40rem; color: #0F2C4A; border-width: 10px; border-color: #081626">
            <slot />
        </div>
    </div>
    <div style="background-color: #0F2C4A;"><br><br>
    </div>
</template>
