<script setup>
import { defineStore } from 'pinia';
import { Link } from '@inertiajs/vue3';
import { ArchiveBoxIcon, BuildingLibraryIcon, BuildingStorefrontIcon, ChatBubbleLeftRightIcon, GlobeAltIcon, HomeIcon, ReceiptPercentIcon } from '@heroicons/vue/24/solid'
import { onMounted, ref } from 'vue';
import axios from 'axios';

const business = ref({
    business_id: null,
    name: '',
    image: null,
});
const LogoPic = ref(null);

onMounted(async () => {
    try {
        const response = await axios.get('/api/Business');
        if (response.data && Array.isArray(response.data) && response.data.length > 0) {
            const businessData = response.data[0];
            business.value = {
                business_id: businessData.business_id,
                name: businessData.business_Name,
                image: businessData.business_image,
            };

            LogoPic.value = businessData.business_image
                ? `/storage/business_logos/${businessData.business_image}`
                : '/storage/business_logos/default_logo.png';

        } else {
            console.error("No business data found.");
        }
    } catch (error) {
        console.error("Error fetching business data:", error);
    }
});

</script>

<template>
    <div class="sidebar fixed sm:relative sm:translate-x-0 -translate-x-full">
        <div class="logo">
            <img :src="LogoPic" alt="Business Logo" class="logo-image" />
            <h1>{{ business.name }}</h1>
        </div>
        <nav>
            <ul>
                <li class="flex items-center mb-4"><Link :href="route('home')" :class="{ active: route().current('home') }">
                    <HomeIcon class="size-6"/>
                    <span class="ml-3">Home</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('website')" :class="{ active: route().current('website') }">
                    <GlobeAltIcon class="size-6"/>
                    <span class="ml-3">Website</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('chats')" :class="{ active: route().current('chats') }">
                    <ChatBubbleLeftRightIcon class="size-6"/>
                    <span class="ml-3">Chats</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('inventory')" :class="{ active: route().current('inventory') }">
                    <ArchiveBoxIcon class="size-6"/>
                    <span class="ml-3">Inventory</span>
                </Link></li> 

                <li class="flex items-center mb-4"><Link :href="route('receipt')" :class="{ active: route().current('receipt') }">
                    <ReceiptPercentIcon class="size-6"/>
                    <span class="ml-3">Receipt</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('finance')" :class="{ active: route().current('finance') }">
                    <BuildingLibraryIcon class="size-6"/>
                    <span class="ml-3">Finance</span>
                </Link></li>

                
                <li class="flex items-center mb-4"><Link :href="route('BusinessInfo')" :class="{ active: route().current('BusinessInfo') }">
                    <BuildingStorefrontIcon class="size-6"/>
                    <span class="ml-3">BusinessInfo</span>
                </Link></li>
            </ul>
        </nav>
    </div>
</template>

<style scoped>
.sidebar {
    background-color: #1a202c;
    color: #fff;
    width: 250px;
    height: 100vh;
    z-index: 50; /* Adjust z-index to ensure the sidebar overlays other content */
}


.sidebar h1 {
    font-size: 1.25rem;
    color: #f0f0f0;
    text-align: center;
    margin-top: 1rem;
}

nav ul {
    margin-top: 2rem;
    display: flex;
    flex-direction: column; 
    align-items: center; 
}

nav ul li {
    list-style-type: none;
    margin: 0;
    width: 100%; 
}

nav ul li a {
    width: 100%;
    justify-content: flex-start;
    text-decoration: none;
    color: #f0f0f0;
    font-size: 1rem;
    display: flex;
    align-items: center;
    padding-left: 50px;
    padding: 0.75rem 1rem;
    transition: background-color 0.2s ease-in-out;
}

nav ul li a svg {
    margin-right: 10px;
    margin-left:50px; 
    width: 30px;
    height: 30px;
}

nav ul li a span {
    display: inline-block; 
    width: calc(100% - 50px); 
    text-align: left; 
}

nav ul li a.active { 
    width: 100%;
    background-color: #ddd;
    color: #202c34;
    
}

nav ul li a:hover {
    width: 100%;
    background-color: #ddd;
    color: #202c34;
}

.logo {
    text-align: center;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center; /* Aligns items horizontally center */
}

.logo-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}

.logo h1 {
    font-size: 1.5em;
    margin: 0; /* Reset margin for h1 */
}
nav ul {
    list-style-type: none;
    padding: 0;
}
nav ul li {
    margin: 10px 0;
}
nav ul li a {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 10px 20px;
}
nav ul li a i {
    margin-right: 10px;
}
</style>
