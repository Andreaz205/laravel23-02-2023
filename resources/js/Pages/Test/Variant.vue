<template>
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
    <ModelSelect :options="options" placeholder="Выберите город доставки" v-model="item"
                 @input="debounce(handleChangeSelect, $event.target.value, 3000)"/>
    <button class="btn btn-primary" @click="calculateCDEK" :disabled="!item.value">
        Рассчитать доставку
    </button>

    <div v-if="cdekTariffs && cdekTariffs.length">
        <div v-for="tariff in cdekTariffs">
            {{tariff.tariff_name}} - {{tariff.delivery_sum}}
        </div>
    </div>

</template>

<script>
import "vue-search-select/dist/VueSearchSelect.css"
import {Link} from "@inertiajs/vue3"
import {ModelSelect} from 'vue-search-select'

const cheliybinskCodeCDEK = 259
export default {
    name: "Variant",
    props: ['data'],
    components: {Link, ModelSelect},
    data() {
        return {
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
        async calculateCDEK() {
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
        },
        async handleChangeSelect(term)
            {
                let response = await axios.get('/api/delivery/cdek/cities')
                let result = response.data.filter(city => city.city.toLowerCase().includes(term[1].toLowerCase()))
                this.options = []
                for (let i = 0; i < 20; i++) {
                    if (result && result[i]) {
                        this.options.push({text: result[i].city, value: result[i].code})
                    }
                }
            }
        ,
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
        watch: {
            item() {
                console.log(1111)
            }
        }
    }
</script>

<style scoped>

</style>
