<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import { ref, reactive, computed, watch, onMounted } from 'vue';
    import axios from 'axios';
    import { Link , Head} from '@inertiajs/vue3';
    import { useForm } from '@inertiajs/vue3';


    let props = defineProps({
        appointment: Object,
        doctors: Object,
        services: Object,
        patients:Object,
        isAdminOrDoctor:Boolean,
        isDoctor: Boolean,

    });

    let form = useForm({
       'date' : props.appointment.date,
       'time' : props.appointment.time,
       'pat_id': props.appointment.patient.id,
       'reason': props.appointment.reason,
       'doc_id': props.appointment.doctor.user.id,
       'service_id': props.appointment.service.id,


    })



    const availableServices = ref([]);
    const searchTerm = ref('');
    const searchResults = ref([]);
    const selectedUser = reactive({
        id: null,
        lastname: '',
        firstname: ''
    });

    const searchUsers = async () => {
        try {
            const response = await axios.get(`/api/patients/search?term=${searchTerm.value}`);
            const data = response.data; // Assuming the API response contains the search results.
            const activeUsers = data.patients.filter(patient => patient.status === 1);
            searchResults.value = activeUsers; // Update searchResults with the retrieved user data.
        } catch (error) {
            console.error('Error searching for users:', error);
        }
    };


    const selectUser = (patient) => {

        // Set the selected patiend and populate the form fields
        selectedUser.id = patient.id;
        selectedUser.fullname = `${patient.firstname} ${patient.lastname}`;
        form.pat_id = patient.id; // Populate the user_id field with the selected user's ID

        searchTerm.value = '';
    };

    const fetchServicesByDoctorId = async () => {
        if (form.doc_id) {
            try {
            // Make an API request to fetch services offered by the selected doctor
            const response = await axios.get(`/api/doctors/${form.doc_id}/services`);
            const data = response.data; // Assuming the API response contains the services

            // Update the availableServices array with the fetched services
            availableServices.value = data.services;
            } catch (error) {
            console.error('Error fetching services:', error);
            }
        } else {
            // Clear availableServices if no doctor is selected
            availableServices.value = [];
        }
    };

    const isAdminOrDoctor = props.isAdminOrDoctor;

    onMounted(() => {
  // Fetch services based on the logged-in user's role
  if (props.isDoctor) {
    // User is a doctor, set doctor and services automatically
    const doctor = props.doctors[0]; // You might need to get the correct doctor based on your data
    form.doc_id = doctor.id;
    availableServices.value = doctor.services;
  }
});

const filteredDoctors = computed(() => {
    return props.doctors.filter(doctor => doctor.user.status === 1);
});

    const submit = () =>{
        // console.log(props.appointment);
        form.post('/appointment/store-for-patient')
    }


</script>

<template>
    <Head title="Create Appointment"/>
    <Sidebar>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Appointment</h2>
        </template>
        <div>
            <div class="w-full mt-10 mx-auto px-4 ">

                <form @submit.prevent="submit">
                    <div class="space-y-6">
                        <div class="block pl-12 font-semibold text-xl self-start text-gray-700">
                            <h1 class="leading-relaxed">Appointment Details Form</h1>
                            <hr>
                          </div>
                      <div class="border-b border-gray-900/10 pb-12">
                        <div class=" px-12 py-2 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-3 mx-auto">



                        <div class="sm:col-span-1" v-if="isAdminOrDoctor">
                            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Patient Name</label>
                            <div class="mt-2">
                              <input id="name" v-model="form.pat_id" name="name" type="text" readonly class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                              <div class="text-sm text-red-500 italic" v-if="form.errors.name">{{ form.errors.time }}</div>
                            </div>
                          </div>
                          <div class="sm:col-span-1">
                            <label for="time" class="block text-sm font-medium leading-6 text-gray-900">Time</label>
                            <div class="mt-2">
                              <input id="time" v-model="form.time" name="time" type="time" autocomplete="time" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                              <div class="text-sm text-red-500 italic" v-if="form.errors.time">{{ form.errors.time }}</div>
                            </div>
                          </div>
                          <div class="sm:col-span-1">
                            <label for="date" class="block text-sm font-medium leading-6 text-gray-900">Date</label>
                            <div class="mt-2">
                              <input id="date" v-model="form.date" name="date" type="date" autocomplete="date" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                              <div class="text-sm text-red-500 italic" v-if="form.errors.date">{{ form.errors.date }}</div>
                            </div>
                          </div>

                          <div class="sm:col-span-1" v-if="!isDoctor">
                                <label for="doctor" class="block text-sm font-medium leading-6 text-gray-900">Doctor</label>
                                <div class="mt-2">
                                <select id="doctor" v-model="form.doc_id" name="doctor" @change="fetchServicesByDoctorId" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    <option selected disabled >Select doctor</option>
                                    <option v-for="doc in filteredDoctors" :key="doc.id" :value="doc.id">{{ doc.user.firstname }} {{ doc.user.lastname }}</option>
                                </select>
                                <div class="text-sm text-red-500 italic" v-if="form.errors.doc_id">{{ form.errors.doc_id }}</div>
                                </div>
                          </div>


                          <div class="sm:col-span-2" >
                            <label for="service" class="block text-sm font-medium leading-6 text-gray-900">Services</label>
                            <div class="mt-2">
                                <select id="service" v-model="form.service_id"  name="service" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6" >
                                  <option selected disabled >Select services</option>
                                  <option v-for="service in availableServices" :key="service.id" :value="service.id">{{ service.name }}</option>
                                </select>
                                <div class="text-sm text-red-500 italic" v-if="form.errors.service_id">{{ form.errors.service_id }}</div>
                              </div>
                          </div>

                          <div class="sm:col-span-3">
                            <label for="reason" class="block text-sm font-medium leading-6 text-gray-900">Reason</label>
                            <div class="mt-2">
                              <textarea id="reason" v-model="form.reason" name="reason" type="text"  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                              <div class="text-sm text-red-500 italic" v-if="form.errors.reason">{{ form.errors.reason }}</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6 mb-3">
                      <!-- <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button> -->
                      <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                    </div>
                  </form>
            </div>
        </div>
    </Sidebar>

</template>
