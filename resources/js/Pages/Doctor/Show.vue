<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import Breadcrumb from '@/Components/Breadcrumbs.vue'
    import { ref } from 'vue';
    import { Link, Head } from '@inertiajs/vue3';
    import moment from 'moment';

    const props = defineProps({
        doctor: Object,
        services: Array,
        serviceCount:Number,
        recentAppointments: Array
    })

    const getStatusText = (status) => {
      return status === 1 ? 'Active' : 'Inactive';
    };

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

    const crumbs = [
        {
            name: 'Dashboard',
            url: '/dashboard',
            active: false,
        },
        {
            name: 'List of Doctors',
            url: '/doctor',
            active: false,
        },
        {
            name:  props.doctor.user.firstname + ' '+ props.doctor.user.lastname  ,
            url: null,
            active: true,
        },
    ]
</script>

<template>
    <Sidebar>
        <Head title="Doctor Details"/>
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Doctor Details</h2>
        </template> -->
        <div class="mt-5">
            <Breadcrumb :crumbs="crumbs"  />
        </div>



            <div class="container mx-auto my-5 p-5">
        <div class="md:flex no-wrap md:-mx-2 ">
            <!-- Left Side -->
            <div class="w-full md:w-3/12 md:mx-2">
                <!-- Profile Card -->
                <div class="bg-white p-3 border-t-4 border-green-400">

                    <h1 class="text-gray-900 font-bold text-xl leading-8 my-1">{{ doctor.user.firstname }} {{ doctor.user.lastname }}</h1>

                    <h3 class="text-gray-600 font-lg text-semibold leading-6"><span class="font-bold">Specialization</span>: {{ doctor.specialization }}</h3>
                    <div class="flex mt-2 font-bold">Services
                    </div>
                    <div class="py-4">
                                <div class=" inline-block mr-2" v-for="service in doctor.services" >
                                    <div class="flex  pr-2 h-full items-center">
                                        <svg class="text-blue-500 w-6 h-6 mr-1"  width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z"/>
                                            <circle cx="12" cy="12" r="9" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                        <p class="title-font font-medium">    {{ service.name }}</p>
                                    </div>
                                </div>


                            </div>

                    <!-- <ul
                        class="bg-gray-100 text-gray-600 hover:text-gray-700 hover:shadow py-2 px-3 mt-3 divide-y rounded shadow-sm">
                        <li class="flex items-center justify-center py-3">
                            <span>Status</span>
                            <span class="ml-auto"><span
                                :class="{

                                    'bg-red-600': doctor.user.status == 0,
                                    'bg-green-600 ': doctor.user.status == 1,
                                }"
                                class=" py-1 px-2 rounded text-white text-sm">{{doctor.user.status ? 'Active' : 'Inactive' }}</span></span>

                        </li>

                    </ul> -->
                    <div class="flex justify-center mt-3 ml-2 mr-4" v-if="doctor.user.status===1">
                        <Link class="border border-red-500 bg-red-500 text-white rounded-md px-3 py-2 m-2 transition duration-500 ease select-none hover:bg-red-600 focus:outline-none focus:shadow-outline" as="button" method="POST"  :href="'/doctor/deactivate/'+ doctor.id" >Deactivate Doctor</Link>

                    </div>
                    <div class="flex justify-center mt-3 ml-2 mr-4" v-if="doctor.user.status===0">
                        <Link class="border border-green-500 bg-green-500 text-white rounded-md px-3 py-2 m-2 transition duration-500 ease select-none hover:bg-green-600 focus:outline-none focus:shadow-outline" as="button" method="POST" :href="'/doctor/activate/'+ doctor.id" >Activate Doctor</Link>
                    </div>
                </div>

            </div>
            <!-- Right Side -->
            <div class="w-full md:w-9/12 mx-2 h-64">
                <!-- Profile tab -->
                <!-- About Section -->
                <div class="bg-white   shadow-sm rounded-sm">
                    <!-- <div class="flex items-center py-3 px-4 space-x-2 font-semibold text-gray-900 leading-8">
                        <span clas="text-green-500">
                            <i class="fa-regular fa-user fa-lg"></i>
                        </span>
                        <span class="tracking-wide text-xl">Other Details</span>
                    </div> -->


                    <div class="flex flex-col">
        <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 p-6 md:p-10 rounded-full">
            <div class="flex flex-col">
                <div class="flex-1">
                    <h5 class="text-m font-bold">Gender</h5>
                    <p class="mt-1 text-l text-gray-600">{{ doctor.user.gender }}</p>
                </div>
                <div class="border-b-2 font-bold border-gray-100"></div>
            </div>

            <div class="flex flex-col">
                <div class="flex-1">
                    <h5 class="text-m font-bold">Birthday</h5>
                    <p class="mt-2 text-lg text-gray-600">{{ formattedDate(doctor.user.dob)}}</p>
                </div>
                <div class="border-b-2 font-bold border-gray-100"></div>
            </div>

            <div class="flex flex-col">
                <div class="flex-1">
                    <h5 class="text-m font-bold">Contact No</h5>
                    <p class="mt-2 text-lg text-gray-600">{{ doctor.user.contact_no }}</p>
                </div>
                <div class="border-b-2 font-bold border-gray-100"></div>
            </div>

            <div class="flex flex-col">
                <div class="flex-1">
                    <h5 class="mb-1 text-m font-bold">Status</h5>
                    <p class="mt-2 text-sm uppercase px-2 py-1 w-20 text-center rounded-full border font-bold"
                        :class="{
                            'bg-green-200 text-green-600': doctor.user.status == 1,
                            'bg-red-200 text-red-600': doctor.user.status == 0,
                        }">{{doctor.user.status ? 'Active' : 'Inactive' }}
                    </p>

                </div>
                <div class="border-b-2 font-bold border-gray-100"></div>
            </div>
        </div>
