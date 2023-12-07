<script setup>
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Chart from 'chart.js/auto';
import moment from 'moment';

const props = defineProps({
    yearlyReport: Object,
    yearlyReport2: Object,
    currentYear: Number,
    currentMonth: String,
    totalPatient: Number
})

function formattedDate(date){
    return moment(date).format('MMMM   D, YYYY');
}

// const currentMonthNumber = ref(props.currentMonth);
// const currentMonthName = ref(getMonthName(currentMonthNumber.value));

  const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
const chartRef = ref(null);

onMounted(() => {
    if (chartRef.value) {
        const ctx = chartRef.value.getContext('2d');

        if (!props.yearlyReport && !props.yearlyReport2 || !Array.isArray(props.yearlyReport) && !Array.isArray(props.yearlyReport2)) {
            return;
        }

        // const chartData = props.yearlyReport;
        const chartData = props.yearlyReport;
        const chartData2 = props.yearlyReport2;

        new Chart(ctx, {
            type: 'bar',
            data: {
               labels: props.yearlyReport.map(({ month, year }) => `${monthNames[month - 1]}-${year}`),

                // datasets: [
                //     {
                //         label: 'Highblood',
                //         data: props.yearlyReport.map(({ total_highblood }) => total_highblood),
                //         backgroundColor: 'rgba(39,150,248,0.68)', // Blue for 'Pending'
                //         borderColor: 'rgba(23, 68, 88, 1)',
                //         borderWidth: 1,
                //     },

                // ],
                 datasets: [
              {
                label: 'Highblood',
                data: chartData.map(({ total_highblood }) => total_highblood),
                backgroundColor: 'rgba(39,150,248,0.68)', // Blue for 'Highblood'
                borderColor: 'rgba(23, 68, 88, 1)',
                borderWidth: 1,
              },
              {
                label: 'Diabetes', // Label for the second dataset
                data: chartData2.map(({ total_diabetes }) => total_diabetes), // Replace with the actual property name in your structure
                backgroundColor: 'rgba(255, 99, 132, 0.7)', // Red for the second dataset
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
              },
            ],
            },
            options: {
                scales: {
                    x: {
                        stacked: true, // Enable stacking for x-axis (grouped bar chart)
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Number', // Your desired label for the y-axis
                        },
                    },
                },
            },
        });
    }
});
</script>

<template>
    <Sidebar>
        <div class="w-full px-5 py-4">
            <div class="h-12">
                <div class=" overflow-hidden ">

                    <div class="mb-7">
                        <div class="flex justify-center mb-2">
                            <div>
                                <img src="/images/mdclogo.png" alt="Logo" class="w-[70px] h-[70px] rounded-full object-cover">
                            </div>
                            <div class="text-sm text-center mt-5">
                                <h3 class="font-bold text-slate-700">Mater Dei College Clinic</h3>
                                <h3 class="font-bold text-slate-700">Cabulijan, Tubigon, Bohol</h3>
                                <h3 class="font-bold text-slate-700 text-center">Medical Report Chart for {{ currentYear }}</h3>
                            </div>
                        </div>

                    </div>
                    <h1>Number of Patient: {{totalPatient}}</h1>
                    <div v-if="yearlyReport">
                        <canvas ref="chartRef" width="200" height="80"></canvas>


                      </div>
                      <div v-else>
                        <p class="text-center text-xl font-bold text-gray-900 py-6">No Medical Report Chart yet.</p>
                        </div>


                </div>
            </div>
        </div>
    </Sidebar>
</template>
