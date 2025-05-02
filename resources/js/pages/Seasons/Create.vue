<template>
  <div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-8">
          <div class="flex items-center space-x-4">
            <button
              @click="goBack"
              class="text-gray-600 hover:text-gray-900 focus:outline-none"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </button>
            <h1 class="text-3xl font-bold text-gray-900">Create New Season</h1>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <!-- Global Error Message -->
            <div v-if="$page.props.errors.season" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
              <p class="text-sm text-red-600">{{ $page.props.errors.season }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Season Name</label>
                <div class="mt-1">
                  <input
                    type="text"
                    id="name"
                    v-model="form.name"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-gray-900 px-4 py-2"
                    :class="{ 'border-red-500': form.errors.name }"
                    placeholder="Enter season name"
                  />
                  <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>
              </div>

              <div class="flex items-center justify-end space-x-4">
                <button
                  type="button"
                  @click="goBack"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  :disabled="processing"
                >
                  {{ processing ? 'Creating...' : 'Create Season' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const page = usePage();

if (page.props.season_slug) {
    window.location.href = route('home');
}

const form = useForm({
  name: '',
});

const processing = ref(false);

const goBack = () => {
  window.location.href = route('home');
};

const submit = async () => {
  processing.value = true;
  try {
    const response = await axios.post(route('seasons.store'), form);
    window.location.href = route('seasons.show', { slug: response.data.slug });
  } catch (error) {
    if (error.response?.data?.errors) {
      form.errors = error.response.data.errors;
    }
    processing.value = false;
  }
};
</script> 