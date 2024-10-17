<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Head } from '@inertiajs/vue3';

function logout(button){
    Inertia.post(route('logout'), {button});
}

function account(){
    Inertia.visit(route('account_settings'));
}
let userInfo = ref({
    user_id: '',
    profile_img: '',
    name: '',
    email: '',
    address: '',
    contact_number: ''
});

let businessImage = ref('');

const profilePicture = ref(null);
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

        businessImage.value =`/storage/${getBusinessInfo.data.business_image}`;
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}
onMounted(async () => {
    getWebsiteInfo();
try {
    const response = await axios.get('/showUser');
    console.log("API Response:", response.data);

    if (response.data) {
        userInfo.value = {
            user_id: response.data.id,
            profile_img: response.data.profile_img,
            name: response.data.name,
            email: response.data.email,
            address: response.data.address,
            contact_number: response.data.contact_number
    };

        // Set profile picture or default if not available
        profilePicture.value = response.data.profile_img 
        ? `/storage/user_profile/${response.data.profile_img}` 
        : '/storage/user_profile/default-profile.png';

    } else {
        console.error("No business data found or invalid format.");
    }
} catch (error) {
    console.error("Error fetching business data:", error);
    alert('Failed to fetch business profile');
}
});




// Handle file input for the profile picture
const onProfilePictureChange = (event) => {
const file = event.target.files[0];
if (file) {
    profilePicture.value = URL.createObjectURL(file);  // Display selected image preview
    userInfo.value.profile_img = file;  // Store the file to be sent to the backend
} else {
    profilePicture.value = '/storage/user_profile/default-profile.png';  // Default image fallback
    userInfo.value.profile_img = null;
}
};

const updateBusiness = async () => {

const formData = new FormData();
formData.append('name', userInfo.value.name.trim());
formData.append('email', userInfo.value.email.trim());
formData.append('address', userInfo.value.address.trim());
formData.append('contact_number', userInfo.value.contact_number.trim());

if (userInfo.value.profile_img instanceof File) {
    formData.append('profile_img', userInfo.value.profile_img);
}

for (let pair of formData.entries()) {
    console.log(`Data to be updated: ${pair[0]}: ${pair[1]}`);
}

try {
    const response = await axios.post(`/update_user/${userInfo.value.user_id}`, formData, {
    headers: {
        'Content-Type': 'multipart/form-data',
        'X-HTTP-Method-Override': 'PUT'
    },
    });
    if (response.data.success) {
    alert('Business profile updated successfully');
    } else {
    console.error("Update failed:", response.data);
    alert('Please input only .jpg .png or .jpeg');
    }
} catch (error) {
    console.error("Error updating business data:", JSON.stringify(error.response?.data, null, 2));
    alert('Failed to update business profile. Invalid Image input.');
}
};
</script>

<template>
        <Head title="Update Business Profile" />

        <!-- header -->
        <div class=" bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessImage.value' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a>Home</a>
                    <a class="text-white text-[18px]">Chat with Us</a>
                    <a class="text-white text-[18px]">Products & Services</a>
                    <a class="text-white text-[18px]">About Us</a>
                    <p>|</p>
                    <div class="flex flex-col">
                        <a @click="logout('logout')" class=" cursor-pointer text-white text-[14px] underline">Log Out</a>
                        <a @click="account" class=" cursor-pointer text-white text-[14px] underline">Account</a>
                    </div> 
                    <div class="w-[50px] h-[50px]">
                        <img v-if="isLoading" src='/storage/user_profile/default-profile.png'/>
                        <img v-else-if="profilePicture" :src='profilePicture' alt="Logo" class="h-full object-cover rounded-full" />
                        <img v-else src='/storage/user_profile/default-profile.png'/>
                    </div>
                    
                </div>
        </div>

        <div class="bg-website-main flex min-h-screen">
        <div class="container mx-auto p-4 flex flex-col md:flex-row">
            <!-- Left Column (Form Fields) -->
            <div class="ml-[80px] w-full md:w-1/2 pt-20">
                <h1 class="text-3xl text-white font-bold mb-4">Update Profile</h1>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="business-name" class="block text-gray-700 text-sm font-bold mb-2">
                                Name
                            </label>
                            <input
                                type="text"
                                id="business-name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="userInfo.name"
                            >
                    </div>

                        <div>
                            <label for="email-address" class="block text-gray-700 text-sm font-bold mb-2">
                                Email Address
                            </label>
                            <input
                                type="email"
                                id="email-address"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="userInfo.email"
                            >
                        </div>
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="address" class="block text-gray-700 text-sm font-bold mb-2">
                                Address
                            </label>
                            <input
                                type="text"
                                id="address"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="userInfo.address"
                            >
                    </div>

                        <div>
                            <label for="phone-number" class="block text-gray-700 text-sm font-bold mb-2">
                                Phone Number
                            </label>
                            <input
                                type="text"
                                id="phone-number"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="userInfo.contact_number"
                            >
                        </div>
                    </div>

                    <div class="mx-auto">
                        <button
                            type="button"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            @click="updateBusiness"
                        >
                            Update Profile
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column (Profile Picture)-->
            <div class="w-full md:w-1/2 flex flex-col items-center mt-[30px]">
                <div class="relative group">
                    
                        <img v-if="isLoading" src='/storage/user_profile/default-profile.png'/>
                        <img v-else-if="profilePicture" :src='profilePicture' alt="Profile Picture" class="w-[350px] h-[350px] rounded-full border-4 border-black object-cover" />
                        <img v-else src='/storage/user_profile/default-profile.png'/> 
                        <input
                            type="file"
                            id="profile-upload"
                            class="absolute inset-0 opacity-0 cursor-pointer z-10" 
                            @change="onProfilePictureChange"
                        >
                    <label for="profile-upload" class="absolute bottom-0 right-0 bg-gray-800 text-white p-1 rounded-full cursor-pointer">
                        <i class="fas fa-camera"></i>
                    </label>
                    <!-- Overlay for hover effect -->
                    <div class="absolute inset-0 pl-4 bg-gray-800 bg-opacity-50 flex items-center justify-center text-white text-sm font-bold opacity-0 rounded-full group-hover:opacity-100 transition-opacity z-0">
                        Please select a profile picture
                    </div>
                </div>
               
            </div>


        </div>
    </div>
</template>


