<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FinanceCategoriesModal  from "@/Components/FinanceCategories.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const finances = ref([]);
const financeListedCategories = ref([]);
const showAddFinanceModal = ref(false);
const showFinanceCategoriesModal = ref(false);
const showEditFinanceModal = ref(false);
const searchQuery = ref('');

const newFinance = ref({
    id: null,
    description: '',
    date: '',
    category: '',
    amount: 0, 
});

const editFinance = ref({
    id: null,
    description: '',
    date: '',
    category: '',
    amount: 0, 

});

// watch(() => editFinance.value.on_sale, (newValue) => {
//     if (newValue === 'no') {
//         editFinance.value.on_sale_price = 0;
//     }
// });

const fetchFinances = async () => {
    try {
        const response = await axios.get('/api/finance');
        finances.value = response.data.finances;
        financeListedCategories.value = response.data.financeCategories;
    } catch (error) {
        console.error("Error fetching finance and finance Categories:", error);
    }
};

// const fetchFinanceCategories = async () => {
//     try {
//         const response = await axios.get('/api/finance');
//         finances.value = response.data;
//         financeListedCategories.value = response.data;
//     } catch (error) {
//         console.error("Error fetching finance and finance Categories:", error);
//     }
// };

const handleFinanceCategoryAdded = () => {
    fetchFinances();
};

