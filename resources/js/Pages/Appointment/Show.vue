<script setup>
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head, Link} from '@inertiajs/vue3';
import moment from 'moment';
import { ref, computed} from 'vue';


   const props = defineProps({
       appointment: Object,
       doctor: Object,
    })

    const patientFirstName = ref(props.appointment.patient.firstname);
    const patientLastName = ref(props.appointment.patient.lastname);

    const patientInitials = computed(() => {
      const firstNameInitial = patientFirstName.value.charAt(0).toUpperCase();
      const lastNameInitial = patientLastName.value.charAt(0).toUpperCase();
      return `${firstNameInitial} ${lastNameInitial}`;
    });

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

    const formattedTimeAgo = computed(() => {
      if (props.appointment && props.appointment.created_at) {
        // Parse the 'created_at' timestamp into a Date object
        const createdTime = new Date(props.appointment.created_at);

        // Calculate the time difference in milliseconds
        const currentTime = new Date();
        const timeDifference = currentTime - createdTime;

        // Calculate the time in seconds, minutes, hours, and days
        const seconds = Math.floor(timeDifference / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);

        if (days > 0) {
          return `${days} day${days > 1 ? 's' : ''} ago`;
        } else if (hours > 0) {
          return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        } else if (minutes > 0) {
          return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        } else {
          return `${seconds} second${seconds > 1 ? 's' : ''} ago`;
        }
      }
      return 'Time unknown'; // Handle cases where 'created_at' is not available
    });

</script>

