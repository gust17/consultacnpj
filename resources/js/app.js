require('./bootstrap');

import Vue from 'vue';

const app = new Vue({
    el: '#app',
    data: {
        results: [],
        total: 0,
        processed: 0,
    },
    mounted() {
        this.getProgress();
        setInterval(this.getProgress, 30000); // Atualizar a cada 30 segundos
    },
    methods: {
        getProgress() {
            axios.get('/api/progress')
                .then(response => {
                    this.results = response.data.results;
                    this.total = response.data.total;
                    this.processed = response.data.processed;
                })
                .catch(error => {
                    console.error(error);
                });
        }
    }
});
