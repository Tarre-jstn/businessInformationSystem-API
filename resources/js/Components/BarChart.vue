<template>
    <div>
      <BarChart :chart-data="chartData" :options="options" />
    </div>
  </template>
  
  <script>
import { defineComponent } from 'vue';
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js';

// Register required Chart.js components
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

export default defineComponent({
  components: {
    BarChart: defineComponent({
      extends: Bar,  // Extend the Bar chart
      props: ['chartData', 'options'],  // Pass chartData and options as props
      mounted() {
        this.renderChart(this.chartData, this.options);  // Render the chart
      },
    }),
  },
  data() {
    return {
      chartData: {
        labels: ['January', 'February', 'March', 'April', 'May'],
        datasets: [
          {
            label: 'Sales',
            backgroundColor: '#42A5F5',
            data: [40, 20, 60, 30, 80],
          },
        ],
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Horizontal Bar Chart Example',
          },
        },
      },
    };
  },
});
  </script>
  
  <style scoped>
  /* Optional: You can add custom styles here */
  div {
  width: 100%;
  height: 400px;
}

  </style>
