<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head, Link } from '@inertiajs/vue3';
import moment from 'moment';
import { ref, onMounted, computed } from 'vue';


let props = defineProps({
    // pendingApp: Number,
    // appointment: Object,
    user: Object
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

    const currentTime = ref('');

    onMounted(() => {
    // Fetch the current time from the data passed by Laravel
    currentTime.value = props.currentTime;
    });
    // onMounted(() => {
    // // Fetch the authenticated user from the data passed by Laravel
    // user.value = props.user;
    // });

    const greeting = computed(() => {
    const hour = currentTime.value ? parseInt(currentTime.value.split(':')[0]) : 0;

  if (hour >= 5 && hour < 12) {
    return 'Good Morning';
  } else if (hour >= 12 && hour < 17) {
    return 'Good Afternoon';
  } else {
    return 'Good Evening';
  }
});

</script>

<template>
    <Head title="Dashboard" />

    <Sidebar>
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template> -->

        <!-- <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col space-y-8"> -->
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
                    <!-- <div class="grid grid-cols-1 gap-4 px-4 mt-3 sm:grid-cols-4 sm:px-8">
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
        </div> -->
        <div class="flex flex-col flex-1 w-full overflow-y-auto">
            <!--Start Topbar -->
            <!--End Topbar -->
          <main class="relative z-0 flex-1 pb-8 px-6 bg-white">
              <div class="grid pb-10  mt-4 ">
                  <!-- Start Content-->

                    <!-- <div class="grid grid-cols-1 gap-2 sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 mt-3">
                      <div class="relative w-full h-52 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg transition duration-300 ease-in-out">
                          <div class="absolute inset-0 bg-blue-600 bg-opacity-40 transition duration-300 ease-in-out"></div>
                          <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex items-center justify-center">
                                <div>
                                    <h3 class="text-center text-white text-lg">
                                        Total Balance
                                    </h3>
                                    <h3 class="text-center text-white text-3xl mt-2 font-bold">
                                        RM 27,580
                                    </h3>
                                    <div class="flex space-x-4 mt-4">
                                        <button class="block uppercase mx-auto shadow bg-white text-indigo-600 focus:shadow-outline
                                        focus:outline-none text-white text-xs py-3 px-4 rounded font-bold">
                                        Transfer
                                        </button>
                                        <button class="block uppercase mx-auto shadow bg-indigo-800 hover:bg-indigo-700 focus:shadow-outline
                                        focus:outline-none text-white text-xs py-3 px-4 rounded font-bold">
                                        Request
                                        </button>
                                    </div>
                                </div>
                          </div>
                      </div>
                       <div class="relative w-full h-52 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg transition duration-300 ease-in-out"
                        style="background-image: url('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/f868ecef-4b4a-4ddf-8239-83b2568b3a6b/de7hhu3-3eae646a-9b2e-4e42-84a4-532bff43f397.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiJcL2ZcL2Y4NjhlY2VmLTRiNGEtNGRkZi04MjM5LTgzYjI1NjhiM2E2YlwvZGU3aGh1My0zZWFlNjQ2YS05YjJlLTRlNDItODRhNC01MzJiZmY0M2YzOTcuanBnIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.R0h-BS0osJSrsb1iws4-KE43bUXHMFvu5PvNfoaoi8o');">
                          <div class="absolute inset-0 bg-yellow-600 bg-opacity-75 transition duration-300 ease-in-out"></div>
                            <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex items-center">
                            <div>
                              <div class="text-white text-lg flex space-x-2 items-center">
                                <div class="bg-white rounded-md p-2 flex items-center">
                                  <i class="fas fa-toggle-off fa-sm text-yellow-300"></i>
                                </div>
                                <p>Finished Appt</p>
                              </div>
                              <h3 class="text-white text-3xl mt-2 font-bold">
                                  120
                              </h3>
                               <h3 class="text-white text-lg mt-2 text-yellow-100 ">
                                  4 not confirmed
                              </h3>
                            </div>
                          </div>
                      </div>
                       <div class="relative w-full h-52 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg transition duration-300 ease-in-out"
                        style="background-image: url('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/f868ecef-4b4a-4ddf-8239-83b2568b3a6b/de7hhu3-3eae646a-9b2e-4e42-84a4-532bff43f397.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiJcL2ZcL2Y4NjhlY2VmLTRiNGEtNGRkZi04MjM5LTgzYjI1NjhiM2E2YlwvZGU3aGh1My0zZWFlNjQ2YS05YjJlLTRlNDItODRhNC01MzJiZmY0M2YzOTcuanBnIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.R0h-BS0osJSrsb1iws4-KE43bUXHMFvu5PvNfoaoi8o');">
                          <div class="absolute inset-0 bg-blue-900 bg-opacity-75 transition duration-300 ease-in-out"></div>
                          <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex items-center">
                            <div>
                              <div class="text-white text-lg flex space-x-2 items-center">
                                <div class="bg-white rounded-md p-2 flex items-center">
                                  <i class="fas fa-clipboard-check fa-sm text-blue-800"></i>
                                </div>
                                <p>Finished Appt</p>
                              </div>
                              <h3 class="text-white text-3xl mt-2 font-bold">
                                  72
                              </h3>
                               <h3 class="text-white text-lg mt-2 ">
                                 3.4% <span class='font-semibold text-blue-200'>vs last month</span>
                              </h3>
                            </div>
                          </div>
                      </div>
                    </div> -->
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 mt-3">
                        <!-- Left Card - 50% width -->
                        <div class="col-span-2 sm:col-span-1 md:col-span-2 lg:col-span-2 xl:col-span-2">
                        <div class="relative w-full h-52 bg-cover bg-blue-600 bg-opacity-50 group rounded-lg overflow-hidden shadow-lg transition duration-300 ease-in-out">
                            <!-- Content -->
                            <div class="flex flex-wrap mt-2 justify-center">
                                <img src="/images/test.png" alt="" class="h-[200px] w-full sm:w-auto">

                                <div class="flex flex-col sm:flex-row ml-2 mt-10">
                                    <h1 class="font-bold text-2xl mb-1 sm:mb-0">{{ greeting }},  <span class="text-xl font-normal">{{ user.firstname }} {{ user.lastname }}</span></h1>
                                </div>

                                <p class="text-center sm:text-left mt-4 sm:ml-2">
                                    Whatever you do, do with determination. You have one life to live; do your work with passion and give your best.
                                </p>
                            </div>


                        </div>

                    </div>


    <!-- Center Card - 25% width -->
    <div class="col-span-1">
        <div class="relative w-full h-52 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg transition duration-300 ease-in-out"
            style="background-image: url('...');">
            <!-- Content -->
            <!-- ... (rest of the content) ... -->
        </div>
    </div>

    <!-- Right Card - 25% width -->
    <div class="col-span-1">
        <div class="relative w-full h-52 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg transition duration-300 ease-in-out"
            style="background-image: url('...');">
            <!-- Content -->
            <!-- ... (rest of the content) ... -->
        </div>
    </div>
</div>


                  <!-- End Content-->
              </div>
          </main>
        </div>
    </Sidebar>
</template>
