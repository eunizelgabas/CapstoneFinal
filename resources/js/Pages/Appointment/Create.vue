<script setup>
import Modal from  '@/Components/Modal.vue';
import { ref, reactive, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import { Link , Head} from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

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
       'pat_id': '',
       'firstname': '',
       'lastname':'',
       'reason': '',
       'doc_id': '',
       'service_id': '',
       'email': '',


    })

    let props = defineProps({
        appointment: Array,
        doctors: Array,
        services: Object,


    });

    const availableServices = ref([]);




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

const filteredDoctors = computed(() => {
    return props.doctors.filter(doctor => doctor.user.status === 1);
});

    const submit = () =>{
        // console.log(props.appointment);
        form.post('/appointment')
        form.date ="" ,
       form.time ="" ,
       form.pat_id="" ,
       form.firstname="" ,
       form.lastname="",
       form.reason="" ,
       form.doc_id="" ,
       form.service_id="",
       form.email = ""
    }

      onMounted(() => {
    // Set a timeout to hide the success flash message after 3 seconds
        const successFlashMessage = document.getElementById('flash-success-message');
            if (successFlashMessage) {
                setTimeout(() => {
                successFlashMessage.style.display = 'none';
                }, 3000);
        }

        // Set a timeout to hide the error flash message after 3 seconds
        const errorFlashMessage = document.getElementById('flash-error-message');
            if (errorFlashMessage) {
                setTimeout(() => {
                errorFlashMessage.style.display = 'none';
            }, 3000);
        }
    });


</script>

