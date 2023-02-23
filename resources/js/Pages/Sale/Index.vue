<template>
    <AuthenticatedLayout>
        <div v-if="sales && sales.length">
            <div v-for="sale in sales" class="border">
                <a :href='`/admin/sales/` + sale.id'>{{sale.title}}</a>
            </div>
        </div>
        <div @click="setCreateFormIsOpen(!isCreateFormOpen)">
            <button class="btn btn-outline-primary">
                Добавить акцию
            </button>
        </div>
        <div v-if="isCreateFormOpen">

            <input type="text" placeholder="Введите название акцию" v-model="saleTitle">

            <button class="btn btn-primary" @click="addSale()">
                Добавить
            </button>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    name: "Index",
    components: {AuthenticatedLayout},
    props: ['data'],
    data() {
        return {
            sales: this.$props.data,
            saleTitle: null,
            isCreateFormOpen: false
        }
    },
    methods: {
        async addSale() {
            try {
                let dto = {title: this.saleTitle}
                let {data} = await axios.post('/admin/sales', dto)
                this.saleTitle =  null
                this.sales.unshift(data)
            } catch (e) {
                alert(e)
            }


        },
        setCreateFormIsOpen(isOpen) {
            this.isCreateFormOpen = isOpen
        }
    }
}
</script>

<style scoped>

</style>
