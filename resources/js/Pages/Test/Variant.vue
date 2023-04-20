<template>
    <Spinner v-if="isLoading"/>
    <div class="text-xl my-2">
        {{data.product.title}}
    </div>
    <div v-for="unit in data.material_units">
        <span class="text-xl">
            {{ unit.title }}
        </span>
        <div class="flex flex-nowrap gap-x-2 my-2">
            <div v-for="value in unit.values"
                 :class="['bg-gray-100 rounded-xl cursor-pointer', {'bg-gray-300': value.active}]">
                <Link :href="'/api/test-variant/' + value.linkedVariantId">{{ value.value }}</Link>
            </div>
        </div>
    </div>

    <button
            type="button"
            class="TINKOFF_BTN_YELLOW TINKOFF_SIZE_L"
            @click="handleTinkoffClick"
    >Купить в рассрочку</button>

    <!--TODO:CDEK-->
    <div class="mx-3 my-5">
        <img
            src="data:image/png;base64,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"
            alt=""
        >
        <ModelSelect :options="options" placeholder="Выберите город доставки" v-model="item"
                     @input="debounce(handleChangeSelect, $event.target.value, 3000)"/>
        <button class="btn btn-primary" @click="calculateCDEK" :disabled="!item.value">
            Рассчитать доставку
        </button>

        <div v-if="cdekTariffs && cdekTariffs.length">
            <div v-for="tariff in cdekTariffs">
                {{tariff.tariff_name}} - {{tariff.delivery_sum}}
            </div>
            <strong>Без учёта стоимости обрешётки и доставки со склада до адреса</strong>
        </div>


    </div>

    <!--TODO:Деловые линии-->
    <BusinessLinesCalculator :debounce="debounce" :variant-data="data"/>

    <!--TODO:Яндекс доставка-->
    <YandexDeliveryCalculator :debounce="debounce" :variant-data="data"/>

</template>

<script>
import tinkoff from '@tcb-web/create-credit';

tinkoff.methods.on(tinkoff.constants.SUCCESS, onMessage);
async function onMessage(data) {
    console.log(data)
    switch (data.type) {
        case tinkoff.constants.SUCCESS:
            let items = data.meta.iframe.url.split('/')
            let id = items[items.length - 1]
            let body = {
                "user_name": "user",
                "phone": "891214124",
                "email": "some@mail.ru",
                "payment_variant": "installment_tinkoff",
                "tinkoff_application_id": id,
                "delivery_type": "pickup",
                "address": "address",
                "variants": [
                    {
                        "id": 224,
                        "quantity": 1
                    },
                    // {
                    //     "id": 225,
                    //     "quantity": 1
                    // }
                ]
            }
            let response = await axios.post('/api/orders', body)
            console.log(response)
            break;
        default:
            return;
    }
    tinkoff.methods.off(tinkoff.constants.SUCCESS, onMessage);
    data.meta.iframe.destroy();
}

import "vue-search-select/dist/VueSearchSelect.css"
import {Link} from "@inertiajs/vue3"
import {ModelSelect} from 'vue-search-select'
import BusinessLinesCalculator from "@/Pages/Test/BusinessLinesCalculator.vue";
import Spinner from "@/Components/Spinner.vue";
import YandexDeliveryCalculator from "@/Pages/Test/YandexDeliveryCalculator.vue";

const cheliybinskCodeCDEK = 259

export default {
    name: "Variant",
    props: ['data'],
    components: {YandexDeliveryCalculator, Spinner, BusinessLinesCalculator, Link, ModelSelect},
    data() {
        return {
            isLoading: false,
            timer: null,
            lastTerm: null,
            options: [],
            item: {
                text: null,
                value: null,
            },
            cdekTariffs: null,
        }
    },
    methods: {
        async handleTinkoffClick() {
            let data = await tinkoff.createDemo(
                {
                    sum: 80000,
                    items: [{name: 'Курс веб-программирования с 0', price: 80000, quantity: 1}],
                    promoCode: 'default',
                    shopId: '539d7a60-153d-42a8-9f60-111f1911c28f',
                    showcaseId: '9c80bdb3-1362-49a2-9647-0b602c9e6175',
                    webhookURL: 'https://cb2d-88-206-10-222.ngrok-free.app/payment/tinkoff/installment/callback'
                },
                {view: 'modal'}
            )
            console.log(data)
        },
        async calculateCDEK() {
            try {
                this.isLoading = true

                let data = {
                    'from_location': {
                        'code': cheliybinskCodeCDEK,
                    },
                    "to_location": {
                        "code": this.item.value
                    },
                    "packages": {
                        "number": 1,
                        "weight": this.data.product.weight * 1000,
                        "length": this.data.product.length,
                        "width": this.data.product.width,
                        "height": this.data.product.height
                    }
                }
                let response = await axios.post(`/api/delivery/cdek/calculate-by-available-tariffs`, data)
                this.cdekTariffs = response.data.tariff_codes

                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e.response) return alert(e.response.message)
                alert(e?.message ?? e)
            }
        },

        async handleChangeSelect(term) {
            let response = await axios.get('/api/delivery/cdek/cities')
            let result = response.data.filter(city => city.city.toLowerCase().includes(term[1].toLowerCase()))
            this.options = []
            for (let i = 0; i < 20; i++) {
                if (result && result[i]) {
                    this.options.push(
                        {text: result[i].city, value: result[i].code}
                    )
                }
            }
        },

        debounce(fn, term, delay)
            {
                if (!this.timer) {
                    this.timer = setTimeout(() => {
                        this.timer = null
                        if (this.lastTerm) fn.call(this, ['', this.lastTerm])
                    }, delay)
                    this.lastTerm = null
                    return fn.call(this, arguments)
                }
                this.lastTerm = term
            }
        },
    }
</script>

<style scoped>

</style>
