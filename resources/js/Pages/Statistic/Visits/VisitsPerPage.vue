<template>
    <div class="card">
        <div class="card-header text-center text-lg">
            Посещения по страницам за месяц
        </div>

        <div class="card-body">
            <div v-for="item in visitsPerPage">
                {{item[0]}} - {{item[1]}}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "VisitsPerPage",
    props: ['visitsPerPage'],
}
</script>

<style scoped>

</style>
