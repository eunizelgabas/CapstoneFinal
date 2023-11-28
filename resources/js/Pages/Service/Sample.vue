<script setup>
import Sidebar from '@/Layouts/Sidebar.vue'
import Modal from  '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue'
import { ref, watch, onMounted, computed } from 'vue';
import { useForm, Link, Head,router } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue';

let showConfirm = ref(false)
let showEdit = ref(false)
let showAdd = ref(false)
let selectedServiceForDelete = null
let selectedService = null
let deleteForm = useForm({});

let form = useForm({
    name: '',
    description: '',
})

let props = defineProps({
    services: Array,
    filters: Object,
    serviceCount: Number
})


function add() {
showAdd.value = true;
form.name = '';
form.description = ''
}

function edit(serv) {
    selectedService = serv
    showEdit.value = true;
    form.name = serv.name
    form.description = serv.description
}

function closeModal(){
    showConfirm.value = false;
}

function close(){
    showEdit.value = false;
}

function close2(){
    showAdd.value = false;
}

function remove(serv) {
    selectedServiceForDelete = serv
    showConfirm.value = true;
}

function deleteServ(){
    deleteForm.delete('/service/' + selectedServiceForDelete.id)
    showConfirm.value = false;
}

const submit = () =>{
    form.post('/service')
    form.name = "";
    form.description = "";
    close2();
}

const update = () =>{
    form.put('/service/' + selectedService.id)
    form.name = "";
    form.description = "";
    close();
}

