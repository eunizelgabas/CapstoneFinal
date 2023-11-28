<script setup>
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Chart from 'chart.js/auto';
import moment from 'moment';

const props = defineProps({
    monthlyAppointment: Object,
    currentYear: Number,
    currentMonth: String
})

function formattedDate(date){
    return moment(date).format('MMMM   D, YYYY');
}

const currentMonthNumber = ref(props.currentMonth);
const currentMonthName = ref(getMonthName(currentMonthNumber.value));

function getMonthName(monthNumber) {
  const monthNames = [
    'January', 'February', 'March', 'April',
    'May', 'June', 'July', 'August',
    'September', 'October', 'November', 'December'
  ];

  // Adjusting the monthNumber to be zero-based
  const adjustedMonthNumber = monthNumber - 1;

  return monthNames[adjustedMonthNumber];
}

const chartRef = ref(null);
onMounted(() => {
    if (chartRef.value) {
        const ctx = chartRef.value.getContext('2d');

        if (!props.monthlyAppointment || !Array.isArray(props.monthlyAppointment)) {
            return;
        }

        const chartData = props.monthlyAppointment;

        // Arrays to store total counts for each status
        const totalPending = [];
        const totalCancelled = [];
        const totalAccepted = [];

        chartData.forEach(entry => {
            totalPending.push(entry.status === 'Pending' ? entry.total_appointments : 0);
            totalCancelled.push(entry.status === 'Cancelled' ? entry.total_appointments : 0);
            totalAccepted.push(entry.status === 'Accepted' ? entry.total_appointments : 0);
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(entry => {
                    const formattedDate = new Date(`${entry.month}-01 ${new Date().getFullYear()}`).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                    });

                    return formattedDate;
                }),
                datasets: [
                    {
                        label: 'Pending',
                        data: totalPending,
                        backgroundColor: 'rgba(39,150,248,0.68)', // Blue for 'Pending'
                        borderColor: 'rgba(23, 68, 88, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Cancelled',
                        data: totalCancelled,
                        backgroundColor: 'rgba(255, 0, 0, 0.5)', // Red for 'Cancelled'
                        borderColor: 'rgba(23, 68, 88, 1)',

                        borderWidth: 1,
                    },
                    {
                        label: 'Accepted',
                        data: totalAccepted,
                        backgroundColor: 'rgba(0, 255, 0, 0.5)', // Green for 'Accepted'
                        borderColor: 'rgba(23, 68, 88, 1)',
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
                            text: 'Number of Appointments', // Your desired label for the y-axis
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
                <div class=" overflow-hidden shadow-sm sm:rounded-lg">

                    <div class="mb-7">
                        <div class="flex justify-center mb-2">
                            <div>
                                <img src="/images/mdclogo.png" alt="Logo" class="w-[70px] h-[70px] rounded-full object-cover">
                            </div>
                            <div class="text-sm text-center mt-5">
                                <h3 class="font-bold text-slate-700">Mater Dei College Clinic</h3>
                                <h3 class="font-bold text-slate-700">Cabulijan, Tubigon, Bohol</h3>
                                <h3 class="font-bold text-slate-700 text-center">Total Appointment Chart for {{ currentMonthName }} {{ currentYear }}</h3>
                            </div>
                        </div>

                    </div>
                    <div v-if="monthlyAppointment">
                        <canvas ref="chartRef" width="200" height="80"></canvas>


                      </div>
                      <div v-else>
                        <p class="text-center text-xl font-bold text-gray-900 py-6">No Appointment Chart yet.</p>
                        </div>


                </div>
            </div>
        </div>
    </Sidebar>
</template>