<template>
    <Head title="Create Appointment"/>
    <div class="h-screen flex justify-center items-center bg-cover bg-center" style="background-image: linear-gradient(rgb(0 0 0 / 20%), rgb(0 0 0 / 20%)), url('/images/mdcs.jpg')">

    <div class="flex justify-center">
        <div v-if="$page.props.flash.success" id="flash-success-message" class="absolute top-20 right-1 p-4 bg-green-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.success }}
                <div class="progress-bar success"></div>
            </div>
            <div v-if="$page.props.flash.error" id="flash-error-message" class=" absolute top-20 right-1 p-4 bg-red-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.error }}
                <div class="progress-bar error"></div>
            </div>
        <form @submit.prevent="submit" class="form bg-white p-6 my-10 relative">
                <h3 class="text-2xl text-gray-900 font-semibold">Appointment Form</h3>
                <p class="text-gray-600"> To facilitate quicker and more convenient appointments for you.</p>
                <div class="mt-3">
                    <label for="pat_id" class=" text-sm font-bold text-gray-700 mb-2">Student/Teacher ID</label>
                    <input type="text" name="" v-model="form.pat_id" id="" placeholder="" class="border p-2  w-full">
                    <div class="text-sm text-red-500 italic" v-if="form.errors.pat_id">{{ form.errors.pat_id }}</div>
                </div>
               <div class="grid grid-cols-2 gap-5 mt-3">
                    <div>
                        <label for="firstname" class="text-sm font-bold text-gray-700 mb-2">Firstname</label>
                        <input type="text" name="firstname" id="firstname" v-model="form.firstname" placeholder="" class="border p-2 w-full mb-2">
                        <div class="text-sm text-red-500 italic" v-if="form.errors.firstname">{{ form.errors.firstname }}</div>
                    </div>
                    
                    <div>
                        <label for="pat_id" class="text-sm font-bold text-gray-700 mb-2">Lastname</label>
                        <input type="tel" name="" id="" placeholder="" v-model="form.lastname" class="border p-2 w-full">
                         <div class="text-sm text-red-500 italic" v-if="form.errors.lastname">{{ form.errors.lastname }}</div>
                    </div>
                </div>
                 <div class="grid grid-cols-2 gap-5 mt-3">
                    <div>
                        <label for="doc_id" class="text-sm font-bold text-gray-700 mb-2">Doctor</label>
                        <select name="doc_id"  placeholder="" id="doctor"  v-model="form.doc_id"  @change="fetchServicesByDoctorId" class="border p-2 w-full mb-2">
                            <option selected disabled >Select doctor</option>
                            <option v-for="doc in filteredDoctors" :key="doc.id" :value="doc.id">{{ doc.user.firstname }} {{ doc.user.lastname }}</option>
                        </select>
                         <div class="text-sm text-red-500 italic" v-if="form.errors.doc_id">{{ form.errors.doc_id }}</div>
                    </div>
                    
                    <div>
                        <label for="doc_id" class="text-sm font-bold text-gray-700 mb-2">Service</label>
                        <select name="doc_id"  placeholder="" id="doctor" v-model="form.service_id" class="border p-2 w-full mb-2">
                            <option selected disabled >Select services</option>
                            <option v-for="service in availableServices" :key="service.id" :value="service.id">{{ service.name }}</option>
                        </select>
                         <div class="text-sm text-red-500 italic" v-if="form.errors.service_id">{{ form.errors.service_id }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-5 mt-3">
                    <div>
                        <label for="date" class="text-sm font-bold text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" id="date" v-model="form.date"  placeholder="" class="border p-2 w-full mb-2">
                         <div class="text-sm text-red-500 italic" v-if="form.errors.date">{{ form.errors.date }}</div>
                    </div>
                    
                    <div>
                        <label for="time" class="text-sm font-bold text-gray-700 mb-2">Time</label>
                        <input type="time" name="time" v-model="form.time"  id="time" placeholder="" class="border p-2 w-full">
                         <div class="text-sm text-red-500 italic" v-if="form.errors.time">{{ form.errors.time }}</div>
                    </div>
                </div>
                <label for="email" class="text-sm font-bold text-gray-700 mb-2">Email</label>
                <input type="email" name="" id="" placeholder="" v-model="form.email"  class="border p-2 w-full mb-2">
                 <div class="text-sm text-red-500 italic" v-if="form.errors.email">{{ form.errors.email }}</div>
                <label for="reason" class="text-sm font-bold text-gray-700 mb-2">Reason</label>
                <textarea name="reason" id="reason" cols="10" rows="3" v-model="form.reason"  placeholder="" class="border p-2 w-full"></textarea>
                 <div class="text-sm text-red-500 italic" v-if="form.errors.reason">{{ form.errors.reason }}</div>
               
                <button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-500 text-white font-semibold p-3">Submit</button>

            </form> 
    </div>

    <div class="flex items-end justify-end fixed bottom-0 right-0 mb-4 mr-4 z-10">
                <div>
                    <a title="About the System" @click="about()" href="#" class="block w-10 h-10 rounded-full transition-all shadow hover:shadow-lg transform hover:scale-110 hover:rotate-12">
                        <img class="object-cover object-center w-10 h-10 rounded-full" src="/images/question.png"/>
                    </a>
                    <Modal :show="showAbout" @close="closeModal">
                        <div class=" text-center overflow-y-auto flex flex-col items-center">
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
    </div>
</template>


<style scoped>
    /* .bg-image {
      background-image: url('/images/mdcs.jpg');
      background-size: cover;
      background-position: center;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    } */

   
     .icon::after{
  content: '';
  display: block;
  position: absolute;
  border-top: 23px solid transparent;
  border-bottom: 17px solid transparent;
  border-left: 12px solid #3182ce;
  left: 100%;
  top: 0;
}


#flash-success-message {
    animation: fadeOut 6s ease-in-out forwards;
}

.progress-bar {
    height: 5px;
    width: 100%;
    background-color: #4CAF50; /* Green color */
    animation: progressBar 3s linear;
}
#flash-error-message {
    animation: fadeOut 7s ease-in-out forwards;
}

.success .progress-bar {

    animation: progressBar 5s linear;
}
.error .progress-bar {
    background-color: #FF5733; /* Red color */
    animation: progressBar 5s linear;
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

@keyframes progressBar {
    0% {
        width: 100%;
    }
    100% {
        width: 0;
    }
}    
</style>
