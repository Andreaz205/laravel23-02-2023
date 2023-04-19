<template>
    <div class="card m-3">
        <div class="card-header text-center text-lg">
            Прибыль с заказов
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <Errors :errors="errors" />
                        <Spinner v-if="isLoading"/>

                        <div class="row">
                            <label class="col-2">С</label>
                            <div class="col-10">
                                <VueDatePicker v-model="from" placeholder="Укажите дату начала ..." text-input />
                            </div>
                        </div>

                        <div class="row mt-2">
                            <label class="col-2">По</label>
                            <div class="col-10">
                                <VueDatePicker v-model="to" placeholder="Укажите дату окончания..." text-input />
                            </div>
                        </div>

                        <div class="row mt-3">
                            <label class="col-2">Интервал</label>
                            <div class="col-10">
                                <label for="radio-day">День</label>
                                <input type="radio" id='radio-day' v-model="interval" value="day">

                                <label for="radio-week">Неделя</label>
                                <input type="radio" id='radio-week' v-model="interval" value="week">

                                <label for="radio-month">Месяц</label>
                                <input type="radio" id='radio-month' v-model="interval" value="month">
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button class="btn btn-primary" @click="handleChangePeriod">
                                Расчитать
                            </button>
                        </div>

                        <div class="mt-3">
                            <LineChart
                                :labels-prop="dateLabelsFinalData"
                                :chart-data-from-back="profitDataFinalData"
                                background-color="#f87979"
                                chart-label="Статистика заказов"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Errors from "@/Components/Errors/Errors.vue";
import Spinner from "@/Components/Spinner.vue";
import LineChart from "@/Pages/Statistic/LineChart.vue";
import VueDatePicker from "@vuepic/vue-datepicker";

export default {
    name: "OrdersDateProfitChart",
    components: {Errors, Spinner, LineChart, VueDatePicker},
    props: ['dateLabels', 'profitData'],
    data () {
        return {
            // from: this.formatDateToDatepicker(JSON.parse(JSON.stringify(this.dateLabels[0]))),
            from: JSON.parse(JSON.stringify(this.dateLabels[0])) + ' 00:00',
            to: JSON.parse(JSON.stringify(this.dateLabels[this.dateLabels.length - 1])) + ' 23:59',
            errors: null,
            dateLabelsFinalData: JSON.parse(JSON.stringify(this.dateLabels)),
            profitDataFinalData: JSON.parse(JSON.stringify(this.profitData)),
            interval: 'day',
            isLoading: false,
        }
    },
    methods: {
        async handleChangePeriod() {
            try {
                this.isLoading = true
                let data = {
                    from: this.from,
                    to: this.to,
                    detailing: this.interval
                }

                let response = await axios.post(`/admin/statistics/calculate-orders-profit-period`, data)
                this.dateLabelsFinalData = response.data.ordersDateLabels
                this.profitDataFinalData = response.data.ordersProfitData
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e?.message ?? e)
            }
        }
    },

}
</script>

<style scoped>

</style>
