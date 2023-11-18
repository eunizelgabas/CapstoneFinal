<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import { Link, Head } from '@inertiajs/vue3';
    import moment from 'moment';
    import { ref } from 'vue';
    import { computed } from 'vue';

    const props = defineProps({
        patient: Object,
        patientAppointments: Object,
        doctor: Object,
        form: Object
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

    const patientFirstName = ref(props.patient.firstname);
    const patientLastName = ref(props.patient.lastname);

    const patientInitials = computed(() => {
      const firstNameInitial = patientFirstName.value.charAt(0).toUpperCase();
      const lastNameInitial = patientLastName.value.charAt(0).toUpperCase();
      return `${firstNameInitial} ${lastNameInitial}`;
    });

    const openTab = ref(1);


</script>

<template>
    <Sidebar>
        <Head title="Patients"/>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Patients</h2>
        </template>

        <div class="flex justify-center">
            <div class="lg:25 sm:w-25 md:w-25 mt-4 mx-2 mb-4 bg-white rounded-lg shadow-md">
                <div class="p-4 text-center text-gray-500">
                    <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 rounded-full p-4"
                        :class="{
                            'bg-blue-200 text-blue-600': patient.sex == 'Male',
                            'bg-red-200 text-red-600': patient.sex == 'Female',
                        }">
                        <div class="w-12 h-12 flex justify-center items-center rounded-full uppercase font-bold text-3xl">{{ patientInitials }}</div>
                    </div>
                    <div class="text-sm mt-2">
                        <h1 class="font-bold text-xl text-gray-900">{{ patient.firstname }} {{ patient.lastname }}</h1>
                        <p>{{ patient.email }}</p>
                    </div>
                    <div class="text-center mt-3">
                        <div class="flex items-center justify-between">
                            <div class="text-center ml-8">
                                <p class="font-bold text-zinc-700">345</p>
                                <p class="text-sm font-semibold text-zinc-700">Appointments</p>
                            </div>
                            <div class="border-l border-bold border-gray-950 h-6 mx-6 "></div>
                            <div class="text-center mr-8">
                                <p class="font-bold text-zinc-700">200k</p>
                                <p class="text-sm font-semibold text-zinc-700">Medical</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 m-4 w-full sm:w-90 md:w-90 bg-white rounded-lg shadow-md">
                <div class="flex flex-col">
                    <div class="grid gap-8 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 lg:gap-12 p-6 md:p-10 ">
                        <div class="flex flex-col">
                            <div class="flex-1">
                                <h5 class="mb-1 text-m font-bold">Gender</h5>
                                <p class="mt-1 text-lg text-gray-600">{{ patient.sex }}</p>
                            </div>
                            <div class="mt-2 border-b-4 border-indigo-100"></div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex-1">
                                <h5 class="mb-1 text-m font-bold">Birthday</h5>
                                <p class="mt-2 text-lg text-gray-600">{{ formattedDate(patient.dob)}}</p>
                            </div>
                            <div class="border-b-4 border-indigo-100"></div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex-1">
                                <h5 class="mb-1 text-m font-bold">Contact No</h5>
                                <p class="mt-2 text-lg text-gray-600">{{ patient.contact_no }}</p>
                            </div>
                            <div class="mt-3 border-b-4 border-indigo-100"></div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex-1">
                                <h5 class="mb-1 text-m font-bold">Emergency Contact No</h5>
                                <p class="mt-2 text-lg text-gray-600">{{ patient.emergency_contact }}</p>
                            </div>
                            <div class="border-b-4 border-indigo-100"></div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex-1">
                                <h5 class="mb-1 text-m font-bold">Address</h5>
                                <p class="mt-2 text-lg text-gray-600">{{ patient.address }}</p>
                            </div>
                            <div class="border-b-4 border-indigo-100"></div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex-1">
                                <h5 class="mb-1 text-m font-bold">Status</h5>
                                <p class="mt-2 text-sm uppercase px-2 py-1 w-20 text-center rounded-full border font-bold"
                                    :class="{
                                        'bg-green-200 text-green-600': patient.status == 1,
                                        'bg-red-200 text-red-600': patient.status == 0,
                                    }">{{patient.status ? 'Active' : 'Inactive' }}
                                </p>
                            </div>
                            <div class="mt-3 border-b-4 border-indigo-100"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class=" px-2" >
            <div class="w-full grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-4">
                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 2xl:col-span-2">
                    <div ref="tabs" class="p-2">
                        <div class="max-w-full mx-auto">
                            <div class="mb-4 flex space-x-4 p-2 bg-gray-200 rounded-lg shadow-md">
                                <button @click="openTab = 1" :class="{ 'bg-white text-blue-500': openTab === 1 }" class="flex-1 py-2 px-4 rounded-md focus:outline-none focus:shadow-outline-blue transition-all duration-300">
                                    Appointment History
                                </button>
                                <button @click="openTab = 2" :class="{ 'bg-white text-blue-500': openTab === 2 }" class="flex-1 py-2 px-4 rounded-md focus:outline-none focus:shadow-outline-blue transition-all duration-300">
                                    Medical Records
                                </button>
                                <!-- <button @click="openTab = 3" :class="{ 'bg-white text-blue-500': openTab === 3 }" class="flex-1 py-2 px-4 rounded-md focus:outline-none focus:shadow-outline-blue transition-all duration-300">
                                Weanings
                                </button> -->
                            </div>

                            <div v-show="openTab === 1" class="transition-all duration-300 bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-600">
                                <h2 class="text-2xl font-semibold mb-2 text-blue-600">Appointment History</h2>
                                <div class="max-w-2xl mx-auto">
                                    <div class="flex gap-3 bg-white border border-gray-300 rounded-xl overflow-hidden items-center justify-start" v-for="appointment in patientAppointments" :key="appointment.id">
                                        <div class="flex flex-col gap-2 px-4 py-2">

                                            <!-- <p class="text-xl font-bold">Post title</p>

                                            <p class="text-gray-500">
                                                Description of your post/article,
                                                Description of your post/article,
                                            </p> -->
                                            <div class="text-center mt-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="text-center ml-8">
                                                        <p class="font-bold text-xl text-zinc-700">{{ formattedDate(appointment.date)}}</p>
                                                        <p class="text-sm font-semibold text-zinc-700">{{ formatTimeToAMPM(appointment.time) }}
                                                        <span class="text-xs px-2 py-1 rounded-full " :class="{
                                                                'bg-blue-200 text-blue-600': appointment.status == 'Pending',
                                                                'bg-red-200 text-red-600':appointment.status == 'Cancelled',
                                                                'bg-green-200 text-green-600': appointment.status =='Accepted',
                                                            }">
                                                            {{ appointment.status }}
                                                        </span></p>
                                                    </div>
                                                    <div class="border-l border-bold border-gray-500 h-8 mx-6 "></div>
                                                    <div class="text-center mr-8">
                                                        <p class="text-sm text-zinc-700">Medical Service</p>
                                                        <p class="text-xl font-semibold text-zinc-700"> {{ appointment.service.name }}</p>
                                                    </div>
                                                    <div class="border-l border-bold border-gray-500 h-8 mx-6 "></div>
                                                    <div class="text-center mr-8">
                                                        <p class=" text-sm text-zinc-700">Doctor</p>
                                                        <p class="text-xl font-semibold text-zinc-700"> {{ appointment.doctor.user.firstname }}  {{ appointment.doctor.user.lastname }}</p>
                                                    </div>
                                                    <div class="text-center mr-8">
                                                        <p class=" text-sm text-zinc-700">Doctor</p>
                                                        <p class="text-xl font-semibold text-zinc-700"> {{ appointment.doctor.user.firstname }}  {{ appointment.doctor.user.lastname }}</p>
                                                    </div>
                                                    <!-- <div class="text-center mr-8">
                                                        <p class=" text-sm text-zinc-700">Status</p>
                                                        <p class="text-xl font-semibold text-zinc-700"> {{ appointment.doctor.user.firstname }}  {{ appointment.doctor.user.lastname }}</p>
                                                    </div> -->
                                                </div>
                                            </div>

                                            <span class="flex items-center justify-start text-gray-500">

                                            </span>

                                        </div>

                                    </div>

                                </div>
                                <!-- <Pagination :links="breedings.links" class="mt-6 flex justify-center"/> -->

                                <!-- <template v-else>
                                    <p class="text-center font-bold text-gray-900 py-6">No breedings found</p>
                                </template> -->
                            </div>

                            <div v-show="openTab === 2" class="transition-all duration-300 bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-600">
                                <h2 class="text-2xl font-semibold mb-2 text-blue-600">Labors</h2>
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                            <th class="py-2 px-2">Breeding ID</th>
                                            <th class="py-2 px-2">Actual Farrowing Date</th>
                                            <th class="py-2 px-2">Pigs Born</th>
                                            <th class="py-2 px-2">Pigs Alive</th>
                                            <th class="py-2 px-2">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr >
                                            <td class="py-2 px-2 text-left whitespace-nowrap">
                                                <p class="font-medium text-center"></p >
                                            </td>
                                            <td class="py-2 px-2">
                                                <p class="font-medium text-center"></p >
                                            </td>
                                            <td class="py-2 px-2 text-left whitespace-nowrap">
                                                <p class="font-medium text-center"></p >
                                            </td>
                                            <td class="py-2 px-2 text-left whitespace-nowrap">
                                                <p class="font-medium text-center"></p >
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                <!-- <div class="flex justify-center mb-2">
                                                    <span v-if="lab.remarks ==='Weaned'" class="text-md font-semibold text-green-500">Weaned</span>
                                                    <span v-if="lab.remarks ==='Waiting for results'" class="text-md font-semibold text-blue-500">Waiting for results</span>
                                                    <span v-if="lab.remarks ===''" class="text-md font-semibold text-red-500">N/A</span>
                                                </div> -->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div v-show="openTab === 3" class="transition-all duration-300 bg-white p-4 rounded-lg shadow-md border-l-4 border-blue-600">
                                <h2 class="text-2xl font-semibold mb-2 text-blue-600">Weanings</h2>

                                    <table class="w-full table-auto">
                                        <thead>
                                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                                <th class="py-2 px-2">Labor ID</th>
                                                <th class="py-2 px-2">No of Pigs Born</th>
                                                <th class="py-2 px-2">No of Pigs Alive</th>
                                                <th class="py-2 px-2">No of Pigs Weaned</th>
                                                <th class="py-2 px-2">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr >
                                                <td class="py-2 px-2 text-left whitespace-nowrap">
                                                    <p class="font-medium text-center"></p >
                                                </td>
                                                <td class="py-2 px-2 text-left whitespace-nowrap">
                                                    <p class="font-medium text-center"></p >
                                                </td>
                                                <td class="py-2 px-2 text-left whitespace-nowrap">
                                                    <p class="font-medium text-center"></p >
                                                </td>
                                                <td class="py-2 px-2">
                                                    <p class="font-medium text-center"></p >
                                                </td>
                                                <td class="py-2 px-2 text-center whitespace-nowrap">
                                                    <p class="text-md font-semibold text-green-500"></p >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>


                                    <!-- <template v-else>
                                        <p class="text-center font-bold text-gray-900 py-6">No weanings found</p>
                                    </template> -->
                            </div>
                        </div>
                    </div>

                </div>
                <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8  ">

                </div>
            </div>
        </div>






                <!-- </div>
                <div class="w-full md:w-3/5 lg:w-3/5 xl:w-3/5 md:ml-4">

                    <div class="bg-white p-6 shadow-sm">
                        <div class="flex justify-between ">
                            <h4 class="text-2xl mb-3">Appointment History</h4>
                            <Link href="/appointment/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-2 mr-4" >+</Link>
                        </div>
                        <table class="w-full text-sm text-left text-black-500 dark:text-gray-400">
                            <thead class="text-xs text-white uppercase text-center bg-gray-50 dark:bg-blue-500 dark:text-white-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Date and Time
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                      Doctor
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                       Service
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Reason
                                     </th>
                                     <th scope="col" class="px-6 py-3">
                                        Status
                                     </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                     </th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr class="bg-blue-200 hover:bg-s-50 text-center font-medium text-black  whitespace-nowrap" v-for="appointment in patientAppointments" :key="appointment.id">

                                    <td scope="row" >
                                        {{ formattedDate(appointment.date)}} at {{ formatTimeToAMPM(appointment.time) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ appointment.doctor.user.firstname }} {{ appointment.doctor.user.middlename }} {{ appointment.doctor.user.lastname }}{{ appointment.doctor.user.suffix }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ appointment.service.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ appointment.reason }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ appointment.status }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div  class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                            <Link :href="'/appointment/show/'+ appointment.id" title="Show Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div> -->

                    <!-- <div class="bg-white p-6 mt-12 shadow-sm">
                        <div class="flex justify-between ">
                            <h4 class="text-2xl mb-3">History And Physical Form</h4>
                            <Link :href="'/healthForm/create/'+patient.id" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-2 mr-4" >+</Link>
                        </div>
                        <table class="w-full text-sm text-left text-black-500 dark:text-gray-400">
                            <thead class="text-xs text-white uppercase text-center bg-gray-50 dark:bg-blue-500 dark:text-white-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Doctor
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr class="bg-blue-200 hover:bg-s-50 text-center font-medium text-black  whitespace-nowrap" v-for="f in form" :key="form.id">

                                    <td scope="row" >
                                        {{ f.date }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ f.doctor.user.firstname }}  {{ f.doctor.user.middlename }}  {{ f.doctor.user.lastname }}
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110 ">
                                            <Link :href="'/healthForm/show/'+ f.id" title="Show Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                        </div>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div> -->
                <!-- </div>
            </div>
        </div> -->

    </Sidebar>
</template>

<style scoped>
 .vertical-line {
            height: 40px; /* Adjust the height of the line as needed */
            border-left: 1px solid #000; /* Set the color and thickness of the line */
            margin-left: 0.5px; /* Adjust the left margin as needed */
        }
</style>
