<script setup>
import Sidebar from '@/Layouts/Sidebar.vue'
import Modal from  '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue'
import { ref, watch, onMounted, computed } from 'vue';
import { useForm, Link, Head,router } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue';
import moment from 'moment';

let props = defineProps({
    logEntries: Object,

})


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

function formattedDate(date){
        return moment(date).format('MMMM   D, YYYY');
    }

</script>

<template>
    <Sidebar>
        <Head title="Logs"/>

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
                        <h2 class="text-3xl font-bold text-black">Logs</h2>

                        <!-- <span class="px-3 py-1 text-xs text-blue-600 bg-blue-100 rounded-full">{{ categoryCount }} category</span> -->
                    </div>
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

                                <tr  class="border-b border-gray-200 hover:bg-gray-100" v-for="logs in logEntries.data" :key="logs.id">
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{  formattedDate(logs.created_at) }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ logs.log_entry }}</p>
                                        </div>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-show="logEntries.data.length < 1" class="flex flex-col w-full mt-9">
                        <h1 class="text-center text-xl text-gray-400 mb-6">No logs found</h1>

                    </div>

                    <!-- Paginator -->
                    <div class="flex justify-between">
                        <div class="mt-2" v-if="logEntries.data.length > 0">Showing page {{ logEntries.current_page }} of {{ logEntries.last_page }}</div>
                        <Pagination v-if="logEntries.data.length > 0" :links="logEntries.links" class="mt-6"/>
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
