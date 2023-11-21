<script setup>
import Sidebar from '@/Layouts/Sidebar.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from '@vue/reactivity';
import moment from 'moment';
import { watch } from 'vue';
import { ref } from 'vue';

const openTab = ref(1);
const sort = ref('latest');

const props = defineProps({
        appointments: Array,
        // doctorAppointments: Array, // Doctor-specific appointments data received from Laravel
        // patientAppointments: Array,
        user:Object,
        acceptedApp: Array,
        cancelApp : Array
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

const sortedAppointments = computed(() => {
    let appointments = [...props.appointments];

    // Sort the appointments based on the selected sort order
    if (sort.value === 'latest') {
    appointments.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    } else if (sort.value === 'oldest') {
    appointments.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    }

    return appointments;
});


</script>

<template>
    <Sidebar>
        <div class="sm:px-6 w-full">

        <div class="px-4 md:px-10 py-4 md:py-7">
            <div class="flex items-center justify-between">
                <p tabindex="0" class="focus:outline-none text-base sm:text-lg md:text-xl lg:text-2xl font-bold leading-normal text-gray-800">Appointment Lists</p>
                <div class="py-3 px-4 flex items-center text-sm font-medium leading-none text-gray-600 cursor-pointer rounded">
                    <p>Sort By:</p>
                    <select aria-label="select" model="sort" @change="updateSort" class="focus:text-indigo-600 focus:outline-none bg-transparent ml-1">
                        <option value="latest" class="text-sm text-indigo-800">Latest</option>
                        <option value="oldest" class="text-sm text-indigo-800">Oldest</option>
                        <!-- <option class="text-sm text-indigo-800">Latest</option> -->
                    </select>
                </div>
            </div>
        </div>
            <div class="bg-white py-4 md:py-7 px-4 md:px-8 xl:px-10">
                <div class="sm:flex items-center justify-between">
                    <div class="flex items-center">
                        <button @click="openTab = 1" :class="{ 'bg-indigo-50 focus:bg-indigo-50 focus:ring-indigo-800 font-bold': openTab === 1 }" class="rounded-full focus:outline-none focus:ring-2  " href=" javascript:void(0)">
                            <div class="py-2 px-8  text-gray-600 rounded-full" :class="{ 'text-indigo-700 font-bold': openTab === 1 }">
                                <p>All</p>
                            </div>
                        </button>
                        <button @click="openTab = 2" :class="{ 'bg-indigo-50 focus:bg-indigo-50 focus:ring-indigo-800 font-bold ': openTab === 2 }" class="rounded-full focus:outline-none focus:ring-2 ml-4 sm:ml-8" href="javascript:void(0)">
                            <div class="py-2 px-8 text-gray-600 rounded-full" :class="{ 'text-indigo-700 font-bold': openTab === 2 }">
                                <p>Accepted</p>
                            </div>
                        </button >
                        <button @click="openTab = 3" :class="{ 'bg-indigo-50 focus:bg-indigo-50 focus:ring-indigo-800 font-bold ': openTab === 3 }" class="rounded-full focus:outline-none focus:ring-2 focus:bg-indigo-50 focus:ring-indigo-800 ml-4 sm:ml-8" href="javascript:void(0)">
                            <div class="py-2 px-8 text-gray-600  rounded-full " :class="{ 'text-indigo-700 font-bold': openTab === 3 }">
                                <p>Cancelled</p>
                            </div>
                        </button>
                    </div>
                    <Link :href="route('appointment.createForPatient')" class="focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 mt-4 sm:mt-0 inline-flex items-start justify-start px-6 py-3 bg-indigo-700 hover:bg-indigo-600 focus:outline-none rounded">
                        <p class="text-sm font-medium leading-none text-white">Add Appointment</p>
                    </Link>
                </div>
                <div  v-show="openTab === 1" class="mt-7 overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr>
                                <td>Patient Name</td>
                                <td class="text-left">Date</td>
                                <td class="">Doctor</td>
                                <td class="text-center">Service</td>
                                <td class="text-center">Status</td>
                                <td class="text-center">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="appointments.length === 0">
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-lg  text-gray-800 text-center">
                                        No appointment found.
                                </td>
                            </tr>
                            <tr tabindex="0" class="focus:outline-none h-16 border border-gray-100 rounded" v-for="app in appointments" :key="app.id">
                                <td>
                                    <div class="flex items-center pl-5">
                                        <p class="text-base font-medium leading-none text-gray-700 mr-2">{{ app.patient.firstname }} {{ app.patient.lastname }}</p>

                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center">
                                        <p class="text-base font-medium leading-none text-gray-700 mr-2">
                                            {{ formattedDate(app.date)}}  at {{ formatTimeToAMPM(app.time) }}
                                        </p>

                                    </div>
                                </td>
                                <td class="">
                                        <p class="text-sm leading-none text-gray-600 ml-2">{{ app.doctor.user.firstname }} {{ app.doctor.user.lastname }}</p>
                                </td>
                                <td class="text-center">
                                        <p class="text-sm leading-none text-gray-600 ml-2">{{ app.service.name }}</p>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center">

                                        <p class="text-xs uppercase px-2 py-1 rounded-full border font-bold" :class="{
                                            'bg-blue-200 text-blue-600': app.status == 'Pending',
                                            'bg-red-200 text-red-600':app.status == 'Cancelled',
                                            'bg-green-200 text-green-600': app.status =='Accepted',
                                        }">{{ app.status }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="relative px-5 pt-2 text-center">
                                        <button class="focus:ring-2 rounded-md focus:outline-none" @click="dropdownFunction(this)"   role="button" aria-label="option">
                                            <svg class="dropbtn" onclick="dropdownFunction(this)" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M4.16667 10.8332C4.62691 10.8332 5 10.4601 5 9.99984C5 9.5396 4.62691 9.1665 4.16667 9.1665C3.70643 9.1665 3.33334 9.5396 3.33334 9.99984C3.33334 10.4601 3.70643 10.8332 4.16667 10.8332Z" stroke="#9CA3AF" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M10 10.8332C10.4602 10.8332 10.8333 10.4601 10.8333 9.99984C10.8333 9.5396 10.4602 9.1665 10 9.1665C9.53976 9.1665 9.16666 9.5396 9.16666 9.99984C9.16666 10.4601 9.53976 10.8332 10 10.8332Z" stroke="#9CA3AF" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M15.8333 10.8332C16.2936 10.8332 16.6667 10.4601 16.6667 9.99984C16.6667 9.5396 16.2936 9.1665 15.8333 9.1665C15.3731 9.1665 15 9.5396 15 9.99984C15 10.4601 15.3731 10.8332 15.8333 10.8332Z" stroke="#9CA3AF" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                        <div class="dropdown-content bg-white shadow w-24 absolute z-30 right-0 mr-6 hidden">
                                            <div tabindex="0" class="focus:outline-none focus:text-indigo-600 text-xs w-full hover:bg-indigo-700 py-4 px-4 cursor-pointer hover:text-white">
                                                <p>Edit</p>
                                            </div>
                                            <div tabindex="0" class="focus:outline-none focus:text-indigo-600 text-xs w-full hover:bg-indigo-700 py-4 px-4 cursor-pointer hover:text-white">
                                                <p>Delete</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="h-3"></tr>

                        </tbody>
                    </table>
                </div>
                <div  v-show="openTab === 2" class="mt-7 overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr>
                                <td>Patient Name</td>
                                <td class="text-left">Date</td>
                                <td class="">Doctor</td>
                                <td class="text-center">Service</td>
                                <td class="text-center">Status</td>
                                <td class="text-center">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="acceptedApp.length === 0">
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-lg  text-gray-800 text-center">
                                        No accepted appointment found.
                                </td>
                            </tr>
                            <tr tabindex="0" class="focus:outline-none h-16 border border-gray-100 rounded" v-for="test in acceptedApp" :key="test.id">
                                <td>
                                    <div class="flex items-center pl-5">
                                        <p class="text-base font-medium leading-none text-gray-700 mr-2">{{ test.patient.firstname }} {{ test.patient.lastname }}</p>

                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center">
                                        <p class="text-base font-medium leading-none text-gray-700 mr-2">
                                            {{ formattedDate(test.date)}}  at {{ formatTimeToAMPM(test.time) }}
                                        </p>

                                    </div>
                                </td>
                                <td class="">
                                        <p class="text-sm leading-none text-gray-600 ml-2">{{ test.doctor.user.firstname }} {{ test.doctor.user.lastname }}</p>
                                </td>
                                <td class="text-center">
                                        <p class="text-sm leading-none text-gray-600 ml-2">{{ test.service.name }}</p>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center">

                                        <p class="text-xs uppercase px-2 py-1 rounded-full border font-bold" :class="{
                                            'bg-blue-200 text-blue-600': test.status == 'Pending',
                                            'bg-red-200 text-red-600':test.status == 'Cancelled',
                                            'bg-green-200 text-green-600': test.status =='Accepted',
                                        }">{{ test.status }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="relative px-5 pt-2 text-center">
                                        <button class="focus:ring-2 rounded-md focus:outline-none" @click="dropdownFunction(this)"   role="button" aria-label="option">
                                            <svg class="dropbtn" onclick="dropdownFunction(this)" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M4.16667 10.8332C4.62691 10.8332 5 10.4601 5 9.99984C5 9.5396 4.62691 9.1665 4.16667 9.1665C3.70643 9.1665 3.33334 9.5396 3.33334 9.99984C3.33334 10.4601 3.70643 10.8332 4.16667 10.8332Z" stroke="#9CA3AF" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M10 10.8332C10.4602 10.8332 10.8333 10.4601 10.8333 9.99984C10.8333 9.5396 10.4602 9.1665 10 9.1665C9.53976 9.1665 9.16666 9.5396 9.16666 9.99984C9.16666 10.4601 9.53976 10.8332 10 10.8332Z" stroke="#9CA3AF" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M15.8333 10.8332C16.2936 10.8332 16.6667 10.4601 16.6667 9.99984C16.6667 9.5396 16.2936 9.1665 15.8333 9.1665C15.3731 9.1665 15 9.5396 15 9.99984C15 10.4601 15.3731 10.8332 15.8333 10.8332Z" stroke="#9CA3AF" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                        <div class="dropdown-content bg-white shadow w-24 absolute z-30 right-0 mr-6 hidden">
                                            <div tabindex="0" class="focus:outline-none focus:text-indigo-600 text-xs w-full hover:bg-indigo-700 py-4 px-4 cursor-pointer hover:text-white">
                                                <p>Edit</p>
                                            </div>
                                            <div tabindex="0" class="focus:outline-none focus:text-indigo-600 text-xs w-full hover:bg-indigo-700 py-4 px-4 cursor-pointer hover:text-white">
                                                <p>Delete</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="h-3"></tr>

                        </tbody>
                    </table>
                </div>
                <div v-show="openTab === 3" class="mt-7 overflow-x-auto" >
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr>
                                <td>Patient Name</td>
                                <td class="text-left">Date</td>
                                <td class="">Doctor</td>
                                <td class="text-center">Service</td>
                                <td class="text-center">Status</td>
                                <td class="text-center">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="cancelApp.length === 0">
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-lg  text-gray-800 text-center">
                                        No cancelled appointment found.
                                </td>
                            </tr>
                            <tr tabindex="0" class="focus:outline-none h-16 border border-gray-100 rounded" v-for="sam in cancelApp" :key="sam.id" >
                                <td>
                                    <div class="flex items-center pl-5">
                                        <p class="text-base font-medium leading-none text-gray-700 mr-2">{{ sam.patient.firstname }} {{ sam.patient.lastname }}</p>

                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center">
                                        <p class="text-base font-medium leading-none text-gray-700 mr-2">
                                            {{ formattedDate(sam.date)}}  at {{ formatTimeToAMPM(sam.time) }}
                                        </p>

                                    </div>
                                </td>
                                <td class="">
                                        <p class="text-sm leading-none text-gray-600 ml-2">{{ sam.doctor.user.firstname }} {{ sam.doctor.user.lastname }}</p>
                                </td>
                                <td class="text-center">
                                        <p class="text-sm leading-none text-gray-600 ml-2">{{ sam.service.name }}</p>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center">

                                        <p class="text-xs uppercase px-2 py-1 rounded-full border font-bold" :class="{
                                            'bg-blue-200 text-blue-600': sam.status == 'Pending',
                                            'bg-red-200 text-red-600':sam.status == 'Cancelled',
                                            'bg-green-200 text-green-600': sam.status =='Accepted',
                                        }">{{ sam.status }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="relative px-5 pt-2 text-center">
                                        <button class="focus:ring-2 rounded-md focus:outline-none" @click="dropdownFunction(this)"   role="button" aria-label="option">
                                            <svg class="dropbtn" onclick="dropdownFunction(this)" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M4.16667 10.8332C4.62691 10.8332 5 10.4601 5 9.99984C5 9.5396 4.62691 9.1665 4.16667 9.1665C3.70643 9.1665 3.33334 9.5396 3.33334 9.99984C3.33334 10.4601 3.70643 10.8332 4.16667 10.8332Z" stroke="#9CA3AF" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M10 10.8332C10.4602 10.8332 10.8333 10.4601 10.8333 9.99984C10.8333 9.5396 10.4602 9.1665 10 9.1665C9.53976 9.1665 9.16666 9.5396 9.16666 9.99984C9.16666 10.4601 9.53976 10.8332 10 10.8332Z" stroke="#9CA3AF" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                                <path d="M15.8333 10.8332C16.2936 10.8332 16.6667 10.4601 16.6667 9.99984C16.6667 9.5396 16.2936 9.1665 15.8333 9.1665C15.3731 9.1665 15 9.5396 15 9.99984C15 10.4601 15.3731 10.8332 15.8333 10.8332Z" stroke="#9CA3AF" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                        <div class="dropdown-content bg-white shadow w-24 absolute z-30 right-0 mr-6 hidden">
                                            <div tabindex="0" class="focus:outline-none focus:text-indigo-600 text-xs w-full hover:bg-indigo-700 py-4 px-4 cursor-pointer hover:text-white">
                                                <p>Edit</p>
                                            </div>
                                            <div tabindex="0" class="focus:outline-none focus:text-indigo-600 text-xs w-full hover:bg-indigo-700 py-4 px-4 cursor-pointer hover:text-white">
                                                <p>Delete</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="h-3"></tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Sidebar>


</template>
<style scoped>
.checkbox:checked + .check-icon {
    display: flex;
  }
  </style>
