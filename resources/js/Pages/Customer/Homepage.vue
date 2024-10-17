<script setup>
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref } from 'vue';

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
    businessDescription: ref(''),
    businessDetails: ref(''), 
    homePageImage: ref('')
}


const textAreas = {
    about_us1: ref(''),
    about_us2: ref(''),
    about_us3: ref(''),
    card1: ref(''),
    card1_img: ref(''),
    card2: ref(''),
    card2_img: ref(''),
    card3: ref(''),
    card3_img: ref(''),
    card4: ref(''),
    card4_img: ref(''),
    card5: ref(''),
    card5_img: ref(''),
    card6: ref(''),
    card6_img: ref(''),
    website_footNote: ref('')
}

const feature_toggle=ref('');
const onSale_toggle=ref('');

function logout(button){
    Inertia.post(route('logout'), {button});
}

onMounted(()=>{
    getWebsiteInfo();
})

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

        businessInfo.businessDescription.value = getWebsiteInfo1.data.website_description;
        businessInfo.businessDetails.value = getWebsiteInfo1.data.website_details;
        const imgUrl = `/storage/${getWebsiteInfo1.data.website_image}`;
        businessInfo.homePageImage.value=imgUrl;

        
       
        textAreas.about_us1.value = getWebsiteInfo1.data.about_us1;
        textAreas.about_us2.value = getWebsiteInfo1.data.about_us2;
        textAreas.about_us3.value = getWebsiteInfo1.data.about_us3;
        textAreas.website_footNote.value = getWebsiteInfo1.data.website_footNote;

        const getProductsInfo = await axios.get('/api/featured-products', {
            params: {business_id: 1}
        });

        const featuredProducts = getProductsInfo.data.slice(0, 6);

        if(featuredProducts.length<5){
            console.error('Featured Products must be 6 in number');
        }else{
            
        featuredProducts.forEach((product, index) => {
            const imgPath = product.product_img.replace('products/', '');
        if (index === 0) {
            textAreas.card1.value = product.product_name;
            textAreas.card1_img.value = imgPath;
        } else if (index === 1) {
            textAreas.card2.value = product.product_name;
            textAreas.card2_img.value = imgPath;
        } else if (index === 2) {
            textAreas.card3.value = product.product_name;
            textAreas.card3_img.value = imgPath;
        } else if (index === 3) {
            textAreas.card4.value = product.product_name;
            textAreas.card4_img.value = imgPath;
        } else if (index === 4) {
            textAreas.card5.value = product.product_name;
            textAreas.card5_img.value = imgPath;
        }else if (index === 5) {
            textAreas.card6.value = product.product_name;
            textAreas.card6_img.value = imgPath;
        }
        });
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
                    <a>Home</a>
                    <a class="text-white text-[18px]">Chat with Us</a>
                    <a class="text-white text-[18px]">Products & Services</a>
                    <a class="text-white text-[18px]">About Us</a>
                    <p>|</p>
                    <button @click="logout('register')" class="text-white">Register</button>
                    <button @click="logout('logout')" class=" cursor-pointer bg-white border border-white rounded-sm py-1 px-3">Log Out</button>
                </div>
        </div>

        <!-- section 1/EditWebsite1 -->
        <section>
        <div class="bg-website-main flex min-h-screen">

            <div class="mt-[150px] ml-[80px] flex-col h-1/2">
                <div>
                    <p class="text-white text-[40px] tracking-[5px]">{{businessInfo.businessName.value}}</p>
                </div>
                <div class="mt-[30px]">
                    <div class="max-w-[550px]">
                    <p class="font-extrabold text-[25px] text-white">{{ businessInfo.businessDescription.value }}</p>
                    </div>
                </div>
                <div class="mt-[30px]" >
                    <div class="max-w-[550px]">
                        <p id="business-details" class="text-[19px] text-white">{{ businessInfo.businessDetails.value }} </p>
                    </div>
                </div>

                <div class="mt-[90px] flex flex-row">
                    <button @click="logout('register')" class="mr-[20px] cursor-pointer bg-white border border-white rounded-sm py-[8px] px-[70px]">Register</button>
                    <p class="text-white text-xl">|</p>
                    <a class="ml-[35px] justify-center text-white text-[18px]">See All Products</a>
                </div>
            </div>


            <!-- image -->
            <div class="mr-[8px] mt-[130px] ml-auto flex-grow-0 w-1/2 max-w-xl">
                
                <img :src='businessInfo.homePageImage.value' class ="mt-8 w-full h-[390px] object-cover rounded-tl-[30px]"/>
            </div>
        </div>
        </section>

        <!-- section 2/EditWebsite2 -->
<section>
        
<div class=" bg-website-main1 flex min-h-screen relative">
        <div class="flex items-center p-3 absolute top-[5px] left-0 right-0 bottom-[500px] m-auto">
        <p class="mt-[10px] text-[70px] tracking-[3px] text-white font-bold flex-grow text-center">About Us</p>
    </div>

<!-- edit business info wag to iedit kasi business name ito-->
<div class=" mx-auto flex flex-row items-center justify-between w-full max-w-screen-lg mt-[250px]">
    
    <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3">
        <div class="flex justify-center w-full">
            <a class="icon-color border border-transparent rounded-[30px] p-12 flex inline-flex items-center justify-center">
                <i class="fa fa-check-circle text-white text-[50px]"></i></a>
        </div>
        <div class="max-w-[330px] min-h-[170px] mt-[100px]">
            <p class="text-white text-[19px] text-center break-words">{{textAreas.about_us1}}</p>
        </div>
    </div>

    <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3 mx-[100px]">
        <div class="flex justify-center w-full">
            <a class="icon-color border border-transparent rounded-[30px] p-12 flex inline-flex items-center justify-center">
            <i class="fa fa-tag text-white text-[50px]"></i></a>
        </div>
        <div class="max-w-[330px] min-h-[170px] mt-[100px]">
            <p class="text-white text-[19px] text-center break-words">{{textAreas.about_us2}}</p>
        </div>
    </div>

    <div class="flex -mt-[20px] flex-col items-center space-y-4 w-1/3">
        <div class="flex justify-center w-full">
            <a class="icon-color border border-transparent rounded-[30px] p-12 flex inline-flex items-center justify-center">
            <i class="fa fa-phone text-white text-[40px]"></i></a>
        </div>
        <div class="max-w-[330px] min-h-[170px] mt-[100px]">
            <p class="text-white text-[19px] text-center break-words">{{textAreas.about_us3}}</p>
        </div>
    </div>
</div>
</div>
</section>

    <!-- section 3/EditWebsite3 -->
    
    <section v-if="feature_toggle==='true'">
        <div class=" bg-website-main flex min-h-screen relative" style="min-height: calc(100vh + 100px);">

<div class="flex flex-col items-center p-3 absolute top-[10px] left-0 right-0 bottom-[500px] m-auto">
    <p class="mt-[30px] text-[40px]  text-white font-bold  text-center">Featured Products</p>
    <p class="mt-[10px] text-[20px]  text-white  text-center">
        A list of the most popular products loved by customers. 
        Best prices guaranteed everyday.
    </p>
</div>

<!-- edit business info wag to iedit kasi business name ito-->
<div class=" mt-8 mx-auto my-auto flex flex-wrap justify-center gap-4 w-full max-w-screen-lg mt-[10px] px-4 pt-[200px]">
    
    <div class="block flex flex-row gap-5">
    <!-- card 1 -->
        <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card1_img.value}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card1.value}}</p>
        </div>

     <!-- card 2 -->
     <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card2_img.value}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card2.value}}</p>
        </div>
    

    <!-- card 3 -->
    <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card3_img.value}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card3.value}}</p>
        </div>
    </div>

    <div class="block flex flex-row gap-5">
    <!-- card 4 -->
    <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card4_img.value}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card4.value}}</p>
        </div>

    <!-- card 5 -->
    <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card5_img.value}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card5.value}}</p>
        </div>

    <!-- card 6 -->
    <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card6_img.value}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card6.value}}</p>
        </div> 
    </div>
