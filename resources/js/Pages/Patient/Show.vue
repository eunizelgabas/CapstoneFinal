<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import { Link, Head, useForm } from '@inertiajs/vue3';
    import Modal from  '@/Components/Modal.vue';
    import DangerButton from '@/Components/DangerButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import Breadcrumb from '@/Components/Breadcrumbs.vue'
    import Pagination from '@/Components/Pagination.vue'
    import moment from 'moment';
    import { ref,computed } from 'vue';
    import { onMounted } from 'vue';


    const props = defineProps({
        patient: Object,
        patientAppointments: Object,
        medicalHistory: Object,
        doctor: Object,
        form: Object,
        radiologic: Array,
        appCount: Number,
        medCount: Number
    })



    let showConfirm = ref(false)
    let show = ref(false)
    let selectedAppointmentForDelete = null
    let selectedPatient = null
    let selectedAppointment = null
    let deleteForm = useForm({});function closeModal(){
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

    function edit(patient){
        selectedPatient = patient
        show.value = true
    }
    function close(){
        show.value = false;
    }


    const isStudent = ref(props.patient.student);


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

    const crumbs = [
        {
            name: 'Dashboard',
            url: '/dashboard',
            active: false,
        },
        {
            name: 'List of Patients',
            url: '/patient',
            active: false,
        },
        {
            name:  props.patient.firstname + ' '+ props.patient.lastname  ,
            url: null,
            active: true,
        },
    ]

    let form = useForm({
        lastname: props.patient.lastname,
        firstname:props.patient.firstname,
        middlename: props.patient.middlename,
        ext_name: props.patient.ext_name,
        email:props.patient.email,
        sex:props.patient.sex,
        type: props.patient.type,
        contact_no: props.patient.contact_no,
        emergency_contact: props.patient.emergency_contact,
        dob: props.patient.dob,
        address: props.patient.address,
        student_no:  '',
        teacher_no: '',
        course: '',
    })

    if (props.patient.type === 'Student' && props.patient.student) {
        form.student_no = props.patient.student.student_no;
        form.course = props.patient.student.course;
    } else if (props.patient.type === 'Teacher' && props.patient.teacher) {
        form.teacher_no = props.patient.teacher.teacher_no;
    }
    const submit = () =>{
        form.put('/patient/ '+props.patient.id)
        show.value = false;
    }

    const radiologicData = ref(props.radiologic);

        // Method to get the image URL
        const getPicUrl = (examResults) => `/results/radiologic_results/${examResults}`;

        // On mounted, update the ref if the prop changes
        onMounted(() => {
            radiologicData.value = props.radiologic;
        });


        function extractFilename(storageLink) {
      const parts = storageLink.split('/');
      const filenameWithExtension = parts[parts.length - 1];
      const filenameParts = filenameWithExtension.split('.');
      return filenameParts[0]; // Only get the filename without extension
    }
</script>

<template>
    <Sidebar>
        <Head title="Patients"/>

        <div class="mt-5">
            <div class="flex justify-between">
                <div v-if="$page.props.flash.success" id="flash-success-message" class="absolute top-20 right-1 p-4 bg-green-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.success }}
            </div>

            <div v-if="$page.props.flash.error" id="flash-error-message" class=" absolute top-20 right-1 p-4 bg-red-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.error }}
            </div>
                <Breadcrumb :crumbs="crumbs"  />
                <button v-if="$page.props.auth.permissions.includes('edit-patient')"  @click="edit(patient)" class="px-4 py-2 mr-5 border flex gap-2 items-center bg-white hover:bg-blue-300 border-slate-200 rounded-lg text-slate-700 hover:border-slate-400 hover:text-slate-900 hover:shadow transition duration-150">
                    <i class="fa-regular fa-pen-to-square"></i>
                    <span class="">Edit Patient</span>
                </button>
                <Modal :show="show" @close="closeModal">
                    <div class="p-4 sm:p-10 overflow-y-auto">
                        <div class="flex items-center justify-center ">
                            <div class="mx-auto w-full max-w-[550px] bg-white">
                                <form @submit.prevent = "submit">
                                    <div class="mb-5 pt-3">
                                        <label class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl">
                                            Patient Details
                                        </label>
                                        <div class="-mx-3 flex flex-wrap">
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="firstname" class="text-left"> First name</label>
                                                    <input type="text" v-model="form.firstname" name="firstname" id="firstname" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="middlename" class="text-left"> Middle name</label>
                                                    <input type="text" v-model="form.middlename" name="middlename" id="middlename" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="lastname" class="text-left"> Last name</label>
                                                    <input type="text" v-model="form.lastname" name="lastname" id="lastname" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="ext_name" class="text-left"> Extension name</label>
                                                    <input type="text" v-model="form.ext_name" name="ext_name" id="ext_name" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="sex" class="text-left"> Gender</label>
                                                    <select id="sex" v-model="form.sex" name="sex" autocomplete="sex" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md">
                                                        <option selected disabled>Select Gender</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="dob" class="text-left"> Date of Birth</label>
                                                    <input type="date" v-model="form.dob" name="dob" id="dob" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="contact_no" class="text-left">Contact No</label>
                                                    <input type="number" v-model="form.contact_no" name="contact_no" id="contact_no" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2">
                                                <div class="mb-5">
                                                    <label for="emergency_contact" class="text-left">Emergency Contact No</label>
                                                    <input type="number" v-model="form.emergency_contact" name="emergency_contact" id="emergency_contact" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3">
                                                <div class="mb-5">
                                                    <label for="type" class="text-left">Patient Type</label>
                                                    <select id="type" v-model="form.type" name="type" autocomplete="type" class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md">
                                                        <option selected disabled>Select Type</option>
                                                        <option value="Student">Student</option>
                                                        <option value="Teacher">Teacher</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2" v-if="form.type === 'Student'">
                                                <div class="mb-5">
                                                    <label for="student_no" class="text-left">Student ID</label>
                                                    <input type="text" v-model="form.student_no" name="student_no" id="student_no" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 sm:w-1/2" v-if="form.type === 'Student'">
                                                <div class="mb-5">
                                                    <label for="course" class="text-left">Course</label>
                                                    <input type="text" v-model="form.course" name="course" id="course" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 " v-if="form.type === 'Teacher'">
                                                <div class="mb-5">
                                                    <label for="teacher_no" class="text-left">Teacher ID</label>
                                                    <input type="text" v-model="form.teacher_no" name="teacher_no" id="teacher_no" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                            <div class="w-full px-3 ">
                                                <div class="mb-5">
                                                    <label for="email" class="text-left">Email</label>
                                                    <input type="email" v-model="form.email" name="email" id="email" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>

                                            <div class="w-full px-3 ">
                                                <div class="mb-5">
                                                    <label for="address" class="text-left">Address</label>
                                                    <input type="address" v-model="form.address" name="address" id="address" placeholder=""
                                                        class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                        <div class="mt-6 flex justify-end gap-x-4">
                                            <SecondaryButton @click="close">Cancel</SecondaryButton>
                                            <DangerButton type="submit" @click="submit">Save</DangerButton>
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </Modal>

            </div>
        </div>
        <div class="flex flex-col lg:flex-row">
            <div class="lg:w-1/2 w-full p-4">

                <div class=" bg-white shadow-sm flex max-w-xs flex-col items-center rounded-xl border px-4 py-4 text-center md:max-w-lg md:flex-row md:items-start md:text-left">
                    <div class="mb-4 md:mr-6 md:mb-0">
                        <img v-if="patient.sex === 'Male'" class="h-56 rounded-lg object-cover md:w-56" src="/images/boy.jpg" alt="Male Image" />
                        <img v-else class="h-56 rounded-lg object-cover md:w-56" src="/images/girl.png" alt="Female Image" />
                    </div>
                    <div class="">
                        <p class="text-xl font-medium text-gray-700">{{ patient.firstname }} {{ patient.lastname }}</p>
                        <p class="mb-4 text-sm font-bold text-gray-500">{{ patient.type }} - ID No: {{ isStudent ? (patient.student ? patient.student.student_no : patient.teacher.teacher_no) : (patient.teacher ? patient.teacher.teacher_no : patient.student.student_no) }}</p>
                        <div class="flex space-x-2">
                        <div class="flex flex-col items-center rounded-xl bg-gray-100 px-4 py-2">
                            <p class="text-sm font-medium text-gray-500">Medical</p>
                            <p class="text-3xl font-medium text-gray-600">{{ medCount }}</p>
                        </div>
                        <div class="flex flex-col items-center rounded-xl bg-gray-100 px-4 py-2">
                            <p class="text-sm font-medium text-gray-500">Appointments</p>
                            <p class="text-3xl font-medium text-gray-600">{{ appCount }}</p>
                        </div>


                        </div>
                        <div class="mb-3"></div>
                        <div class="flex space-x-2">
                        <div class="w-full rounded-lg border-transparent bg-indigo-600 px-4 py-2 font-medium text-white text-center" >{{patient.status ? 'Active' : 'Inactive' }} </div>
                        </div>
                        <!-- <button class="w-full rounded-lg border-2 mt-2 bg-white px-4 py-2 font-medium text-gray-500">Message</button> -->
                    </div>
                    </div>


            </div>

            <div class="lg:w-3/4 w-full p-4">

                <div class="bg-white py-3 px-4 text-start h-full rounded-lg shadow-sm flex">

                    <div class="flex flex-col w-full">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="p-4 ">
                                <h5 class="mb-1 text-m font-bold text-gray-700">Gender</h5>
                                <p class="mt-1 mb-1 text-l text-gray-500">{{ patient.sex }}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>
                            </div>


                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold text-gray-700">Birthday</h5>
                                <p class="mt-1 mb-1 text-l text-gray-500">{{ formattedDate(patient.dob)}}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>
                            </div>

                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold  text-gray-700">Contact No</h5>
                                <p class="mt-1 mb-1 text-l text-gray-500">{{ patient.contact_no}}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>

                            </div>

                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold text-gray-700">Emergency Contact</h5>
                                <p class="mt-1 mb-1 text-l text-gray-500">{{ patient.emergency_contact}}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>

                            </div>

                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold text-gray-700">Address</h5>
                                <p class="mt-1 mb-1 text-l text-gray-500">{{patient.address}}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>

                            </div>
                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold text-gray-700">Status</h5>
                                <p class="mt-1 mb-1 text-sm uppercase px-2 py-1 w-20 text-center rounded-full  font-bold"
                                    :class="{
                                        'bg-green-200 text-green-600': patient.status == 1,
                                        'bg-red-200 text-red-600': patient.status == 0,
                                    }">{{patient.status ? 'Active' : 'Inactive' }}
                                </p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex">
            <!-- Left Card with 60% width -->
            <div class="w-3/4 p-4">
                <div class="bg-white h-full rounded-lg shadow-sm">
                    <div ref="tabs" class="p-2">
                        <div class="max-w-full mx-auto">
                            <div class="mb-4 flex space-x-4 p-2 bg-gray-50 rounded-lg shadow-md font-semibold text-gray-500">
                                <button @click="openTab = 1" :class="{ 'bg-white text-blue-500 font-semibold': openTab === 1 }" class="flex-1 py-2 px-4  rounded-md focus:outline-none focus:shadow-outline-blue transition-all duration-300">
                                    Medical Records
                                </button>
                                <button @click="openTab = 2" :class="{ 'bg-white text-blue-500 font-semibold': openTab === 2 }" class="flex-1 py-2 px-4  rounded-md focus:outline-none focus:shadow-outline-blue transition-all duration-300">
                                    Lab Results
                                </button>

                            </div>
                            <div v-show="openTab === 1" class="transition-all duration-300 bg-white p-4 ">
                                <div class=" flex justify-between">
                                        <h2 class="text-l font-semibold mb-2 text-gray-500">Medical History</h2>
                                        <Link  title="Add medical" :href="'/healthForm/create/'+ patient.id" class=" mb-2 flex items-center justify-center w-1/4 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg sm:w-auto gap-x-2 hover:bg-blue-600 dark:hover:bg-blue-500 dark:bg-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>

                                        </Link>
                                </div>
                                <div v-if="medicalHistory.data && medicalHistory.data.length > 0">
                                    <table class="w-full table-auto">
                                        <thead>
                                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                                <th class="py-2 px-2">Date</th>
                                                <th class="py-2 px-2">Patient</th>
                                                <th class="py-2 px-2">Attending Doctor</th>
                                                <th class="py-2 px-2 ">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="med in medicalHistory.data" :key="med.id">
                                                <td class="py-2 px-2 text-left whitespace-nowrap">
                                                    <p class="font-medium text-center">{{ formattedDate(med.date)}}</p >
                                                </td>
                                                <td class="py-2 px-2 text-left whitespace-nowrap">
                                                    <p class="font-medium text-center">{{ med.patient.firstname}}</p >
                                                </td>
                                                <td class="py-2 px-2">
                                                    <p class="font-medium text-center">{{ med.doctor.user.firstname }} {{ med.doctor.user.lastname }}</p >
                                                </td>
                                                <td class="py-2 px-2">
                                                    <div class="flex items-center justify-center ">

                                                        <div class="w-4 mr-2 transform hover:text-indigo-500 hover:scale-110">
                                                            <Link :href="'/healthForm/show/'+ med.id" title="Show Details">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </Link>
                                                        </div>

                                                        <!-- <div class="w-4  ml-2 mr-2 transform hover:text-red-500 hover:scale-110">
                                                            <a href="#" @click="remove(med)" class="btn" title="Delete Category">
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
                                                                        Delete Medical History
                                                                    </h3>
                                                                    <p class="text-gray-500">
                                                                        Are you sure you want like to delete this Medical History?
                                                                    </p>

                                                                    <div class="mt-6 flex justify-center gap-x-4">
                                                                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                                                                        <DangerButton @click="deleteApp()">Delete</DangerButton>
                                                                    </div>
                                                                </div>
                                                            </Modal>
                                                        </div> -->
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else>
                                    <p class="text-center text-gray-900 py-6">No medical history found</p>
                                </div>

                            </div>
                            <div v-show="openTab === 2" class="transition-all duration-300 bg-white p-4 ">
                                <div class=" flex justify-between">
                                        <h2 class="text-l font-semibold mb-2 text-gray-500">Radiologic and Laboratories Examination Results</h2>

                                    </div>
                                    <div v-if="radiologic && radiologic.length > 0">
                                        <!-- <div v-for="rad in radiologic" :key="rad.id" class="flex w-full items-center justify-between rounded-2xl bg-gray-50 p-3 shadow-3xl shadow-shadow-500 mb-5">
                                            <div class="flex items-center">
                                                <div class="">

                                                    <a :href="getPicUrl(rad.radiologic.exam_results)" target="_blank" data-lightbox="radiologic" data-title="Radiologic Image">

                                                        <img v-if="rad.radiologic && rad.radiologic.exam_results && rad.radiologic.exam_results !== ''" :src="getPicUrl(rad.radiologic.exam_results)" alt="Radiologic Image" class="aspect-square w-[40%] object-contain mb-4 text-black">
                                                    </a>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-base font-medium text-gray-700 ">
                                                        {{ formattedDate( rad.date) }}
                                                    </p>
                                                    <p class="text-base font-medium text-gray-700 ">
                                                        {{ rad.radiologic.firstname }}
                                                    </p>

                                                </div>
                                            </div>
                                        </div> -->
                                        <div class="w-full lg:w-full p-3" v-for="rad in radiologic" :key="rad.id">
                                            <div class="flex flex-col lg:flex-row rounded overflow-hidden h-auto lg:h-32 border shadow shadow-lg">
                                                <a :href="getPicUrl(rad.radiologic.exam_results)" target="_blank" data-lightbox="radiologic" data-title="Radiologic Image">
                                                    <img v-if="rad.radiologic && rad.radiologic.exam_results && rad.radiologic.exam_results !== ''" :src="getPicUrl(rad.radiologic.exam_results)" alt="Radiologic Image" class="block h-auto w-full lg:w-48 flex-none bg-cover" >
                                                </a>
                                                <div class="bg-white rounded-b lg:rounded-b-none lg:rounded-r p-4 flex flex-col justify-between leading-normal">
                                                <div class="text-black font-bold text-xl mt-5 leading-tight"> {{ rad.patient.firstname }}  {{ rad.patient.lastname }}</div>
                                                <p class="text-grey-darker text-base"> {{ formattedDate( rad.date) }}</p>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else>
                                    <p class="text-center text-gray-900 py-6">No radiologic results found</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card with 40% width -->
            <div class="w-1/2 p-4">

            <div class="relative flex flex-col items-center rounded-[10px] border-[1px] border-gray-200 w-[576px] mx-auto p-4 bg-white bg-clip-border shadow-md shadow-[#F3F3F3]">
                <div class="flex items-center justify-between rounded-t-3xl  w-full mb-2">
                    <div class="text-lg font-bold text-navy-700 ">
                       Appointment History
                    </div>
                    <Link title="Add appointment" :href="'/appointment/create-for-patient2/'+ patient.id" class="flex items-center justify-center w-1/4 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg sm:w-auto gap-x-2 hover:bg-blue-600 dark:hover:bg-blue-500 dark:bg-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                    </Link>

                </div>

                <div v-if="patientAppointments.data && patientAppointments.data.length > 0">
                    <!-- <div class="flex h-full w-full items-start justify-between rounded-md border-[1px] border-indigo-200 p-5 mb-2 " v-for="appointment in patientAppointments.data" :key="appointment.id">
                    <div class="flex items-center gap-3">

                        <div class="flex flex-col">
                        <h5 class="text-base font-bold text-gray-700 ">
                           {{ appointment.service.name }}
                        </h5>
                        <p class="mt-1 text-sm font-normal text-gray-600">
                           {{ formattedDate(appointment.date) }}
                        </p>

                        </div>
                    </div>
                    <div class="mt-1 flex items-center justify-center text-gray-700 ">
                        <Link :href="'/appointment/show/'+appointment.id" class="linear px-4 py-2 text-base font-medium text-brand-500 transition duration-200 hover:text-indigo-700 text-indigo-500">
                        See Details
                        </Link>
                    </div>
                </div> -->
                    <div class="  mt-2 " v-for="appointment in patientAppointments.data" :key="appointment.id">
                        <div class=" ">
                            <div class="relative px-7 py-6 bg-white ring-1 ring-gray-900/5 rounded-lg leading-none flex items-top justify-start space-x-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-10 text-indigo-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                </svg>

                                <div class="space-y-2">
                                <p class="text-slate-800"> {{ appointment.doctor.user.firstname }} {{ appointment.doctor.user.lastname }} - {{ appointment.service.name }} | {{ formattedDate(appointment.date) }}  </p>
                                <Link :href="'/appointment/show/'+appointment.id" class="block text-indigo-400 group-hover:text-slate-800 transition duration-200" target="_blank">See Details →</Link>
                                </div>
                            </div>
                        </div>
                        </div>
                </div>

                <div v-else>
                    <p class="text-center text-gray-900 py-6">No appointment history found</p>
                </div>


            </div>

        </div>
        </div>
    </Sidebar>
</template>


<style scoped>
 .vertical-line {
            height: 40px; /* Adjust the height of the line as needed */
            border-left: 1px solid #000; /* Set the color and thickness of the line */
            margin-left: 0.5px; /* Adjust the left margin as needed */
        }
</style>
