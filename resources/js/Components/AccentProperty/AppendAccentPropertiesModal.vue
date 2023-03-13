<template>
    <div class="modal fade" id="appendAccentPropertiesModal" tabindex="-1" role="dialog" aria-labelledby="createAccentPropertyModal"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Указать специальное свойство товару</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid" v-if="allAccentProperties && allAccentProperties.length">
                        <div class="row mb-4 border border-b-2 border-gray-500" v-for="accentProperty in allAccentProperties" :key="accentProperty.id">
                            <div class="col-6">
                                <div class="mb-2 text-lg">
                                    {{accentProperty.title}}
                                </div>
                                <div class="text-sm">
                                    {{accentProperty.description}}
                                </div>
                            </div>
                            <div class="col-4 h-[200px] w-[200px] bg-gray-50">

                                <img v-if="accentProperty.media && accentProperty.media.image_url" :src="accentProperty.media.image_url" alt="" class="w-full h-full object-contain">
                                <video autoplay loop muted v-if="accentProperty.media && accentProperty.media.video_url" class="w-full h-full">
                                    <source :src="accentProperty.media.video_url">
                                </video>
                            </div>
                            <div class="col-1 flex justify-center items-center">
                                <input type="checkbox" v-model="accentPropertiesIds" :value="accentProperty.id" >
                            </div>
                            <div class="flex justify-center items-center">
                                <button class="btn btn-danger" @click="deleteAccentProperty(accentProperty)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        Вы еще не создавали особых свойств!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">
                        Закрыть
                    </button>
                    <button
                        class="btn btn-primary"
                        @click="submit"
                    >
                        Сохранить
                    </button>
                    <!--                        <button type="button" class="btn btn-primary bg-blue-500" @click="saveOption">Сохранить--><!--                        </button>-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AppendAccentPropertiesModal",
    props: [
        'productAccentProperties',
        'allAccentProperties',
        'product'
    ],
    emits: ['bound', 'delete'],
    data () {
        return {
            accentPropertiesIds: this.productAccentProperties?.map(p => p.id),
        }
    },
    methods: {
        async submit() {
            try {
                let data = {
                    accent_properties_ids: this.accentPropertiesIds
                }
                let response = await axios.post(`/admin/products/${this.product.id}/accent-properties`, data)

                this.$emit('bound', response.data.accent_properties)
            } catch (e) {
                alert('Error')
            }
        },
        async deleteAccentProperty(property) {
            try {
                await axios.delete(`/admin/accent-properties/${property.id}`)
                this.$emit('delete', property)
                // let searchedProperty = this.allAccentProperties.find(p => p.id === property.id)
                // let index = this.allAccentProperties.indexOf(searchedProperty)
                // this.allAccentProperties.splice(1, index)
            } catch (e) {
                alert('Error')
            }
        }
    }
}
</script>

<style scoped>

</style>
