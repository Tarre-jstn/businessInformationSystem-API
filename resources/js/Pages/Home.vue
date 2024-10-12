<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <div class="flex flex-row">
                <div class="max-w-7xl sm:px-6 lg:px-8 py-6 flex flex-col" style="width: 60vw;">
                    <div class="bg-whiteoverflow-hidden shadow-sm sm:rounded-lg" style="background-color: #0F2C4A;">
                        <div class="p-6 text-gray-900 dark:text-gray-100">Welcome to the Home Page!</div>
                    </div>
                    <div class="flex flex-row justify-center">
                        <div class="inventory_table m-4 ">
                            <table>
                                <tr style="font-size: 1.375rem;">
                                    <div style="padding: 0.4rem 0.938rem 0.4rem;"><b>Inventory</b></div>
                                </tr>
                                <tr style="font-size: 1rem; background-color: white">
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Sold</th>
                                    <th>Stock</th>
                                </tr>
                                <tr v-for="product in filteredProducts" :key="product.id">
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.id }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <img :src="'/storage/' + product.image" alt="Product Image" class="w-12 h-12 object-cover"/>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.name }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.price }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.category }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.stock }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.sold }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            <span :class="{
                                                'bg-red-500 text-white py-1 px-3 rounded-full': product.status === 'Unlisted',
                                                'bg-green-500 text-white py-1 px-3 rounded-full': product.status === 'Listed',
                                                'bg-gray-500 text-white py-1 px-3 rounded-full': product.status === 'Out of Stock',
                                            }">{{ product.status }}</span>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.expDate }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex space-x-4">
                                        <button @click="editProductDetails(product)" class="bg-yellow-500 text-white p-3 rounded-full">
                                            <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                        </button>
                                        <button @click="deleteProduct(product.id)" class="bg-red-500 text-white p-3 rounded-full">
                                            <font-awesome-icon :icon="['fas', 'trash-can']" size="sm" />
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>You</td>
                                    <td>You</td>
                                    <td>You</td>
                                    <td>You</td>
                                    <td>You</td>
                                </tr>
                                <tr>
                                    <td>You</td>
                                    <td>You</td>
                                    <td>You</td>
                                    <td>You</td>
                                    <td>You</td>
                                </tr>
                                <tr>
                                    <td>You</td>
                                    <td>You</td>
                                    <td>You</td>
                                    <td>You</td>
                                    <td>You</td>
                                </tr>

                            </table>
                            <button style = "margin-left: 11vw; margin-top: 1.3vh;">
                                <ResponsiveNavLink :href="route('inventory')" :active="route().current('inventory')" style="color: white; font-size: 12px;">View all products</ResponsiveNavLink>
                            </button>
                        </div>
                        <DoughnutChart/>
                    </div>
                    <BarChart/>
                </div>
                <div class="flex flex-col">
                    <vue-cal hide-view-selector :time="false" active-view="month" xsmall class=" p-6" style = "background-color: #0F2C4A; margin-right: 30px; margin-top: 25px; height: 45vh; color: white; font-weight: bold; border-radius: 1rem;">
                        <template #arrow-prev>
                            <i class="icon material-icons">Previous</i>
                        </template>
                        <template #arrow-next>
                            <i class="icon material-icons">Next</i>
                        </template>
                    </vue-cal>
                    <div>
                        Social Media
                        <div class="flex flex-row">
                            <button>
                                Facebook
                            </button>
                            <button>
                                Twitter
                            </button>
                        </div>
                        <div class="flex flex-row">
                            <button>
                                Instagram
                            </button>
                            <button>
                                Tiktok
                            </button>
                        </div>
                    </div>
                    <BarChart/>
                </div>
    </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import BarChart from '@/Components/BarChart.vue';
import DoughnutChart from '@/Components/DoughnutChart.vue';
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { ref, computed } from 'vue';
import axios from 'axios';
import CategoriesModal from "@/Components/CategoriesModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const products = ref([]);
const showAddProductModal = ref(false);
const showCategoriesModal = ref(false);
const showEditProductModal = ref(false);
const listedCategories = ref([]);
const searchQuery = ref('');

const newProduct = ref({
    name: '',
    price: 0,
    category: '',
    stock: 0,
    sold: 0,
    status: '',
    expDate: '',
    image: null,
});

const editProduct = ref({
    id: null,
    name: '',
    price: 0,
    category: '',
    stock: 0,
    sold: 0,
    status: '',
    expDate: '',
    image: null,
});

const fetchProducts = async () => {
    try {
        const response = await axios.get('/api/products');
        products.value = response.data;
    } catch (error) {
        console.error("Error fetching products:", error);
    }
};

const fetchListedCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        listedCategories.value = response.data.filter(category => category.status === 'listed');
    } catch (error) {
        console.error("Error fetching categories:", error);
    }
};

const addProduct = async () => {
    try {
        const formData = new FormData();
        for (const key in newProduct.value) {
            formData.append(key, newProduct.value[key]);
        }
        const response = await axios.post('/api/products', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        products.value.push(response.data);
        showAddProductModal.value = false;
        resetNewProduct();
    } catch (error) {
        console.error("Error adding product:", error);
    }
};


export default {
    name: 'App',
    components: { BarChart, AuthenticatedLayout , VueCal, DoughnutChart, ResponsiveNavLink},
  }
</script>

<style>
/* Default styles for larger screens */
button {
    background-color: #0F2C4A;
    color:#FFFFFF;
    border-radius: 14px;
}
tr{

}

td, th {
    border-top: 1px solid #0F2C4A;
    border-bottom: 1px solid #0F2C4A;
    text-align: center;
}

td {
    font-size: 0.875rem;
}

table {
    border: 5px solid #0F2C4A;
    border-collapse: separate;
    width: 33vw;
    height: 26vh;
    border-radius: 14px;
}

/* Media queries for smaller screens */
@media (max-width: 1024px) {
    .flex {
        flex-direction: column; /* Change flex direction to column for tablets and smaller laptops */
    }

    .inventory_table {
        width: 100%;
        margin: 1rem 0;
    }
    
    table {
        width: 100%;
    }
    
    .max-w-7xl {
        width: 90vw;
    }
    
    .py-6 {
        padding: 1rem;
    }

    .vue-cal {
        height: 40vh;
        margin-right: 10px;
    }
}

@media (max-width: 480px) {
    .max-w-7xl {
        width: 100vw;
    }
    
    .py-6 {
        padding: 0.5rem;
    }
    
    .vue-cal {
        height: 30vh;
        margin-right: 5px;
    }
    
    td, th {
        font-size: 0.625rem;
        padding: 0.25rem 0.25rem;
    }
}
</style>
