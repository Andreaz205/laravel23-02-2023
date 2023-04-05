<template>
    <Spinner v-if="isLoading"/>
    <div class="mx-3 my-5">

        <img
            src="data:image/png;base64,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"
            alt=""
        >

        <ModelSelect :options="options" placeholder="Выберите город доставки" v-model="item"
                     @input="debounce(handleChangeSelect, $event.target.value, 3000)"/>

        <button class="btn btn-primary" @click="calculate" :disabled="!item.value">
            Рассчитать доставку
        </button>

        <div v-if="deliveryData" class="flex gap-x-2">
            <div>
                Стоимость - <strong>{{deliveryData.price}}</strong> p
            </div>
            <div>
                Дата доставки до терминала города доставки - {{deliveryData.orderDates.arrivalToOspReceiver}}
            </div>
        </div>

    </div>
</template>

<script>
import "vue-search-select/dist/VueSearchSelect.css"
import {ModelSelect} from 'vue-search-select'
import Spinner from "@/Components/Spinner.vue";

const chelyabinskTerminalCode = 305
export default {
    name: "BusinessLinesCalculator",
    components: {Spinner, ModelSelect},
    props: ['debounce'],
    data() {
        return {
            isLoading: false,
            deliveryData: null,
            options: [],
            item: {
                text: null,
                value: null,
            },
        }
    },
    methods: {
        async handleChangeSelect(term) {
            let response = await axios.get(`/api/delivery/business-lines/cities-by-term?q=${term[1].toLowerCase()}`)
            this.options = response.data?.cities?.map(city => ({
                text: city.aString,
                value: city.code
            }))
        },
        async calculate() {
            try {
                if (!this.item.value) return
                this.isLoading = true
                let data = {
                    delivery: {
                        deliveryType: {
                            type: 'auto'
                        },
                        arrival: {
                            variant: 'terminal',
                            city: this.item.value,
                        },
                        derival: {
                            produceDate: '2023-04-06',
                            variant: 'terminal',
                            terminalID: chelyabinskTerminalCode,
                        },
                    }
                }
                let response = await axios.post(`/api/delivery/business-lines/calculate`, data)
                this.deliveryData = response.data.data
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e.response) return alert(e.response.message)
                alert(e?.message ?? e)
            }

        }
    }
}
</script>

<style scoped>

</style>
