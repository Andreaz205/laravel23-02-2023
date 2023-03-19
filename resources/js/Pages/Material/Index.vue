<template>
    <AuthenticatedLayout>
        <template v-if="categories && categories.length">
            <div class="container-fluid mt-3">
                <div class="row">
                    <div v-for="category in categories" :key="category.id" class="col-4">
                        <Link :href="`/admin/materials/${category.id}`">
                            <div class="card p-4 min-h-[200px]">
                                <div class="card-body">
                                    <h5 class="card-title">{{category.name}}</h5>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

        </template>

        <template v-else>
            Вы еще не создавали категорий!
        </template>
    </AuthenticatedLayout>
</template>

<script>
import {Link} from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
export default {
    name: "Index",
    components: {AuthenticatedLayout, Link},
    props: ['categories']

}
</script>

<style scoped>

</style>
