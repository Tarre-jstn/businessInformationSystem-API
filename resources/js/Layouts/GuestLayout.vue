<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const business_Facebook = ref('');
const business_X = ref('');
const business_Instagram = ref('');
const business_Tiktok = ref('');
const business_image = ref('');

const fetchsocialmediaLinks = async() =>{
    try {   const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });
        console.log(getBusinessInfo.data);
        const businessId = getBusinessInfo.data.business_id;

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
    <div style="background-color: #0F2C4A;"><br>
    </div>
    <div class="min-h-screen flex flex-row justify-center sm:justify-center items-center pt-6 sm:pt-0" style="background-color: #FFFFFF; justify-content: space-evenly;">
        <div class="max-w-xl h-auto">
            <img :src="business_image" class="hidden md:block" alt="Logo" />
        </div>
        <div
            class="w-full sm:max-w-md mt-6 px-6 py-20 max-w-xl shadow-md overflow-hidden sm:rounded-lg" style="background-color: #0F2C4A; max-width: 35rem;">
            <slot />
        </div>
    </div>
    <div style="background-color: #0F2C4A;"><br>
    </div>
</template>
