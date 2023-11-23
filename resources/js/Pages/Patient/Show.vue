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

    const props = defineProps({
        patient: Object,
        patientAppointments: Object,
        medicalHistory: Object,
        doctor: Object,
        form: Object
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
                <button @click="edit(patient)" class="px-4 py-2 mr-5 border flex gap-2 items-center bg-white hover:bg-blue-300 border-slate-200 rounded-lg text-slate-700 hover:border-slate-400 hover:text-slate-900 hover:shadow transition duration-150">
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
            <div class="lg:w-1/4 w-full p-4">

                <div class="bg-white h-full rounded-lg shadow-sm text-center flex flex-col items-center justify-center">
                    <div class="flex items-center justify-center w-20 h-20 mx-auto rounded-full p-4 mt-4"
                        :class="{
                            'bg-blue-200 text-blue-600': patient.sex == 'Male',
                            'bg-red-200 text-red-600': patient.sex == 'Female',
                        }">
                        <div class="w-12 h-12 flex justify-center items-center rounded-full uppercase font-bold text-2xl">{{ patientInitials }}</div>
                    </div>
                    <div class="text-sm mt-2">
                        <h1 class="font-bold text-xl text-gray-900">{{ patient.firstname }} {{ patient.lastname }}</h1>
                        <p>{{ patient.email }}</p>
                    </div>
                    <div class="text-center mt-3">
                        <div class="flex items-center justify-between">
                            <div class="text-center ml-8">
                                <p class="font-bold text-zinc-700">345</p>
                                <p class="text-sm font-semibold text-zinc-700">Appointments</p>
                            </div>
                            <div class="border-l border-bold border-gray-950 h-6 mx-6 "></div>
                            <div class="text-center mr-8">
                                <p class="font-bold text-zinc-700">200k</p>
                                <p class="text-sm font-semibold text-zinc-700">Medical</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-5">
                        <button class="flex justify-center items-center w-full px-4 py-2 mb-3 border gap-2 border-slate-200 rounded-lg text-slate-700 hover:border-slate-400 hover:text-slate-900 hover:shadow transition duration-150">
                            <p class="font-bold text-zinc-700">{{ patient.type }}</p>
                            <span class="font-bold text-zinc-700">
                                ID No: {{ isStudent ? (patient.student ? patient.student.student_no : patient.teacher.teacher_no) : (patient.teacher ? patient.teacher.teacher_no : patient.student.student_no) }}
                            </span>

                        </button>
                    </div>
                </div>

            </div>

            <div class="lg:w-3/4 w-full p-4">

                <div class="bg-white py-3 px-4 text-start h-full rounded-lg shadow-sm flex">

                    <div class="flex flex-col w-full">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="p-4 ">
                                <h5 class="mb-1 text-m font-bold">Gender</h5>
                                <p class="mt-1 mb-1 text-lg text-gray-600">{{ patient.sex }}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>
                            </div>


                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold">Birthday</h5>
                                <p class="mt-1 mb-1 text-lg text-gray-600">{{ formattedDate(patient.dob)}}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>
                            </div>

                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold">Contact No</h5>
                                <p class="mt-1 mb-1 text-lg text-gray-600">{{ patient.contact_no}}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>

                            </div>

                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold">Emergency Contact</h5>
                                <p class="mt-1 mb-1 text-lg text-gray-600">{{ patient.emergency_contact}}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>

                            </div>

                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold">Address</h5>
                                <p class="mt-1 mb-1 text-lg text-gray-600">{{patient.address}}</p>
                                <div class="border-b-2 mt-1 border-gray-100"></div>

                            </div>
                            <div class="p-4">
                                <h5 class="mb-1 text-m font-bold">Status</h5>
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
            <div class="w-3/5 p-4">
                <div class="bg-white h-full rounded-lg shadow-sm">
                    <div ref="tabs" class="p-2">
                        <div class="max-w-full mx-auto">
                            <div class="mb-4 flex space-x-4 p-2 bg-gray-50 rounded-lg shadow-md font-semibold text-gray-500">
                                <button @click="openTab = 1" :class="{ 'bg-white text-blue-500 font-semibold': openTab === 1 }" class="flex-1 py-2 px-4  rounded-md focus:outline-none focus:shadow-outline-blue transition-all duration-300">
                                    Appointment History
                                </button>
                                <button @click="openTab = 2" :class="{ 'bg-white text-blue-500 font-semibold': openTab === 2 }" class="flex-1 py-2 px-4  rounded-md focus:outline-none focus:shadow-outline-blue transition-all duration-300">
                                    Medical Records
                                </button>

                            </div>

                            <div v-show="openTab === 1" class="transition-all duration-300 bg-white p-4">
                                <div class="flex justify-between">
                                    <h2 class="text-l font-semibold mb-2 text-gray-500">Appointment History</h2>
                                    <Link :href="'/appointment/create/'+ patient.id" class="flex items-center justify-center w-1/4 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg sm:w-auto gap-x-2 hover:bg-blue-600 dark:hover:bg-blue-500 dark:bg-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>

                                    </Link>
                                </div>

                                <div class="max-w-2xl mx-auto mt-2">
                                    <div v-if="patientAppointments.data && patientAppointments.data.length > 0">
                                        <div class="flex gap-3 mb-2 bg-white border border-gray-300 rounded-xl overflow-hidden items-center justify-start" v-for="appointment in patientAppointments.data" :key="appointment.id">
                                        <div class="flex flex-col gap-2 px-4 py-2">
                                            <div class="text-center mt-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="text-center ml-8">
                                                        <p class="font-bold text-xl text-zinc-700">{{ formattedDate(appointment.date)}}</p>
                                                        <p class="text-sm font-semibold text-zinc-700">{{ formatTimeToAMPM(appointment.time) }}
                                                        <span class="text-xs px-2 py-1 rounded-full " :class="{
                                                                'bg-blue-200 text-blue-600': appointment.status == 'Pending',
                                                                'bg-red-200 text-red-600':appointment.status == 'Cancelled',
                                                                'bg-green-200 text-green-600': appointment.status =='Accepted',
                                                            }">
                                                            {{ appointment.status }}
                                                        </span></p>
                                                    </div>
                                                    <div class="border-l border-bold border-gray-500 h-8 mx-6 "></div>
                                                    <div class="text-center mr-6">
                                                        <p class="text-sm text-zinc-700">Medical Service</p>
                                                        <p class="text-xl font-semibold text-zinc-700"> {{ appointment.service.name }}</p>
                                                    </div>
                                                    <div class="border-l border-bold border-gray-500 h-8 mx-4 "></div>
                                                    <div class="text-center mr-8">
                                                        <p class=" text-sm text-zinc-700">Doctor</p>
                                                        <p class="text-m font-semibold text-zinc-700"> {{ appointment.doctor.user.firstname }}  {{ appointment.doctor.user.lastname }}</p>
                                                    </div>
                                                    <div class="text-center ml-auto">
                                                        <p class=" text-sm text-zinc-700">Actions</p>
                                                        <div class="flex item-center ">

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

                                            <span class="flex items-center justify-start text-gray-500">

                                            </span>

                                        </div>

                                    </div>
                                    <Pagination :links="patientAppointments.links" class="mt-6 flex justify-center"/>
                                    </div>

                                    <div v-else>
                                        <p class="text-center text-gray-900 py-6">No appointment found</p>
                                    </div>



                                </div>
                            </div>

                            <div v-show="openTab === 2" class="transition-all duration-300 bg-white p-4 ">
                                <div class=" flex justify-between">
                                        <h2 class="text-l font-semibold mb-2 text-gray-500">Medical History</h2>
                                        <Link :href="'/healthForm/create/'+ patient.id" class=" mb-2 flex items-center justify-center w-1/4 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg sm:w-auto gap-x-2 hover:bg-blue-600 dark:hover:bg-blue-500 dark:bg-blue-600">
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
                                                        <div class="flex items-center ">

                                                            <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                                <Link :href="'/healthForm/show/'+ med.id" title="Show Details">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                </Link>
                                                            </div>

                                                            <div class="w-4  ml-2 mr-2 transform hover:text-red-500 hover:scale-110">
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
                                                            </div>
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


                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card with 40% width -->
            <div class="w-2/5 p-4">
                <div class="bg-white h-full rounded-lg shadow-sm px-4 py-2">
                    <div class="flex justify-between items-center">
                        <h2 class="font-bold">Lab Results</h2>

                        <div class="flex items-center">
                            <Link href="#" class="flex items-center justify-center w-1/2 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg sm:w-auto gap-x-2 hover:bg-blue-600 dark:hover:bg-blue-500 dark:bg-blue-600">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_3098_154395)">
                                    <path d="M13.3333 13.3332L9.99997 9.9999M9.99997 9.9999L6.66663 13.3332M9.99997 9.9999V17.4999M16.9916 15.3249C17.8044 14.8818 18.4465 14.1806 18.8165 13.3321C19.1866 12.4835 19.2635 11.5359 19.0351 10.6388C18.8068 9.7417 18.2862 8.94616 17.5555 8.37778C16.8248 7.80939 15.9257 7.50052 15 7.4999H13.95C13.6977 6.52427 13.2276 5.61852 12.5749 4.85073C11.9222 4.08295 11.104 3.47311 10.1817 3.06708C9.25943 2.66104 8.25709 2.46937 7.25006 2.50647C6.24304 2.54358 5.25752 2.80849 4.36761 3.28129C3.47771 3.7541 2.70656 4.42249 2.11215 5.23622C1.51774 6.04996 1.11554 6.98785 0.935783 7.9794C0.756025 8.97095 0.803388 9.99035 1.07431 10.961C1.34523 11.9316 1.83267 12.8281 2.49997 13.5832" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_3098_154395">
                                    <rect width="20" height="20" fill="white"/>
                                    </clipPath>
                                    </defs>
                                </svg>

                                <span>Add file</span>
                            </Link>
                        </div>
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
