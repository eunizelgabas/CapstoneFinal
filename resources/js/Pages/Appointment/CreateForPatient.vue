<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import { ref, reactive, computed, watch, onMounted } from 'vue';
    import axios from 'axios';
    import { Link , Head} from '@inertiajs/vue3';
    import { useForm } from '@inertiajs/vue3';



    let form = useForm({
       'date' : '',
       'time' : '',
       'pat_id': '',
       'reason': '',
       'doc_id': '',
       'service_id': ''

    })

    let props = defineProps({
        appointment: Array,
        doctors: Array,
        services: Object,
        patients:Array,
        isAdminOrDoctor:Boolean,
        isDoctor: Boolean,

    });

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
        selectedUser.firstname = `${patient.firstname}`;
        selectedUser.lastname = `${patient.lastname}`;
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

                <!-- component -->
                <div class="" v-if="isAdminOrDoctor">
                    <div class="inline-flex flex-col justify-center relative text-gray-500">
                        <div class="relative">
                            <input type="text" v-model="searchTerm" @input="searchUsers" placeholder="Search patients by name" class="p-2 pl-8 rounded border border-gray-200 bg-gray-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-yellow-600 focus:border-transparent"/>
                            <svg class="w-4 h-4 absolute left-2.5 top-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <template v-if="searchResults.length > 0 && searchTerm.trim() !== ''">
                            <h3 class="mt-2 text-sm">Patients:</h3>
                            <ul class="bg-white border border-gray-100 w-full mt-2">
                                <li v-for="patient in searchResults" :key="patient.id" @click="selectUser(patient)" class="pl-8 pr-2 py-1 border-b-2 border-gray-100 relative cursor-pointer hover:bg-yellow-50 hover:text-gray-900">
                                <b>{{ patient.firstname }} {{ patient.lastname }}</b>
                                </li>
                            </ul>
                        </template>
                    </div>
                </div>


                  <div class="w-full mt-10 mx-auto px-4 ">
                  <div class="bg-white border border-4 rounded-lg shadow relative m-10">

                    <div class="flex items-start justify-between p-5 border-b rounded-t">
                        <h3 class="text-xl font-semibold">
                        Appointment Details
                        </h3>
                    </div>

                    <div class="p-6 ">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-6 sm:col-span-2" v-if="isAdminOrDoctor">
                                    <label for="pat_id" class="text-sm font-medium text-gray-900 block mb-2">First Name</label>
                                    <input type="text" name="pat_id" id="pat_id" v-model="selectedUser.firstname" readonly class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                </div>
                                <div class="col-span-6 sm:col-span-2" v-if="isAdminOrDoctor">
                                    <label for="pat_id" class="text-sm font-medium text-gray-900 block mb-2">Last Name</label>
                                    <input type="text" name="pat_id" id="pat_id" v-model="selectedUser.lastname" readonly class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="date" class="text-sm font-medium text-gray-900 block mb-2">Date</label>
                                    <input type="date" name="date" v-model="form.date" id="date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.date">{{ form.errors.date }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="time" class="text-sm font-medium text-gray-900 block mb-2">Time</label>
                                    <input type="time" name="time" id="time" v-model="form.time"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" required="">
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.time">{{ form.errors.time }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2" v-if="!isDoctor">
                                    <label for="doc_id" class="text-sm font-medium text-gray-900 block mb-2">Doctor</label>
                                    <select v-model="form.doc_id" @change="fetchServicesByDoctorId"  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5" >
                                        <option selected disabled >Select doctor</option>
                                        <option v-for="doc in filteredDoctors" :key="doc.id" :value="doc.id">{{ doc.user.firstname }} {{ doc.user.lastname }}</option>
                                    </select>
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.doc_id">{{ form.errors.doc_id }}</div>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="service_id" class="text-sm font-medium text-gray-900 block mb-2">Service</label>
                                    <select v-model="form.service_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-2.5">
                                        <option selected disabled >Select services</option>
                                        <option v-for="service in availableServices" :key="service.id" :value="service.id">{{ service.name }}</option>
                                    </select>
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.service_id">{{ form.errors.service_id }}</div>
                                </div>
                                <div class="col-span-full">
                                    <label for="reason" class="text-sm font-medium text-gray-900 block mb-2">Reason</label>
                                    <textarea id="reason" rows="2" v-model="form.reason" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-indigo-600 focus:border-indigo-600 block w-full p-4" ></textarea>
                                    <div class="text-sm text-red-500 italic" v-if="form.errors.reason">{{ form.errors.reason }}</div>
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-200 rounded-b flex justify-end">
                                <button class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </Sidebar>

</template>
