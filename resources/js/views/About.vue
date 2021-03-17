<template id="about-template">
<div class="app-container pb-0">
    <div class="d-flex align-items-center justify-content-center py-5 px-2 bg-main-dark">
        <p class="h1 text-center m-0 text-main-color color-white fw-300">{{ trans.messages.about.about_us }}</p>
    </div>

    <div class="d-flex align-items-center justify-content-center py-4 px-2" style="background-color: #e1e8f1">
        <p class="h3 text-center m-0 color-main-dark">{{ trans.messages.about.what_is }}</p>
    </div>

    <div class="container pt-4">
        <p class="h5 text-center color-main-dark fw-300" v-html="trans.messages.about.what_is_answer"></p>
        <div class="row mt-5">
            <div class="col-12 col-md-4 mb-5 mb-md-0">
                <div class="d-flex flex-column justify-content-center">
                    <div class="mx-auto d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; border-radius: 50%; background-color: #4584ae">
                        <p class="h4 color-white m-0">{{ trans.messages.about.mission }}</p>
                    </div>
                    <p class="h4 text-center color-main-dark mt-3">{{ trans.messages.about.mission_sub }}</p>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-5 mb-md-0">
                <div class="d-flex flex-column justify-content-center">
                    <div class="mx-auto d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; border-radius: 50%; background-color: #fbb040">
                        <p class="h4 color-white m-0">{{ trans.messages.about.vision }}</p>
                    </div>
                    <p class="h4 text-center color-main-dark mt-3">{{ trans.messages.about.vision_sub }}</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="d-flex flex-column justify-content-center">
                    <div class="mx-auto d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; border-radius: 50%; background-color: #662d91">
                        <p class="h4 color-white m-0">{{ trans.messages.about.values }}</p>
                    </div>
                    <p class="h4 text-center color-main-dark mt-3">{{ trans.messages.about.values_sub }}</p>
                </div>
            </div>
        </div>

        <img class="d-block mx-auto my-5" :src="url + '/images/flower.svg'" alt="flower" width="40">
    </div>

    <div class="d-flex align-items-center justify-content-center py-4 px-2" style="background-color: #e1e8f1">
        <p class="h3 text-center m-0 color-main-dark">{{ trans.messages.about.offer }}</p>
    </div>

    <div class="container pt-5">
        <div class="row">
            <div class="col-12 col-md-6 d-none d-md-block">
                <div class="d-flex justify-content-end">
                    <img :src="url + '/images/about_us_1.svg'" alt="offer" height="400px">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="d-flex flex-column justify-content-around" style="height: 400px;">
                    <p class="h5 color-main-dark">{{ trans.messages.about.offer_1 }}</p>
                    <p class="h5 color-main-dark">{{ trans.messages.about.offer_2 }}</p>
                    <p class="h5 color-main-dark">{{ trans.messages.about.offer_3 }}</p>
                    <p class="h5 color-main-dark" style="position: relative; bottom: -15px">{{ trans.messages.about.offer_4 }}</p>
                </div>
            </div>
        </div>

        <img class="d-block mx-auto my-5" :src="url + '/images/flower.svg'" alt="flower" width="40">
    </div>

    <div class="d-flex align-items-center justify-content-center py-4 px-2" style="background-color: #ebe6f3">
        <p class="h3 text-center m-0 color-main-dark">{{ trans.messages.about.patient_instructions }}</p>
    </div>

    <div class="container pt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <ol class="about-ol-purple h5 color-main-dark fw-300">
                    <li class="vertical-line-purple" style="--fader: #bda1e9;">{{ trans.messages.about.p2_instructions_1 }}</li>
                    <li class="vertical-line-purple" style="--fader: #af8ee4;">{{ trans.messages.about.p2_instructions_2 }}</li>
                    <li class="vertical-line-purple" style="--fader: #a27ce0;">{{ trans.messages.about.p2_instructions_3 }}</li>
                    <li class="vertical-line-purple" style="--fader: #9569db;">{{ trans.messages.about.p2_instructions_4 }}</li>
                    <li class="vertical-line-purple" style="--fader: #8856d7;">{{ trans.messages.about.p2_instructions_5 }}</li>
                    <li class="vertical-line-purple" style="--fader: #7b44d3; box-shadow: none">{{ trans.messages.about.p2_instructions_6 }}</li>
                </ol>
            </div>
        </div>
        <img class="d-block mx-auto my-5" :src="url + '/images/flower.svg'" alt="flower" width="40">
    </div>

    <div class="d-flex align-items-center justify-content-center py-4 px-2" style="background-color: #e1e8f1">
        <p class="h3 text-center m-0 color-main-dark">{{ trans.messages.about.psycho_instructions }}</p>
    </div>

    <div class="container pt-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <ol class="about-ol-blue h5 color-main-dark fw-300">
                    <li class="vertical-line-blue" style="--fader: #4584ae;">{{ trans.messages.about.p_instructions_1 }}</li>
                    <li class="vertical-line-blue" style="--fader: #5790b6;">{{ trans.messages.about.p_instructions_2 }}</li>
                    <li class="vertical-line-blue" style="--fader: #6a9cbe;">{{ trans.messages.about.p_instructions_3 }}</li>
                    <li class="vertical-line-blue" style="--fader: #7ca8c6;">{{ trans.messages.about.p_instructions_4 }}</li>
                    <li class="vertical-line-blue" style="--fader: #8fb5ce;">{{ trans.messages.about.p_instructions_5 }}</li>
                    <li class="vertical-line-blue" style="--fader: #a2c1d6;">{{ trans.messages.about.p_instructions_6 }}</li>
                    <li class="vertical-line-blue" style="--fader: #b4cdde; box-shadow: none">{{ trans.messages.about.p_instructions_7 }}</li>
                </ol>
            </div>
        </div>
        <img class="d-block mx-auto my-5" :src="url + '/images/flower.svg'" alt="flower" width="40">
    </div>

    <div class="d-flex align-items-center justify-content-center py-4 px-2" style="background-color: #e1e8f1">
        <p class="h3 text-center m-0 color-main-dark">{{ trans.messages.about.logo_title }}</p>
    </div>

    <div class="container pt-4">
        <img class="d-block mx-auto mb-3" :src="url + '/images/logo.svg'" alt="flower" height="50">

        <p class="h5 text-center m-0 color-main-dark fw-300" v-html="trans.messages.about.logo_about"></p>

        <img class="d-block mx-auto my-3" :src="url + '/images/about_us_2.svg'" alt="flower" width="100%">

        <p class="h5 text-center m-0 color-main-dark fw-300">{{ trans.messages.about.logo_about_2 }}</p>

        <img class="d-block mx-auto my-3" :src="url + '/images/about_us_3.svg'" alt="flower" height="120px">

        <p class="h5 text-center m-0 color-main-dark fw-300">{{ trans.messages.about.logo_about_3 }}</p>

        <img class="d-block mx-auto my-5" :src="url + '/images/flower.svg'" alt="flower" width="40">
    </div>
</div>
</template>