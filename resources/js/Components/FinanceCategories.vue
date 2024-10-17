<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const emit = defineEmits(['close-categories-modal']);

const financeCategories = ref([]);

onMounted(async () => {
    await fetchFinanceCategories();
});


const fetchFinanceCategories = async () => {
    try {
        const response = await axios.get('/api/finance');
        financeCategories.value = response.data.financeCategories;
    } catch (error) {
        console.error("Error fetching finance and finance Categories:", error);
    }
};


const addFinanceCategory = async () => {
    const categoryName = prompt("Enter category name:");
    if (!categoryName) return;

    const newCategory = {
        category: categoryName,
    };

    try {
        const response = await axios.post('/api/finance_category', newCategory);
        const savedCategory = response.data;
            financeCategories.value.push(savedCategory);
        
        fetchFinanceCategories();
        emit('category-added');
    } catch (error) {
        console.error("Error adding category:", error);
    }
};

const removeFinanceCategory = async (category) => {
    try {
            financeCategories.value = financeCategories.value.filter(c => c.id !== category.id);
            await axios.delete(`/api/finance_category/${category.id}`, category);

    } catch (error) {
        console.error("Error removing category:", error);
    }
};


const closeModal = () => {
    emit('close-categories-modal');
};

const saveFinanceCategories = async () => {
        closeModal();
};
</script>

<template>


    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-lg">
            <h2 class="text-2xl mb-4">Categories</h2>
            <div class="mb-4">
                <h3 class="text-lg font-semibold">Listed Categories</h3>
                <div class="border p-4 rounded-lg mb-2">
                    <div v-for="category in financeCategories" :key="category.id" class="inline-flex items-center bg-gray-200 text-gray-800 py-1 px-3 rounded-full mr-2 mb-2">
                        {{ category.category }}
                        <!-- Conditionally render the remove button -->
                        <button 
                            v-if="category.category !== 'income' && category.category !== 'expense'"
                            @click="removeFinanceCategory(category)"
                            class="ml-2 text-red-500">x</button>
                    </div>
                </div>
                <button @click="addFinanceCategory" class="bg-blue-500 text-white py-2 px-4 rounded">+ Add Category</button>
                <p class="text-sm text-gray-500">*Listed Categories will BE SHOWN on the website</p>
            </div>
            <div class="flex justify-end">
                <button @click="closeModal" class="bg-red-500 text-white py-2 px-4 rounded mr-2">Cancel</button>
                <button @click="saveFinanceCategories" class="bg-green-500 text-white py-2 px-4 rounded">OK</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fixed {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
