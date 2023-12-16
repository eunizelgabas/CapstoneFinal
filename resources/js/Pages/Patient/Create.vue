<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';

    import {  Head} from '@inertiajs/vue3';
    import { useForm } from '@inertiajs/vue3';

    let form = useForm({
        lastname: '',
        firstname:'',
        middlename: '',
        ext_name: '',
        email:'',
        sex:'',
        type: '',
        contact_no: '',
        emergency_contact: '',
        dob: '',
        address: '',
        student_no: '',
        teacher_no:'',
        status: '',
        course:'',

    })

    let props = defineProps({
       patient:Array

    })

    function toggleFields() {
        if (form.type === 'Student') {
            form.student_no = '';
            form.course = '';

        } else if (form.type === 'Teacher') {
            form.teacher_no = '';

        }
    }


    const submit = () =>{
        form.post('/patient')
    }

</script>

<template>
    <Head title="Create Patient"/>
    <Sidebar>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Patient</h2>
        </template>
        <div>
            <div class="w-full mt-10 mx-auto px-4 ">
                <form @submit.prevent="submit">
                    <div class="space-y-6">
                        <div class="block pl-12 font-semibold text-xl self-start text-gray-700">
                            <h1 class="leading-relaxed">Patient Details Form</h1>
                            <hr>
                          </div>
                      <div class="border-b border-gray-900/10 pb-12">

                        <div class=" px-12 py-2 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-3 mx-auto">
                            <div class="sm:col-span-1">
                                <label for="firstname" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
                                <div class="mt-2">
                                <input type="text" v-model="form.firstname" name="firstname" id="firstname" autocomplete="firstname" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.firstname">{{ form.errors.firstname }}</div>
                                </div>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="middlename" class="block text-sm font-medium leading-6 text-gray-900">Middle name</label>
                                <div class="mt-2">
                                <input type="text" v-model="form.middlename" name="middlename" id="middlename" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.middlename">{{ form.errors.middlename }}</div>
                                </div>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="lastname" class="block text-sm font-medium leading-6 text-gray-900">Last name</label>
                                <div class="mt-2">
                                <input type="text" v-model="form.lastname" name="lastname" id="lastname" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.lastname">{{ form.errors.lastname }}</div>
                                </div>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="ext_name" class="block text-sm font-medium leading-6 text-gray-900">Extension name</label>
                                <div class="mt-2">
                                <input id="ext_name" v-model="form.ext_name" name="ext_name" type="text" autocomplete="ext_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.ext_name">{{ form.errors.ext_name }}</div>
                                </div>
                            </div>

                            <div class="m:col-span-1">
                                <label for="sex" class="block text-sm font-medium leading-6 text-gray-900">Gender</label>
                                <div class="mt-2">
                                <select id="sex" v-model="form.sex" name="sex" autocomplete="sex" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    <option selected disabled>Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <div class="text-sm text-red-500 italic" v-if="form.errors.gender">{{ form.errors.gender }}</div>
                                </div>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="dob" class="block text-sm font-medium leading-6 text-gray-900">Date of Birth</label>
                                <div class="mt-2">
                                <input id="dob" v-model="form.dob" name="dob" type="date" autocomplete="dob" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.dob">{{ form.errors.dob }}</div>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="contact_no" class="block text-sm font-medium leading-6 text-gray-900">Contact No</label>
                                <div class="mt-2">
                                <input id="contact_no" v-model="form.contact_no" name="contact_no" type="number" autocomplete="contact_no" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.contact_no">{{ form.errors.contact_no }}</div>
                                </div>
                            </div>

                            <!-- <div class="sm:col-span-1">
                                <label for="emergency_contact" class="block text-sm font-medium leading-6 text-gray-900">Emergency Contact No</label>
                                <div class="mt-2">
                                <input id="emergency_contact" v-model="form.emergency_contact" name="emergency_contact" type="number" autocomplete="emergency_contact" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.emergency_contact">{{ form.errors.emergency_contact }}</div>
                                </div>
                            </div> -->

                            <div class="m:col-span-1">
                                <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Type</label>
                                <div class="mt-2">
                                <select id="type" v-model="form.type" name="type" @change="toggleFields" autocomplete="type" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                                    <option selected disabled>Select Type</option>
                                    <option value="Student">Student</option>
                                    <option value="Teacher">Teacher</option>
                                </select>
                                <div class="text-sm text-red-500 italic" v-if="form.errors.type">{{ form.errors.type }}</div>
                                </div>
                            </div>


                            <div class="sm:col-span-1" v-if="form.type === 'Student'">
                                <label for="student_no" class="block text-sm font-medium leading-6 text-gray-900">Student ID</label>
                                <div class="mt-2">
                                <input id="student_no" v-model="form.student_no" name="student_no" type="text" autocomplete="student_no" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.student_no">{{ form.errors.student_no }}</div>
                                </div>
                            </div>

                            <div class="sm:col-span-1" v-if="form.type === 'Teacher'">
                                <label for="teacher_no" class="block text-sm font-medium leading-6 text-gray-900">Teacher ID</label>
                                <div class="mt-2">
                                <input id="teacher_no" v-model="form.teacher_no" name="teacher_no" type="text" autocomplete="teacher_no" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.teacher_no">{{ form.errors.teacher_no }}</div>
                                </div>
                            </div>

                            <!-- <div class="sm:col-span-1">
                                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                                <div class="mt-2">
                                <input id="email" v-model="form.email" name="email" type="email" autocomplete="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.email">{{ form.errors.email }}</div>
                                </div>
                            </div> -->
                            <div class="sm:col-span-2">
                                <label for="address" class="block text-sm font-medium leading-6 text-gray-900">Address</label>
                                <div class="mt-2">
                                <input id="address" v-model="form.address" name="address" type="text" autocomplete="address" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <div class="text-sm text-red-500 italic" v-if="form.errors.address">{{ form.errors.address }}</div>
                                </div>
                            </div>

                        </div>
                      </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6 mb-3">
                      <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                      <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                    </div>
                  </form>
            </div>
        </div>
    </Sidebar>

</template>
