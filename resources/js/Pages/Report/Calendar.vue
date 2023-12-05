<script setup>
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head } from '@inertiajs/vue3';
import { Qalendar } from "qalendar";
import { ref, onMounted } from 'vue';
import "qalendar/dist/style.css";
import axios from 'axios';

let props = defineProps({
  appointments: Array,
  currentMonthAppointments: Number
});

const events = ref([]);

onMounted(async () => {
  try {
    // Make a request to your backend API to fetch appointments
    // const response = await axios.get('/api/appointments');

    // // Assuming the API returns an array of appointments, adjust the next line based on your API response structure
    // const appointments = response.data || [];
    // console.log('Appointments:', appointments);

    // Map the formatted appointments to the Qalendar events structure
    events.value = props.appointments.map(appointment => { // Change props.appointments to appointments
      // Combine date and time into a single property
      const startDateTime = new Date(`${appointment.date}T${appointment.time}`);
     let color;
    switch (appointment.status) {
        case 'Pending':
        color = 'blue';
        break;
        case 'Accepted':
        color = 'green';
        break;
        case 'Cancelled':
        color = 'red';
        break;
        default:
        color = 'gray'; // or any default color for other cases
    }
      return {
        title: appointment.service.name,
        with: `${appointment.patient.firstname} ${appointment.patient.lastname} -  Dr. ${appointment.doctor.user.firstname} ${appointment.doctor.user.lastname}`,
        time: { start: appointment.date, end: appointment.date },
        color: color,
        isEditable: false,
        id: appointment.id,
        description: appointment.reason,
      };
    });
  } catch (error) {
    console.error('Error fetching appointments:', error);
  }
});



</script>

<template>
    <Sidebar>
        <Head title="Appointment Calendar"/>

        <section class="container px-4 py-6 mx-auto">
             <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-x-3">
                    <h2 class="text-3xl font-bold text-black">Appointment Calendar</h2>

                    <span class="px-3 py-1 text-xs text-blue-600 bg-blue-100 rounded-full">{{ currentMonthAppointments }} appointments</span>
                </div>
            </div>
        </div>
        <div class="w-full px-4 py-5">

            <div class="calendar-container is-light-mode">
                <Qalendar :events="events"  />
            </div>

        </div>
        </section>


    </Sidebar>

</template>
