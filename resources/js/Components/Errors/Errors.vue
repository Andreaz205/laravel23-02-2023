<template>
    <div v-if="errors" class="bg-red-100 border-2 border-red-200 mb-4 p-4 relative">
        <div  class="row" v-for="errorField in errors">
            <div class="col-12" v-for="error in errorField">
                {{error}}
            </div>
        </div>

        <div class="absolute right-0 top-0 bg-gray-400 cursor-pointer w-[20px] h-[20px] flex justify-center items-center" @click="errors = null">
            <span class="text-black">X</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "Errors",
    props: ['errors']
}
</script>

<style scoped>

</style>
