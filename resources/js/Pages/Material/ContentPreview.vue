<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-xl text-center">
                    Блок контента
                </div>
                <div class="card-body">
                    <button class="btn btn-warning">
                        <Link href="/admin/materials/variants-content">
                            Перейти
                        </Link>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {Link} from '@inertiajs/vue3'
export default {
    name: "ContentPreview",
    components: {Link},
}
</script>

<style scoped>

</style>
