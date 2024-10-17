<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoriesModal from "@/Components/CategoriesModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const products = ref([]);
const showAddProductModal = ref(false);
const showCategoriesModal = ref(false);
const showEditProductModal = ref(false);
const listedCategories = ref([]);
const searchQuery = ref('');
const imagePreviewUrl = ref(null); 
const editImagePreviewUrl = ref(null);


const newProduct = ref({
    name: '',
    brand: '',
    price: 0,
    category: '',
    stock: 0,
    sold: 0,
    status: '',
    expDate: '',
    image: null,
    seniorPWD_discountable: 'no',
    description: '',
    on_sale: 'no',
    on_sale_price: 0,
    featured: 'false',
});

const editProduct = ref({
    id: null,
    name: '',
    brand: '',
    price: 0,
    category: '',
    stock: 0,
    sold: 0,
    status: '',
    expDate: '',
    image: null,
    seniorPWD_discountable: null,
    description: '',
    on_sale: null,
    on_sale_price: 0,
    featured: 'null'
});

watch(() => editProduct.value.on_sale, (newValue) => {
    if (newValue === 'no') {
        editProduct.value.on_sale_price = 0;
    }
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

const handleCategoryAdded = () => {
    fetchListedCategories();
};

const addProduct = async () => {
    try {
        const formData = new FormData();
        for (const key in newProduct.value) {
            if (newProduct.value[key] !== null) {
                formData.append(key, newProduct.value[key]);
            }
        }
        const response = await axios.post('/api/products', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        products.value.push(response.data.product);
        showAddProductModal.value = false;
        resetNewProduct();
        imagePreviewUrl.value = null; 
    } catch (error) {
        console.error("Error adding product:", error);
    }
};

const validateProduct = (product) => {
    if (!product.name || !product.brand || !product.price || !product.category || !product.stock || !product.sold || !product.status || !product.expDate || !product.seniorPWD_discountable || !product.description) {
        return false;
    }
    return true;
};

const updateProduct = async () => {
    try {
        const formData = new FormData();

        
        for (const key in editProduct.value) {
            if (key !== 'image' && editProduct.value[key] !== null) {
                formData.append(key, editProduct.value[key]);
            }
        }

        
        if (editProduct.value.image instanceof File) {
            formData.append('image', editProduct.value.image);
        }

        const response = await axios.post(`/api/products/${editProduct.value.id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-HTTP-Method-Override': 'PUT',
            }
        });

        
        const index = products.value.findIndex(product => product.id === editProduct.value.id);
        products.value[index] = response.data.product;
        showEditProductModal.value = false;
        resetEditProduct();
    } catch (error) {
        if (error.response && error.response.data) {
            console.error("Validation errors:", error.response.data);
        } else {
            console.error("Error updating product:", error);
        }
    }
};

const deleteProduct = async (id) => {
    if (confirm("Are you sure you want to delete this product?")) {
        try {
            await axios.delete(`/api/products/${id}`);
            products.value = products.value.filter(product => product.id !== id);
        } catch (error) {
            console.error("Error deleting product:", error);
        }
    }
};

const editProductDetails = (product) => {
    editProduct.value = { ...product };

    editImagePreviewUrl.value = product.image ? `/storage/${product.image}` : null;

    
    const fileInput = document.getElementById('edit_image');
    if (fileInput) {
        fileInput.value = ''; 
    }

    showEditProductModal.value = true;
};

const cancelAddProduct = () => {
    showAddProductModal.value = false;
    resetNewProduct();
    imagePreviewUrl.value = null;
};

const resetNewProduct = () => {
    newProduct.value = {
        name: '',
        brand: '',
        price: 0,
        category: '',
        stock: 0,
        sold: 0,
        status: '',
        expDate: '',
        image: null,
        seniorPWD_discountable: 'no',
        description: '',
        on_sale: 'no',
        on_sale_price: 0,
        featured: 'false'
    };
};

const resetEditProduct = () => {
    editProduct.value = {
        id: null,
        name: '',
        brand: '',
        price: 0,
        category: '',
        stock: 0,
        sold: 0,
        status: '',
        expDate: '',
        image: null,
        seniorPWD_discountable: null,
        description: '',
        on_sale: null,
        on_sale_price: 0,
        featured: 'false'
    };
    editImagePreviewUrl.value = null; 
    const fileInput = document.getElementById('edit_image');
    if (fileInput) {
        fileInput.value = ''; 
    }
};

const filteredProducts = computed(() => {
    if (!searchQuery.value) {
        return products.value;
    }
    return products.value.filter(product => {
        const searchTerm = searchQuery.value.toLowerCase();
        return (
            product.name.toLowerCase().includes(searchTerm) ||
            product.category.toLowerCase().includes(searchTerm) ||
            product.status.toLowerCase().includes(searchTerm) || 
            product.brand.toLowerCase().includes(searchTerm)
        );
    });
});

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        newProduct.value.image = file;
        imagePreviewUrl.value = URL.createObjectURL(file); 
    }
};

const handleEditImageUpload = (event) => {
    const file = event.target.files[0];
    if (file && ['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
        editProduct.value.image = file;
        editImagePreviewUrl.value = URL.createObjectURL(file); 
    } else {
        alert('Please upload a valid image file (jpg, jpeg, png).');
        editProduct.value.image = null; 

    }
};



const updateDiscountable = async (productId, discountableStatus) => {
    try {
        // PUT request para maupdate yung discountable sa front-end
        await axios.put(`/api/products/${productId}/discountable`, {
            seniorPWD_discountable: discountableStatus
        });

        console.log(`Updated product ${productId} with discountable status: ${discountableStatus}`);
    } catch (error) {
        console.error("Error updating discountable status:", error);

        // kahit wala, ibabalik lang yung state ng button kapag failed
        const product = products.value.find(product => product.id === productId);
        if (product) {
            product.seniorPWD_discountable = discountableStatus === 'yes' ? 'no' : 'yes'; 
        }
    }
};
const sortOrder = ref('asc'); // Default sort order

function sortByName() {
    // Toggle the sort order
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    products.value.sort((a, b) => {
        if (sortOrder.value === 'asc') {
            return a.name.localeCompare(b.name);
        } else {
            return b.name.localeCompare(a.name);
        }
    });
}

const expDateSortOrder = ref('asc'); // Default sort order for Exp. Date

function sortByExpDate() {
    // Toggle the sort order for Exp. Date
    expDateSortOrder.value = expDateSortOrder.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current expDate sort order
    products.value.sort((a, b) => {
        const dateA = new Date(a.expDate);
        const dateB = new Date(b.expDate);

        if (expDateSortOrder.value === 'asc') {
            return dateA - dateB; // Sort ascending by date (earliest first)
        } else {
            return dateB - dateA; // Sort descending by date (latest first)
        }
    });
}


fetchProducts();
fetchListedCategories();
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-5">
            <div class="max-w-auto mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold">List of Products</h2>
                            <div class="relative w-64">
                                <font-awesome-icon
                                    :icon="['fas', 'magnifying-glass']"
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search products..."
                                    class="pl-10 pr-4 py-2 border rounded-md dark:bg-gray-700 dark:text-gray-300 w-full h-10"
                                />
                            </div>
                        </div>
                        <div class="overflow-auto" style="max-height: 430px;">
                            <table class="min-w-full bg-white dark:bg-gray-800 text-xs">
                                <thead>
                                <tr>
                                    <th class="px-2 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">ID No.</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Image</th>

                                    <th 
                                        class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer"
                                        @click="sortByName">
                                        <div class="flex items-center space-x-1">
                                            <font-awesome-icon 
                                                :icon="['fas', 'angle-down']"
                                                :class="sortOrder === 'asc' ? 'rotate-180' : 'rotate-0'"
                                                class="ml-2 transition-transform duration-300 ease-in-out" 
                                            /> 
                                            <span>Name</span>
                                        </div>
                                    </th>

                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle">Brand</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Price (PHP)</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Category</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Stock</th>
                                    <th class="px-2 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Sold</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Status</th>

                                    <th 
                                        class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle cursor-pointer whitespace-nowrap"
                                        @click="sortByExpDate">
                                        <div class="flex items-center space-x-1">
                                            <font-awesome-icon 
                                                :icon="['fas', 'angle-down']" 
                                                :class="expDateSortOrder === 'asc' ? 'rotate-180' : 'rotate-0'"
                                                class="ml-2 transition-transform duration-300 ease-in-out" 
                                            />
                                            <span>Exp. Date</span>
                                        </div>
                                    </th>

                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Senior/PWD Discountable</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="filteredProducts.length === 0">
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700" colspan="12">No products available.</td>
                                </tr>
                                <tr v-for="product in filteredProducts" :key="product.id">

                                    <td class="px-2 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.id }}</td>

                                    <td class="px-2 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.id }}</td>

                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-center">
                                            <img :src="'/storage/' + product.image" alt="Product Image" class="w-12 h-12 object-cover"/>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-left align-middle">{{ product.name }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-left align-middle">{{ product.brand }}</td>

                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.price }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.category }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.stock }}</td>

                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.price }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.category }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.stock }}</td>
                                    <td class="px-2 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.sold }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center">
                                            <span :class="{
                                                'bg-red-500 text-white py-1 px-3 rounded-full': product.status === 'Unlisted',
                                                'bg-green-500 text-white py-1 px-3 rounded-full': product.status === 'Listed',
                                                'bg-gray-500 text-white py-1 px-3 rounded-full': product.status === 'Out of Stock',
                                            }">{{ product.status }}</span>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.expDate }}</td>

                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 space-x-1">
                                        <div class="flex items-center"></div>
                                    </td>

                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 whitespace-nowrap">
                                        <div class="flex items-left justify-center">

                                            <label class="switch">
                                                <input type="checkbox" v-model="product.seniorPWD_discountable" true-value="yes" false-value="no" @change="updateDiscountable(product.id, product.seniorPWD_discountable)" />
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="ml-3 text-sm text-gray-700">{{ product.seniorPWD_discountable === 'yes' ? 'Yes' : 'No' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">

                                        <div class="flex space-x-4"></div>

                                        <div class="flex items-center justify-center space-x-4">

                                            <button @click="editProductDetails(product)" class="bg-yellow-500 text-white py-2 px-3 rounded-full">
                                                <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                            </button>
                                            <button @click="deleteProduct(product.id)" class="bg-red-500 text-white py-2 px-3 rounded-full">
                                                <font-awesome-icon :icon="['fas', 'trash-can']" size="sm" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-4 mr-5 space-x-4">
                    <button @click="showAddProductModal = true" class="bg-blue-500 text-white py-2 px-4 rounded">+ Add Product</button>
                    <button @click="showCategoriesModal = true" class="bg-gray-500 text-white py-2 px-4 rounded">Categories</button>
                </div>
            </div>
        </div>

        <div v-if="showAddProductModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white p-4 rounded-lg shadow-lg w-full max-w-2xl relative">
                <h3 class="text-lg font-semibold text-center mb-3">Add a Product</h3>
                <form @submit.prevent="addProduct">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Image <span class="text-red-500">*</span></label>
                            <div class="image-upload relative w-full h-40 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 cursor-pointer">
                                <input type="file" id="image" @change="handleImageUpload" class="absolute inset-0 opacity-0 cursor-pointer" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <img v-if="imagePreviewUrl" :src="imagePreviewUrl" class="absolute inset-0 w-full h-full object-cover" />
                            </div>
                        </div>
                        <div class="col-span-2 grid grid-cols-2 gap-3">
                            <!-- Name Field -->
                            <div class="col-span-2">
                                <label for="name" class="block text-xs font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                                <input type="text" id="name" v-model="newProduct.name" class="input-field w-full text-xs p-1" required />
                            </div>
                            <!-- Price Field -->
                            <div>
                                <label for="price" class="block text-xs font-medium text-gray-700">Price (PHP) <span class="text-red-500">*</span></label>
                                <input type="number" id="price" v-model="newProduct.price" class="input-field text-xs p-1" required />
                            </div>
                            <!-- Category Field -->
                            <div>
                                <label for="category" class="block text-xs font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                                <select id="category" v-model="newProduct.category" class="input-field text-xs p-1" required>
                                    <option v-for="category in listedCategories" :key="category.id" :value="category.name">{{ category.name }}</option>
                                </select>
                            </div>
                            <!-- Stock Field -->
                            <div>
                                <label for="stock" class="block text-xs font-medium text-gray-700">Stock <span class="text-red-500">*</span></label>
                                <input type="number" id="stock" v-model="newProduct.stock" class="input-field text-xs p-1" required />
                            </div>

                            <!-- Sold Field -->
                            <div>
                                <label for="sold" class="block text-xs font-medium text-gray-700">Sold</label>
                                <input type="number" id="sold" v-model="newProduct.sold" class="input-field text-xs p-1" />
                            </div>
                            <!-- Brand Field -->
                            <div>
                                <label for="brand" class="block text-xs font-medium text-gray-700">Brand <span class="text-red-500">*</span></label>
                                <input type="text" id="brand" v-model="newProduct.brand" class="input-field text-xs p-1"/>
                            </div>

                            <!-- Status Field -->
                            <div>
                                <label for="status" class="block text-xs font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                                <select id="status" v-model="newProduct.status" class="input-field text-xs p-1" required>
                                    <option value="Listed">Listed</option>
                                    <option value="Unlisted">Unlisted</option>
                                    <option value="Out of Stock">Out of Stock</option>
                                </select>
                            </div>

                            <!-- Brand Field -->
                            <div>
                                <label for="brand" class="block text-xs font-medium text-gray-700">Brand <span class="text-red-500">*</span></label>
                                <input type="text" id="brand" v-model="newProduct.brand" class="input-field text-xs p-1"/>
                            </div>

                        </div>
                        <!-- Description -->
                        <div class="col-span-3">
                            <label for="description" class="block text-xs font-medium text-gray-700">Description: <span class="text-red-500">*</span></label>
                            <textarea id="description" v-model="newProduct.description" rows="2" class="input-field text-xs p-1" placeholder="Enter your description here (will be shown on the website)…"></textarea>
                        </div>
                        <!-- Expiry Date, Discountable, and Featured -->

                        <div class="col-span-2 grid grid-cols-3 gap-3">
                            <div>
                                <label for="expDate" class="block text-xs font-medium text-gray-700">Expiry Date <span class="text-red-500">*</span></label>
                                <input type="date" id="expDate" v-model="newProduct.expDate" class="input-field text-xs p-1"/>
                            </div>
                            <!-- Discountable Toggle -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Senior/PWD Discountable:</label>
                                <div class="flex items-center space-x-2">
                                    <label class="switch">
                                        <input type="checkbox" v-model="newProduct.seniorPWD_discountable" true-value="yes" false-value="no" />
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="text-xs text-gray-700">{{ newProduct.seniorPWD_discountable === 'yes' ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                            <!-- Featured Toggle -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Featured:</label>
                                <div class="flex items-center space-x-2">
                                    <label class="switch">
                                        <input type="checkbox" v-model="newProduct.featured" true-value="true" false-value="false"/>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="text-xs text-gray-700">{{ newProduct.featured === 'true' ? 'True' : 'False' }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- On Sale Toggle (Separate Row) -->
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-700">On Sale:</label>
                        </div>
                            

                    <div class="col-span-2 grid grid-cols-3 gap-3">
                        <!-- Expiry Date -->
                        <div>
                            <label for="expDate" class="block text-xs font-medium text-gray-700">Expiry Date <span class="text-red-500">*</span></label>
                            <input type="date" id="expDate" v-model="newProduct.expDate" class="input-field text-xs p-1"/>
                        </div>
                        <!-- Discountable Toggle -->
                        <div class="flex flex-col space-y-2">
                            <label class="flex items-center text-xs font-medium text-gray-700 whitespace-nowrap">
                                Senior/PWD Discountable:
                                <span class="relative group ml-1">
                                    <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                    <!-- Tooltip -->
                                    <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                        Enable this to apply discount for seniors/PWDs.
                                    </span>
                                </span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <label class="switch">
                                    <input type="checkbox" v-model="newProduct.seniorPWD_discountable" true-value="yes" false-value="no" />
                                    <span class="slider round"></span>
                                </label>
                                <span class="text-xs text-gray-700">{{ newProduct.seniorPWD_discountable === 'yes' ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>
                        <!-- Featured Toggle -->
                        <div class="flex flex-col space-y-2 ml-8">
                            <label class="flex items-center text-xs font-medium text-gray-700 whitespace-nowrap">
                                Featured:
                                <span class="relative group ml-1">
                                    <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                    <!-- Tooltip -->
                                    <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                        Enable this to feature the product on the website.
                                    </span>
                                </span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <label class="switch">
                                    <input type="checkbox" v-model="newProduct.featured" true-value="true" false-value="false"/>
                                    <span class="slider round"></span>
                                </label>
                                <span class="text-xs text-gray-700">{{ newProduct.featured === 'true' ? 'True' : 'False' }}</span>
                            </div>
                        </div>
                        <!-- On Sale Toggle -->
                        <div class="col-span-2">
                            <label class="flex items-center text-xs font-medium text-gray-700">
                                On Sale:
                                <span class="relative group ml-1">
                                    <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                    <!-- Tooltip -->
                                    <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                        Enable this to make the product on sale.
                                    </span>
                                </span>
                            </label>

                            <div class="flex items-center space-x-2">
                                <label class="switch">
                                    <input type="checkbox" v-model="newProduct.on_sale" true-value="yes" false-value="no"/>
                                    <span class="slider round"></span>
                                </label>
                                <span class="text-xs text-gray-700">{{ newProduct.on_sale === 'yes' ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>

                    </div>

                        <!-- On Sale Price (Visible only if 'On Sale' is selected) -->
                        <div class="col-span-3" v-if="newProduct.on_sale === 'yes'">
                            <label for="on_sale_price" class="block text-xs font-medium text-gray-700">On Sale Price (PHP):</label>
                            <input type="number" step="0.01" id="on_sale_price" v-model="newProduct.on_sale_price" class="input-field text-xs p-1"/>
                        </div>
                        <!-- Action Buttons -->
                        <div class="col-span-3 flex justify-end mt-3 space-x-2">

                            <button @click="showAddProductModal = false" class="button-cancel text-xs">Cancel</button>

                            <button @click="cancelAddProduct" class="button-cancel text-xs">Cancel</button>

                            <button type="submit" class="button-ok text-xs">OK</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showEditProductModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white p-4 rounded-lg shadow-lg w-full max-w-2xl relative">
                <h3 class="text-lg font-semibold text-center mb-3">Edit Product</h3>
                <form @submit.prevent="updateProduct">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Image</label>
                            <div class="image-upload relative w-full h-40 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400">
                                <input type="file" id="edit_image" @change="handleEditImageUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10"/>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-if="!editImagePreviewUrl">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <img v-if="editImagePreviewUrl" :src="editImagePreviewUrl" class="absolute inset-0 w-full h-full object-cover"/>
                            </div>
                        </div>
                        <div class="col-span-2 grid grid-cols-2 gap-3">
                            <!-- Name Field -->
                            <div class="col-span-2">
                                <label for="edit_name" class="block text-xs font-medium text-gray-700">Name *</label>
                                <input type="text" id="edit_name" v-model="editProduct.name" class="input-field w-full text-xs p-1" required />
                            </div>
                            <!-- Price Field -->
                            <div>
                                <label for="edit_price" class="block text-xs font-medium text-gray-700">Price (PHP) *</label>
                                <input type="number" id="edit_price" v-model="editProduct.price" class="input-field text-xs p-1" required />
                            </div>
                            <!-- Category Field -->
                            <div>
                                <label for="edit_category" class="block text-xs font-medium text-gray-700">Category *</label>
                                <select id="edit_category" v-model="editProduct.category" class="input-field text-xs p-1" required>
                                    <option v-for="category in listedCategories" :key="category.id" :value="category.name">{{ category.name }}</option>
                                </select>
                            </div>
                            <!-- Stock Field -->
                            <div>
                                <label for="edit_stock" class="block text-xs font-medium text-gray-700">Stock *</label>
                                <input type="number" id="edit_stock" v-model="editProduct.stock" class="input-field text-xs p-1" required />
                            </div>

                            <!-- Sold Field -->
                            <div>
                                <label for="edit_sold" class="block text-xs font-medium text-gray-700">Sold</label>
                                <input type="number" id="edit_sold" v-model="editProduct.sold" class="input-field text-xs p-1" />
                            </div>
                            <!-- Brand Field -->
                            <div>
                                <label for="edit_brand" class="block text-xs font-medium text-gray-700">Brand</label>
                                <input type="text" id="edit_brand" v-model="editProduct.brand" class="input-field text-xs p-1"/>
                            </div>

                            <!-- Status Field -->
                            <div>
                                <label for="edit_status" class="block text-xs font-medium text-gray-700">Status *</label>
                                <select id="edit_status" v-model="editProduct.status" class="input-field text-xs p-1" required>
                                    <option value="Listed">Listed</option>
                                    <option value="Unlisted">Unlisted</option>
                                    <option value="Out of Stock">Out of Stock</option>
                                </select>
                            </div>


                            <!-- Brand Field -->
                            <div>
                                <label for="edit_brand" class="block text-xs font-medium text-gray-700">Brand</label>
                                <input type="text" id="edit_brand" v-model="editProduct.brand" class="input-field text-xs p-1"/>
                            </div>

                        </div>
                        <!-- Description -->
                        <div class="col-span-3">
                            <label for="edit_description" class="block text-xs font-medium text-gray-700">Description:</label>
                            <textarea id="edit_description" v-model="editProduct.description" rows="2" class="input-field text-xs p-1" placeholder="Enter your description here (will be shown on the website)…"></textarea>
                        </div>
                        <!-- Expiry Date, Discountable, and Featured -->
                        <div class="col-span-2 grid grid-cols-3 gap-3">
                            <div>
                                <label for="edit_expDate" class="block text-xs font-medium text-gray-700">Expiry Date</label>
                                <input type="date" id="edit_expDate" v-model="editProduct.expDate" class="input-field text-xs p-1"/>
                            </div>
                            <!-- Discountable Toggle -->
                            <div>

                                <label class="block text-xs font-medium text-gray-700"> Senior/PWD Discountable:</label>

                                <label class="block text-xs font-medium text-gray-700"> Senior/PWD Discountable:
                                    <span class="relative group ml-1">
                                    <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                    <!-- Tooltip -->
                                    <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                        Enable this to apply discount for seniors/PWDs.
                                    </span>
                                </span>
                                </label>

                                <div class="flex items-center space-x-2">
                                    <label class="switch">
                                        <input type="checkbox" v-model="editProduct.seniorPWD_discountable" true-value="yes" false-value="no" />
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="text-xs text-gray-700">{{ editProduct.seniorPWD_discountable === 'yes' ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                            <!-- Featured Toggle -->
                            <div>

                                <label class="block text-xs font-medium text-gray-700">Featured:</label>

                                <label class="block text-xs font-medium text-gray-700">Featured:
                                    <span class="relative group ml-1">
                                    <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                    <!-- Tooltip -->
                                    <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                        Enable this to feature the product on the website.
                                    </span>
                                </span>
                                </label>

                                <div class="flex items-center space-x-2">
                                    <label class="switch">
                                        <input type="checkbox" v-model="editProduct.featured" true-value="true" false-value="false"/>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="text-xs text-gray-700">{{ editProduct.featured === 'true' ? 'True' : 'False' }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- On Sale Toggle (Separate Row) -->
                        <div class="col-span-2">

                            <label class="block text-xs font-medium text-gray-700">On Sale:</label>

                            <label class="block text-xs font-medium text-gray-700">On Sale:
                                <span class="relative group ml-1">
                                    <font-awesome-icon icon="fa-solid fa-circle-info" class="text-gray-500 cursor-pointer" />
                                    <!-- Tooltip -->
                                    <span class="absolute left-full top-1/2 transform -translate-y-1/2 ml-4 bg-gray-800 text-white text-xs rounded-md px-3 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity duration-300 whitespace-nowrap z-10 pointer-events-none">
                                        Enable this to make the product on sale.
                                    </span>
                                </span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <label class="switch">
                                    <input type="checkbox" v-model="editProduct.on_sale" true-value="yes" false-value="no"/>
                                    <span class="slider round"></span>
                                </label>
                                <span class="text-xs text-gray-700">{{ editProduct.on_sale === 'yes' ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>
                        <!-- On Sale Price (Visible only if 'On Sale' is selected) -->
                        <div class="col-span-3" v-if="editProduct.on_sale === 'yes'">
                            <label for="edit_on_sale_price" class="block text-xs font-medium text-gray-700">On Sale Price (PHP):</label>
                            <input type="number" step="0.01" id="edit_on_sale_price" v-model="editProduct.on_sale_price" class="input-field text-xs p-1"/>
                        </div>
                        <!-- Action Buttons -->
                        <div class="col-span-3 flex justify-end mt-3 space-x-2">
                            <button @click="showEditProductModal = false" class="button-cancel text-xs">Cancel</button>
                            <button type="submit" class="button-ok text-xs">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <CategoriesModal v-if="showCategoriesModal" @close-categories-modal="showCategoriesModal = false" />
        <CategoriesModal v-if="showCategoriesModal" 
                        @close-categories-modal="showCategoriesModal = false" 
                        @category-added="fetchListedCategories" />
    </AuthenticatedLayout>
</template>


<style scoped>
.input-field {
    width: 100%;
    padding: 0.4rem 0.6rem;
    border: 1px solid #CBD5E0;
    border-radius: 0.375rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.input-field:focus {
    border-color: #3182CE;
    box-shadow: 0 0 0 1px #3182CE;
    outline: none;
}

.button-cancel {
    background-color: #DC2626;
    color: #FFFFFF;
    padding: 0.4rem 1rem;
    border-radius: 0.375rem;
    font-weight: 600;
    transition: background-color 0.2s ease-in-out;
}

.button-cancel:hover {
    background-color: #B91C1C;
}

.button-ok {
    background-color: #059669;
    color: #FFFFFF;
    padding: 0.4rem 1rem;
    border-radius: 0.375rem;
    font-weight: 600;
    transition: background-color 0.2s ease-in-out;
}

.button-ok:hover {
    background-color: #047857;
}



.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 12px;
    width: 12px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #4CAF50;
}

input:checked + .slider:before {
    transform: translateX(20px);
}

input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0; /* Make the input invisible */
    cursor: pointer; 
    z-index: 10; /* Bring the input above other elements */
}

.image-upload {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
}



.image-upload {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
}
.image-upload svg,
.image-upload img {
    position: absolute;
    z-index: 1; /* Ensure the preview image is below the file input */
}

</style>