<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const business = ref({
    business_id: null,
    name: '',
    email: '',
    province: '',
    city: '',
    barangay: '',
    address: '',
    phone: '',
    telephone: '',
    facebook: '',
    x: '',
    instagram: '',
    tiktok: '',
    image: null,
});

// Profile picture
const profilePicture = ref(null);

// Reactive properties for provinces, cities, and barangays
const provinces = ref([]);
const filteredCities = ref([]);
const filteredBarangays = ref([]);

// Fetch provinces from PSGC API
const fetchProvinces = async () => {
    try {
        const response = await axios.get('https://psgc.gitlab.io/api/provinces/');
        provinces.value = response.data;
        console.log("Provinces fetched: ", provinces.value);
    } catch (error) {
        console.error("Error fetching provinces:", error);
    }
};

// Fetch cities by province
const fetchCitiesByProvince = async (provinceCode) => {
    try {
        const response = await axios.get(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities-municipalities/`);
        filteredCities.value = response.data;
        console.log("Cities fetched for province:", provinceCode, filteredCities.value);
    } catch (error) {
        console.error("Error fetching cities:", error);
    }
};

// Fetch barangays by city
const fetchBarangaysByCity = async (cityCode) => {
    try {
        const response = await axios.get(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`);
        filteredBarangays.value = response.data;
        console.log("Barangays fetched for city:", cityCode, filteredBarangays.value);
    } catch (error) {
        console.error("Error fetching barangays:", error);
    }
};

const isMounting = ref(true);  // Flag to track if data is being mounted

// Watch province change to fetch cities and reset city and barangay
watch(() => business.value.province, async (newProvince) => {
    const selectedProvince = provinces.value.find(p => p.name === newProvince);
    if (selectedProvince) {
        await fetchCitiesByProvince(selectedProvince.code);
        
        if (!isMounting.value) {
            business.value.city = ''; // Reset city to show "Select City" only if not mounting
            business.value.barangay = ''; // Reset barangay to show "Select Barangay" only if not mounting
            filteredBarangays.value = [];
        }
    }
});

// Watch city change to fetch barangays
watch(() => business.value.city, async (newCity) => {
    const selectedCity = filteredCities.value.find(c => c.name === newCity);
    if (selectedCity) {
        await fetchBarangaysByCity(selectedCity.code);
        if (!isMounting.value) {
            business.value.barangay = ''; // Reset barangay to show "Select Barangay" only if not mounting
        }
    } else {
        filteredBarangays.value = [];
    }
});

// Fetch business data on mount and populate dropdowns
onMounted(async () => {
    await fetchProvinces();

    try {
        const response = await axios.get('/api/Business');
        if (response.data && Array.isArray(response.data) && response.data.length > 0) {
            const businessData = response.data[0];
            business.value = {
                business_id: businessData.business_id,
                name: businessData.business_Name,
                email: businessData.business_Email,
                province: businessData.business_Province,
                city: businessData.business_City,
                barangay: businessData.business_Barangay,
                address: businessData.business_Address,
                phone: businessData.business_Phone_Number,
                telephone: businessData.business_Telephone_Number,
                facebook: businessData.business_Facebook,
                x: businessData.business_X,
                instagram: businessData.business_Instagram,
                tiktok: businessData.business_Tiktok,
                image: businessData.business_image,
            };

            // Set profile picture
            profilePicture.value = businessData.business_image
                ? `/storage/business_logos/${businessData.business_image}`
                : '/storage/business_logos/default_logo.png';

            // Pre-select cities and barangays based on fetched data
            const selectedProvince = provinces.value.find(p => p.name === businessData.business_Province);
            if (selectedProvince) {
                await fetchCitiesByProvince(selectedProvince.code);
                const selectedCity = filteredCities.value.find(c => c.name === businessData.business_City);
                if (selectedCity) {
                    await fetchBarangaysByCity(selectedCity.code);
                    business.value.barangay = businessData.business_Barangay;
                }
            }
        } else {
            console.error("No business data found or invalid format.");
        }
    } catch (error) {
        console.error("Error fetching business data:", error);
        alert('Failed to fetch business profile');
    } finally {
        isMounting.value = false;  // End mounting state after data is loaded
    }
});