</div>
        </div>
    </section>

    <!-- section 4/Chat Section -->
<section>
    <div class="bg-website-main h-[2px] w-full"></div>
        <div class="bg-website-main1 flex flex-col min-h-screen">

        
    <div class="flex w-full justify-center items-center p-3">
        <p class="mt-[20px] text-[45px] tracking-[3px] text-white font-bold flex-grow text-center">Connect with Us!</p>
    </div>

<div class="flex flex-row items-center">
            <!-- image -->
    <div class="ml-[50px] mt-[100px] mr-auto w-1/2 max-w-xl">
        <img src="/storage/images/chat_img_homepage.jpeg" class ="mt-6 w-full h-[390px] object-cover rounded-[20px]"/>
    </div>

    <div class=" w-1/2 ml-auto flex-col h-1/2">
            <p class="text-white text-[30px] text-center mr-[50px]">
            Chat us using this website. Inquiries and feedback are accepted
             and to further reach us please refer to our contact information.
            </p>
            <img src="/storage/images/chat_icon.png" 
            class =" mx-auto mt-[90px] w-[88px] h-[120px] object-cover"
            @click="goTochatPage"/>
            
    </div>
</div>
</div>

    </section>

    <section>
        <div class="ml-1 bg-website-main flex flex-col min-h-screen" style="min-height: calc(70vh);">

<div class="flex flex-row justify-between mt-[5px] ml-8 mr-8 w-full">
<!-- FootNote -->
<div class="mr-auto mt-40 ml-8 flex flex-col h-1/2 w-1/2 max-w-md">
    <div class="max-w-[50px]">
        <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
    </div>

    <div class="mt-5">
        <p class=" text-xl text-white">{{ textAreas.website_footNote }}</p>
        <div class="mt-[20px]">
        <a :href="businessInfo.business_Facebook.value" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-facebook-f"></i></a>
        <a :href="businessInfo.business_X.value" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa fa-twitter"></i></a>
        <a :href="businessInfo.business_Instagram.value" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-instagram"></i></a>
        <a :href="businessInfo.business_Tiktok.value" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-tiktok"></i></a>
        </div>
    </div>

</div>

<!-- Contact Us -->
<div class="mt-[100px] ml-auto flex flex-grow-0 w-1/2 max-w-md w-1/2 max-w-md">
    <div class="mt-2 flex flex-col ">
        <p class="text-white font-bold text-[50px]">Contact Us</p>
        <p class="text-white mt-[10px]">Email: {{ businessInfo.business_Email }} </p>
        <p class="text-white">Contact No.: {{ businessInfo.business_Contact_Number }} </p>
        <p class="text-white">Address: {{ businessInfo.business_Address }} </p>
    </div>
</div>
</div>

<div class="mt-[60px] w-full">
    <hr class="border-white mx-auto w-11/12">
</div>

<div class="ml-[60px] mr-auto w-full">
    <p class="text-[17px] text-white mt-2"><i class="fa fa-copyright"></i> {{ textAreas.businessName }} All rights reserved</p>
</div>
</div>
    </section>
</template>
<style>
.icon-color {
    background-color: #306091; /* Replace with your desired color */
}
.fa.fa-twitter{
	font-family:sans-serif;
}
.fa.fa-twitter::before{
	content:"𝕏";
	font-size:1.2em;
}
</style>