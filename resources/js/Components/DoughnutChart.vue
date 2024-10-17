<script>
import { defineComponent } from 'vue';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js';

// Register components globally
ChartJS.register(Title, Tooltip, Legend, ArcElement);

export default defineComponent({
    name: 'DoughnutChart',
    components: { 
        Doughnut
    },
    data() {
        return {
            chartData: {
                labels: ['January', 'February', 'March'],
                datasets: [
                    {
                        label: 'Data One',
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                        data: [40, 20, 12]
                    },
                ],
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false
            }
        }
    }
});
</script>

<template>
    <div class="chart-container m-4">
        <Doughnut :data="chartData" :options="chartOptions"/>
    </div>
</template>

<style>
.chart-container {
    width: 16vw;
    height: 32vh;
    border: 1px solid #000000;
}
</style>