const addFinance = async () => {
    try {
        const formData = new FormData();
        for (const key in newFinance.value) {
            if (newFinance.value[key] !== null) {
                formData.append(key, newFinance.value[key]);
            }
        }
        const response = await axios.post('/api/finance', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        finances.value.push(response.data.finance);
        showAddFinanceModal.value = false;
        resetNewFinance();
    } catch (error) {
        console.error("Error adding finance:", error);
    }
};

// const validateFinance = (finance) => {
//     if (!product.name || !product.brand || !product.price || !product.category || !product.stock || !product.sold || !product.status || !product.expDate || !product.seniorPWD_discountable || !product.description) {
//         return false;
//     }
//     return true;
// };

const updateFinance = async () => {
    try {
        const formData = new FormData();

        
        // for (const key in editFinance.value) {
        //     if (editFinance.value[key] !== null) {
        //         formData.append(key, editFinance.value[key]);
        //     }
        // }

        for (const key in editFinance.value) {
            if (editFinance.value[key] !== null) {
                console.log(`Appending ${key}:`, editFinance.value[key]);
                formData.append(key, editFinance.value[key]);
            }
        }

        const response = await axios.post(`/api/finance/${editFinance.value.id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        
        const index = finances.value.findIndex(finance => finance.id === editFinance.value.id);
        finances.value[index] = response.data.finance;
        showEditFinanceModal.value = false;
        resetEditFinance();
    } catch (error) {
        if (error.response && error.response.data) {
            console.error("Validation errors:", error.response.data);
        } else {
            console.error("Error updating finance:", error);
        }
    }
};

const deleteFinance = async (id) => {
    if (confirm("Are you sure you want to delete this finance?")) {
        try {
            await axios.delete(`/api/finance/${id}`);
            finances.value = finances.value.filter(finance => finance.id !== id);
        } catch (error) {
            console.error("Error deleting finance:", error);
        }
    }
};

const editFinanceDetails = (finance) => {
    editFinance.value = { ...finance };
    showEditFinanceModal.value = true;
};

const cancelAddFinance = () => {
    showAddFinanceModal.value = false;
    resetNewFinance();
    imagePreviewUrl.value = null;
};

const resetNewFinance = () => {
    newFinance.value = {
        description: '',
        date: '',
        category: '',
        amount: 0, 
    };
};

const resetEditFinance = () => {
    editFinance.value = {
        description: '',
        date: '',
        category: '',
        amount: 0, 
    };
};

const filteredFinances = computed(() => {
    if (!searchQuery.value) {
        return finances.value;
    }
    return finances.value.filter(finance => {
        const searchTerm = searchQuery.value.toLowerCase();
        return (
            finance.description.toLowerCase().includes(searchTerm) ||
            finance.date.toLowerCase().includes(searchTerm) ||
            finance.category.toLowerCase().includes(searchTerm) || 
            finance.amount.toLowerCase().includes(searchTerm)
        );
    });
});





const sortOrder = ref('asc'); // Default sort order

function sortByDescription() {
    // Toggle the sort order
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    finances.value.sort((a, b) => {
        if (sortOrder.value === 'asc') {
            return a.name.localeCompare(b.name);
        } else {
            return b.name.localeCompare(a.name);
        }
    });
}

const DateSortOrder = ref('asc'); // Default sort order for Exp. Date

function sortByDate() {
    // Toggle the sort order for DateSortOrder. Date
    DateSortOrder.value = DateSortOrder.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current expDate sort order
    finances.value.sort((a, b) => {
        const dateA = new Date(a.Date);
        const dateB = new Date(b.Date);

        if (DateSortOrder.value === 'asc') {
            return dateA - dateB; // Sort ascending by date (earliest first)
        } else {
            return dateB - dateA; // Sort descending by date (latest first)
        }
    });
}

const isDateFiltered = ref(false);
const startDate = ref('');
const endDate = ref('');
const financesByDate = ref([]);

const fetchFinancesByDate = async () => {
    try {
        const response = await axios.get('/api/finance_by_date', {
            params: {
                start_date: startDate.value,
                end_date: endDate.value
            }
        });
        financesByDate.value = response.data;
        isDateFiltered.value = true; // Set flag to true after fetching by date
    } catch (error) {
        console.error("Error fetching finances by date:", error);
    }
};

const clearFetchFinancesByDate = async () => {

    startDate.value = '';
    endDate.value = '';
    financesByDate.value = '';
    isDateFiltered.value = false;
};
watch([startDate, endDate], async ([newStartDate, newEndDate]) => {
    if (newStartDate && newEndDate) {
        await fetchFinancesByDate();
    } else {
        // Reset financesByDate and isDateFiltered if dates are cleared
        financesByDate.value = [];
        isDateFiltered.value = false;
    }
});

fetchFinances();
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-5">

            <div class="max-w-auto mx-auto sm:px-6 lg:px-8">
                
                
                <div>
                    <input type="date" v-model="startDate" />
                    <input type="date" v-model="endDate" />
                    <button @click="clearFetchFinancesByDate">Clear</button>

                </div>
            

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold">Finances</h2>

                            <div class="flex">
                                <div>
                                    <div class="relative w-64">
                                    <font-awesome-icon
                                        :icon="['fas', 'magnifying-glass']"
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                    />
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search finances..."
                                        class="pl-10 pr-4 py-2 border rounded-md dark:bg-gray-700 dark:text-gray-300 w-full h-10"
                                    />
                                    </div>
                                </div>


                                
                                <div>OR SEARCH BY DATE
                                    <input type="date" v-model="startDate" />
                                    <input type="date" v-model="endDate" />
                                    <button @click="clearFetchFinancesByDate">Clear</button>

                                </div>
                            </div>

                        </div>
                        <div class="overflow-auto" style="max-height: 430px;">
                            <table class="min-w-full bg-white dark:bg-gray-800 text-xs">
                                <thead>
                                <tr>
                                     <th 
                                        class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-center align-middle cursor-pointer whitespace-nowrap"
                                        @click="sortByDate">
                                        <div class="items-center text-center space-x-1">
                                            <font-awesome-icon 
                                                :icon="['fas', 'angle-down']" 
                                                :class="DateSortOrder === 'asc' ? 'rotate-180' : 'rotate-0'"
                                                class="ml-2 transition-transform duration-300 ease-in-out" 
                                            />
                                            <span>Date</span>
                                        </div>
                                    </th>
                                    <th 
                                        class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-center align-middle cursor-pointer"
                                        @click="sortByDescription">
                                        <div class="items-center text-center space-x-1">
                                            <font-awesome-icon 
                                                :icon="['fas', 'angle-down']"
                                                :class="sortOrder === 'asc' ? 'rotate-180' : 'rotate-0'"
                                                class="ml-2 transition-transform duration-300 ease-in-out" 
                                            /> 
                                            <span>Decription</span>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 ">Category</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Amount</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="filteredFinances.length === 0 || (financesByDate.length === 0 && isDateFiltered)">
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700" colspan="5">No finances available.</td>
                                </tr>

                                

                                <tr v-for="finance in financesByDate" :key="finance.id" v-if="isDateFiltered" >
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.date }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.description }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.category }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.amount }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-center space-x-4">
                                            <button @click="editFinanceDetails(finance)" class="bg-yellow-500 text-white py-2 px-3 rounded-full">
                                                <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                            </button>
                                            <button @click="deleteFinance(finance.id)" class="bg-red-500 text-white py-2 px-3 rounded-full">
                                                <font-awesome-icon :icon="['fas', 'trash-can']" size="sm" />
                                            </button>
                                        </div>
                                    </td>
                                </tr
                                >
                                <tr v-for="finance in filteredFinances" :key="finance.id" v-if="!isDateFiltered">
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.date }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.description }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.category }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.amount }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-center space-x-4">
                                            <button @click="editFinanceDetails(finance)" class="bg-yellow-500 text-white py-2 px-3 rounded-full">
                                                <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                            </button>
                                            <button @click="deleteFinance(finance.id)" class="bg-red-500 text-white py-2 px-3 rounded-full">
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
                    <button @click="showAddFinanceModal = true" class="bg-blue-500 text-white py-2 px-4 rounded">+ Add Finance</button>
                    <button @click="showFinanceCategoriesModal = true" class="bg-gray-500 text-white py-2 px-4 rounded">Categories</button>
                </div>
            </div>



            
        </div>


        <div v-if="showAddFinanceModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white p-4 rounded-lg shadow-lg w-full max-w-2xl relative">
                <h3 class="text-lg font-semibold text-center mb-3">Add a Finance</h3>
                <form @submit.prevent="addFinance">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2 grid grid-cols-2 gap-3">
                            <!-- Name Field -->
                            <div class="col-span-2">
                                <label for="name" class="block text-xs font-medium text-gray-700">Date<span class="text-red-500">*</span></label>
                                <input type="date" id="name" v-model="newFinance.date" class="input-field w-full text-xs p-1" required />
                            </div>
                            <!-- Price Field -->
                            <div>
                                <label for="price" class="block text-xs font-medium text-gray-700">Description<span class="text-red-500">*</span></label>
                                <input type="text" id="price" v-model="newFinance.description" class="input-field text-xs p-1" required />
                            </div>
                            <!-- Category Field -->
                            <div>
                                <label for="category" class="block text-xs font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                                <select id="category" v-model="newFinance.category" class="input-field text-xs p-1" required>
                                    <option v-for="category in financeListedCategories" :key="category.id" :value="category.category">{{ category.category }}</option>
                                </select>
                            </div>
                            <!-- Stock Field -->
                            <div>
                                <label for="stock" class="block text-xs font-medium text-gray-700">Amount<span class="text-red-500">*</span></label>
                                <input type="number" id="stock" v-model="newFinance.amount" class="input-field text-xs p-1" required />
                            </div>
                            <!-- Status Field -->
                            
                        <!-- Action Buttons -->
                            <div class="col-span-3 flex justify-end mt-3 space-x-2">
                                <button @click="cancelAddFinance" class="button-cancel text-xs">Cancel</button>
                                <button type="submit" class="button-ok text-xs">OK</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showEditFinanceModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white p-4 rounded-lg shadow-lg w-full max-w-2xl relative">
                <h3 class="text-lg font-semibold text-center mb-3">Edit Finance</h3>

                <form @submit.prevent="updateFinance">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2 grid grid-cols-2 gap-3">
                            <!-- Name Field -->
                            <div class="col-span-2">
                                <label for="name" class="block text-xs font-medium text-gray-700">Date<span class="text-red-500">*</span></label>
                                <input type="date" id="name" v-model="editFinance.date" class="input-field w-full text-xs p-1" required />
                            </div>
                            <!-- Price Field -->
                            <div>
                                <label for="price" class="block text-xs font-medium text-gray-700">Description<span class="text-red-500">*</span></label>
                                <input type="text" id="price" v-model="editFinance.description" class="input-field text-xs p-1" required />
                            </div>
                            <!-- Category Field -->
                            <div>
                                <label for="edit_category" class="block text-xs font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                                <select id="edit_category" v-model="editFinance.category" class="input-field text-xs p-1" required>
                                    <option v-for="category in financeListedCategories" :key="category.id" :value="category.category">{{ category.category }}</option>
                                </select>
                            </div>
                            <!-- Stock Field -->
                            <div>
                                <label for="stock" class="block text-xs font-medium text-gray-700">Amount<span class="text-red-500">*</span></label>
                                <input type="number" id="stock" v-model="editFinance.amount" class="input-field text-xs p-1" required />
                            </div>
                        </div>
                                                <!-- Action Buttons -->
                        <div class="col-span-3 flex justify-end mt-3 space-x-2">
                            <div class="col-span-3 flex justify-end mt-3 space-x-2">
                                <button @click="showEditFinanceModal = false" class="button-cancel text-xs">Cancel</button>
                                <button type="submit" class="button-ok text-xs">Save</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <FinanceCategoriesModal v-if="showFinanceCategoriesModal" 
                        @close-categories-modal="showFinanceCategoriesModal = false" 
                        @category-added="fetchFinances" />
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

.image-upload svg,
.image-upload img {
    position: absolute;
    z-index: 1; /* Ensure the preview image is below the file input */
}

</style>