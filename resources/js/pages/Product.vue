<template>
  <navbar />
    <div class="container mr-auto px-8 py-8">
        
  

  

  <div class="interview-test mt-10 ml-10 mr-10 bg-white p-6">
      <div class="card w-full">
        <div class="p-2 mb-4">
            <router-link :to="{name:'ProductCreate'}">
          <div class="bg-customCyan hover:bg-hoverCyan p-2 w-40 cursor-pointer" >
            <span class="inline-flex items-center text-white">
              Add New Product
              <icon name="plus" size="15px" style="margin-left: 8px" />
            </span>
          </div>
        </router-link>
        </div>
        <div class="overflow-x-auto">
            <table class="border-collapse border border-slate-400 w-full min-w-max">
          <thead>
            <tr>
              <th class="border border-slate-300 h-10">Sr No.</th>
              
              <th class="border border-slate-300">Product Name</th>
              <th class="border border-slate-300">Image</th>
              <th class="border border-slate-300">Category</th>
            
              <th class="border border-slate-300">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(dataInfo, index) in products" :key="dataInfo.id">
              <td class="border border-slate-300 pl-4 h-16 text-sm">{{ index + 1 }}</td>
              <td class="border border-slate-300 pl-4 text-sm">
                {{ dataInfo.name }}
              </td>
            
              
              <td class="border border-slate-300 pl-4 text-sm">
               <img :src="dataInfo.image" class="h-20 w-20" />
              </td>
              <td class="border border-slate-300 pl-4 text-sm">
                <ul>
                <li v-for="(categoryProduct, idx) in dataInfo.category_products" :key="categoryProduct.id">
                  {{ idx + 1 }}. {{ categoryProduct.categories.name }}
                </li>
              </ul>
              </td>
              
              <td class="border border-slate-300 pl-4">
                <span class="inline-flex items-center">
                  <router-link
                          :to="{
                            name: 'ProductEdit',
                            params: { dataId: dataInfo.id },
                          }"
                        
                        >
                  <span class="p-2 cursor-pointer mr-6 inline-flex items-center text-xs bg-purpoleCus hover:bg-purpoleCusHover text-white">
                   
                   Edit
                  </span>
                </router-link>
                  <router-link
                          :to="{
                            name: 'ProductDetails',
                            params: { dataId: dataInfo.id },
                          }"
                        
                        >
                  <span class="p-2 cursor-pointer mr-6 inline-flex items-center text-xs bg-customCyan hover:bg-hoverCyan text-white" >
                   
                    View
                  </span>
                  </router-link>
                  <span class="p-2 cursor-pointer inline-flex items-center text-xs bg-red-500 ml-4 hover:bg-red-700 text-white" @click="openModalDelete(dataInfo)">
                    
                    Delete
                  </span>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
        </div>
      </div>
  
    
  
    </div>
    <div v-if="isModalOpenDelete" ref="modalBackdrop" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-start justify-center pt-20" @click="handleBackdropClick">
      <!-- Modal -->
      <div class="bg-white shadow-lg w-full max-w-2xl " @click.stop>
        <div class="flex justify-between items-center p-6 mb-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold">Confirmation</h2>
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700 px-2.5 bg-slate-200">
            &times;
          </button>
        </div>
        <form @submit.prevent="deleteInfo">
          <div class="modal-body p-6">
            <span>
              Are you sure you want to delete this Product:
              <span class="font-bold">{{ name }}?</span>
            </span>
            <div class="bg-deleteColor p-4 border-l-8 border-borderColor">
              <span class="font-bold ">Note:</span> All the
              <span class="font-bold"></span> associated with this product will also be deleted.
            </div>
          </div>
          <div class="border-t border-gray-200">
            <div class="flex items-end justify-end space-x-4 p-6">
              <button @click="closeModal" class="bg-white-500 hover:bg-black hover:text-white text-black font-bold py-2 px-4 border border-black focus:outline-none focus:shadow-outline text-right" type="button">
                Cancel
              </button>
              <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4  focus:outline-none focus:shadow-outline" type="submit">
                Delete
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    </div>
 
</template>
<script setup>

import axios from 'axios';
import navbar from './Navbar.vue';
import { ref, onMounted, watch } from 'vue';
const isModalOpenDelete = ref(false);
const modalBackdrop = ref(null);
const products = ref([]);
const dataId = ref(null);
const name=ref("");
onMounted(async () => {
  await getProduct();
});


async function getProduct() {
  try {
    let response = await axios.get('/api/product/get');
    products.value = response.data.products;
  } catch (error) {
    console.error('Error fetching products:', error);
  }
}
function openModalDelete(dataInfo) {
  dataId.value = dataInfo.id;
  name.value = dataInfo.name;
  isModalOpenDelete.value = true;
}
function closeModal() {

  isModalOpenDelete.value = false;
 
  dataId.value = null;
 
}

function handleBackdropClick(event) {
  if (event.target === modalBackdrop.value) {
    closeModal();
  }
}

async function deleteInfo() {
  try {

    
    const payload = {
      dataId: dataId.value || null
    };
    const response = await axios.post('/api/product/delete', payload);
    await getProduct();
      closeModal();
    if (response.data.msgFlag) {
      toast.success(response.data.msg);
      
    } else {
      toast.error(response.data.errMsg);
    }
  } catch (error) {
    console.error('Error deleting Product:', error);
    toast.error('An error occurred while deleting the Product.');
  }
}
</script>