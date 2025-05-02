<template>
  <div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
      <div class="bg-white shadow-soft rounded-2xl overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
          <button @click="returnToHome" class="text-black hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>
          <h1 class="text-2xl font-bold text-black">{{ season.name }}</h1>
          <div class="flex space-x-2">
            <button
              v-if="season.is_active && season.teams && season.teams.length > 0"
              @click="simulateWeek"
              :disabled="isSimulating"
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
            >
              {{ isSimulating ? 'Simulating...' : 'Simulate Week' }}
            </button>
            <button
              v-if="season.is_active && season.teams && season.teams.length > 0"
              @click="simulateAll"
              :disabled="isSimulating"
              class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded"
            >
              {{ isSimulating ? 'Simulating...' : 'Simulate All' }}
            </button>
            <button
              v-if="season.is_active && season.teams && season.teams.length > 0"
              @click="resetSeason"
              :disabled="isResetting"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded"
            >
              Reset Season
            </button>
          </div>
        </div>

        <!-- Winner Announcement -->
        <div v-if="!season.is_active && season.standings && season.standings.length > 0" class="text-center px-6 py-6 border-b border-gray-200">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">🏆 Season Winner 🏆</h2>
          <div class="flex items-center justify-center">
            <img
              :src="season.standings[0].season_team.team.logo"
              :alt="season.standings[0].season_team.team.name"
              class="h-16 w-16 rounded-full mr-4"
              @error="handleImageError"
            />
            <span class="text-2xl font-semibold text-gray-900">
              {{ season.standings[0].season_team.team.name }}
            </span>
          </div>
        </div>

        <!-- Team Selection Section -->
        <div v-if="!season.teams || season.teams.length === 0" class="p-6">
          <div class="mt-4">
            <select
              id="teamSize"
              v-model="selectedTeamSize"
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white text-gray-900"
            >
              <option value="">Select size</option>
              <option v-for="size in teamSizes" :key="size" :value="size">{{ size }} teams</option>
            </select>
          </div>

          <div class="mt-4">
            <button
              @click="selectTeams"
              :disabled="!selectedTeamSize || processing"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
            >
              {{ processing ? 'Selecting Teams...' : 'Select Teams' }}
            </button>
          </div>

          <!-- Selected Teams Display -->
          <div v-if="selectedTeams && selectedTeams.length > 0" class="mt-6">
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

            <div class="mt-6">
              <button
                @click="startSeason"
                :disabled="!selectedTeams.length || startingSeason"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
              >
                {{ startingSeason ? 'Starting Season...' : 'Start Season' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div v-if="season.teams && season.teams.length > 0" class="px-6 pt-6">
          <div class="flex border-b border-gray-200 mb-6">
            <button
              v-for="tab in tabs"
              :key="tab.value"
              @click="activeTab = tab.value"
              :class="[
                'flex-1 py-2 text-center font-medium',
                activeTab === tab.value ? 'text-black border-b-2 border-indigo-500' : 'text-black'
              ]"
            >
              {{ tab.label }}
            </button>
          </div>

          <!-- Standings Tab -->
          <div v-if="activeTab === 'standings'">
            <div class="overflow-x-auto">
              <table class="w-full text-left divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-2 text-black">#</th>
                    <th class="px-4 py-2 text-black">Team</th>
                    <th class="px-4 py-2 text-black">Power</th>
                    <th class="px-4 py-2 text-black">Played</th>
                    <th class="px-4 py-2 text-black">Won</th>
                    <th class="px-4 py-2 text-black">Drawn</th>
                    <th class="px-4 py-2 text-black">Lost</th>
                    <th class="px-4 py-2 text-black">GF</th>
                    <th class="px-4 py-2 text-black">GA</th>
                    <th class="px-4 py-2 text-black">GD</th>
                    <th class="px-4 py-2 text-black">Points</th>
                    <th v-if="predictions" class="px-4 py-2 text-black">Chance</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(standing, index) in season.standings" :key="standing.id" class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium text-black">{{ index + 1 }}</td>
                    <td class="px-4 py-2 flex items-center text-black">
                      <img
                        :src="standing.season_team.team.logo"
                        :alt="standing.season_team.team.name"
                        class="h-6 w-6 rounded-full mr-2"
                        @error="handleImageError"
                      />
                      <span class="text-black">{{ standing.season_team.team.name }}</span>
                    </td>
                    <td class="px-4 py-2">
                      <span 
                        :class="[
                          'px-2 py-1 rounded text-xs font-medium',
                          standing.season_team.power > 90 ? 'bg-green-600 text-white' : 
                          standing.season_team.power > 80 ? 'bg-blue-600 text-white' : 
                          standing.season_team.power > 70 ? 'bg-indigo-600 text-white' : 
                          'bg-gray-600 text-white'
                        ]"
                      >
                        {{ standing.season_team.power }}
                      </span>
                    </td>
                    <td class="px-4 py-2 text-black">{{ standing.played }}</td>
                    <td class="px-4 py-2 text-black">{{ standing.won }}</td>
                    <td class="px-4 py-2 text-black">{{ standing.drawn }}</td>
                    <td class="px-4 py-2 text-black">{{ standing.lost }}</td>
                    <td class="px-4 py-2 text-black">{{ standing.goals_for }}</td>
                    <td class="px-4 py-2 text-black">{{ standing.goals_against }}</td>
                    <td class="px-4 py-2 text-black">{{ standing.goal_difference }}</td>
                    <td class="px-4 py-2 font-bold text-black">{{ standing.points }}</td>
                    <td v-if="predictions" class="px-4 py-2 text-black">
                      <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2.5 mr-2">
                          <div 
                            class="bg-blue-600 h-2.5 rounded-full" 
                            :style="{ width: predictions[standing.season_team.id] + '%' }"
                          ></div>
                        </div>
                        <span>{{ predictions[standing.season_team.id].toFixed(1) }}%</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Matches Tab -->
          <div v-if="activeTab === 'matches'" class="grid md:grid-cols-2 gap-6">
            <div class="p-4 bg-white rounded-lg shadow-sm">
              <h3 class="font-semibold mb-3 text-black">Current Week</h3>
              <ul class="space-y-2">
                <li
                  v-for="m in currentMatches"
                  :key="m.id"
                  class="flex items-center justify-between p-2 hover:bg-gray-50 rounded"
                >
                  <div class="flex items-center w-[40%]">
                    <img :src="m.home_team.team.logo" :alt="m.home_team.team.name" class="h-8 w-8 rounded-full mr-2" @error="handleImageError">
                    <span class="text-black">{{ m.home_team.team.name }}</span>
                  </div>
                  <div class="flex items-center justify-center w-[20%]">
                    <span class="font-medium text-black text-lg">
                      {{ m.home_team_score !== null ? m.home_team_score : '0' }}
                      -
                      {{ m.away_team_score !== null ? m.away_team_score : '0' }}
                    </span>
                    <div class="relative ml-2 group">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400 cursor-pointer"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        @click="openSimulationModal(m)"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                  </div>
                  <div class="flex items-center justify-end w-[40%]">
                    <span class="text-black mr-2">{{ m.away_team.team.name }}</span>
                    <img :src="m.away_team.team.logo" :alt="m.away_team.team.name" class="h-8 w-8 rounded-full" @error="handleImageError">
                  </div>
                </li>
              </ul>
            </div>
            <div class="p-4 bg-white rounded-lg shadow-sm">
              <h3 class="font-semibold mb-3 text-black">Next Week</h3>
              <ul class="space-y-2">
                <li
                  v-for="m in nextMatches"
                  :key="m.id"
                  class="flex items-center justify-between p-2 hover:bg-gray-50 rounded"
                >
                  <div class="flex items-center w-[40%]">
                    <img :src="m.home_team.team.logo" :alt="m.home_team.team.name" class="h-8 w-8 rounded-full mr-2" @error="handleImageError">
                    <span class="text-black">{{ m.home_team.team.name }}</span>
                  </div>
                  <div class="flex items-center justify-center w-[20%]">
                    <span class="font-medium text-black text-lg">vs</span>
                    <div class="relative ml-2 group">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-400 cursor-pointer"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        @click="openSimulationModal(m)"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                  </div>
                  <div class="flex items-center justify-end w-[40%]">
                    <span class="text-black mr-2">{{ m.away_team.team.name }}</span>
                    <img :src="m.away_team.team.logo" :alt="m.away_team.team.name" class="h-8 w-8 rounded-full" @error="handleImageError">
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <!-- Fixtures Tab -->
          <div v-if="activeTab === 'fixtures'">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <section
                v-for="(matches, idx) in season.fixtures"
                :key="idx"
                class="bg-white p-4 rounded-lg shadow-sm"
              >
                <h3 class="font-semibold mb-2 text-black">Week {{idx}}</h3>
                <ul class="space-y-2">
                  <li 
                    v-for="m in matches" 
                    :key="m.id" 
                    class="flex items-center justify-between p-2 hover:bg-gray-50 rounded"
                  >
                    <div class="flex items-center w-[40%]">
                      <img :src="m.home_team.team.logo" :alt="m.home_team.team.name" class="h-8 w-8 rounded-full mr-2" @error="handleImageError">
                      <span class="text-black">{{ m.home_team.team.name }}</span>
                    </div>
                    <div class="flex items-center justify-center w-[20%]">
                      <span class="font-medium text-black text-lg">
                        {{ m.home_team_score !== null ? m.home_team_score : '0' }}
                        -
                        {{ m.away_team_score !== null ? m.away_team_score : '0' }}
                      </span>
                      <div class="relative ml-2 group">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5 text-gray-400 cursor-pointer"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                          @click="openSimulationModal(m)"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </div>
                    </div>
                    <div class="flex items-center justify-end w-[40%]">
                      <span class="text-black mr-2">{{ m.away_team.team.name }}</span>
                      <img :src="m.away_team.team.logo" :alt="m.away_team.team.name" class="h-8 w-8 rounded-full" @error="handleImageError">
                    </div>
                  </li>
                </ul>
              </section>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Simulation Details Modal -->
  <div v-if="showSimulationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
      <button @click="closeSimulationModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
      <div v-if="selectedSimulation">
        <div class="text-lg font-bold mb-4 text-gray-900">Simulation Details</div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="font-medium text-gray-900 mb-1">Home Team</div>
            <div class="text-xs text-black space-y-1">
              <div>Power: {{ selectedSimulation.home_team.power }}</div>
              <div>Supporter Strength: {{ selectedSimulation.simulation_details?.home_team?.supporter_strength || '-' }}</div>
              <div>Advantage: {{ selectedSimulation.simulation_details?.home_team?.supporter_advantage?.toFixed(2) || '-' }}</div>
              <div>Form Factor: {{ selectedSimulation.simulation_details?.home_team?.form_factor?.toFixed(2) || '-' }}</div>
              <div>Fatigue Factor: {{ selectedSimulation.simulation_details?.home_team?.fatigue_factor?.toFixed(2) || '-' }}</div>
              <div>Expected Goals: {{ selectedSimulation.simulation_details?.home_team?.expected_goals?.toFixed(2) || '-' }}</div>
              <div>Actual Goals: {{ selectedSimulation.simulation_details?.home_team?.actual_goals || '-' }}</div>
            </div>
          </div>
          <div>
            <div class="font-medium text-gray-900 mb-1">Away Team</div>
            <div class="text-xs space-y-1 text-black">
              <div>Power: {{ selectedSimulation.away_team.power }}</div>
              <div>Supporter Strength: {{ selectedSimulation.simulation_details?.away_team?.supporter_strength || '-' }}</div>
              <div>Advantage: {{ selectedSimulation.simulation_details?.away_team?.supporter_boost?.toFixed(2) || '-' }}</div>
              <div>Form Factor: {{ selectedSimulation.simulation_details?.away_team?.form_factor?.toFixed(2) || '-' }}</div>
              <div>Fatigue Factor: {{ selectedSimulation.simulation_details?.away_team?.fatigue_factor?.toFixed(2) || '-' }}</div>
              <div>Expected Goals: {{ selectedSimulation.simulation_details?.away_team?.expected_goals?.toFixed(2) || '-' }}</div>
              <div>Actual Goals: {{ selectedSimulation.simulation_details?.away_team?.actual_goals || '-' }}</div>
            </div>
          </div>
        </div>
        <div class="mt-4 text-xs text-gray-600 space-y-1">
          <div>Weather: {{ selectedSimulation.simulation_details?.weather?.condition || '-' }}</div>
          <div>Goal Factor: {{ selectedSimulation.simulation_details?.weather?.goal_factor || '-' }}</div>
          <div>Simulated at: {{ selectedSimulation.simulation_details?.simulation_timestamp ? new Date(selectedSimulation.simulation_details.simulation_timestamp).toLocaleString() : '-' }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const season = page.props.season;
const predictions = page.props.predictions;

// Tabs state
const activeTab = ref(localStorage.getItem('seasonActiveTab') || 'standings');
const tabs = [
  { label: 'Standings', value: 'standings' },
  { label: 'Fixtures', value: 'fixtures' },
  { label: 'Matches', value: 'matches' },
];

// Watch for tab changes to store in localStorage
watch(activeTab, (newTab) => {
  localStorage.setItem('seasonActiveTab', newTab);
});

// Team selection state
const teamSizes = [4, 10, 20];
const selectedTeamSize = ref('');
const selectedTeams = ref([]);
const processing = ref(false);
const startingSeason = ref(false);

// Action states
const isSimulating = ref(false);
const isResetting = ref(false);

// Image error fallback
const handleImageError = (e) => { e.target.src = 'https://placehold.co/90x90'; };

// Navigation
const returnToHome = () => router.visit(route('home'));

// Team selection
const selectTeams = async () => {
  processing.value = true;
  try {
    const { data } = await axios.post(route('seasons.select-teams'), {
      team_count: selectedTeamSize.value
    });
    selectedTeams.value = data || [];
  } catch (e) {
    console.error('Error:', e);
  } finally {
    processing.value = false;
  }
};

const startSeason = async () => {
  startingSeason.value = true;
  try {
    const { data } = await axios.post(route('seasons.start', { slug: season.slug }), {
      teams: selectedTeams.value
    });
    router.visit(window.location.href);
  } catch (e) {
    console.error('Error starting season:', e);
  } finally {
    startingSeason.value = false;
  }
};

// Season operations
const simulateWeek = async () => {
  if (isSimulating.value) return;
  isSimulating.value = true;
  try {
    await axios.post(route('seasons.simulate-week', season.slug));
    router.visit(window.location.href);
  } finally {
    isSimulating.value = false;
  }
};

const simulateAll = async () => {
  if (isSimulating.value) return;
  isSimulating.value = true;
  try {
    await axios.post(route('seasons.simulate-all', season.slug));
    router.visit(window.location.href);
  } finally {
    isSimulating.value = false;
  }
};

const resetSeason = async () => {
  if (isResetting.value) return;
  isResetting.value = true;
  try {
    await axios.post(route('seasons.reset', season.slug));
    router.visit(window.location.href);
  } finally {
    isResetting.value = false;
  }
};

// Computed for current and next week matches
const currentMatches = computed(() => (
  season.fixtures && season.current_week
    ? season.fixtures[season.current_week - 1]
    : []
));

const nextMatches = computed(() => (
  season.fixtures && season.current_week
    ? season.fixtures[season.current_week]
    : []
));

const showSimulationModal = ref(false);
const selectedSimulation = ref(null);

const openSimulationModal = (match) => {
  selectedSimulation.value = match;
  showSimulationModal.value = true;
};

const closeSimulationModal = () => {
  showSimulationModal.value = false;
  selectedSimulation.value = null;
};

</script> 