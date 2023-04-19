<template>

    <div class="card m-3">
        <div class="card-header text-center text-xl relative">
            Блок контента

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
                        {{renderMaterialValues(contentItem)}}
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

</template>

<script>
import {Link} from '@inertiajs/vue3'
export default {
    name: "ContentPreview",
    components: {Link},
    props: ['contentItems'],
    methods: {
        renderMaterialValues(contentItem) {
            let result = ''
            let valuesLength = contentItem?.material_unit_values?.length
            contentItem?.material_unit_values?.slice(0, 5).map((value, idx) => {
                if (idx === valuesLength - 1) {
                    return result += value.value
                }
                result += value.value + ', '
            })

            return result
        },
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
    },

}
</script>

<style scoped>

</style>
