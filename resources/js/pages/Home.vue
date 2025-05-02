<template>
  <div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Champions League Seasons</h1>
          <button
            v-if="!activeSeason"
            @click="showCreateModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Create New Season
          </button>
        </div>

        <div v-if="seasons.length === 0" class="bg-white overflow-hidden shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <div class="text-center">
              <h3 class="text-lg font-medium text-gray-900">Welcome to Champions League Manager</h3>
              <p class="mt-2 text-sm text-gray-500">
                This application helps you manage Champions League seasons, teams, and matches.
                Create a new season to get started!
              </p>
              <div class="mt-5">
                <button
                  @click="showCreateModal = true"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Create Your First Season
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="season in seasons"
            :key="season.id"
            class="bg-white overflow-hidden shadow rounded-lg"
            :class="{ 'ring-2 ring-blue-500': season.is_active }"
          >
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">{{ season.name }}</h3>
                <span
                  v-if="season.is_active"
                  class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full"
                >
                  Active
                </span>
              </div>
              <div class="mt-4">
                <p v-if="season.winner" class="text-sm text-gray-500">
                  {{ season.is_active ? 'Leader:' : 'Winner:' }}
                </p>
                <div v-if="season.winner" class="mt-1 flex items-center space-x-2">
                  <img 
                    :src="season.winner.logo" 
                    :alt="season.winner.name + ' logo'"
                    class="h-8 w-8 object-contain"
                  />
                  <p class="text-lg font-semibold text-gray-900">
                    {{ season.winner.name }}
                  </p>
                </div>
                <div v-else class="mt-1">
                  <p class="text-sm text-gray-500 mb-2">No teams selected yet</p>
                  <button
                    @click="selectTeamsForSeason(season)"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    Select Teams
                  </button>
                </div>
              </div>
              <div class="mt-4">
                <a
                  :href="`/seasons/${season.slug}`"
                  class="text-sm text-blue-600 hover:text-blue-500"
                >
                  View Details →
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Season Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 z-10 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create New Season</h3>
                
                <!-- Global Error Message -->
                <div v-if="form.errors.season" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                  <p class="text-sm text-red-600">{{ form.errors.season }}</p>
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
                </form>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="submit"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
              :disabled="processing"
            >
              {{ processing ? 'Creating...' : 'Create Season' }}
            </button>
            <button
              type="button"
              @click="showCreateModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Team Selection Modal -->
    <div v-if="showTeamSelectionModal" class="fixed inset-0 z-10 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Select Teams for {{ newSeasonName }}</h3>
                
                <div class="mt-4 max-w-xs">
                  <label for="teamSize" class="block text-sm font-medium text-gray-700 mb-1">Select Team Size</label>
                  <select
                    id="teamSize"
                    v-model="selectedTeamSize"
                    @change="selectedTeamSize && selectTeams()"
                    class="block w-full pl-3 pr-10 py-2 border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white text-gray-900"
                  >
                    <option value="">Select size</option>
                    <option v-for="size in teamSizes" :key="size" :value="size">{{ size }} teams</option>
                  </select>
                </div>

                <!-- Selected Teams Display -->
                <div v-if="selectedTeams && selectedTeams.length > 0" class="mt-6">
                  <div class="flex justify-end mb-4">
                    <button
                      @click="startSeason"
                      :disabled="!selectedTeams.length || startingSeason"
                      class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                    >
                      {{ startingSeason ? 'Starting Season...' : 'Start Season' }}
                    </button>
                  </div>
                  <h3 class="text-lg font-medium text-black mb-2">Selected Teams</h3>
                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="(team, index) in selectedTeams" :key="index" class="bg-gray-50 p-4 rounded-lg relative">
                      <div 
                        :class="[
                          'absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium',
                          team.power > 90 ? 'bg-green-600 text-white' : 
                          team.power > 80 ? 'bg-blue-600 text-white' : 
                          team.power > 70 ? 'bg-indigo-600 text-white' : 
                          'bg-gray-600 text-white'
                        ]"
                      >
                        {{ team.power }}
                      </div>
                      <div class="flex flex-col items-center">
                        <img 
                          :src="team.team_logo" 
                          :alt="team.team_name + ' logo'"
                          class="w-24 h-24 object-contain mb-3"
                          @error="handleImageError"
                        />
                        <p class="text-sm font-medium text-black">{{ team.team_name }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="showTeamSelectionModal = false"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'

interface Season {
  id: number
  year: number
  is_active: boolean
  slug: string
  name: string
  winner?: {
    name: string
    logo: string
  }
}

const props = defineProps<{
  seasons: Season[]
  activeSeason: Season | null
}>()

const showCreateModal = ref(false)
const showTeamSelectionModal = ref(false)
const processing = ref(false)
const newSeasonName = ref('')
const newSeasonSlug = ref('')

// Team selection state
const teamSizes = [4, 10, 20]
const selectedTeamSize = ref('')
const selectedTeams = ref([])
const processingTeams = ref(false)
const startingSeason = ref(false)

const form = useForm({
  name: '',
})

const page = usePage()
page.props.title = 'Champions League Home'

const handleImageError = (e) => { e.target.src = 'https://placehold.co/90x90' }

const submit = async () => {
  processing.value = true
  try {
    const response = await axios.post(route('seasons.store'), form)
    newSeasonName.value = form.name
    newSeasonSlug.value = response.data.slug
    showCreateModal.value = false
    showTeamSelectionModal.value = true
  } catch (error) {
    if (error.response?.data?.errors) {
      form.errors = error.response.data.errors
    }
    processing.value = false
  }
}

// Team selection functions
const selectTeams = async () => {
  processingTeams.value = true
  try {
    const { data } = await axios.post(route('seasons.select-teams'), {
      team_count: selectedTeamSize.value
    })
    selectedTeams.value = data || []
  } catch (e) {
    console.error('Error:', e)
  } finally {
    processingTeams.value = false
  }
}

const startSeason = async () => {
  startingSeason.value = true
  try {
    const { data } = await axios.post(route('seasons.start', { slug: newSeasonSlug.value }), {
      teams: selectedTeams.value
    })
    window.location.href = route('seasons.show', { slug: newSeasonSlug.value })
  } catch (e) {
    console.error('Error starting season:', e)
  } finally {
    startingSeason.value = false
  }
}

const selectTeamsForSeason = (season) => {
  newSeasonName.value = season.name
  newSeasonSlug.value = season.slug
  showTeamSelectionModal.value = true
}
</script> 