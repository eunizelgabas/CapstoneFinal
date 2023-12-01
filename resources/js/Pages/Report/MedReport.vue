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
                                <h3 class="font-bold text-slate-700 text-center">Total Appointment Chart for {{ currentMonthName }} {{ currentYear }}</h3>
                            </div>
                        </div>

                    </div>

            </div>
        </div>
    </Sidebar>
</template>
