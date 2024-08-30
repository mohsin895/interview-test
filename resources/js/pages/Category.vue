<template>
  
<navbar />
<div class="interview-category mt-10 ml-10 mr-10 bg-white p-6">
    <div class="card w-full">
      <div class="p-2 mb-4">
        <div class="bg-customCyan hover:bg-hoverCyan p-2 w-48 cursor-pointer" @click="openModal">
          <span class="inline-flex items-center text-white">
            Add New Category
          
          </span>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="border-collapse border border-slate-400 w-full min-w-max">
        <thead>
          <tr>
            <th class="border border-slate-300 h-10">Sr No.</th>
            <th class="border border-slate-300">Category Name</th>
         
            <th class="border border-slate-300">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(dataInfo, index) in dataList" :key="dataInfo.id">
            <td class="border border-slate-300 pl-4 h-16 text-sm">{{ index + 1 }}</td>
            <td class="border border-slate-300 pl-4 text-sm">
              {{ dataInfo.name }}
            </td>
            
            <td class="border border-slate-300 pl-4">
              <span class="inline-flex items-center">
                <span class="p-2 cursor-pointer mr-6 inline-flex items-center text-xs bg-purpoleCus hover:bg-purpoleCusHover text-white" @click="openModalEdit(dataInfo)">
                  <icon name="pencil" size="15px" style="margin-right: 10px" />
                  View/Edit
                </span>
                <span class="p-2 cursor-pointer inline-flex items-center text-xs bg-red-500 ml-4 hover:bg-red-700 text-white" @click="openModalDelete(dataInfo)">
                  <icon name="trash" size="15px" class="mr-2" />
                  Delete
                </span>
              </span>
            </td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="isModalOpen" ref="modalBackdrop" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-start max-h-screen overflow-y-auto justify-center pt-20" @click="handleBackdropClick">
      <!-- Modal -->
      <div class="bg-white shadow-lg w-full max-w-lg " @click.stop>
        <div class="flex justify-between items-center p-6 mb-4 border-b border-gray-200 modal-header">
          <h2 class="text-xl inline-flex items-center font-semibold">
            <span v-if="editMode"> <icon name="pencil" size="15px" style="margin-right: 8px" /></span><span v-else> <icon name="plus" size="15px" style="margin-right: 8px" /></span>
            {{ editMode ? 'Edit Category' : 'Add New Category' }}
          </h2>
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700 px-2.5 bg-slate-200">
            &times;
          </button>
        </div>
        <form @submit.prevent="submitForm">
          <div class="pl-6 pr-6 modal-body">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="catName">
                <span :class="{ 'text-red-500': nameError }">Category</span>
              </label>
              <input
                v-model="catName"
                type="text"
                class="appearance-none border w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                placeholder="Category Name"
                :style="{ border: nameError ? '1px solid red' : '1px solid #CED4DA' }"
                @keyup="formValidation('catName')"
              />
              <div class="text-red-500 text-xs mt-1">{{ nameErrorMessage }}</div>
            </div>
            
          </div>

          <div class="border-t border-gray-200 modal-footer">
            <div class="p-6 flex items-end justify-end space-x-4">
              <button @click="closeModal" class="bg-white-500 hover:bg-black hover:text-white text-black font-bold py-2 px-4 border border-black focus:outline-none focus:shadow-outline text-right" type="button">
                Cancel
              </button>
              <button class="bg-customCyan hover:bg-hoverCyan text-white font-bold py-2 px-4 focus:outline-none focus:shadow-outline text-right" type="submit">
                <span class="inline-flex items-center text-white">
                 
                  {{ editMode ? 'Update' : 'Submit' }}
                </span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Modal -->
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
              Are you sure you want to delete this Category:
              <span class="font-bold">{{ catName }}?</span>
            </span>
            <div class="bg-deleteColor p-4 border-l-8 border-borderColor">
              <span class="font-bold ">Note:</span> All the
              <span class="font-bold">Products</span> associated with this category will also be deleted.
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
import navbar from './Navbar.vue'
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const isModalOpen = ref(false);
const isModalOpenDelete = ref(false);
const modalBackdrop = ref(null);
const catName = ref('');
const dataList = ref([]);
const dataId = ref(null);
const editMode = ref(false);
const nameError = ref(false);;
const nameErrorMessage = ref('');

function openModal() {
  editMode.value = false;
  isModalOpen.value = true;
}

function openModalEdit(dataInfo) {
  editMode.value = true;
  dataId.value = dataInfo.id;
  catName.value = dataInfo.name;
  isModalOpen.value = true;
}

function openModalDelete(dataInfo) {
  dataId.value = dataInfo.id;
  catName.value = dataInfo.name;
  isModalOpenDelete.value = true;
}

function closeModal() {
  isModalOpen.value = false;
  isModalOpenDelete.value = false;
  catName.value = '';
  dataId.value = null;
  editMode.value = false;
  nameError.value = false;
  nameErrorMessage.value = '';
}



function handleBackdropClick(event) {
  if (event.target === modalBackdrop.value) {
    closeModal();
  }
}


const fetchDataList = async () => {
  try {
    
    const response = await axios.get('/api/category/get/list');
    dataList.value = response.data;
  } catch (error) {
    console.error('Error fetching category data:', error);
  }
};

function validateName() {
  if (!catName.value || catName.value.length === 0) {
    nameError.value = true;
    nameErrorMessage.value = 'Category Name Field cannot be empty';
    return false;
  } else {
    nameError.value = false;
    nameErrorMessage.value = '';
    return true;
  }
}

function formValidation(fieldName) {
  if (fieldName === 'submit') {
    const isNameValid = validateName();
    return isNameValid;
  } else if (fieldName === 'catName') {
    validateName();
  }
}

async function submitForm() {
 
  if (formValidation('submit')) {

    try {
     
      const payload = {
        catName: catName.value,
       
        dataId: dataId.value || null
      };
      const response = await axios.post('/api/category/insert/update', payload);
      if (response.data.msgFlag) {
        toast.success(response.data.msg);
        await fetchDataList();
        closeModal();
      } else {
        toast.error(response.data.errMsg);
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      toast.error('An error occurred while submitting the form.');
    }
  }
}

async function deleteInfo() {
  try {

    
    const payload = {
      dataId: dataId.value || null
    };
    const response = await axios.post('/api/category/delete', payload);
    if (response.data.msgFlag) {
      toast.success(response.data.msg);
      await fetchDataList();
      closeModal();
    } else {
      toast.error(response.data.errMsg);
    }
  } catch (error) {
    console.error('Error deleting Category:', error);
    toast.error('An error occurred while deleting the Category.');
  }
}



onMounted(fetchDataList);
</script>

<style scoped>
.helper-text-product-add {
  color: red;
}
</style
