
<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Define the chatbot data structure using ref
const chatbot = ref({
    id: null,
    bwh: '',     // Business Working Hours
    pd: '',      // Product Description
    shopee: '',  // Shopee link
    lazada: '',  // Lazada link
    region1: '', // Region 1 info
    region2: '', // Region 2 info
    region3: '', // Region 3 info
    region4a: '', // Region 4-A info
    region4b: '', // Region 4-B info
    region5: '',  // Region 5 info
    ncr: '',      // NCR info
    car: ''       // CAR info
});

onMounted(async () => {
    try {
        const response = await axios.get('/api/chatbot-response');
        const chatbotData = response.data[0];  // Get the first record
        if (chatbotData) {
            chatbot.value = {
                id: chatbotData.chatbot_id, 
                bwh: chatbotData.chabot_BWHours,
                pd: chatbotData.chabot_BPDescription,
                shopee: chatbotData.chabot_Shopee,
                lazada: chatbotData.chabot_Lazada,
                region1: chatbotData.chabot_Region1,
                region2: chatbotData.chabot_Region2,
                region3: chatbotData.chabot_Region3,
                region4a: chatbotData.chabot_Region4A,
                region4b: chatbotData.chabot_Region4B,
                region5: chatbotData.chabot_Region5,
                ncr: chatbotData.chabot_NCR,
                car: chatbotData.chabot_CAR
            };
        }
    } catch (error) {
        console.error(error);
    }
});


const updateChatbotData = async () => {
    try {
        const response = await axios.put(`/api/chatbot-response/${chatbot.value.id}`, {
            chabot_BWHours: chatbot.value.bwh,
            chabot_BPDescription: chatbot.value.pd,
            chabot_Lazada: chatbot.value.lazada,
            chabot_Shopee: chatbot.value.shopee,
            chabot_Region1: chatbot.value.region1,
            chabot_Region2: chatbot.value.region2,
            chabot_Region3: chatbot.value.region3,
            chabot_Region4A: chatbot.value.region4a,
            chabot_Region4B: chatbot.value.region4b,
            chabot_Region5: chatbot.value.region5,
            chabot_NCR: chatbot.value.ncr,
            chabot_CAR: chatbot.value.car
        });
        console.log("Response data:", response.data);

        if (response.status === 200) {
            alert('Chatbot Responses updated successfully');
        } else {
            console.error("Update failed:", response.data);
            alert('Update failed');
        }
    } catch (error) {
        console.error("Error updating responses:", JSON.stringify(error.response?.data, null, 2));
        alert('Failed to update responses profile');
    }
};

</script>


<template>
    <Head title="Update Chatbot Responses" />
    
    <AuthenticatedLayout>
        <div class="container mx-auto p-4 flex flex-col md:flex-row">
            <!-- Left Column (Business Details) -->
            <div class="w-full md:w-1/2 pt-20">
                <h1 class="text-3xl font-bold mb-4">Update Chatbot Responses</h1>
                <div class="space-y-6">
                    <div>
                        <label for="business-working-hours" class="block text-gray-700 text-sm font-bold mb-2">
                                Business Working Hours
                            </label>
                            <input
                                type="text"
                                placeholder="9AM - 9PM" 
                                id="business-working-hours"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="chatbot.bwh"
                            >
                    </div>
                    <div>
                        <label for="product-description" class="block text-gray-700 text-sm font-bold mb-2">
                                Product Description
                            </label>
                            <input
                                type="text"
                                id="product-description"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="chatbot.pd"
                            >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="lazada" class="block text-gray-700 text-sm font-bold mb-2">
                               Lazada
                            </label>
                            <input
                                type="text"
                                id="lazada"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="chatbot.lazada"
                            >
                        </div>

                        <div>
                            <label for="shopee" class="block text-gray-700 text-sm font-bold mb-2">
                                Shopee
                            </label>
                            <input
                                type="text"
                                id="shopee"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="chatbot.shopee"
                            >
                        </div>
                    </div>


                    <div>
                        <button
                            type="button"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            @click="updateChatbotData"
                        >
                            Update Responses
                        </button>
                    </div>
                </div>
            </div>


            <!-- Right Column (Regional Delivery Times) -->
            <div class="w-full md:w-1/2 flex flex-col items-center mt-8 md:mt-0 py-16">
                    <div class="mt-6 w-3/4 space-y-4">
                        <h4 class="text-xl font-bold mb-4">Regional Delivery Times</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 1 - Ilocos Region
                                </label>
                                <input
                                    type="text"
                                    placeholder="X days" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region1"
                                >
                            </div>

                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 2 - Cagayan Valley
                                </label>
                                <input
                                    type="text"
                                    placeholder="X days" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region2"
                                >
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 3 - Central Luzon
                                </label>
                                <input
                                    type="text"
                                    placeholder="X days" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region3"
                                >
                            </div>

                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 4A - CALABARZON
                                </label>
                                <input
                                    type="text"
                                    placeholder="X days" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region4a"
                                >
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 4B - MIMAROPA
                                </label>
                                <input
                                    type="text"
                                    placeholder="X days" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region4b"
                                >
                            </div>

                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 5 - Bicol
                                </label>
                                <input
                                    type="text"
                                    placeholder="X days" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region5"
                                >
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                NCR - National Capital Region
                                </label>
                                <input
                                    type="text"
                                    placeholder="X days" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.ncr"
                                >
                            </div>

                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                CAR - Cordillera Administrative Region
                                </label>
                                <input
                                    type="text"
                                    placeholder="X days" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.car"
                                >
                            </div>
                        </div>
                    </div>
                </div>
</template>

