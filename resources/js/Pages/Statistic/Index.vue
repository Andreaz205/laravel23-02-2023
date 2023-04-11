<template>
    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header">
                <div class="text-center text-lg">
                    Статистика посещений и заказов
                </div>
            </div>
        </div>



                        <!--                        <div class="col-12">-->
                        <!--                            <LineChart-->
                        <!--                                :labels-prop="visitsLabels"-->
                        <!--                                :chart-data-from-back="visitsData"-->
                        <!--                                :background-color="'#f87979'"-->
                        <!--                                :chart-label="'Статистика посещений за последний месяц'"-->
                        <!--                            />-->
                        <!--                        </div>-->


                    <!--                    <div class="row">-->
                    <!--                        <div class="col-12">-->
                    <!--                            <BarChart-->
                    <!--                                :labels="visitsLabels"-->
                    <!--                                :chart-data-from-back="visitsData"-->
                    <!--                                :background-color="'#f87979'"-->
                    <!--                                :chart-label="'Статистика посещений за последний месяц'"-->
                    <!--                            />-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                    <div class="row">-->
                    <!--                        <div class="col-6">-->
                    <!--                            <PieChart-->
                    <!--                                :labelsProp="['Диваны', 'Кровати', 'Кресла', 'Столы']"-->
                    <!--                                :chart-data-from-back="visitsData.slice(0,4)"-->
                    <!--                                :background-colors="['#f87979', '#f87009', '#f00979', '#907979']"-->
                    <!--                            />-->
                    <!--                        </div>-->
                    <!--                        <div class="col-6">-->
                    <!--                            <DoughnutChart-->
                    <!--                                :labelsProp="['Диваны', 'Кровати', 'Кресла', 'Столы']"-->
                    <!--                                :chart-data-from-back="visitsData.slice(0,4)"-->
                    <!--                                :background-colors="['#f87979', '#f87009', '#f00979', '#907979']"-->
                    <!--                            />-->
                    <!--                        </div>-->
                    <!--                    </div>-->


        <OrdersDateCountChart
            :count-data="ordersCountData"
            :date-labels="ordersDateLabels"
        />

        <VisitsCountChart
            :pages-visits="pagesVisits"
        />

        <DevicesChart
            :devices-data="devicesData"
        />

        <VisitsPerPage :visits-per-page="visitsPerPage"/>



    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import LineChart from "@/Pages/Statistic/LineChart.vue";
import BarChart from "@/Pages/Statistic/BarChart.vue";
import PieChart from "@/Pages/Statistic/PieChart.vue";
import DoughnutChart from "@/Pages/Statistic/DoughnutChart.vue";
import OrdersDateCountChart from "@/Pages/Statistic/Orders/OrdersDateCountChart.vue";
import VisitsCountChart from "@/Pages/Statistic/Visits/VisitsCountChart.vue";
import DevicesChart from "@/Pages/Statistic/Devices/DevicesChart.vue";
import VisitsPerPage from "@/Pages/Statistic/Visits/VisitsPerPage.vue";

export default {
    name: "Index",
    components: {
        VisitsPerPage,
        DevicesChart,
        VisitsCountChart,
        OrdersDateCountChart,
        DoughnutChart,
        BarChart,
        LineChart,
        AuthenticatedLayout,
        PieChart
    },
    props: ['ordersDateLabels', 'ordersCountData', 'pagesVisits', 'devicesData', 'visitsPerPage'],
    data() {
        return {
            // visitsLabels: [
            //     '1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
            //     '11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
            //     '21', '22', '23', '24', '25', '26', '27', '28', '29', '30',
            // ],
            // visitsData: [
            //     1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
            //     11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
            //     21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
            // ],
            // ordersData: [],
        }
    }

}
</script>

<style scoped>

</style>
