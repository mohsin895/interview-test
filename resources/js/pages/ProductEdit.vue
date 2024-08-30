<template>
    <div class="interview-test">
      <navbar />
      <div class="sm:ml-2 sm:mr-2 md:ml-10 md:mr-10">
        <div class="card w-full bg-white mt-4">
          <form @submit.prevent="submitForm">
            <div class="flex flex-col md:flex-row p-6">
              <!-- Left Section -->
              <div class="basis-1/2 mr-4">
                <div class="border-solid border-2 border-indigo-600 p-4">
                  <div class="mb-4"><span>Product Details   </span></div>
  
                  <!-- Name Section -->
                  <div class="flex flex-col sm:flex-row gap-4 mb-4">
                    <div class="w-full sm:w-3/12 flex items-center">
                      Product Name <span class="text-3xl text-red-500 mt-2 ml-2">*</span>
                    </div>
                    <div class="flex-grow">
                      <input
                        type="text"
                        v-model="productName"
                        :style="{
                          border: nameError ? '1px solid red' : '1px solid #CED4DA',
                        }"
                        @keyup="validateName"
                        class="appearance-none border w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                        placeholder="Product Name"
                      />
                      <div class="text-red-500 text-xs mt-1">{{ nameErrorMessage }}</div>
                    </div>
                  </div>
  
           
  
                  <div class="flex flex-col sm:flex-row gap-4 mb-4">
                    <div class="w-full sm:w-3/12">
                      Image <span class="text-3xl text-red-500 mt-2 ml-2">*</span>
                    </div>
                    <div class="flex-grow">
                      <input
                        type="file"
                        @change="handleFileChange($event, 'image')"
                        class="appearance-none border w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                      />
                      <div v-if="imageError" class="text-red-500 text-xs mt-1">{{ imageErrorMessage }}</div>
                      <!-- Image Preview -->
                      <div v-if="imageUrl" class="mt-4">
                        <img :src="imageUrl" alt="Image Preview" class="max-w-xs rounded-md shadow-md"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
  
              <!-- Right Section -->
          
            </div>
  
            <div class="border-t border-gray-200 modal-footer">
              <div class="p-6 flex items-end justify-end space-x-4">
                <button
                  @click="closeModal"
                  class="bg-white-500 hover:bg-black hover:text-white text-black font-bold py-2 px-4 border border-black focus:outline-none focus:shadow-outline"
                  type="button"
                >
                  Cancel
                </button>
                <button
                  class="bg-customCyan hover:bg-hoverCyan text-white font-bold py-2 px-4 focus:outline-none focus:shadow-outline"
                  type="submit"
                >
                  <span class="inline-flex items-center text-white">
                    <icon name="check" size="15px" style="margin-right: 8px" />
                  </span>
                  Submit
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
  import { useToast } from 'vue-toastification';
  import { useRouter, useRoute } from 'vue-router';
  
  const route = useRoute();
  const toast = useToast();
  const router = useRouter();;
  const productInfo = ref([]);
  const productName = ref('');
  const dataId = ref(route.params.dataId);
  const image = ref(null);
  const imageUrl = ref('');
  const nameError = ref(false);
  const nameErrorMessage = ref('');
  const imageError = ref(false);
  const imageErrorMessage = ref('');
  
  onMounted(async () => {
    await getProductInfo();
  });
  
  function handleFileChange(event, fileType) {
    const file = event.target.files[0];
    if (fileType === 'image' && file) {
        const reader = new FileReader();
        reader.onloadend = () => {
            imageUrl.value = reader.result; // This will be the base64 string
            image.value = file;
            validateImage();
        };
        reader.readAsDataURL(file); // Convert to base64
    }
}

  
 
  
  async function getProductInfo() {
    try {
        const response = await axios.get("/api/product/info", {
        params: { dataId: dataId.value }
      });
      productInfo.value = response.data.productInfo;
      let dataInfo = response.data.productInfo;
      productName.value = dataInfo.name;
       imageUrl.value = dataInfo.image ? `/${dataInfo.image}` : '';
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  }
  
  
  function validateName() {
    if (!productName.value) {
      nameError.value = true;
      nameErrorMessage.value = 'Product Name Field cannot be empty';
      return false;
    }
    nameError.value = false;
    nameErrorMessage.value = '';
    return true;
  }
  

  
  
  
  function formValidation(fieldName) {
    if (fieldName === 'submit') {
      const isNameValid = validateName();
   
      
  
      return isNameValid  ;
    } else if (fieldName === 'productName') {
      validateName();
    }  
  }
  
  async function submitForm() {
    if (formValidation('submit')) {
        try {
            const formData = new FormData();
            formData.append('productName', productName.value);
            formData.append('dataId', dataId.value);
         
            if (image.value) {
              
                formData.append('image', image.value);
            } else {
         
                formData.append('imageUrl', imageUrl.value);
            }
           

            const response = await axios.post('/api/product/update', formData);
            router.push({ name: 'productList' });

            if (response.data.msgFlag) {
                toast.success(response.data.msg);
            } else {
                toast.error(response.data.errMsg);
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            toast.error('Something went wrong. Please try again.');
        }
    }
}

  </script>
  
  <style>
  /* Your styles here */
  </style>
  