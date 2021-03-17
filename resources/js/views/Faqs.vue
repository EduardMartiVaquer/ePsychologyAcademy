<template id="faqs-template">
<div id="faq" class="app-container container">
    <div class="row">
        <div class="d-none d-md-block col-md-3" style="padding-top: 115px">
            <div v-for="(key, index) in order" :key="index" class="mb-5">
                <p class="h4 m-0">{{ titles[key] }}</p>
                <div v-for="(category, key1, index1) in trans.messages.faqs.questions[keys[key]]" :key="index1">
                    <p class="h5 mt-4 mb-1">{{ key1 }}</p>
                    <h5 class="h6 pointer" v-for="(question, number, index2) in category" :key="index2 * -1" @click="goToQuestion(0, number)">{{ question[0] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <h1 class="mb-2">Preguntas frecuentes</h1>

            <div class="d-flex mb-3">
                <button class="btn btn-success mr-2" @click="order = [1, 0]">¿Éres psicólogo/a?</button>
                <button class="btn btn-dark mr-2" @click="order = [0, 1]">¿Éres paciente?</button>
            </div>

            <div v-for="(key, index) in order" :key="index" class="mb-5">
                <p class="h2 m-0">{{ titles[key] }}</p>
                <div v-for="(category, key1, index1) in trans.messages.faqs.questions[keys[key]]" :key="index1">
                    <p class="h3">{{ key1 }}</p>
                    <div v-for="(question, key2, index2) in category" :key="index2 * -1">
                        <h4 :id="'faq-0-' + key2" class="h3">{{ question[0] }}</h4>
                        <p>{{ question[1] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    template: "#faqs-template",
    data: () => ({
        previousId: null,
        order: [0, 1],
        titles: ["Pacientes", "Psicólogos/as"],
        keys: ["patients", "psychologists"],
        q: ''
    }),
    methods: {
        goToQuestion(x, n){
            if(this.previousId != null){
                $('#' + this.previousId).css({'color': '#333333', 'font-weight': '400'});
            }
            this.previousId = 'faq-' + x + '-' + n;
            $('#' + this.previousId).css({'color': '#0072ff', 'font-weight': '500'});
            $("html, body").animate({ scrollTop: $('#' + this.previousId).offset().top - 200 }, 1000);
        }
    }
}
</script>