<template>
    <Sidebar>
        <Head title="Doctor"/>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Appointment | {{ appointment.patient.firstname }} {{ appointment.patient.lastname }} </h2>
        </template>
        <!-- component -->

        <div class="flex justify-center mt-16">
            <div class=" sm:w-1/2 md:w-3/4 lg:w-3/4 xl:w-3/4 flex flex-col shadow-lg rounded-lg overflow-hidden">
                <div class="bg-gray-200 px-6 py-4">
                    <div class="uppercase text-xs text-gray-600 font-bold">Patient</div>

                    <div class="flex pt-3">
                        <div class="bg-blue-700 w-12 h-12 flex justify-center items-center rounded-full uppercase font-bold text-white">{{ patientInitials }}</div>
                        <div class="ml-4">
                            <p class="font-bold">{{appointment.patient.firstname}} {{ appointment.patient.lastname }}</p>
                            <p class="text-sm text-gray-700 mt-1">{{appointment.patient.type}}</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center px-6 py-4">
                    <div class=" text-xs uppercase px-2 py-1 rounded-full border font-bold"  :class="{
                            'bg-blue-200 text-blue-600': appointment.status == 'Pending',
                            'bg-red-200 text-red-600':appointment.status == 'Cancelled',
                            'bg-green-200 text-green-600': appointment.status =='Accepted',
                        }">{{appointment.status}}</div>
                    <div class="text-sm">{{ formattedDate(appointment.date)}}  at {{ formatTimeToAMPM(appointment.time) }}</div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="border rounded-lg p-4 bg-gray-200 flex justify-between">
                        <div>
                            <p >Doctor: <span class="font-bold"> {{ doctor.user.firstname }} {{ doctor.user.lastname }}</span></p>
                        </div>
                        <div>
                            <p >Service: <span class="font-bold"> {{ appointment.service.name }}</span> </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p >Reason: <span class="font-bold"> {{ appointment.reason }}</span> </p>

                    </div>
                    <div class="text-grey-500 flex flex-row space-x-1  my-4">
                        <svg stroke="currentColor" fill="none" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xs">{{ formattedTimeAgo }}</p>
                    </div>

                    <div class="flex justify-end" v-if="appointment.status === 'Pending'">
                        <div
                            class="bg-red-500 shadow-lg shadow- mr-3 shadow-red-600 text-white cursor-pointer px-3 py-1 text-center justify-center items-center rounded-xl flex space-x-2 flex-row">
                            <Link :href="'/appointment/cancel/' + appointment.id" method="post" class="btn flex" title="Cancel Appointment">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" class="text-xl" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M885.9 490.3c3.6-12 5.4-24.4 5.4-37 0-28.3-9.3-55.5-26.1-77.7 3.6-12 5.4-24.4 5.4-37 0-28.3-9.3-55.5-26.1-77.7 3.6-12 5.4-24.4 5.4-37 0-51.6-30.7-98.1-78.3-118.4a66.1 66.1 0 0 0-26.5-5.4H144c-17.7 0-32 14.3-32 32v364c0 17.7 14.3 32 32 32h129.3l85.8 310.8C372.9 889 418.9 924 470.9 924c29.7 0 57.4-11.8 77.9-33.4 20.5-21.5 31-49.7 29.5-79.4l-6-122.9h239.9c12.1 0 23.9-3.2 34.3-9.3 40.4-23.5 65.5-66.1 65.5-111 0-28.3-9.3-55.5-26.1-77.7zM184 456V172h81v284h-81zm627.2 160.4H496.8l9.6 198.4c.6 11.9-4.7 23.1-14.6 30.5-6.1 4.5-13.6 6.8-21.1 6.7a44.28 44.28 0 0 1-42.2-32.3L329 459.2V172h415.4a56.85 56.85 0 0 1 33.6 51.8c0 9.7-2.3 18.9-6.9 27.3l-13.9 25.4 21.9 19a56.76 56.76 0 0 1 19.6 43c0 9.7-2.3 18.9-6.9 27.3l-13.9 25.4 21.9 19a56.76 56.76 0 0 1 19.6 43c0 9.7-2.3 18.9-6.9 27.3l-14 25.5 21.9 19a56.76 56.76 0 0 1 19.6 43c0 19.1-11 37.5-28.8 48.4z"></path></svg>
                                <span>Cancel</span>
                            </Link>

                        </div>
                        <div
                            class="bg-green-500 shadow-lg shadow-  shadow-green-600 text-white cursor-pointer px-3 text-center justify-center items-center py-1 rounded-xl flex space-x-2 flex-row">
                            <Link :href="'/appointment/accept/' + appointment.id" method="post" class="btn flex" title="Accept Appointment">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" class="text-xl" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M885.9 533.7c16.8-22.2 26.1-49.4 26.1-77.7 0-44.9-25.1-87.4-65.5-111.1a67.67 67.67 0 0 0-34.3-9.3H572.4l6-122.9c1.4-29.7-9.1-57.9-29.5-79.4A106.62 106.62 0 0 0 471 99.9c-52 0-98 35-111.8 85.1l-85.9 311H144c-17.7 0-32 14.3-32 32v364c0 17.7 14.3 32 32 32h601.3c9.2 0 18.2-1.8 26.5-5.4 47.6-20.3 78.3-66.8 78.3-118.4 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7 0-12.6-1.8-25-5.4-37 16.8-22.2 26.1-49.4 26.1-77.7-.2-12.6-2-25.1-5.6-37.1zM184 852V568h81v284h-81zm636.4-353l-21.9 19 13.9 25.4a56.2 56.2 0 0 1 6.9 27.3c0 16.5-7.2 32.2-19.6 43l-21.9 19 13.9 25.4a56.2 56.2 0 0 1 6.9 27.3c0 16.5-7.2 32.2-19.6 43l-21.9 19 13.9 25.4a56.2 56.2 0 0 1 6.9 27.3c0 22.4-13.2 42.6-33.6 51.8H329V564.8l99.5-360.5a44.1 44.1 0 0 1 42.2-32.3c7.6 0 15.1 2.2 21.1 6.7 9.9 7.4 15.2 18.6 14.6 30.5l-9.6 198.4h314.4C829 418.5 840 436.9 840 456c0 16.5-7.2 32.1-19.6 43z"></path></svg>
                                <span>Accept</span>
                            </Link>

                        </div>
                    </div>

                </div>

        </div>

    </div>



    </Sidebar>
</template>
