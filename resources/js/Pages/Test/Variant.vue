<template>
    <div v-for="unit in data.material_units">
        <span class="text-xl">
            {{unit.title}}
        </span>
        <div class="flex flex-nowrap gap-x-2 my-2">
            <div v-for="value in unit.values" :class="['bg-gray-100 rounded-xl cursor-pointer', {'bg-gray-300': value.active}]">
                <Link :href="'/api/test-variant/' + value.linkedVariantId">{{value.value}}</Link>
            </div>
        </div>

    </div>
</template>

<script>
import {Link} from "@inertiajs/vue3"
export default {
    name: "Variant",
    props: ['data'],
    components: {Link},
}
</script>

<style scoped>

</style>
