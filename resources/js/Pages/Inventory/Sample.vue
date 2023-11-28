<script setup>
import Sidebar from '@/Layouts/Sidebar.vue'
import { Head } from '@inertiajs/vue3'

let props = defineProps({
    inventories: Array,

})

</script>

<template>
    <Sidebar>
        <Head title="Medicine Inventory"/>

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
                        <h2 class="text-3xl font-bold text-black">Medicine Inventory</h2>
                    </div>
                </div>
            </div>

            <div class="w-full px-2 mt-5">
                <div class="h-12">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table class="min-w-max w-full table-auto">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-center">Medicine</th>
                                    <th class="py-3 px-6 text-center">Stock In</th>
                                    <th class="py-3 px-6 text-center">Stock Out</th>
                                    <th class="py-3 px-6 text-center">Stock Available</th>

                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light" >
                                <tr  class="border-b border-gray-200 hover:bg-gray-100" v-for="inv in inventories" :key="inv.id">
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ inv.medicine.name }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ inv.stock_in }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ inv.stock_out || 0}}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex items-center justify-center">
                                            <p class="font-medium">{{ inv.Available }} pc/s</p>
                                        </div>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- <div v-show="inventories.data.length < 1" class="flex flex-col w-full mt-9">
                        <h1 class="text-center text-xl text-gray-400 mb-6">No inventory found</h1>

                    </div>


                    <div class="flex justify-between">
                        <div class="mt-2" v-if="inventories.data.length > 0">Showing page {{ inventories.current_page }} of {{ inventories.last_page }}</div>
                        <Pagination v-if="inventories.data.length > 0" :links="inventories.links" class="mt-6"/>
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
