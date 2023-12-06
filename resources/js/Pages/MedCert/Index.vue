<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import Modal from  '@/Components/Modal.vue';
    import DangerButton from '@/Components/DangerButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import Pagination from '@/Components/Pagination.vue'
    import { ref, watch, onMounted, computed, reactive   } from 'vue';
    import { useForm, Link, Head,router } from '@inertiajs/vue3';
    import moment from 'moment';


let props = defineProps({

        patient: Object,
        doctor: Object,
        isDoctor: Boolean,
})

let form = useForm({
       'date' : '',
       'pat_id': '',
       'diagnosis': '',
       'doc_id': '',
       'days': ''

    })
const searchTerm = ref('');
    const searchResults = ref([]);
    const selectedUser = reactive({
        id: null,
        lastname: '',
        firstname: '',
        address: '',
        dob: '',
        doc_name:"",
    });

 if (props.patient && props.patient.type === 'Student') {
        form.course = props.patient.student.course;
        }


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
        selectedUser.address = `${patient.address}`;
        selectedUser.age =  calculateAge(patient.dob) + ` / ${patient.sex}`;
        form.pat_id = patient.id; // Populate the user_id field with the selected user's ID

        searchTerm.value = '';
    };

     function calculateAge(dateOfBirth) {
        const today = new Date();
        const birthDate = new Date(dateOfBirth);

        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        return age;
    }

     onMounted(() => {
  // Fetch services based on the logged-in user's role
  if (props.isDoctor && props.doctor && props.doctor.length > 0) {
    // User is a doctor, set doctor and services automatically
    const doctor = props.doctor[0]; // You might need to get the correct doctor based on your data
    form.doc_id = doctor.id;
    form.doc_name = `${doctor.user.firstname} ${doctor.user.lastname}`;
    form.lic_no = doctor.lic_no;
  }
});

const filteredDoctors = computed(() => {
    return props.doctor.filter(doctor => doctor.user.status === 1);
});

  function formattedDate(date){
        return moment(form.date).format('MMMM   D, YYYY');
    }

 const updateFormattedDate = () => {
      // Trigger the computed property update on date change
      formattedDate.value;
    };

const submit = () =>{
    form.post('/medcert')
}
</script>

<template>
    <Head title="Medical Certificate" />
    <Sidebar>
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Medical Certificate</h2> -->
            <div v-if="$page.props.flash.success" id="flash-success-message" class="absolute top-20 right-1 p-4 bg-green-100 border-s-4 border-green-500 rounded-md shadow-md">
                {{ $page.props.flash.success }}
            </div>
           

            <div v-if="$page.props.flash.error" id="flash-error-message" class=" absolute top-20 right-1 p-4 bg-red-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.error }}
            </div>
        <!-- </template> -->


        <div class="px-2 mt-5">


            <div class="flex -mx-2">
                <div class="w-full md:w-full lg:w-full px-2">
                    <!-- <img src="images/mdclogo.png" alt="">
                    <h1 class="font-bold text-2xl text-center">Mater Dei College </h1>
                    <h1 class="font-medium text-xl text-center mt-2">Cabulijan, Tubigon, Bohol </h1>
                    <h1 class="font-medium text-xl text-center mt-2">School Clinic </h1> -->
                    <div class="flex justify-center mb-2">
                        <div>
                            <img src="/images/mdclogo.png" alt="Logo" class="w-[70px] h-[70px] rounded-full object-cover">
                        </div>
                        <div class=" text-sm">
                            <h3 class="font-bold text-center text-xl text-slate-700">Mater Dei College</h3>
                            <h3 class="font-bold text-slate-700 text-center">Cabulijan, Tubigon, Bohol</h3>
                            <h3 class="font-bold text-l  text-slate-700 text-center">School Clinic</h3>
                            <h3 class="font-bold text-2xl mt-4  text-slate-700 text-center">Medical Certificate</h3>
                        </div>

                    </div>
                   <div class="inline-flex flex-col justify-center relative text-gray-500 mb-4">
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
                    <div class="h-auto">
                        <div class="relative px-4 py-10 bg-white mx-2 md:mx-0 shadow rounded-3xl sm:p-10">
                            <div class="max-w-full mx-auto">

                                <div class="divide-y divide-gray-200">
                                    <form class="" @submit.prevent="submit">
                                        <div class="py-5 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                                        <div class="text-right">
                                            Date: <input type="date" v-model="form.date"  @change="updateFormattedDate">
                                        </div>
                                         <div class="text-start">
                                           <p class="font-bold text-m">To Whom It May Concern:</p>

                                        </div>
                                            <div class="flex items-center">
                                                <h1 class="text-l font-medium ml-4">This is to certify that</h1>
                                                <input type="text" class="input" name="pat_id" id="pat_id" v-model="selectedUser.fullname" readonly >
                                                <h1 class="text-l font-medium ml-4">Age/Sex</h1>
                                                <input type="text" class="input" name="pat_id" id="pat_id" v-model="selectedUser.age" readonly >
                                               <h1 class="ml-2 mr-2 "> from </h1>  <input type="text" class="input" name="pat_id" id="pat_id" v-model="selectedUser.address" readonly >
                                            </div>

                                            <div class="flex items-center">
                                                <h1 class="text-l font-medium ml-4">Was examined and treated at Mater Dei College Clinic on <span class="border-b-2 border-gray-700" v-if="form.date"> {{ formattedDate(form.date)  }}</span> <span class="border-b-2 border-gray-700" v-else> &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span>   with the following diagnosis:  </h1>

                                            </div>
                                             <textarea type="text" class=" w-full"  v-model="form.diagnosis"></textarea>
                                            <div class="flex items-center">
                                                <h1 class="text-l font-medium ml-4">And would need for medical attention for </h1>
                                                <input type="text" class="input" v-model="form.days">
                                                <p>days barrying complication.</p>
                                            </div>
                                           <div class="flex flex-col items-end"  v-if="isDoctor">
                                                <input type="text" v-model="form.doc_name" disabled  class="input border-b-2 border-gray-300 focus:border-black outline-none mb-2">
                                                <p>Attending Physician</p>
                                            </div>
                                             <div class="flex flex-col items-end"  v-if="!isDoctor">
                                                 <select id="doctor" v-model="form.doc_id" name="doctor"  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                                    <option selected disabled >Select doctor</option>
                                                    <option v-for="doc in filteredDoctors" :key="doc.id" :value="doc.id">{{ doc.user.firstname }} {{ doc.user.lastname }}</option>
                                                </select>
                                                <p>Attending Physician</p>
                                            </div>



                                        </div>
                                        <div class="pt-4 flex items-end justify-end space-x-4">
                                            <button type="submit" class="bg-blue-500 flex justify-end items-end w-50 text-white px-4 py-3 rounded-md focus:outline-none">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

            </div>

    </Sidebar>
</template>

<style scoped>
    .input {
  outline: 0;
  border-width: 0 0 2px;
  border-color: #000;
  margin-bottom: 5;

}
.input:focus {
  border-color: green;
  outline: 1px dotted #000
}


</style>
