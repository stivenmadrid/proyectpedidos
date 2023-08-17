<template>
    <div class="chart-container">
      <div class="chart-left">
        <Bar
          :id="'chart-left-1'"
          :options="chartOptions"
          :data="chartData[0]"
        />
      </div>
      <div class="chart-right">
        <Bar
          :id="'chart-right-1'"
          :options="chartOptions"
          :data="chartData[1]"
        />
      </div>
    </div>
    <div class="chart-container">
      <div class="chart-left">
        <Bar
          :id="'chart-left-2'"
          :options="chartOptions"
          :data="chartData[2]"
        />
      </div>
      <div class="chart-right">
        <Bar
          :id="'chart-right-2'"
          :options="chartOptions"
          :data="chartData[3]"
        />
      </div>
    </div>
  </template>
  
  <script>
  import { Bar } from 'vue-chartjs'
  import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
  
  ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
  
  export default {
    name: 'MultipleDualBarCharts',
    components: { Bar },
    data() {
      return {
        chartData: [
          {
            labels: ['January', 'February', 'March'],
            datasets: [
              {
                label: 'Data Set 1',
                backgroundColor: 'red', // Cambiar el color aquí
                data: [40, 20, 12]
              }
            ]
          },
          {
            labels: ['April', 'May', 'June'],
            datasets: [
              {
                label: 'Data Set 2',
                backgroundColor: 'blue', // Cambiar el color aquí
                data: [30, 25, 18]
              }
            ]
          },
          {
            labels: ['July', 'August', 'September'],
            datasets: [
              {
                label: 'Data Set 3',
                backgroundColor: 'green', // Cambiar el color aquí
                data: [15, 12, 9]
              }
            ]
          },
          {
            labels: ['October', 'November', 'December'],
            datasets: [
              {
                label: 'Data Set 4',
                backgroundColor: 'purple', // Cambiar el color aquí
                data: [28, 32, 20]
              }
            ]
          }
        ],
        chartOptions: {
          responsive: true
          // otras opciones de gráfico comunes
        }
      }
    }
  }
  </script>
  
  <style>
  .chart-container {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px; /* Espaciado entre los conjuntos de gráficos */
  }
  
  .chart-left,
  .chart-right {
    width: 45%; /* Ajusta este valor según tus preferencias */
  }
  </style>
  