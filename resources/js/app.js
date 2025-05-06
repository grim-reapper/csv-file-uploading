import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import FileUploader from './components/FileUploader.vue';
import UploadList from './components/UploadList.vue';

const app = createApp({
    components: {
        FileUploader,
        UploadList
    }
});

app.mount('#app');