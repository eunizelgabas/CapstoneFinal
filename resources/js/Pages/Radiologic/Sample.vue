<script setup>
import Sidebar from '@/Layouts/Sidebar.vue';
import { ref,  computed , watch} from 'vue';
import {  useForm, Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

let props = defineProps({

        patient: Object,
        doctor: Array,
})
let form = useForm({
    exam_results: '',
    fullname: `${props.patient.firstname} ${props.patient.lastname}`,
    form_id: '',
    gender: props.patient.sex,
    address: props.patient.address,
    dob: props.patient.dob,
    doc_id: '',
    pat_id: props.patient.id,
    date: '',
    vaccine: '',
    course: null,
    test:calculateAge(props.patient.dob)
})

if (props.patient && props.patient.type === 'Student') {
        form.course = props.patient.student.course;
        }

const image = ref(null);

    const handleFileChange = (event) => {
      const file = event.target.files[0];
      form.exam_results = file;
      const reader = new FileReader();
      reader.onload = (e) => (image.value = e.target.result);
      reader.readAsDataURL(file);
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
    const submit = () =>{
        form.post('/radiologic')


    }
</script>

<template>
    <Sidebar>
        <form @submit,prevent="submit">
        <div class="py-2 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-3">

<div class="sm:col-span-1">
    <label for="firstname" class="block text-sm font-medium leading-6 text-gray-900">Fullname</label>
    <div class="mt-2">
    <input type="text" name="firstname" id="firstname" readonly :value="form.fullname" autocomplete="firstname" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    <div class="text-sm text-red-500 italic" ></div>
    </div>
</div>
<div class="sm:col-span-1">
    <label for="age" class="block text-sm font-medium leading-6 text-gray-900">Age</label>
    <div class="mt-2">
    <input type="text" :value="calculateAge(form.dob)" readonly name="age" id="age" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    <div class="text-sm text-red-500 italic" ></div>
    </div>
</div>
<div class="sm:col-span-1">
    <label for="gender" class="block text-sm font-medium leading-6 text-gray-900">Sex</label>
    <div class="mt-2">
    <input type="text" :value="form.gender" readonly  name="gender" id="gender" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    <div class="text-sm text-red-500 italic" ></div>
    </div>
</div>
<div class="sm:col-span-1">
    <label for="address" class="block text-sm font-medium leading-6 text-gray-900">Address</label>
    <div class="mt-2">
    <input type="text" :value="form.address" readonly name="address" id="address" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    <div class="text-sm text-red-500 italic" ></div>
    </div>
</div>
<div class="sm:col-span-1">
    <label for="course" class="block text-sm font-medium leading-6 text-gray-900">Course</label>
    <div class="mt-2">
    <input type="text" :value="form.course"   readonly name="course" id="course" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    <div class="text-sm text-red-500 italic" ></div>
    </div>
</div>
<div class="sm:col-span-1">
    <label for="vaccine" class="block text-sm font-medium leading-6 text-gray-900">Covid Vaccine/Booster</label>
    <div class="mt-2">
    <input type="text" v-model="form.vaccine"  name="vaccine" id="vaccine" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    <div class="text-sm text-red-500 italic" ></div>
    </div>
</div>
<div class="sm:col-span-1">
    <label for="date" class="block text-sm font-medium leading-6 text-gray-900">Date</label>
    <div class="mt-2">
    <input type="date" v-model="form.date"  name="date" id="date" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    <div class="text-sm text-red-500 italic" ></div>
    </div>
</div>
<div class="sm:col-span-1" >
    <label for="doctor" class="block text-sm font-medium leading-6 text-gray-900">Doctor</label>
    <div class="mt-2">
    <select id="doctor" v-model="form.doc_id" name="doctor"  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
        <option selected disabled >Select doctor</option>
        <option v-for="doc in doctor" :key="doc.id" :value="doc.id">{{ doc.user.firstname }} {{ doc.user.lastname }}</option>
    </select>
    <div class="text-sm text-red-500 italic" v-if="form.errors.doc_id">{{ form.errors.doc_id }}</div>
</div>

</div>
</div>
        <div class="w-full relative border-2 mt-5 border-gray-300 border-dashed rounded-lg p-6" id="dropzone">
            <input type="file" @change="handleFileChange" class="absolute inset-0 w-full h-full opacity-0 z-50" />
            <div class="text-center">
                <!-- <img class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt=""> -->
                <img v-if="image" id="image" class=" mx-auto w-[100px] h-[100px] " :src="image" />

                <img v-else class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="">
                <h3  class="mt-2 text-sm font-medium text-gray-900">
                    <label for="file-upload" class="relative cursor-pointer">
                        <span>Drag and drop</span>
                        <span class="text-indigo-600"> or browse </span>
                        <span>to upload</span>
                        <input id="file-upload" @change="handleFileChange"  name="file-upload" type="file" class="sr-only">
                    </label>
                </h3>
                <p  class="mt-1 text-xs text-gray-500">
                    PNG, JPG, PDF up to 10MB
                </p>
            </div>


        </div>
        <PrimaryButton type="submit" @click="submit">Save</PrimaryButton>
    </form>
    </Sidebar>
</template>
