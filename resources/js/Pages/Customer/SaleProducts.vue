<script setup>
import { text } from '@fortawesome/fontawesome-svg-core';
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref, reactive } from 'vue';

const businessInfo = {
    businessImage: ref(''),
    businessName: ref(''),
    business_Email: ref(''),
    business_Contact_Number: ref(''),
    business_Address: ref(''),
    business_Facebook: ref(''),
    business_X: ref(''),
    business_Instagram: ref(''),
    business_Tiktok: ref(''),
    website_footNote: ref('')
}

const textAreas = reactive({
    card1: '',
    card1_img: '',
    card1_price: '',
    card2: '',
    card2_img: '',
    card2_price: '',
    card3: '',
    card3_img: '',
    card3_price: '',
    card4: '',
    card4_img: '',
    card4_price: '',
    card5: '',
    card5_img: '',
    card5_price: '',
    card6: '',
    card6_img: '',
    card6_price: '',
    card7: '',
    card7_img: '',
    card7_price: '',
    card8: '',
    card8_img: '',
    card8_price: '',
    card9: '',
    card9_img: '',
    card9_price: '',
    website_footNote: ''
});

const next = ref(false);
const lengthArray = ref(null);

function logout(button){
    Inertia.post(route('logout'), {button});
}

onMounted(()=>{
    getWebsiteInfo();
})

function showMore(){
    next.value=true;
    if(lengthArray.value<9){
        alert(`There are only ${lengthArray.value} to be displayed`);
    }else{
    getWebsiteInfo();
    }
}

async function getWebsiteInfo(){
    try{

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });

        const getWebsiteInfo1 = await axios.get('/api/website', {
            params: {business_id: 1}
        });
        console.log('Business data: ',getBusinessInfo.data);
        
        console.log('Website data: ',getWebsiteInfo1.data);

        const imgBusinessUrl = `/storage/${getBusinessInfo.data.business_image}`;
        businessInfo.businessImage.value = imgBusinessUrl;
        businessInfo.businessName.value = getBusinessInfo.data.business_Name;
        businessInfo.business_Email.value = getBusinessInfo.data.business_Email;
        businessInfo.business_Contact_Number.value = getBusinessInfo.data.business_Contact_Number;
        businessInfo.business_Address.value = getBusinessInfo.data.business_Address;

        businessInfo.business_Facebook.value = getBusinessInfo.data.business_Facebook;
        businessInfo.business_X.value = getBusinessInfo.data.business_X;
        businessInfo.business_Instagram.value = getBusinessInfo.data.business_Instagram;
        businessInfo.business_Tiktok.value = getBusinessInfo.data.business_Tiktok;

        businessInfo.website_footNote.value = getWebsiteInfo1.data.website_footNote;

        const getProductsInfo = await axios.get('/api/sale-products', {
            params: {business_id: 1}
        });

        const saleProducts = getProductsInfo.data;
        lengthArray.value = saleProducts.length;

        if(saleProducts){
        saleProducts.forEach((product, index) => {
            console.log('imgPath: '+product.product_img);
            const imgPath = product.product_img.replace('products/', '');

            
            if(!next.value){
                    if(index<9){
                    textAreas[`card${index+1}`] = product.product_name;
                    textAreas[`card${index+1}_img`] = imgPath;
                    textAreas[`card${index+1}_price`]=product.product_price;
                    }

            }else{

                for(let i=1;i<=9;i++){
                    textAreas[`card${i}`] = '';
                    textAreas[`card${i}_img`] = '';
                    textAreas[`card${i}_price`]='';
                }
                if(index>=9&&index<=17){
                textAreas[`card${index-8}`] = product.product_name;
                textAreas[`card${index-8}_img`] = imgPath;
                textAreas[`card${index-8}_price`]=product.product_desc;
                }
            }
        });
        }else{
            alert("There are currently no on SALE products.");
        }
    
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}

function goTochatPage(){
    //none pa
}
</script>

<template>
        <!-- header -->
        <div class=" bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a class="text-white text-[18px]":href="route('homepage')">Home</a>
                    <a class="text-white text-[18px]">Chat with Us</a>
                    <a class="text-black text-[18px]">Products & Services</a>
                    <a class="text-white text-[18px]" :href="route('aboutUs_page')">About Us</a>
                    <p>|</p>
                    <button @click="logout('register')" class="text-white">Register</button>
                    <button @click="logout('logout')" class=" cursor-pointer bg-white border border-white rounded-sm py-1 px-3">Log Out</button>
                </div>
        </div>

       


    <!-- products -->
    <section>
        <div class=" bg-website-main flex min-h-screen relative" style="min-height: calc(100vh + 800px);">

<div class="flex flex-col items-center p-3 absolute top-[10px] left-0 right-0 bottom-[500px] m-auto">
    <p class="mt-[30px] text-[40px]  text-white font-bold  text-center">SALE Products</p>
</div>

<div class=" mt-8 mx-auto my-auto flex flex-wrap justify-center gap-4 w-full max-w-screen-lg mt-[10px] px-4 pt-[200px]">
    
    <div class="block flex flex-row gap-9">
    <!-- card 1 -->
        <div v-if="textAreas.card1_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card1_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card1}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card1_price}}</p>
        </div>

     <!-- card 2 -->
     <div v-if="textAreas.card2_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card2_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card2}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card2_price}}</p>
        </div>
    

    <!-- card 3 -->
    <div v-if="textAreas.card3_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card3_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card3}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card3_price}}</p>
        </div>
    </div>

    <div class="block flex flex-row gap-9">
    <!-- card 4 -->
    <div v-if="textAreas.card4_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card4_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card4}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card4_price}}</p>
        </div>

    <!-- card 5 -->
    <div v-if="textAreas.card5_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card5_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card5}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card5_price}}</p>
        </div>

    <!-- card 6 -->
    <div v-if="textAreas.card6_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card6_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card6}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card6_price}}</p>
        </div> 
    </div>

    <div class="block flex flex-row gap-9">

        <!-- card 7 -->
        <div v-if="textAreas.card7_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card7_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card7}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card7_price}}</p>
        </div> 

        <!-- card 8 -->
        <div v-if="textAreas.card8_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card8_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card8}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card8_price}}</p>
        </div> 

        <!-- card 9 -->
        <div v-if="textAreas.card9_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card9_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card9}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card9_price}}</p>
        </div> 

    </div>
    <div class="block mx-auto">
        <button @click="showMore" class="cursor-pointer bg-white border border-white rounded-sm py-6 px-9">Show More</button>
        
    </div>
</div>
        </div>
    </section>


</template>
<style>
.icon-color {
    background-color: #306091;
}
.fa.fa-twitter{
	font-family:sans-serif;
}
.fa.fa-twitter::before{
	content:"𝕏";
	font-size:1.2em;
}
</style>