<template>
    <div class="interview-test">
      <navbar />
      <div class="sm:ml-2 sm:mr-2 md:ml-10 md:mr-10">
        <div class="card w-full bg-white mt-4">
            <div class="py-4 px-4">
                    <span>Product Name: {{ productInfo.name }}</span>
                </div>
            <div class="flex flex-col md:flex-row p-6">
               
              <!-- Left Section -->
              <div class="basis-1/2 mr-4">
                <div class="border-solid border-2 border-indigo-600 p-4">
                  <div class="mb-4"><span>Category List</span></div>
  
                  <!-- Name Section -->
                  <table class="border-collapse border border-slate-400 w-full min-w-max">
                    <thead>
                      <tr>
                        <th class="border border-slate-300 h-10">Serail No:</th>
                        <th class="border border-slate-300 h-10">Category Name</th>
                     
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(dataInfo, index) in productInfo.category_products" :key="index" class="mb-4">
                        <td class="border border-slate-300 pl-4 text-sm">
                         {{ index + 1 }}
                        </td>
                        <td class="border border-slate-300 pl-4 text-sm">
                          {{ dataInfo.categories.name }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
  
             
                </div>
              </div>
  
              <!-- Right Section -->
              <div class="basis-1/2">
                <div class="border-solid border-2 border-indigo-600 p-4">
                  <div><span>Feature List</span></div>
                  <table class="border-collapse border border-slate-400 w-full min-w-max">
                    <thead>
                      <tr>
                        <th class="border border-slate-300 h-10">Serail No:</th>
                        <th class="border border-slate-300 h-10">Feature Name</th>
                     
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(dataInfo, index) in productInfo.feature" :key="index" class="mb-4">
                        <td class="border border-slate-300 pl-4 text-sm">
                         
                           {{ index + 1 }}
                        </td>
                        <td class="border border-slate-300 pl-4 text-sm">
                         {{ dataInfo.description }}
                         
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
  </template>
  
  <script setup>
  import MultiSelect from 'primevue/multiselect';
  import axios from 'axios';
  import navbar from './Navbar.vue';
  import { ref, onMounted, watch } from 'vue';
  import { useToast } from 'vue-toastification';
  import { useRouter, useRoute } from 'vue-router';
  
  const route = useRoute();
  const toast = useToast();
  const router = useRouter();
  const productInfo = ref([]);
  const dataId = ref(route.params.dataId);
 
  
  onMounted(async () => {
    await getProductInfo();
  });
  
 
  async function getProductInfo() {
    try {
        const response = await axios.get("/api/product/info", {
        params: { dataId: dataId.value }
      });
      productInfo.value = response.data.productInfo;
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  }
  
 
  
  
  </script>
  
  <style>
  /* Your styles here */
  </style>
  