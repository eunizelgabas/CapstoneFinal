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
let selectedCategoryForDelete = null
let selectedCategory = null
let deleteForm = useForm({});

let form = useForm({
    name: '',

})
let props = defineProps({
    medcategories: Object,
    filters:Object,
    errors: null,
    flash: Object,
    categoryCount:Number
})

function add() {
showAdd.value = true;
form.name = '';
}

function edit(category) {
    selectedCategory = category
    showEdit.value = true;
    form.name = category.name
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

function remove(category) {
    selectedCategoryForDelete = category
    showConfirm.value = true;
}

function deleteCat(){
    deleteForm.delete('/category/' + selectedCategoryForDelete.id)
    showConfirm.value = false;
}

const submit = () =>{
    form.post('/category')
    form.name = "";
    close2();
}

const update = () =>{
    form.put('/category/' + selectedCategory.id)
    form.name = "";
    close();
}

let search = ref(props.filters.search);
watch(search, (value) => {
    router.get(
        "/category",
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
        <Head title="Medicine Category"/>

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
                        <h2 class="text-3xl font-bold text-black">Log Entry</h2>

                        <!-- <span class="px-3 py-1 text-xs text-blue-600 bg-blue-100 rounded-full">{{ categoryCount }} category</span> -->
                    </div>
                </div>
            </div>


            <div class="mt-6 md:flex md:items-center md:justify-between md:w-full">
                <div class="inline-flex overflow-hidden">
                    <div class="py-3 px-4">
                        <div class="relative max-w-xs">
                            <label for="hs-table-search" class="sr-only">Search</label>
                            <input type="search" v-model="search"  name="hs-table-search" id="hs-table-search" class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="Search category">
                            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center  md:mt-0">


                </div>
            </div>

            <div class="w-full px-2 mt-3">
                <div class="h-12">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table class="min-w-max w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">

                                    <th class="py-3 px-6 text-center">Timestamp</th>
                                    <th class="py-3 px-6 text-center">Log Entry</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light" >
<!--
                                <tr  class="border-b border-gray-200 hover:bg-gray-100" v-for="category in medcategories.data" :key="category.id">

                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ category.name }}</p>
                                        </div>
                                    </td>

                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                    <!-- <div v-show="medcategories.data.length < 1" class="flex flex-col w-full mt-9">
                        <h1 class="text-center text-xl text-gray-400 mb-6">No medicine category found</h1>

                    </div>


                    <div class="flex justify-between">
                        <div class="mt-2" v-if="medcategories.data.length > 0">Showing page {{ medcategories.current_page }} of {{ medcategories.last_page }}</div>
                        <Pagination v-if="medcategories.data.length > 0" :links="medcategories.links" class="mt-6"/>
                    </div> -->
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