let search = ref(props.filters.search);
watch(search, (value) => {
    router.get(
        "/service",
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});


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
    <Sidebar>
        <Head title="Medical Service"/>

        <section class="container px-4 py-6 mx-auto">
            <div v-if="$page.props.flash.success" id="flash-success-message" class="absolute top-20 right-1 p-4 bg-green-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.success }}
                <div class="progress-bar success"></div>
            </div>
            <div v-if="$page.props.flash.error" id="flash-error-message" class=" absolute top-20 right-1 p-4 bg-red-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.error }}
                <div class="progress-bar error"></div>
            </div>
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-x-3">
                        <h2 class="text-3xl font-bold text-black">Medical Service</h2>

                        <span class="px-3 py-1 text-xs text-blue-600 bg-blue-100 rounded-full">{{ serviceCount }} service</span>
                    </div>
                </div>
            </div>


            <div class="mt-6 md:flex md:items-center md:justify-between md:w-full">
                <div class="inline-flex overflow-hidden">
                    <div class="py-3 px-4">
                        <div class="relative max-w-xs">
                            <label for="hs-table-search" class="sr-only">Search</label>
                            <input type="search" v-model="search"  name="hs-table-search" id="hs-table-search" class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="Search service">
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center  md:mt-0">

                    <div class="flex items-center  gap-x-3">
                        <button @click="add()" class="flex items-center justify-center w-1/2 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg shrink-0 sm:w-auto gap-x-2 hover:bg-blue-600 dark:hover:bg-blue-500 dark:bg-blue-600">
                            <i class="fa-solid fa-plus"></i>
                            <span class="">Add Medical Service</span>
                        </button>
                        <Modal :show="showAdd" @close="close2">
                            <div class="p-4 sm:p-10 overflow-y-auto">
                                <div class="flex items-center justify-center ">
                                    <div class="mx-auto w-full max-w-[550px] bg-white">
                                        <form @submit.prevent = "submit">
                                            <div class="mb-5 pt-3">
                                                <label class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl">
                                                    Add Medical Service Details
                                                </label>
                                                <div class="-mx-3 flex flex-wrap">
                                                    <div class="w-full px-3">
                                                        <div class="mb-5">
                                                            <label for="name" class="text-left">Name</label>
                                                            <input type="text" v-model="form.name" name="name" id="name" placeholder=""
                                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>
                                                        <div class="mb-5">
                                                            <label for="description" class="text-left">Description</label>
                                                            <input type="text" v-model="form.description" name="description" id="description" placeholder=""
                                                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-6 flex justify-end gap-x-4">
                                                <SecondaryButton @click="close2">Cancel</SecondaryButton>
                                                <PrimaryButton type="submit" @click="submit">Save</PrimaryButton>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>

            <div class="w-full px-2 mt-3">
                <div class="h-12">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table class="min-w-max w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-center">ID</th>
                                    <th class="py-3 px-6 text-center">Name</th>
                                    <th class="py-3 px-6 text-center">Description</th>
                                    <th class="py-3 px-6 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light" >

                                <tr  class="border-b border-gray-200 hover:bg-gray-100" v-for="serv in services.data" :key="serv.id">
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ serv.id }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ serv.name }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ serv.description }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center">
                                            <div class="flex item-center justify-center">
                                                <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                    <a href="#" @click="edit(serv)" class="btn" title="Edit Category">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </a>
                                                    <Modal :show="showEdit" @close="close">
                                                        <div class="p-4 sm:p-10 overflow-y-auto">
                                                            <div class="flex items-center justify-center ">
                                                                <div class="mx-auto w-full max-w-[550px] bg-white">
                                                                    <form @submit.prevent = "update">
                                                                        <div class="mb-5 pt-3">
                                                                            <label class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl">
                                                                            Edit  Medical Service Details
                                                                            </label>
                                                                            <div class="-mx-3 flex flex-wrap">
                                                                                <div class="w-full px-3">
                                                                                    <div class="mb-5">
                                                                                        <label for="name" class="text-left">Name</label>
                                                                                        <input type="text" v-model="form.name" name="name" id="name" placeholder=""
                                                                                            class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                                                    </div>
                                                                                    <div class="mb-5">
                                                                                        <label for="description" class="text-left">Description</label>
                                                                                        <input type="text" v-model="form.description" name="description" id="description" placeholder=""
                                                                                            class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mt-6 flex justify-end gap-x-4">
                                                                            <SecondaryButton @click="close">Cancel</SecondaryButton>
                                                                            <PrimaryButton type="submit" @click="update">Save</PrimaryButton>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </Modal>
                                                </div>
                                            </div>
                                            <div class="w-4  ml-2 mr-2 transform hover:text-red-500 hover:scale-110">
                                                <a href="#" @click="remove(serv)" class="btn" title="Delete Category">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </a>
                                                <Modal :show="showConfirm" @close="closeModal">
                                                    <div class="p-4 sm:p-10 text-center overflow-y-auto">
                                                        <!-- Icon -->
                                                        <span class="mb-4 inline-flex justify-center items-center w-[62px] h-[62px] rounded-full border-4 border-red-50 bg-red-100 text-red-500">
                                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                                                            </svg>
                                                        </span>
                                                        <!-- End Icon -->

                                                        <h3 class="mb-2 text-2xl font-bold text-gray-800">
                                                            Delete Medical Service
                                                        </h3>
                                                        <p class="text-gray-500">
                                                            Are you sure you want like to delete this Medical Service?
                                                        </p>

                                                        <div class="mt-6 flex justify-center gap-x-4">
                                                            <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                                                            <DangerButton @click="deleteServ()">Delete</DangerButton>
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
                    <div v-show="services.data.length < 1" class="flex flex-col w-full mt-9">
                        <h1 class="text-center text-xl text-gray-400 mb-6">No medical service found</h1>
                        <!-- <img src="../../Components/images/no-result.png" alt="no result" class="w-[250px] opacity-25 mx-auto"> -->
                    </div>

                    <!-- Paginator -->
                    <div class="flex justify-between">
                        <div class="mt-2" v-if="services.data.length > 0">Showing page {{ services.current_page }} of {{ services.last_page }}</div>
                        <Pagination v-if="services.data.length > 0" :links="services.links" class="mt-6"/>
                    </div>
                </div>
            </div>
        </section>

    </Sidebar>
</template>

<style scoped>

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
