<template>
<!--    <AuthenticatedLayout>-->
        <div class="card card-white">
            <div class="card-header">
                <div class="flex ">
                    <Link href="/admin/groups">
                        <button class="btn btn-default mr-3">
                            Назад
                        </button>
                    </Link>
                    <span class="bg-gray-300 p-1.5 rounded text-black">Группа {{group.title}}</span>
                </div>


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
<!--    </AuthenticatedLayout>-->
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Link} from "@inertiajs/vue3";
export default {
    name: "Show",
    components: {AuthenticatedLayout, Link},
    props: ['groupData'],
    layout: AuthenticatedLayout,
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
        },
    }
}
</script>

<style scoped>

</style>
