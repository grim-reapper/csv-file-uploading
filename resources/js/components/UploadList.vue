<template>
  <div>
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left">Time</th>
            <th class="px-4 py-2 text-left">File Name</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Progress</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="upload in uploads" :key="upload.id" class="border-b">
            <td class="px-4 py-2 text-gray-600">{{ upload.uploaded_at }}</td>
            <td class="px-4 py-2">{{ upload.filename }}</td>
            <td class="px-4 py-2">
              <span 
                class="px-2 py-1 rounded-full text-sm"
                :class="{
                  'bg-yellow-100 text-yellow-800': upload.status === 'pending',
                  'bg-blue-100 text-blue-800': upload.status === 'processing',
                  'bg-green-100 text-green-800': upload.status === 'completed',
                  'bg-red-100 text-red-800': upload.status === 'failed'
                }">
                {{ upload.status }}
              </span>
            </td>
            <td class="px-4 py-2">
              <div v-if="upload.status === 'processing' && upload.total_rows > 0" 
                   class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" 
                     :style="{ width: `${(upload.processed_rows / upload.total_rows) * 100}%` }"></div>
              </div>
              <div v-if="upload.status === 'failed'" class="text-red-600 text-sm">
                {{ upload.error_message }}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      uploads: [],
      pollInterval: null
    }
  },

  created() {
    this.fetchUploads()
    this.startPolling()
  },

  beforeUnmount() {
    this.stopPolling()
  },

  methods: {
    async fetchUploads() {
      try {
        const response = await fetch('/api/uploads')
        if (response.ok) {
          this.uploads = await response.json()
        }
      } catch (error) {
        console.error('Failed to fetch uploads:', error)
      }
    },

    startPolling() {
      this.pollInterval = setInterval(() => {
        this.fetchUploads()
      }, 2000) // Poll every 2 seconds
    },

    stopPolling() {
      if (this.pollInterval) {
        clearInterval(this.pollInterval)
      }
    }
  }
}
</script>