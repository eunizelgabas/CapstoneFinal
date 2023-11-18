<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head, Link } from '@inertiajs/vue3';
import moment from 'moment';

let props = defineProps({
    pendingApp: Number,
    appointment: Object
})

    function formattedDate(date){
        return moment(date).format('MMMM   D, YYYY');
    }

    const formatTimeToAMPM = (time) => {
        const [hours, minutes] = time.split(':').map(Number);
        const period = hours >= 12 ? 'PM' : 'AM';
        const formattedHours = hours % 12 || 12;
        const formattedMinutes = minutes.toString().padStart(2, '0');
        return `${formattedHours}:${formattedMinutes} ${period}`;
    };
</script>

<template>
    <Head title="Dashboard" />

    <Sidebar>
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template> -->

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col space-y-8">
                    <!-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 px-4 xl:p-0 gap-4 xl:gap-6" >
                        <div class="bg-gray-100 p-6 rounded-xl border border-gray-50">
                            <div class="flex justify-center">
                                <div class="flex flex-col">
                                    <h2 class="mt-1 text-xl text-blue-500  text-center font-bold">{{ pendingApp }}</h2>
                                    <h1 class="text-lg font-bold text-gray-600 tracking-wide">Pending Appointments</h1>
                                </div>

                            </div>
                        </div>
                        <div class="bg-gray-100 p-6 rounded-xl border border-gray-50">
                            <div class="flex justify-center items-start">
                                <div class="flex flex-col">
                                    <h2 class="mt-1 text-xl text-center text-red-500 font-bold">0</h2>
                                    <h1 class="text-lg font-bold text-gray-600 tracking-wide">Cancelled Appointments</h1>

                                </div>

                            </div>
                        </div>
                        <div class="bg-gray-100 p-6 rounded-xl border border-gray-50">
                            <div class="flex justify-center items-start">
                                <div class="flex flex-col">
                                    <h2 class="mt-1 text-xl text-center text-green-500 font-bold">0</h2>
                                    <h1 class="text-lg font-bold text-gray-600 tracking-wide">Doctors</h1>


                                </div>

                            </div>
                        </div>
                        <div class="bg-gray-100 p-6 rounded-xl border border-gray-50">
                            <div class="flex justify-center items-start">
                                <div class="flex flex-col">
                                    <h2 class="mt-1 text-xl text-center text-yellow-500 font-bold">0</h2>
                                    <h1 class="text-lg font-bold text-gray-600 tracking-wide">Services</h1>


                                </div>

                            </div>
                        </div>

                    </div> -->
                    <div class="grid grid-cols-1 gap-4 px-4 mt-3 sm:grid-cols-4 sm:px-8">
    <div class="flex items-center bg-white border rounded-sm overflow-hidden shadow">
        <div class="p-4 bg-green-400"><svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg></div>
        <div class="px-4 text-gray-700">
            <h3 class="text-sm tracking-wider">Total Patient</h3>
            <p class="text-3xl">12,768</p>
        </div>
    </div>
    <div class="flex items-center bg-white border rounded-sm overflow-hidden shadow">
        <div class="p-4 bg-blue-400"><svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
                </path>
            </svg></div>
        <div class="px-4 text-gray-700">
            <h3 class="text-sm tracking-wider">Total Appointment</h3>
            <p class="text-3xl">39,265</p>
        </div>
    </div>
    <div class="flex items-center bg-white border rounded-sm overflow-hidden shadow">
        <div class="p-4 bg-indigo-400"><svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                </path>
            </svg></div>
        <div class="px-4 text-gray-700">
            <h3 class="text-sm tracking-wider">IDK</h3>
            <p class="text-3xl">142,334</p>
        </div>
    </div>
    <div class="flex items-center bg-white border rounded-sm overflow-hidden shadow">
        <div class="p-4 bg-red-400"><svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                </path>
            </svg></div>
        <div class="px-4 text-gray-700">
            <h3 class="text-sm tracking-wider">SAME</h3>
            <p class="text-3xl">34.12%</p>
        </div>
    </div>
</div>

                    <div class="grid grid-cols-1 md:grid-cols-5 items-start px-4 xl:p-0 gap-y-4 md:gap-6">


                        <div class="col-span-3 bg-gray-100 p-6 rounded-xl border border-gray-50 flex flex-col space-y-6">
                            <div class="flex justify-between items-center">
                                <h2 class="text-sm text-gray-600 font-bold tracking-wide">Latest Appointments</h2>
                                <!-- <Link :href="route('appointment.index')"
                                    class="px-4 py-2 text-xs bg-blue-100 text-blue-500 rounded uppercase tracking-wider font-semibold hover:bg-blue-400 hover:text-white">
                                    More
                                </Link> -->
                            </div>

                            <table class="min-w-max w-full table-auto">
                                    <thead>
                                        <tr class=" border-b border-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                            <th class="py-3 px-6 text-center">Patient </th>
                                            <th class="py-3 px-6 text-center">Doctor</th>
                                            <th class="py-3 px-6 text-center">Service</th>
                                            <th class="py-3 px-6 text-center">Date</th>
                                            <th class="py-3 px-6 text-center">Time</th>

                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 text-sm font-light" v-for="app in appointment" :key="app.id" >

                                        <tr  class=" ">

                                            <td class="py-3 px-6 text-center">
                                                <div class="flex items-center justify-center">
                                                    <p class="font-medium">{{ app.user.firstname }} {{ app.user.lastname }}</p>
                                                </div>
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex items-center justify-center">
                                                    <p class="font-medium">{{ app.doctor.user.firstname }} {{ app.doctor.user.lastname }}</p>
                                                </div>
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex items-center justify-center">
                                                    <p class="font-medium">{{ app.service.name }}</p>
                                                </div>
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex items-center justify-center">
                                                    <p class="font-medium">{{ formattedDate(app.date)}}</p>
                                                </div>
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <div class="flex items-center justify-center">
                                                    <p class="font-medium">
                                                        {{ formatTimeToAMPM(app.time) }}
                                                    </p>
                                                </div>
                                            </td>


                                        </tr>
                                    </tbody>
                                </table>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </Sidebar>
</template>
