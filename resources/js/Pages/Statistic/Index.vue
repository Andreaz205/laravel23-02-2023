<template>
    <AuthenticatedLayout>
        <BarChart />
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BarChart from "@/Pages/Statistic/BarChart.vue";

export default {
    name: "Index",
    components: {BarChart, AuthenticatedLayout}

}
</script>

<style scoped>

</style>
