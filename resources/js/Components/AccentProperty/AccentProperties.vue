<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                   <div class="text-center text-xl">
                       Особые свойства
                   </div>
                </div>
                <div class="card-body">

                    <div v-if="accentPropertiesProps && accentPropertiesProps.length">

                        <div v-for="(property, key) in accentPropertiesProps" :key="key" class="row border border-2 rounded-3xl w-full mb-4">

                            <div class="col-8 flex items-center">
                                <div>
                                    <div class="text-lg">{{property.title}}</div>
                                    <div class="text-md">{{property.description}}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="h-[200px] w-[200px] bg-gray-50">
                                    <img v-if="property.media.image_url" :src="property.media.image_url" alt="" class="w-full h-full object-contain">
                                    <video v-if="property.media.video_url" class="w-full h-full">
                                        <source :src="property.media.video_url">
                                    </video>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div v-else class="flex justify-center items-center text-lg">
                        К данному товару вы не указывали особых свойств
                    </div>
                </div>
                <div class="card-footer">
                    <button
                            type="button"
                            class="btn btn-primary bg-blue mr-4"
                            data-toggle="modal"
                            data-target="#createAccentPropertyModal"
                    >
                        Создать
                    </button>
                    <button
                        class="btn btn-warning bg-yellow"
                        type="button"
                        data-toggle="modal"
                        data-target="#appendAccentPropertiesModal"
                    >
                        Редактировать
                    </button>


                </div>

            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AccentProperties",
    props: ['accentPropertiesProps', 'product'],
    data () {
        return {
        //     allAccentProperties: this.accentPropertiesProps,
            productAccentProperties: this.accentPropertiesProps,
        }
    },
    methods: {

    }
}
</script>

<style scoped>

</style>
