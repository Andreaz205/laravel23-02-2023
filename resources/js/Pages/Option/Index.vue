<template>
    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header">
                <div class="flex items-center">
                    <button class="btn btn-default">
                        <Link href="/admin/products">
                            К товарам
                        </Link>
                    </button>
                    <div class="ml-4">
                        Редактировать свойства
                    </div>
                </div>

            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Значений</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <template v-if="optionNames && optionNames.length">
                        <tr v-for="name in optionNames">
                            <td>{{name.id}}</td>
                            <td>{{name.title}}</td>
                            <td>{{name.option_values.length}}</td>
                            <td>
                                <button class="btn btn-default">
                                    <Link :href="`/admin/options/${name.id}`">
                                        Перейти
                                    </Link>

                                </button>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">

            </div>
        </div>
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {Link} from '@inertiajs/vue3'
export default {
    name: "Index",
    components: {AuthenticatedLayout, Link},
    props: [
        'optionNames'
    ]

}
</script>

<style scoped>

</style>
