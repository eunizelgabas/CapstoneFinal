<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import Modal from  '@/Components/Modal.vue';
    import DangerButton from '@/Components/DangerButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import moment from 'moment';
    import { ref, computed } from 'vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';

    let showConfirm = ref(false)
    let selectedAppointmentForDelete = null
    let selectedAppointment = null
    let deleteForm = useForm({});

    const props = defineProps({
        appointments: Array,
        doctorAppointments: Array, // Doctor-specific appointments data received from Laravel
        patientAppointments: Array,
        user:Object
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

    function closeModal(){
        showConfirm.value = false;
    }

    function remove(app) {
        selectedAppointmentForDelete = app
        showConfirm.value = true;
        // console.log(props.errors)
    }

    function deleteApp(){
        deleteForm.delete('/appointment/' + selectedAppointmentForDelete.id)
        showConfirm.value = false;
    }

    const pendingAppointments = computed(() => {
      return props.appointments.filter((appointment) => appointment.status === 'Pending');
    });

    const acceptedAppointments = computed(() => {
      return props.appointments.filter((appointment) => appointment.status === 'Accepted');
    });

    const cancelledAppointments = computed(() => {
      return props.appointments.filter((appointment) => appointment.status === 'Cancelled');
    });

    const patientInitials = computed(() => {
  return props.appointments.map(appointment => {
    const firstNameInitial = appointment.patient.firstname.charAt(0).toUpperCase();
    const lastNameInitial = appointment.patient.lastname.charAt(0).toUpperCase();
    return `${firstNameInitial} ${lastNameInitial}`;
  });
});
</script>

<template>
    <Sidebar>
        <Head title="Appointment"/>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Appointment</h2>
        </template>

        <div class="flex p-3">
            <h1 class="text-3xl font-medium text-gray-700 "></h1>
            <Link :href="route('appointment.createForPatient')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-2 mr-4" >Create Appointment</Link>
        </div>
        <div class="bg-blue w-full mt-2 px-6 font-sans flex flex-row">

            <!-- Pending Card -->
            <div class="flex-grow bg-blue-300 p-2 mr-3 mb-4 min-h-0 h-full">
                <div class="flex justify-between py-1">
                    <h1 class="text-l font-bold ">Pending</h1>
                    <svg class="h-4 fill-current text-grey-dark cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M5 10a1.999 1.999 0 1 0 0 4 1.999 1.999 0 1 0 0-4zm7 0a1.999 1.999 0 1 0 0 4 1.999 1.999 0 1 0 0-4zm7 0a1.999 1.999 0 1 0 0 4 1.999 1.999 0 1 0 0-4z"/>
                    </svg>
                </div>
                <div class="text-sm mt-2">
                    <div  class="bg-white p-2 rounded mt-1 border-b border-grey cursor-pointer hover:bg-grey-lighter" v-for="(appointment, index) in pendingAppointments" :key="index">
                        <div class="flex pt-3">
                            <div class="bg-blue-700 w-12 h-12 flex justify-center items-center rounded-full uppercase font-bold text-white"> {{ patientInitials[index] }}</div>
                            <div class="ml-4">
                                <p class="font-bold">{{appointment.patient.firstname}} {{ appointment.patient.lastname }}</p>
                                <p class="text-sm text-gray-700 mt-1">{{appointment.patient.type}}</p>

                            </div>
                            <div class="flex item-center ml-auto">

                                <div v-if="$page.props.auth.permissions.includes('show-appointment')" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    <Link :href="'/appointment/show/'+ appointment.id" title="Show Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                </div>

                                <div class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110">
                                    <Link :href="'/appointment/edit/'+appointment.id" class="btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </Link>
                                </div>
                                <div class="w-4  ml-2 mr-2 transform hover:text-red-500 hover:scale-110">
                                    <a href="#" @click="remove(appointment)" class="btn" title="Delete Category">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </a>
                                    <Modal :show="showConfirm" @close="closeModal">
                                        <div class="p-4 sm:p-10 text-center overflow-y-auto">

                                            <span class="mb-4 inline-flex justify-center items-center w-[62px] h-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                            </svg>
                                            </span>


                                            <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                                Delete Appointment
                                            </h3>
                                            <p class="text-gray-500">
                                                Are you sure you want like to delete this Appointment?
                                            </p>

                                            <div class="mt-6 flex justify-center gap-x-4">
                                                <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                                                <DangerButton @click="deleteApp()">Delete</DangerButton>
                                            </div>
                                        </div>
                                    </Modal>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Accepted Card -->
            <div class="flex-grow bg-green-300 p-2 mr-3 mb-4 min-h-0 h-full">
                <div class="flex justify-between py-1">
                    <h1 class="text-l font-bold ">Accepted</h1>
                    <svg class="h-4 fill-current text-grey-dark cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M5 10a1.999 1.999 0 1 0 0 4 1.999 1.999 0 1 0 0-4zm7 0a1.999 1.999 0 1 0 0 4 1.999 1.999 0 1 0 0-4zm7 0a1.999 1.999 0 1 0 0 4 1.999 1.999 0 1 0 0-4z"/>
                    </svg>
                </div>
                <div class="text-sm mt-2">
                    <div  class="bg-white p-2 rounded mt-1 border-b border-grey cursor-pointer hover:bg-grey-lighter" v-for="(appointment, index) in acceptedAppointments" :key="index">
                        <div class="flex pt-3">
                            <div class="bg-blue-700 w-12 h-12 flex justify-center items-center rounded-full uppercase font-bold text-white"> {{ patientInitials[index] }}</div>
                            <div class="ml-4">
                                <p class="font-bold">{{appointment.patient.firstname}} {{ appointment.patient.lastname }}</p>
                                <p class="text-sm text-gray-700 mt-1">{{appointment.patient.type}}</p>

                            </div>
                            <div class="flex item-center ml-auto">

                                <div v-if="$page.props.auth.permissions.includes('show-appointment')" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    <Link :href="'/appointment/show/'+ appointment.id" title="Show Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                </div>

                                <div class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110">
                                    <Link href="#" class="btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </Link>
                                </div>
                                <div class="w-4  ml-2 mr-2 transform hover:text-red-500 hover:scale-110">
                                    <a href="#" @click="remove(appointment)" class="btn" title="Delete Category">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </a>
                                    <Modal :show="showConfirm" @close="closeModal">
                                        <div class="p-4 sm:p-10 text-center overflow-y-auto">

                                            <span class="mb-4 inline-flex justify-center items-center w-[62px] h-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                            </svg>
                                            </span>


                                            <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                                Delete Appointment
                                            </h3>
                                            <p class="text-gray-500">
                                                Are you sure you want like to delete this Appointment?
                                            </p>

                                            <div class="mt-6 flex justify-center gap-x-4">
                                                <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                                                <DangerButton @click="deleteApp()">Delete</DangerButton>
                                            </div>
                                        </div>
                                    </Modal>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Cancelled Card -->
            <div class="flex-grow bg-red-300 p-2 mb-4 min-h-0 h-full">
                <div class="flex justify-between py-1">
                    <h1 class="text-l font-bold ">Cancelled</h1>
                    <svg class="h-4 fill-current text-grey-dark cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M5 10a1.999 1.999 0 1 0 0 4 1.999 1.999 0 1 0 0-4zm7 0a1.999 1.999 0 1 0 0 4 1.999 1.999 0 1 0 0-4zm7 0a1.999 1.999 0 1 0 0 4 1.999 1.999 0 1 0 0-4z"/>
                    </svg>
                </div>
                <div class="text-sm mt-2">
                    <div  class="bg-white p-2 rounded mt-1 border-b border-grey cursor-pointer hover:bg-grey-lighter" v-for="(appointment, index) in cancelledAppointments" :key="index">
                        <div class="flex pt-3">
                            <div class="bg-blue-700 w-12 h-12 flex justify-center items-center rounded-full uppercase font-bold text-white"> {{ patientInitials[index] }}</div>
                            <div class="ml-4">
                                <p class="font-bold">{{appointment.patient.firstname}} {{ appointment.patient.lastname }}</p>
                                <p class="text-sm text-gray-700 mt-1">{{appointment.patient.type}}</p>

                            </div>
                            <div class="flex item-center ml-auto">

                                <div v-if="$page.props.auth.permissions.includes('show-appointment')" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    <Link :href="'/appointment/show/'+ appointment.id" title="Show Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                </div>

                                <div class="w-4 mr-2 transform hover:text-blue-500 hover:scale-110">
                                    <Link href="#" class="btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </Link>
                                </div>
                                <div class="w-4  ml-2 mr-2 transform hover:text-red-500 hover:scale-110">
                                    <a href="#" @click="remove(appointment)" class="btn" title="Delete Category">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </a>
                                    <Modal :show="showConfirm" @close="closeModal">
                                        <div class="p-4 sm:p-10 text-center overflow-y-auto">

                                            <span class="mb-4 inline-flex justify-center items-center w-[62px] h-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                            </svg>
                                            </span>


                                            <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                                Delete Appointment
                                            </h3>
                                            <p class="text-gray-500">
                                                Are you sure you want like to delete this Appointment?
                                            </p>

                                            <div class="mt-6 flex justify-center gap-x-4">
                                                <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                                                <DangerButton @click="deleteApp()">Delete</DangerButton>
                                            </div>
                                        </div>
                                    </Modal>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </Sidebar>
</template>
