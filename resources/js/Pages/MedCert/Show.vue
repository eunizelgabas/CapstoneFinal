<script setup>
    import Sidebar from '@/Layouts/Sidebar.vue';
    import Modal from  '@/Components/Modal.vue';
    import DangerButton from '@/Components/DangerButton.vue';
    import SecondaryButton from '@/Components/SecondaryButton.vue';
    import Pagination from '@/Components/Pagination.vue'
    import { ref, watch, onMounted, computed, reactive   } from 'vue';
    import { useForm, Link, Head,router } from '@inertiajs/vue3';
    import moment from 'moment';


let props = defineProps({
        medcert: Object  ,
        age: Number,
})


let form = useForm({
  date: props.medcert.date,
  pat_id: '',
  diagnosis: props.medcert.diagnosis,
  doc_id: '',
  days: props.medcert.days,

});


    function formattedDate(date){
        return moment(date).format('MMMM   D, YYYY');
    }

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
    <Head title="Medical Certificate" />
    <Sidebar>
        <template #header>
            <!-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">Medical Certificate</h2> -->
            <div v-if="$page.props.flash.success" id="flash-success-message" class="absolute top-20 right-1 p-4 bg-green-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.success }}
            </div>

            <div v-if="$page.props.flash.error" id="flash-error-message" class=" absolute top-20 right-1 p-4 bg-red-300 border border-gray-300 rounded-md shadow-md">
                {{ $page.props.flash.error }}
            </div>

            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight"> View Medical Certificate</h2>
                <div class="flex-justify-between">
                    <a as="button" :href="'/medcert/pdf/' + medcert.id" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fa-solid fa-receipt" style="color: #fffff;"></i> Export PDF
                    </a>

                </div>
            </div>
        </template>


        <div class="px-2 mt-5">
            <div class="flex -mx-2">
                <div class="w-full md:w-full lg:w-full px-2">
                    <div class="flex justify-center mb-2">
                        <div>
                            <img src="/images/mdclogo.png" alt="Logo" class="w-[70px] h-[70px] rounded-full object-cover">
                        </div>
                        <div class=" text-sm">
                            <h3 class="font-bold text-center text-xl text-slate-700">Mater Dei College</h3>
                            <h3 class="font-bold text-slate-700 text-center">Cabulijan, Tubigon, Bohol</h3>
                            <h3 class="font-bold text-l  text-slate-700 text-center">School Clinic</h3>
                            <h3 class="font-bold text-2xl mt-4  text-slate-700 text-center">Medical Certificate</h3>
                        </div>

                    </div>

                    <div class="h-auto">
                        <div class="relative px-4 py-10 bg-white mx-2 md:mx-0 shadow rounded-3xl sm:p-10">
                            <div class="max-w-full mx-auto">
                                <div class="divide-y divide-gray-200">

                                        <div class="py-5 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">

                                         <div class="text-start">
                                           <p class="font-bold text-m">To Whom It May Concern:</p>

                                        </div>
                                            <div class="flex items-center">
                                                <h1 class="text-l font-medium ml-4">This is to certify that</h1>
                                                <h1 type="text" class="input" name="pat_id" id="pat_id" readonly >{{medcert.patient.firstname}} {{medcert.patient.lastname}}</h1>
                                                <h1 class="text-l font-medium ml-4">Age/Sex</h1>
                                                <h1 type="text" class="input" name="pat_id" id="pat_id" readonly >{{ age }} / {{medcert.patient.sex}}</h1>
                                               <h1 class="ml-2 mr-2 "> from <span class="input">{{medcert.patient.address}}</span>.</h1>
                                            </div>

                                            <div class="flex items-center">
                                                <h1 class="text-l font-medium ml-4">Was examined and treated at Mater Dei College Clinic on <span class="border-b-2 border-gray-700"> {{ formattedDate(medcert.date)  }}</span> with the following diagnosis:  </h1>

                                            </div>
                                             <h1 type="text" class=" w-full border-gray-500 ml-4" >{{medcert.diagnosis}}</h1>
                                            <div class="flex items-center">
                                                <h1 class="text-l font-medium ml-4">And would need for medical attention for </h1>
                                                <h1 type="text" class="input" >{{medcert.days}}</h1>
                                                <p>days barrying complication.</p>
                                            </div>
                                           <div class="flex flex-col items-end"  >
                                                <h1 class="input border-b-2 border-gray-300 focus:border-black outline-none mb-2">{{medcert.doctor.user.firstname}} {{medcert.doctor.user.lastname}}</h1>
                                                <p>Attending Physician</p>
                                            </div>
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

        </div>

    </Sidebar>
</template>

<style scoped>
    .input {
  outline: 0;
  border-width: 0 0 2px;
  border-color: #000;
  margin-bottom: 5;

}
.input:focus {
  border-color: green;
  outline: 1px dotted #000
}
</style>