</div>


                </div>
                <!-- End of about section -->

                <div class="my-4 mt-5"></div>


                <div class="bg-white p-3 shadow-sm rounded-sm">

                    <div class="grid grid-cols-1">
                        <div>
                            <div class="flex items-center space-x-2 font-semibold text-gray-900 leading-8 mb-3">
                                <span clas="text-green-500">
                                    <svg class="h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                <span class="tracking-wide">Recent Appointment</span>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table class="min-w-max w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">

                                    <th class="py-3 px-6 text-center">Patient</th>
                                    <th class="py-3 px-6 text-center">Date & Time</th>
                                    <th class="py-3 px-6 text-center">Medical Service</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                    <th class="py-3 px-6 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light" >
                                <!-- <tr v-if="doctors.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-center">
                                        No doctor found.
                                    </td>
                                </tr> -->
                                <tr  class="border-b border-gray-200 hover:bg-gray-100" v-for="app in recentAppointments" :key="app.id">
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ app.patient.firstname }} {{ app.patient.lastname }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ formattedDate (app.date) }} at {{ formatTimeToAMPM (app.time) }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ app.service.name }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center" >
                                            <p class="font-medium">

                                                <p class="text-xs uppercase px-2 py-1 rounded-full border font-bold" :class="{
                                            'bg-blue-200 text-blue-600': app.status == 'Pending',
                                            'bg-red-200 text-red-600':app.status == 'Cancelled',
                                            'bg-green-200 text-green-600': app.status =='Accepted',
                                        }">{{ app.status }}</p>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center">
                                            <div class="flex item-center justify-center">
                                                <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                    <Link :href="'/appointment/show/'+ app.id" title="Show Details">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </Link>
                                                </div>
                                            </div>

                                        </div>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                        </div>

                    </div>
                    <!-- End of Experience and education grid -->
                </div>
                <!-- End of profile tab -->
            </div>
        </div>
    </div>

    </Sidebar>

</template>