// Handle profile picture change
const onProfilePictureChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        profilePicture.value = URL.createObjectURL(file);
        business.value.image = file;
    } else {
        profilePicture.value = '/storage/business_logos/default_logo.png';
        business.value.image = null;
    }
};

// Handle business update
const updateBusiness = async () => {
    if (!business.value.business_id) {
        alert("Business ID is missing!");
        return;
    }

    const formData = new FormData();
    formData.append('business_Name', business.value.name.trim());
    formData.append('business_Email', business.value.email.trim());
    formData.append('business_Province', business.value.province);
    formData.append('business_City', business.value.city);
    formData.append('business_Barangay', business.value.barangay);
    formData.append('business_Address', business.value.address.trim());
    formData.append('business_Phone_Number', business.value.phone.trim() || '');
    formData.append('business_Telephone_Number', business.value.telephone.trim() || '');
    formData.append('business_Facebook', business.value.facebook || '');
    formData.append('business_X', business.value.x || '');
    formData.append('business_Instagram', business.value.instagram || '');
    formData.append('business_Tiktok', business.value.tiktok || '');

    if (business.value.image instanceof File) {
        formData.append('business_image', business.value.image);
    }

    try {
        const response = await axios.post(`/api/Business/${business.value.business_id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-HTTP-Method-Override': 'PUT'
            },
        });

        if (response.data.success) {
            alert('Business profile updated successfully');
        } else {
            alert('Failed to update business profile');
        }
    } catch (error) {
        alert('Failed to update business profile');
    }
};
</script>


    <template>
        <AuthenticatedLayout>
            <Head title="Update Business Profile" />

            <div class="container mx-auto p-4 flex flex-col md:flex-row">
                <!-- Left Column (Form Fields) -->
                <div class="w-full md:w-1/2 pt-20">
                    <h1 class="text-3xl font-bold mb-4">Update Business Profile</h1>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="business-name" class="block text-gray-700 text-sm font-bold mb-2">
                                    Business Name
                                </label>
                                <input
                                    type="text"
                                    id="business-name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.name"
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
                                    v-model="business.email"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="province" class="block text-gray-700 text-sm font-bold mb-2">
                                    Province/Region
                                </label>
                                <select
                                    id="province"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.province"
                                >
                                    <option value="">Select Province</option>
                                    <option v-for="province in provinces" :key="province.code" :value="province.name">{{ province.name }}</option>
                                </select>
                            </div>

                            <!-- City Dropdown -->
                            <div>
                                <label for="city" class="block text-gray-700 text-sm font-bold mb-2">
                                    Municipality/City
                                </label>
                                <select
                                    id="city"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.city"
                                >
                                    <option value="">Select City</option>
                                    <option v-for="city in filteredCities" :key="city.code" :value="city.name">{{ city.name }}</option>
                                </select>

                            </div>

                            <!-- Barangay Dropdown -->
                            <div>
                                <label for="barangay" class="block text-gray-700 text-sm font-bold mb-2">
                                    Barangay
                                </label>
                                <select
                                    id="barangay"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.barangay"
                                >
                                    <option value="">Select Barangay</option>
                                    <option v-for="barangay in filteredBarangays" :key="barangay.code" :value="barangay.name">{{ barangay.name }}</option>
                                </select>

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
                                    v-model="business.address"
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
                                    v-model="business.phone"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="telephone-number" class="block text-gray-700 text-sm font-bold mb-2">
                                Telephone Number
                            </label>
                            <input
                                type="text"
                                id="telephone-number"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="business.telephone"
                            >
                        </div>

                        <div>
                            <button
                                type="button"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                @click="updateBusiness"
                            >
                                Update Business
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Profile Picture and Social Links) -->
                <div class="w-full md:w-1/2 flex flex-col items-center mt-8 md:mt-0 py-0">
                    <div class="relative group">
                        <img :src="profilePicture" alt="Profile Picture" class="w-32 h-32 rounded-full border-4 border-blue-500 object-cover">
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


                    <!-- Social Media Links -->
                    <div class="mt-6 w-3/4 space-y-4">
                        <!-- Facebook Section -->
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                Facebook
                            </label>
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24">
                                    <path d="M12,2C6.477,2,2,6.477,2,12c0,5.013,3.693,9.153,8.505,9.876V14.65H8.031v-2.629h2.474v-1.749 c0-2.896,1.411-4.167,3.818-4.167c1.153,0,1.762,0.085,2.051,0.124v2.294h-1.642c-1.022,0-1.379,0.969-1.379,2.061v1.437h2.995 l-0.406,2.629h-2.588v7.247C18.235,21.236,22,17.062,22,12C22,6.477,17.523,2,12,2z"></path>
                                </svg>
                                <input type="text" 
                                    placeholder="Facebook @username" 
                                    class="w-full h-10 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.facebook"
                                >
                            </div>
                        </div>

                        <!-- X Section -->
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                X
                            </label>
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 48 48">
                                    <path d="M 6.9199219 6 L 21.136719 26.726562 L 6.2285156 44 L 9.40625 44 L 22.544922 28.777344 L 32.986328 44 L 43 44 L 28.123047 22.3125 L 42.203125 6 L 39.027344 6 L 26.716797 20.261719 L 16.933594 6 L 6.9199219 6 z"></path>
                                </svg>
                                <input type="text" 
                                    placeholder="X @username" 
                                    class="w-full h-10 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.x"
                                >
                            </div>
                        </div>

                        <!-- Instagram Section -->
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                Instagram
                            </label>
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 48 48">
                                    <path d="M 16 3 C 8.83 3 3 8.83 3 16 L 3 34 C 3 41.17 8.83 47 16 47 L 34 47 C 41.17 47 47 41.17 47 34 L 47 16 C 47 8.83 41.17 3 34 3 L 16 3 z M 37 11 C 38.1 11 39 11.9 39 13 C 39 14.1 38.1 15 37 15 C 35.9 15 35 14.1 35 13 C 35 11.9 35.9 11 37 11 z M 25 14 C 31.07 14 36 18.93 36 25 C 36 31.07 31.07 36 25 36 C 18.93 36 14 31.07 14 25 C 14 18.93 18.93 14 25 14 z M 25 16 C 20.04 16 16 20.04 16 25 C 16 29.96 20.04 34 25 34 C 29.96 34 34 29.96 34 25 C 34 20.04 29.96 16 25 16 z"></path>
                                </svg>
                                <input type="text" 
                                    placeholder="Instagram @username" 
                                    class="w-full h-10 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.instagram"
                                >
                            </div>
                        </div>

                        <!-- TikTok Section -->
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                TikTok
                            </label>
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 48 48">
                                    <path d="M41,4H9C6.243,4,4,6.243,4,9v32c0,2.757,2.243,5,5,5h32c2.757,0,5-2.243,5-5V9C46,6.243,43.757,4,41,4z M37.006,22.323 c-0.227,0.021-0.457,0.035-0.69,0.035c-2.623,0-4.928-1.349-6.269-3.388c0,5.349,0,11.435,0,11.537c0,4.709-3.818,8.527-8.527,8.527 s-8.527-3.818-8.527-8.527s3.818-8.527,8.527-8.527c0.178,0,0.352,0.016,0.527,0.027v4.202c-0.175-0.021-0.347-0.053-0.527-0.053 c-2.404,0-4.352,1.948-4.352,4.352s1.948,4.352,4.352,4.352s4.527-1.894,4.527-4.298c0-0.095,0.042-19.594,0.042-19.594h4.016 c0.378,3.591,3.277,6.425,6.901,6.685V22.323z"></path>
                                </svg>
                                <input type="text" 
                                    placeholder="TikTok @username" 
                                    class="w-full h-10 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.tiktok"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>
