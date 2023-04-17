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
            Рассчитать доставку для данного варианта
        </button>

        <div v-if="deliveryData" class="flex flex-column">
            <div v-for="offer in deliveryData">
                <div>
                    Стоимость - <strong>{{ offer.offer_details.pricing_total }}</strong> p
                </div>
                <div>
                    Интервал доставки от {{Date(offer.offer_details.delivery_interval.min)}} до {{Date(offer.offer_details.delivery_interval.max)}}
                </div>
            </div>

<!--            <div>-->
<!--                Дата доставки до терминала города доставки - {{ deliveryData.orderDates.arrivalToOspReceiver }}-->
<!--                <strong>Без учёта стоимости обрешётки и доставки со склада до адреса</strong>-->
<!--            </div>-->
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
            let response = await axios.get(`/api/delivery/yandex/search-by-term?term=${term[1].toLowerCase()}`)
            let newOptions = response.data.response.GeoObjectCollection.featureMember
            this.options = []
            this.options = newOptions.map(object => ({
                text: object.GeoObject.metaDataProperty.GeocoderMetaData.Address.formatted,
                value: object.GeoObject.Point.pos
            }))
        },
        async calculate() {
            try {
                if (!this.pos) return
                this.isLoading = true
                let coordinates = this.pos.split(' ').map(item => +item)
                let data = {
                    destination: {
                        type: "custom_location",
                        custom_location: {
                            longitude: coordinates[0],
                            latitude: coordinates[1],
                            details: {
                                full_address: this.term
                            }
                        },

                        // interval_utc: {
                        //     "from": "2023-04-25T06:00:00.000000Z",
                        //     "to": "2023-04-25T13:00:00.000000Z"
                        // }
                    },
                    places: [{
                        barcode: `dod-${this.variantData.code}`,
                        physical_dims: {
                            dx: this.variantData.product.length,
                            dy: this.variantData.product.width,
                            dz: this.variantData.product.height,
                            predefined_volume: this.variantData.product.length * this.variantData.product.width * this.variantData.product.height,
                            weight_gross: +this.variantData.product.weight,
                        },
                    }],
                    items: [{
                        article: this.variantData.code,
                        count: 1,
                        name: '1',
                        barcode: `dod-${this.variantData.code}`,
                        place_barcode: `dod-${this.variantData.code}`,
                        physical_dims: {
                            "weight_gross": +this.variantData.product.weight,
                            "predefined_volume": this.variantData.product.length * this.variantData.product.width * this.variantData.product.height
                        },
                        billing_details: {
                            unit_price: this.variantData.price,
                            assessed_unit_price: this.variantData.price,
                            inn: "745212240657",
                            nds: -1,
                            need_nds: false
                        },
                    }],
                    recipient_info: {"first_name": "Стёпа", "phone": "+73215325535"},
                    last_mile_policy: "time_interval",
                    particular_items_refuse: false
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
                // data = {
                //     // info: {
                //     //     operator_request_id: "235353253532"
                //     // },
                //     destination: {
                //         type: "custom_location",
                //         custom_location: {
                //             longitude: 37.540756,
                //             latitude: 55.79211,
                //             details: {
                //                 short_text: "Ленинградский проспект, 37",
                //                 full_text: "Москва, Ленинградский проспект, 37 ",
                //                 description: "Москва",
                //                 point: [37.540756, 55.79211],
                //                 full_address: "Москва, Ленинградский проспект, 37 ",
                //                 room: "5"
                //             }
                //         },
                //         interval_utc: {"from": "2023-04-18T11:00:00.000000Z", "to": "2023-04-20T20:59:00.000000Z"}
                //     },
                //     items: [{
                //         article: "1",
                //         count: 1,
                //         name: "1",
                //         barcode: "dod-235353253532",
                //         place_barcode: "dod-235353253532",
                //         physical_dims: {"weight_gross": 49000, "predefined_volume": 100000000},
                //         billing_details: {
                //             unit_price: 2000000,
                //             assessed_unit_price: 2000000,
                //             inn: "745212240657",
                //             nds: -1,
                //             need_nds: false
                //         }
                //     }],
                //     places: [{
                //         physical_dims: {
                //             dx: 10,
                //             dy: 60,
                //             dz: 50,
                //             predefined_volume: 200000000,
                //             weight_gross: 49000,
                //         },
                //         barcode: "dod-235353253532"
                //     }],
                //     recipient_info: {"first_name": "Стёпа", "phone": "+73215325535"},
                //     last_mile_policy: "time_interval",
                //     particular_items_refuse: false
                // }


                // {
                //     "info": {
                //     "operator_request_id": "676"
                // },
                //     "source": {
                //     "platform_station": {
                //         "platform_id": "3ed47320-6e04-40eb-85ba-ed702cd0d7d2"
                //     }
                // },
                //     "destination": {
                //     "type": "custom_location",
                //         "custom_location": {
                //         "longitude": 37.560375,
                //             "latitude": 55.697914,
                //             "details": {
                //             "short_text": "Ленинский проспект, 54",
                //                 "full_text": "Москва, Ленинский проспект, 54 ",
                //                 "description": "Москва",
                //                 "point": [
                //                 37.560375,
                //                 55.697914
                //             ],
                //                 "full_address": "Москва, Ленинский проспект, 54 "
                //         }
                //     },
                //     "interval_utc": {
                //         "from": "2023-04-25T06:00:00.000000Z",
                //             "to": "2023-04-25T15:00:00.000000Z"
                //     }
                // },
                //     "items": [
                //     {
                //         "article": "2",
                //         "count": 1,
                //         "name": "1",
                //         "barcode": "dod-676",
                //         "place_barcode": "dod-676",
                //         "physical_dims": {
                //             "weight_gross": 0,
                //             "predefined_volume": 0
                //         },
                //         "billing_details": {
                //             "unit_price": 2000000,
                //             "assessed_unit_price": 2000000,
                //             "inn": "745212240657",
                //             "nds": 20,
                //             "need_nds": true
                //         }
                //     }
                // ],
                //     "places": [
                //     {
                //         "physical_dims": {
                //             "dx": 110,
                //             "dy": 80,
                //             "dz": 90,
                //             "weight_gross": 49000,
                //             "predefined_volume": 792000000
                //         },
                //         "barcode": "dod-676"
                //     }
                // ],
                //     "billing_info": {
                //     "payment_method": "already_paid"
                // },
                //     "recipient_info": {
                //     "first_name": "Эдуард",
                //         "phone": "+79325325635"
                // },
                //     "last_mile_policy": "time_interval",
                //     "particular_items_refuse": false
                // }
                let response = await axios.post(`/api/delivery/yandex/calculate`, data)
                this.deliveryData = response.data.offers
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
