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
                <div class="mb-4"><span>Product Details</span></div>

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
                  <div class="w-full sm:w-3/12 flex items-center">
                    Category <span class="text-3xl text-red-500 mt-2 ml-2">*</span>
                  </div>
                  <div class="flex-grow">
                    <MultiSelect
                      v-model="catId"
                      :style="{
                        border: multiselectError ? '1px solid red' : '1px solid #CED4DA',
                      }"
                      @change="validateCategory"
                      display="chip"
                      :options="categories"
                      optionLabel="name"
                      filter
                      placeholder="Select Categories"
                      :maxSelectedLabels="3"
                      class="w-full md:w-80"
                    />
                    <div class="text-red-500 text-xs mt-1">{{ multiselectErrorMessage }}</div>
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
            <div class="basis-1/2">
              <div class="border-solid border-2 border-indigo-600 p-4">
                <div><span>Feature Details</span></div>
                <table>
                  <thead>
                    <tr>
                      <th class="text-start mr-4">Feature Name</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(field, index) in fields" :key="index" class="mb-4">
                      <td>
                        <input
                          v-model="field.feature"
                          type="text"
                          class="appearance-none border w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none"
                          :placeholder="'Feature # ' + (index + 1)"
                          :style="{
                            border: field.error1 ? '1px solid red' : '1px solid #CED4DA',
                          }"
                          @keyup="validateFeature(index)"
                        />
                        <div v-if="field.error1" class="text-red-500 text-xs mt-1">{{ field.error1 }}</div>
                      </td>
                      <td>
                        <button
                          v-if="index === 0"
                          @click="addField"
                          type="button"
                          class="bg-customCyan 500 ml-4 mt-4 hover:bg-hoverCyan text-white font-bold py-2 px-4 mb-4"
                        >
                          <span class="inline-flex items-center text-white">
                            Add Field
                            <icon name="plus" size="15px" style="margin-left: 8px" />
                          </span>
                        </button>
                        <button
                          v-else
                          @click="removeField(index)"
                          type="button"
                          class="bg-red-500 ml-4 mt-4 hover:bg-red-700 text-white font-bold py-2 px-4 mb-4"
                        >
                          <span class="inline-flex items-center text-white">
                            Remove
                            <icon name="minus" size="15px" style="margin-left: 8px" />
                          </span>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
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
import MultiSelect from 'primevue/multiselect';
import axios from 'axios';
import navbar from './Navbar.vue';
import { ref, onMounted, watch } from 'vue';
import { useToast } from 'vue-toastification';
import { useRouter } from 'vue-router';

const toast = useToast();
const router = useRouter();
const categories = ref([]);
const productName = ref('');
const image = ref(null);
const imageUrl = ref('');
const nameError = ref(false);
const nameErrorMessage = ref('');
const fields = ref([{ feature: '', error1: '' }]);
const catId = ref([]);
const multiselectError = ref(false);
const multiselectErrorMessage = ref('');
const imageError = ref(false);
const imageErrorMessage = ref('');

onMounted(async () => {
  await getCategory();
});

function handleFileChange(event, fileType) {
  const file = event.target.files[0];
  if (fileType === 'image') {
    image.value = file;
    validateImage();

    // Generate image preview URL
    if (file) {
      imageUrl.value = URL.createObjectURL(file);
    }
  }
}

function addField() {
  fields.value.push({ feature: '', error1: '' });
}

function removeField(index) {
  fields.value.splice(index, 1);
}

async function getCategory() {
  try {
    let response = await axios.get('/api/category/get');
    categories.value = response.data.categories;
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

function validateCategory() {
  if (catId.value.length === 0) {
    multiselectError.value = true;
    multiselectErrorMessage.value = 'Please select at least one option.';
    return false;
  } else {
    multiselectError.value = false;
    multiselectErrorMessage.value = '';
    return true;
  }
}

function validateImage() {
  if (!image.value) {
    imageError.value = true;
    imageErrorMessage.value = 'Image is required.';
    return false;
  }

  const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
  if (!validTypes.includes(image.value.type)) {
    imageError.value = true;
    imageErrorMessage.value = 'Invalid image type. Please upload a JPG, PNG, or GIF file.';
    return false;
  }

  const maxSize = 5 * 1024 * 1024; // 2MB
  if (image.value.size > maxSize) {
    imageError.value = true;
    imageErrorMessage.value = 'Image size should not exceed 2MB.';
    return false;
  }

  imageError.value = false;
  imageErrorMessage.value = '';
  return true;
}

function formValidation(fieldName) {
  if (fieldName === 'submit') {
    const isNameValid = validateName();
    const isCategoryValid = validateCategory();
    const isImageValid = validateImage();

    return isNameValid && isCategoryValid && isImageValid;
  } else if (fieldName === 'productName') {
    validateName();
  } else if (fieldName === 'catId') {
    validateCategory();
  } else if (fieldName === 'image') {
    validateImage();
  }
}

async function submitForm() {
  if (formValidation('submit')) {
    try {
      const formData = new FormData();
      formData.append('productName', productName.value);
      const categoryIds = catId.value.map(category => category.id);
      formData.append('catId', JSON.stringify(categoryIds));
      fields.value.forEach(field => {
        formData.append('feature[]', field.feature);
      });
      if (image.value) formData.append('image', image.value);

      const response = await axios.post('/api/product/insert', formData);
       router.push({name:'productList'});
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
