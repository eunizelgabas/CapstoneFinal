<script setup>
import Sidebar from '@/Layouts/Sidebar.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    yearlyReport: Object,
    currentYear: Number,
    currentMonth: String
})


</script>

<template>
    <Head title="Dispense Report"/>
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
                                <h3 class="font-bold text-slate-700 text-center text-lg mt-6">Annual Medicine Dispense Report  </h3>
                                  <h3 class="font-medium text-slate-700">For Period January 1, {{currentYear}}  to December 31, {{currentYear}} </h3>
                            </div>
                        </div>

                    </div>
                    <div>

                        <div class="relative flex  w-full flex-col rounded-[10px] border-[1px] border-gray-200 bg-white bg-clip-border shadow-md shadow-[#F3F3F3] ">
                            <div class="flex h-fit w-full items-center justify-between rounded-t-2xl bg-white px-4 pb-[20px] pt-4 shadow-2xl shadow-gray-100">
                                <h4 class="text-lg font-bold text-navy-700 ">
                                    Most Dispense Medicine
                                </h4>

                            </div>
                            <div class="w-full overflow-x-scroll px-4 md:overflow-x-hidden">
                                <table role="table" class="w-full min-w-[500px] overflow-x-scroll">
                                    <thead>
                                        <tr role="row">
                                            <th colspan="1" role="columnheader" title="Toggle SortBy" style="cursor: pointer">
                                                <div class="flex items-center justify-between pb-2 pt-4 text-start uppercase tracking-wide text-gray-600 sm:text-xs lg:text-xs">
                                                    Medicine Name
                                                </div>
                                            </th>
                                            <th colspan="1" role="columnheader" title="Toggle SortBy" style="cursor: pointer">
                                                <div class="flex items-center justify-between pb-2 pt-4 text-start uppercase tracking-wide text-gray-600 sm:text-xs lg:text-xs">
                                                    Total Dispense
                                                </div>
                                            </th>
                                            <th colspan="1" role="columnheader" title="Toggle SortBy" style="cursor: pointer">
                                                <div class="flex items-center justify-between pb-2 pt-4 text-start uppercase tracking-wide text-gray-600 sm:text-xs lg:text-xs">
                                                    Qty
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody role="rowgroup" class="px-4">
                                        <tr role="row" v-for="(item, index) in yearlyReport" :key="index">
                                            <td class="py-3 text-sm" role="cell">
                                                <div class="flex items-center gap-2">
                                                    <div class="h-[30px] w-[30px] rounded-full">

                                                    </div>
                                                    <p
                                                    class="text-sm font-medium text-navy-700"
                                                    >
                                                {{ item.medicine.name }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="py-3 text-sm" role="cell">
                                                <p class="text-md font-medium text-gray-600">
                                                {{ item.total_dispense }}
                                                </p>
                                            </td>
                                            <td class="py-3 text-sm" role="cell">
                                                 <p class="text-md font-medium text-gray-600">
                                                {{ item.total_qty }}
                                                </p>
                                            </td>
                                        </tr>


                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </Sidebar>
</template>
