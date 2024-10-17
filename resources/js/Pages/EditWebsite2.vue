<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const textAreas = {
    about_us1: ref(''),
    about_us2: ref(''),
    about_us3: ref('')
}

onMounted(()=>{

    getWebsiteInfo();
});

// //store default / to be changed data:

async function getWebsiteInfo(){
    try{
        const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
        console.log(userId);

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });
        console.log(getBusinessInfo.data);
        const businessId = getBusinessInfo.data.business_id;

        const getWebsiteInfo = await axios.get('/api/website', {
            params: {business_id: businessId}
        });
        console.log(getWebsiteInfo.data);

        textAreas.about_us1.value = getWebsiteInfo.data.about_us1;
        textAreas.about_us2.value = getWebsiteInfo.data.about_us2;
        textAreas.about_us3.value = getWebsiteInfo.data.about_us3;

    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}


const editButton=ref(null);
function edit(area){
    editButton.value = area;
}

async function save(){
    editButton.value = false;

    const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
    const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });
        const businessId = getBusinessInfo.data.business_id;

        const formData = new FormData();
        formData.append('business_id', businessId);
        formData.append('about_us1', textAreas.about_us1.value);
        formData.append('about_us2', textAreas.about_us2.value);
        formData.append('about_us3', textAreas.about_us3.value);

    const saveBusinessDesc = await axios.post('/api/website-update', formData, {
        headers:{
            'Content-Type': 'multipart/form-data'
        }
    });
    console.log('Save response:', saveBusinessDesc.data);
    
}

function goToEditWebsite3(){
    Inertia.visit(route('editWebsite3'));
}
</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <!-- header of edit website to save changes -->
    
        <!-- <div>
            <button @click="getWebsiteInfo">Show</button>
        </div> -->
     
        <div class="bg-gray-300 flex items-center p-2">
            <p class="flex-grow text-center mr-3">You are currently using the edit mode.</p>
                <button @click="save" class="px-6 py-1 bg-gray-600 ml-auto">Save</button>
        </div>

        
        <div class="ml-1 bg-website-main1 flex min-h-screen relative">

            <div class="flex items-center p-3 absolute top-[5px] left-0 right-0 bottom-[500px] m-auto">
                <p class="mt-[10px] text-[50px]  text-white font-bold flex-grow text-center">About Us</p>
            </div>

            <!-- edit business info wag to iedit kasi business name ito-->
            <div class="ml-[120px] flex flex-row items-center justify-between w-full max-w-screen-lg mt-[200px]">
                
                <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3">
                    <div class="flex justify-center w-full">
                        <a class="icon-color border border-transparent rounded-[30px] p-8 flex inline-flex items-center justify-center">
                            <i class="fa fa-check-circle text-white text-[35px]"></i></a>
                    </div>
                    <div class="max-w-[100px]">
                        <button @click="edit('about_us1')" class="bg-white border border-white rounded-xl p-1 mb-[10px]">Edit Text</button>
                    </div>
                    <div class="max-w-[280px] min-h-[170px]">
                        <p class="text-white text-center break-words">{{textAreas.about_us1}}</p>
                    </div>
                    <div v-if="editButton==='about_us1'">
                         <textarea v-model="textAreas.about_us1.value" class="-mt-[67px] w-full h-40 border boder-black"></textarea>
                        <button @click="save" class="bg-white rounded-xl p-1">Save</button>
                    </div>
                </div>

                <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3">
                    <div class="flex justify-center w-full">
                        <a class="icon-color border border-transparent rounded-[30px] p-8 flex inline-flex items-center justify-center">
                            <i class="fa fa-tag text-white text-[35px]"></i></a>
                    </div>
                    <div class="max-w-[100px] ">
                        <button @click="edit('about_us2')" class="bg-white border border-white rounded-xl p-1 mb-[10px]">Edit Text</button>
                    </div>
                    <div class="max-w-[280px] min-h-[170px]">
                        <p class="text-white text-center break-words">{{textAreas.about_us2}}</p>
                    </div>
                    <div v-if="editButton==='about_us2'">
                         <textarea v-model="textAreas.about_us2.value" class="-mt-[67px] w-full h-40 border boder-black"></textarea>
                        <button @click="save" class="bg-white rounded-xl p-1">Save</button>
                    </div>
                </div>

                <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3">
                    <div class="flex justify-center w-full">
                        <a class="icon-color border border-transparent rounded-[30px] p-8 flex inline-flex items-center justify-center">
                            <i class="fa fa-phone text-white text-[30px]"></i></a>
                    </div>
                    <div class="max-w-[100px] ">
                        <button @click="edit('about_us3')" class="bg-white border border-white rounded-xl p-1 mb-[10px]">Edit Text</button>
                    </div>
                    <div class="max-w-[280px] min-h-[170px]">
                        <p class="text-white text-center break-words">{{textAreas.about_us3}}</p>
                    </div>
                    <div v-if="editButton==='about_us3'">
                         <textarea v-model="textAreas.about_us3.value" class="-mt-[67px] w-full h-40 border boder-black"></textarea>
                        <button @click="save" class="bg-white rounded-xl p-1">Save</button>
                    </div>
                </div>

               

                
            
            </div>

            
        </div>

        <!-- button to next section of homepage -->
        <div class="ml-auto z-50 fixed bottom-4 right-4">
                    <button @click="goToEditWebsite3()" class="bg-white border border-black rounded-2xl p-8">
                        <i class="fa fa-arrow-down "></i>
                    </button>
                </div>
    
    </AuthenticatedLayout>

</template>
<style>
.icon-color {
    background-color: #306091; /* Replace with your desired color */
}
</style>

