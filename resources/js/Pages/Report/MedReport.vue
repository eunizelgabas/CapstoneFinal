<script setup>
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    monthlyAppointment: Object,
    currentYear: Number,
    currentMonth: String
})


</script>

<template>
    <Sidebar>
        <div class="w-full px-5 py-4">
            <div class="h-12">
                <div class=" overflow-hidden shadow-sm sm:rounded-lg">

                    <div class="mb-7">
                        <div class="flex justify-center mb-2">
                            <div>
                                <img src="/images/mdclogo.png" alt="Logo" class="w-[70px] h-[70px] rounded-full object-cover">
                            </div>
                            <div class="text-sm text-center mt-5">
                                <h3 class="font-bold text-slate-700">Mater Dei College Clinic</h3>
                                <h3 class="font-bold text-slate-700">Cabulijan, Tubigon, Bohol</h3>
                                <h3 class="font-bold text-slate-700 text-center text-lg mt-6">Annual Medical Report Form </h3>
                                  <h3 class="font-medium text-slate-700">For Period January 1, {{currentYear}}  to December 31, {{currentYear}} </h3>
                            </div>
                        </div>

                    </div>
                    <div>
                        <!-- <h1 class="mb-3 text-lg"> Name of Establishment : <u class="text-lg"> Mater Dei College</u></h1>
                        <h1 class="mb-3 text-lg">Address: <u class="text-lg">Cabulijan, Tubigon, Bohol</u></h1> -->
                       
                    </div>
                    
                </div>
            </div>
        </div>
    </Sidebar>
</template>
