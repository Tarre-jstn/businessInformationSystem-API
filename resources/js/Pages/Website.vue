<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import { route } from 'ziggy-js';

function goToEditWebsite1(){
    Inertia.visit(route('editWebsite1'));
}

function goToPreviewHomePage(){
        Inertia.visit(route('preview_homepage'));
}

Chart.register(...registerables);

const analyticsData = ref([]);
const totalPageViews = ref(0);
const totalVisitors = ref(0);
const bounceRate = ref(0);
const averageEngagementTime = ref(0);
const totalSessions = ref(0);
const retentionRate = ref(0);

// Dropdown selected period
const selectedPeriod = ref(7); // Default to 7 days

// Watch for changes in the selected period and fetch data accordingly
watch(selectedPeriod, () => {
    fetchAnalyticsData(selectedPeriod.value);
});

let visitorsViewsChart = null;
let retentionRateChart = null;

async function fetchAnalyticsData(days = 7) {
    try {
        const response = await axios.get(`/analytics?days=${days}`);
        analyticsData.value = response.data.analyticsData;
        totalPageViews.value = response.data.totalPageViews;
        totalVisitors.value = response.data.totalVisitors;
        bounceRate.value = response.data.bounceRate;
        averageEngagementTime.value = response.data.averageEngagementTimePerActiveUser;
        totalSessions.value = response.data.totalSessions;
        retentionRate.value = response.data.retentionRates;

        // Update the charts with the new data
        if (days === 14) {
            updateVisitorsViewsChartFor14Days();
        } else {
            updateVisitorsViewsChart();
        }
        updateRetentionRateChart();
    } catch (error) {
        console.error('Error fetching analytics data:', error);
    }
}

onMounted(() => {
    fetchAnalyticsData(selectedPeriod.value);
});

function formatDate(date) {
    return date.toLocaleDateString(); // Format the date
}

function updateVisitorsViewsChart() {
    const ctx = document.getElementById('visitorsViewsChart').getContext('2d');

    if (visitorsViewsChart) visitorsViewsChart.destroy(); // Destroy existing chart before creating new one

    // Adjust the labels based on the selected period (e.g., 7 days or 14 days)
    const labels = Array.from({ length: selectedPeriod.value }, (_, i) => {
        const date = new Date();
        date.setDate(date.getDate() - (selectedPeriod.value - 1 - i)); // Get the correct date
        return formatDate(date); // Format the date for display
    });

    visitorsViewsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels, // Use formatted dates
            datasets: [
                {
                    label: 'Visitors',
                    backgroundColor: '#4D82E9',
                    data: analyticsData.value.map((item) => item.activeUsers), // Use dynamic data
                },
                {
                    label: 'Views',
                    backgroundColor: '#2A4F97',
                    data: analyticsData.value.map((item) => item.screenPageViews), // Use dynamic data
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    beginAtZero: true,
                    stacked: true,
                },
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                    },
                },
            },
        },
    });
}

function updateRetentionRateChart() {
    const ctx = document.getElementById('retentionRateChart').getContext('2d');

    if (retentionRateChart) retentionRateChart.destroy(); // Destroy existing chart before creating new one

    const labels = Array.from({ length: retentionRate.value.length }, (_, i) => {
        const date = new Date();
        date.setDate(date.getDate() - (retentionRate.value.length - 1 - i)); // Get the correct date
        return formatDate(date); // Format the date for display
    });

    retentionRateChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels, // Use formatted dates
            datasets: [
                {
                    label: 'Retention Rate',
                    backgroundColor: '#E0DFFD',
                    borderColor: '#27378F',
                    fill: true,
                    data: retentionRate.value, // Use dynamic data
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });
}
</script>



<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <!-- Top stats section -->
        <div class="mt-4 px-8 border-b border-black">
            <div class="grid grid-cols-6 gap-4 text-center">
                <div>
                    <h2 class="text-3xl font-extrabold">{{ totalPageViews }}</h2>
                    <p class="font-bold">Views</p>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold">{{ totalSessions }}</h2>
                    <p class="font-bold">Visits</p>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold">{{ totalVisitors }}</h2>
                    <p class="font-bold">Visitors</p>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold">{{ bounceRate }}%</h2>
                    <p class="font-bold">Bounce Rate</p>
                </div>
                <div>
                    <h2 class="text-3xl font-bold">{{ averageEngagementTime }}</h2>
                    <p class="font-bold">Avg Visit Time</p>
                </div>
                <div>
                    <select v-model="selectedPeriod" class="ml-4 p-2 pr-8 border rounded">
                        <option value="7">7 Days</option>
                        <option value="14">14 Days</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Visitors & Views and Retention Rate Charts side by side -->
        <div class="flex flex-wrap justify-center mt-10 px-8 space-x-10"> 
            <!-- Visitors & Views Chart -->
            <div class="custom-chart-width p-4 border border-black rounded-lg"> <!-- Add padding, border, and rounded corners -->
                <canvas id="visitorsViewsChart" class="w-full" style="height: 300px;"></canvas> <!-- Set height using inline style -->
            </div>

            <!-- Retention Rate Chart -->
            <div class="custom-chart-width p-4 border border-black rounded-lg"> <!-- Add padding, border, and rounded corners -->
                <canvas id="retentionRateChart" class="w-full" style="height: 300px;"></canvas> <!-- Set height using inline style -->
            </div>
        </div>



        <!-- Footer buttons -->
        <div class="fixed bottom-0 left-0 right-0 flex justify-center mb-10">
            <button class="px-6 py-4 bg-black text-white mr-2" @click="goToPreviewHomePage">Preview Website</button>
            <button class="px-6 py-4 bg-black text-white" @click="goToEditWebsite1">Edit Website</button>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-chart-width {
        width: 580px;
    }
</style>