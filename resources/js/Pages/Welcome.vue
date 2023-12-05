<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import { ref, reactive, computed, watch, onMounted } from 'vue';
    import axios from 'axios';
        // import { Link , Head} from '@inertiajs/vue3';
    import { useForm } from '@inertiajs/vue3';
    import Modal from  '@/Components/Modal.vue';

let showAbout = ref(false)

function closeModal(){
    showAbout.value = false;
}

function about() {

    showAbout.value = true;
}


    let form = useForm({
       'date' : '',
       'time' : '',
       'student_id': '',
       'firstname': '',
       'lastname':'',
       'reason': '',
       'doc_id': '',
       'service_id': '',


    })

    let props = defineProps({
        appointment: Array,
        doctors: Array,
        services: Object,
        users:Array,
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
            const response = await axios.get(`/api/users/search?term=${searchTerm.value}`);
            const data = response.data; // Assuming the API response contains the search results.
            const activeUsers = data.users.filter(user => user.status === 1);
            searchResults.value = activeUsers; // Update searchResults with the retrieved user data.
        } catch (error) {
            console.error('Error searching for users:', error);
        }
    };


    const selectUser = (user) => {

        // Set the selected user and populate the form fields
        selectedUser.id = user.id;
        selectedUser.fullname = `${user.firstname} ${user.lastname}`;
        form.user_id = user.id; // Populate the user_id field with the selected user's ID

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



    onMounted(() => {
  // Fetch services based on the logged-in user's role
  if (props.isDoctor) {
    // User is a doctor, set doctor and services automatically
    const doctor = props.doctors[0]; // You might need to get the correct doctor based on your data
    form.doc_id = doctor.id;
    availableServices.value = doctor.services;
  }
});


</script>

<template>

    <Head title="Landing Page" />

    <nav class="relative px-4 py-4 flex justify-between items-center bg-white">
        <a class="text-3xl font-bold leading-none" href="#">
            <img src="/images/mdclogo.png" class="h-10" alt="MDC Logo">
        </a>

        <Link as="button" class=" lg:inline-block py-2 px-6 bg-indigo-500 hover:bg-indigo-600 text-sm text-white font-bold rounded-xl transition duration-200" :href="route('login')">Log in</Link>
    </nav>

    <div class="min-w-screen min-h-screen bg-indigo-100 p-5 lg:p-10 overflow-hidden relative">

    <div class="flex-1 min-h-full min-w-full rounded-3xl bg-white shadow-xl p-10 lg:p-20 text-gray-800 relative md:flex items-center text-center md:text-left">
        <div class="w-full md:w-1/2">

            <div class=" text-gray-600 font-light">
                <h1 class="font-black uppercase text-5xl lg:text-5xl text-gray-500 mb-2">Mater Dei College's</h1>
                <p class="font-black uppercase text-3xl lg:text-3xl text-indigo-500 mb-2">Clinic Management System</p>
            </div>
            <div class="mb-20 md:mb-0">

                  <Link :href="route('appointment.create')" as="button"  class="text-lg  outline-none focus:outline-none transform  hover:scale-110 w-50 bg-indigo-500 hover:bg-indigo-700 focus:bg-indigo-700 transition-colors text-white rounded-lg px-3 py-2 font-light"><i class="mdi mdi-arrow-right mr-2"></i>Book Appointment</Link>
            </div>
        </div>
        <div class="w-full md:w-1/2 text-center">
         <img src="/images/land.jpg" class="w-full max-w-lg lg:max-w-full mx-auto" alt="">
        </div>
    </div>
    <div class="w-64 md:w-96 h-96 md:h-full bg-indigo-200 bg-opacity-30 absolute -top-64 md:-top-96 right-20 md:right-32 rounded-full pointer-events-none -rotate-45 transform"></div>
    <div class="w-96 h-full bg-gray-200 bg-opacity-20 absolute -bottom-96 right-64 rounded-full pointer-events-none -rotate-45 transform"></div>
</div>

 <div class="flex items-end justify-end fixed bottom-0 right-0 mb-4 mr-4 z-10">
    <div>
        <a title="About the System" @click="about()" href="#" class="block w-10 h-10 rounded-full transition-all shadow hover:shadow-lg transform hover:scale-110 hover:rotate-12">
            <img class="object-cover object-center w-10 h-10 rounded-full" src="/images/question.png"/>
        </a>
        <Modal :show="showAbout" @close="closeModal">
            <div class="p-4 sm:p-10 text-center overflow-y-auto flex flex-col items-center">
                <button @click="closeModal" class="mt-5 ml-auto p-3 shadow-2xl rounded-xl text-black font-bold hover:text-red-800">X</button>
                <div class="px-10 py-10 max-w-md m-auto lg:col-span-2 shadow-xl rounded-xl lg:mt-10 md:shadow-xl md:rounded-xl lg:shadow-none lg:rounded-none lg:w-full lg:mb-10 lg:px-5 lg:pt-5 lg:pb-5 lg:max-w-lg bg-white">
                    <div class="flex flex-col items-center">
                        <img class="h-full" src="/images/mdclogo.png" alt="MDC Logo">
                        <h1 class="mt-5 font-bold text-lg lg:mt-7">About The System</h1>

                        <h1 class="text-lg text-gray-600 text-justify pt-2">MDC CMS was developed by MDC IT students as their capstone project in 2023. This aims to enhance the efficiency of the MDC clinic by providing a faster and easier workflow.</h1>
                    </div>
                </div>

            </div>
        </Modal>
    </div>
</div>

</template>


<style scoped>
    .bg-image {
      background-image: url('/images/mdcs.jpg');
      background-size: cover;
      background-position: center;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }

    /* Overlay with Opacity */
    .overlay {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(203, 199, 199, 0.2); /* Adjust the opacity as needed */
      z-index: 0;
    }
</style>

