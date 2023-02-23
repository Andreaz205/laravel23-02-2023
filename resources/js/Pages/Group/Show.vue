<template>
    <AuthenticatedLayout>
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Группа {{group.title}}</h3>
            </div>
            <form class="card-body" @submit="onSubmit">
                <!-- Color Picker -->
                <div class="form-group">
                    <label>Относительная цена в процентах</label>
                    <input type="text" v-model="percents" name="percents" class="form-control">
                </div>
                <!-- /.form group -->

                <div class="custom-control custom-switch">
                    <input type="checkbox" v-model="isPriceVisibleInProducts" class="custom-control-input" id="customSwitch1">
                    <label class="custom-control-label" for="customSwitch1">Отображать цену в товарах</label>
                </div>
                <!-- /.form group -->
                <button class="btn btn-primary bg-blue-500" type="submit">
                    Сохранить
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
export default {
    name: "Show",
    components: {AuthenticatedLayout},
    props: ['groupData'],
    data () {
        return {
            group: this.groupData,
            percents: this.groupData.percents,
            isPriceVisibleInProducts: this.groupData.is_visible_in_products
        }
    },
    methods: {
        async onSubmit(evt) {
            evt.preventDefault()
            try {
                let data = {
                    is_visible_in_products: this.isPriceVisibleInProducts,
                    percents: this.percents,
                }
                let response = await axios.patch(`/admin/groups/${this.group.id}`, data)
                console.log(response)
            } catch (e) {
                alert(e)
            }


        }
    }
}
</script>

<style scoped>

</style>
