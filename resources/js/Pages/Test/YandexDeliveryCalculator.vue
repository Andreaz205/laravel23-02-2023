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
            <div v-for="option in options" @click="handleOptionClick(option)"
                 class="border-b-2 flex justify-between hover:bg-gray-50 cursor-pointer">
                {{ option.text }}
            </div>
        </div>

        <button class="btn btn-primary" @click="calculate" :disabled="!pos">
            Рассчитать доставку
        </button>

        <div v-if="deliveryData" class="flex gap-x-2">
            <div>
                Стоимость - <strong>{{ deliveryData.price }}</strong> p
            </div>
            <div>
                Дата доставки до терминала города доставки - {{ deliveryData.orderDates.arrivalToOspReceiver }}
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
                    billing_info: {
                        payment_method: 'cashless'
                    },
                    destination: {
                        custom_location: {
                            latitude: coordinates[0],
                            longitude: coordinates[1],
                        }
                    },
                    places: {
                        barcode: 1234,
                        physical_dims: {
                            dx: 110,
                            dy: 90,
                            dz: 80,
                        },
                    },
                    source: {
                        platform_station: {
                            platform_id: "3ed47320-6e04-40eb-85ba-ed702cd0d7d2"
                        }
                    }
                    // items: [{
                    //         quantity: 1,
                    //         size: {
                    //             height: this.variantData.product.height * 0.01,
                    //             width: this.variantData.product.width * 0.01,
                    //             length: this.variantData.product.length * 0.01,
                    //         },
                    //         weight: +this.variantData.product.weight,
                    //     }
                    // ],
                    // route_points: [
                    //     {
                    //         coordinates: [55.160407, 61.408356]
                    //     },
                    //     {
                    //         coordinates: coordinates,
                    //     },
                    // ],
                    // skip_door_to_door: true
                }
                let data2 = {
                    info: {
                        operator_request_id: "235353253532"
                    },
                    source: {
                        platform_station: {
                            platform_id: "3ed47320-6e04-40eb-85ba-ed702cd0d7d2"
                        }
                    },
                    destination: {
                        type: "custom_location",
                        custom_location: {
                            longitude: 37.540756,
                            latitude: 55.79211,
                            details: {
                                short_text: "Ленинградский проспект, 37",
                                full_text: "Москва, Ленинградский проспект, 37 ",
                                description: "Москва",
                                point: [37.540756, 55.79211],
                                full_address: "Москва, Ленинградский проспект, 37 ",
                                room: "5"
                            }
                        },
                        interval_utc: {"from": "2023-04-18T11:00:00.000000Z", "to": "2023-04-18T20:59:00.000000Z"}
                    },
                    items: [{
                        article: "1",
                        count: 1,
                        name: "1",
                        barcode: "dod-235353253532",
                        place_barcode: "dod-235353253532",
                        physical_dims: {"weight_gross": 49000, "predefined_volume": 100000000},
                        billing_details: {
                            unit_price: 2000000,
                            assessed_unit_price: 2000000,
                            inn: "745212240657",
                            nds: -1,
                            need_nds: false
                        }
                    }],
                    places: [{
                        physical_dims: {
                            dx: 10,
                            dy: 60,
                            dz: 50,
                            predefined_volume: 200000000,
                            weight_gross: 49000,
                        },
                        barcode: "dod-235353253532"
                    }],
                    billing_info: {"payment_method": "already_paid"},
                    recipient_info: {"first_name": "Стёпа", "phone": "+73215325535"},
                    last_mile_policy: "time_interval",
                    particular_items_refuse: false
                }
                let response = await axios.post(`/api/delivery/yandex/calculate`, data2)
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
