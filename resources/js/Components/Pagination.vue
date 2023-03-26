<template>
    <div v-if="items">
        <ul class="pagination pagination-sm m-0 float-right">
            <li class="page-item" v-if="items.current_page !== 1">
                <a class="page-link" href="#" >«</a>
            </li>

            <li class="page-item" v-for="link in calcPagination">
                <span :class="{active : link.active}" class="page-link cursor-pointer" @click="fetchPage(link.url)">{{link.label}}</span>
            </li>

            <li class="page-item" v-if="items.current_page !== items.last_page">
                <a class="page-link" href="#">»</a>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "Pagination",
    props: ['items', 'fetchPage'],
    data() {
        return {
            pagination: this.items.links
        }
    },
    watch: {
        items(currValue, prevValue) {
            this.pagination = currValue.links
        }
    },
    computed: {
        calcPagination () {
            return this.pagination?.slice(1, -1)
        }
    },
}
</script>

<style scoped>
.active{
    background: grey;
}
</style>
