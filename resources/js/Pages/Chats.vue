<template>
    <Head title="Home" />
    <AuthenticatedLayout>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">Welcome to the Chats Page!</div>
          </div>
        </div>
      </div>
      <div class="chart-container">
        <BarChart />
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import BarChart from '@/Components/BarChart.vue';
  import { Head } from '@inertiajs/vue3';
  
  export default {
    name: 'App',
    components: { BarChart, AuthenticatedLayout }
  }
  </script>