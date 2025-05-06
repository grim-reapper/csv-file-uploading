<template>
  <div>
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" 
         @dragover.prevent="isDragging = true"
         @dragleave.prevent="isDragging = false"
         @drop.prevent="handleFileDrop"
         @click="$refs.fileInput.click()"
         :class="{ 'border-blue-500 bg-blue-50': isDragging }">
      
      <div class="flex flex-col items-center space-y-4">
        <span class="text-gray-600">Select file or drag and drop</span>
        
        <div v-if="selectedFile" class="w-full">
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</span>
              </div>
              <button @click.stop="removeFile" class="text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <input
        ref="fileInput"
        type="file"
        accept=".csv,.txt"
        class="hidden"
        @change="handleFileSelect"
      >
    </div>

    <div v-if="uploadError" class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg">
      {{ uploadError }}
    </div>

    <div v-if="selectedFile" class="mt-4 flex justify-end">
      <button 
        @click="uploadFile"
        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg"
        :disabled="isUploading"
      >
        {{ isUploading ? 'Uploading...' : 'Upload File' }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isDragging: false,
      uploadError: null,
      selectedFile: null,
      isUploading: false
    }
  },

  methods: {
    handleFileDrop(e) {
      this.isDragging = false
      const file = e.dataTransfer.files[0]
      if (file) {
        this.validateAndSetFile(file)
      }
    },

    handleFileSelect(e) {
      const file = e.target.files[0]
      if (file) {
        this.validateAndSetFile(file)
      }
    },

    validateAndSetFile(file) {
      if (!file.name.toLowerCase().endsWith('.csv') && !file.name.toLowerCase().endsWith('.txt')) {
        this.uploadError = 'Please select a CSV or TXT file'
        return
      }
      this.selectedFile = file
      this.uploadError = null
    },

    removeFile() {
      this.selectedFile = null
      this.uploadError = null
      this.$refs.fileInput.value = ''
    },

    async uploadFile() {
      if (!this.selectedFile) {
        this.uploadError = 'Please select a file first'
        return
      }

      this.isUploading = true
      const file = this.selectedFile
      const formData = new FormData()
      formData.append('file', file)

      try {
        const response = await fetch('/api/uploads', {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        })

        const data = await response.json()

        if (!response.ok) {
          if (response.status === 422) {
            // Validation errors
            this.uploadError = data.errors?.file?.[0] || 'Invalid file'
          } else {
            throw new Error(data.message || 'Upload failed')
          }
        } else {
          this.uploadError = null
          this.selectedFile = null
          this.$refs.fileInput.value = ''
          this.$emit('upload-complete', data)
        }
      } catch (error) {
        this.uploadError = error.message || 'Failed to upload file. Please try again.'
      } finally {
        this.isUploading = false
      }
    }
  }
}
</script>