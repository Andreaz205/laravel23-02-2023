<template>
    <Spinner v-if="isLoading" />
    <div class="card">
        <div class="card-header text-center text-xl relative">
            Блок контента
            <button class="btn btn-default absolute top-1/2 -translate-y-1/2 left-1">
                <Link :href="`/admin/materials`">
                    К материалам
                </Link>
            </button>
        </div>



        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Свойства</th>
                    <th>

                    </th>
                    <th>

                    </th>
                </tr>
                </thead>
                <tbody v-if="contentItems">
                <tr v-for="contentItem in contentItems">
                    <td>
                        {{contentItem.title}}
                    </td>
                    <td>

                    </td>
                    <td>
                        <button class="btn btn-warning">
                            <Link :href="`/admin/materials/variants-content/${contentItem.id}/edit`">
                                Редактировать
                            </Link>
                        </button>
                    </td>
                    <td>
                        <button class="btn btn-danger" @click="deleteContent(contentItem)">
                            Удалить
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary">
                <Link :href="`/admin/materials/variants-content/create`">
                    Добавить
                </Link>
            </button>
        </div>
    </div>


<!--    <div class="row m-3">-->
<!--        <div class="col-12 text-center">-->

<!--        </div>-->
<!--    </div>-->
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Spinner from "@/Components/Spinner.vue";

export default {
    name: "Index",
    components: {Spinner, Link},
    layout: AuthenticatedLayout,
    props: [
        'product',
        'contentItems'
    ],
    data() {
        return {
            isLoading: false
        }
    },
    methods: {
        async deleteContent(contentItem) {
            try {
                this.isLoading = true
                await axios.delete(`/admin/materials/variants-content/${contentItem.id}`)
                let index = this.contentItems.indexOf(contentItem)
                this.contentItems.splice(index, 1)
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e?.message ?? e)
            }
        }
    }
}
</script>

<style scoped>

</style>
