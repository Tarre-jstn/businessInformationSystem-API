<script setup>
import { Link } from '@inertiajs/vue3';
import { ArchiveBoxIcon, BuildingLibraryIcon, ChatBubbleLeftRightIcon, GlobeAltIcon, HomeIcon, ReceiptPercentIcon } from '@heroicons/vue/24/solid'
</script>

<template>
    <div class="sidebar fixed sm:relative sm:translate-x-0 -translate-x-full">
        <div class="logo">
            <img src="https://picsum.photos/200" alt="Logo" />
            <h1>F.C. Products</h1>
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

                <li class="flex items-center mb-4"><Link :href="route('settings')" :class="{ active: route().current('settings') }">
                    <i class="icon-settings"></i>
                    <span class="ml-3">Additional Settings</span>
                </Link></li>
                <li class="flex items-center mb-4"><Link :href="route('BusinessInfo')" :class="{ active: route().current('BusinessInfo') }">
                    <span class="ml-3">BusinessInfo</span>

                <li><Link :href="route('home')"><i class="icon-home"></i> Home</Link></li>
                <li><Link :href="route('website')"><i class="icon-globe"></i> Website</Link></li>
                <li><Link :href="route('chats')"><i class="icon-chat"></i> Chats</Link></li>
                <li><Link :href="route('inventory')"><i class="icon-inventory"></i> Inventory</Link></li>
                <li><Link :href="route('invoice')"><i class="icon-receipt"></i> Receipt</Link></li>
                <li><Link :href="route('finance')"><i class="icon-finance"></i> Finance</Link></li>
                <li><Link :href="route('settings')"><i class="icon-settings"></i> Additional Settings</Link></li>
            </ul>
        </nav>
    </div>
</template>

<style scoped>
.sidebar {
    width: 250px;
    background-color: #202c34;
    transition: all 0.3s ease;
    z-index: 100;
    height: 100vh;
    box-shadow: 2px 0px 12px rgba(0, 0, 0, 0.1);
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
    align-items: center; 
}
.logo img {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    margin-bottom: 10px; 
}
.logo h1 {
    font-size: 1.5em;
    margin: 0; 
}
.active {
    background-color: #ddd;
    color: #202c34;
}



</style>