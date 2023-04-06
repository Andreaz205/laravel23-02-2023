<template>
    <Spinner v-if="isLoading"/>
    <div class="mx-3 my-5">

        <img
            src="https://www.webasyst.com/wa-data/public/hub/img/yandexdelivery-logo.png"
            alt=""
        >

<!--        <ModelSelect :options="options" placeholder="Выберите город доставки" v-model="item"-->
<!--                     @input="debounce(handleChangeSelect, $event.target.value, 3000)"/>-->
        <div class="row">
            <div class="col-12">
                <input type="text" class="form-control" v-model="term">
            </div>
        </div>

        <div v-if="options">
            <div v-for="option in options" @click="handleOptionClick(option)" class="border-b-2 flex justify-between hover:bg-gray-50 cursor-pointer" >
                {{option.text}}
            </div>
        </div>

        <button class="btn btn-primary" @click="calculate" :disabled="!pos">
            Рассчитать доставку
        </button>

        <div v-if="deliveryData" class="flex gap-x-2">
            <div>
                Стоимость - <strong>{{deliveryData.price}}</strong> p
            </div>
            <div>
                Дата доставки до терминала города доставки - {{deliveryData.orderDates.arrivalToOspReceiver}}
                <strong>Без учёта стоимости обрешётки и доставки со склада до адреса</strong>
            </div>
        </div>

    </div>
</template>

<script>
import "vue-search-select/dist/VueSearchSelect.css"
import {ModelSelect} from 'vue-search-select'
import Spinner from "@/Components/Spinner.vue";
export default {
    name: "YandexDeliveryCalculator",
    components: {Spinner, ModelSelect},
    props: ['debounce', 'variantData'],
    data() {
        return {
            term: null,
            pos: null,
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
        handleOptionClick(option) {
            this.pos = option.value
            this.term = option.text
        },
        async handleChangeSelect(term) {
            console.log(term)
            let response = await axios.get(`/api/delivery/yandex/search-by-term?term=${term[1].toLowerCase()}`)
            let newOptions = response.data.response.GeoObjectCollection.featureMember
            this.options = []
            this.options = newOptions.map(object => ({
                text: object.GeoObject.metaDataProperty.GeocoderMetaData.Address.formatted,
                value: object.GeoObject.Point.pos
            }))
            console.log(newOptions)
        },
        async calculate() {
            try {
                if (!this.pos) return
                this.isLoading = true
                let coordinates = this.pos.split(' ').map(item => +item)
                let data = {
                    items: [{
                            quantity: 1,
                            size: {
                                height: this.variantData.product.height * 0.01,
                                width: this.variantData.product.width * 0.01,
                                length: this.variantData.product.length * 0.01,
                            },
                            weight: +this.variantData.product.weight,
                        }
                    ],
                    route_points: [
                        {
                            coordinates: [55.160407, 61.408356]
                        },
                        {
                            coordinates: coordinates,
                        },
                    ],
                    skip_door_to_door: true
                }
                let response = await axios.post(`/api/delivery/yandex/calculate`, data)
                this.deliveryData = response.data.data
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e.response) return alert(e.response.message)
                alert(e?.message ?? e)
            }

        },
    },
    watch: {
        term: {
            handler: function (val) {
                this.debounce(this.handleChangeSelect, val, 3000)
            },
            deep: false,
        }
    }

}
</script>

<style scoped>

</style>
