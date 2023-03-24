<template>
    <div class="modal fade" id="variantMaterialModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Редактировать свойства</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <Errors :errors="errors"/>
                    <ModelSelect
                        v-if="options"
                        :options="options"
                        v-model="item"
                        placeholder="Выберите метериал"
                    />

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary bg-blue" @click="saveMaterial">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {ModelSelect} from 'vue-search-select'
import "vue-search-select/dist/VueSearchSelect.css"
import Errors from "@/Components/Errors/Errors.vue";
export default {
    name: "MaterialVariantModal",
    props: ['material', 'material_sets', 'selected_variant'],
    emits: ['changeVariantMaterial'],
    components: {
        Errors,
        ModelSelect
    },
    data () {
        return {
            errors: null,
            isLoading: false,
            options: this.computedOptions,
            item: {
                text: null,
                value: null
            }
        }
    },
    methods: {
        arrayContains(arr1, arr2) {
            for (let i = 0; i < arr2.length; i++) {
                if (!arr1.includes(arr2[i])) return false
            }
            return true
        },
        checkEqualArray(arr1, arr2) {
            const result = [];
            for (let i = 0; i < arr1.length; i += 1) {
                if (arr2.includes(arr1[i])) result.push(arr1[i]);
            }
            return result
        },
        async saveMaterial () {
            try {
                this.isLoading = true
                console.log(this.item)
                let valuesIds = this.item.value.split('-')
                valuesIds = valuesIds.map(id => +id)
                let data = {
                    material_id: this.material.id,
                    material_unit_values: valuesIds
                }
                let response = await axios.patch(`/admin/variants/${this.selected_variant.id}/materials/${this.material.id}`, data)
                this.$emit('changeVariantMaterial', response.data)
                this.isLoading = false
            } catch (e) {
                console.log(e)
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        }
    },
    computed: {
        clickHandler() {
            if (this.selected_variant && this.material) {
                let item
                this.options = this.computedOptions
                let selectedVariantValuesIds = this.selected_variant?.material_unit_values?.map(val => val.id)
                let materialSets = this.material_sets?.find(set => set.material_id === this.material.id)
                let sets = materialSets['sets']


                sets?.map(set => {
                    let idsString = set.ids.join('-')
                    if (this.arrayContains(selectedVariantValuesIds, set.ids)) item = {value: idsString, text: set.title}
                    // let setValuesIds = set.map(setItem => setItem.id)
                    // let result = this.checkEqualArray(selectedVariantValuesIds, setValuesIds)
                    // if (result.length === set.length) {
                    //     let valuesIds = ''
                    //
                    //     result.map((resultItem, idx) => {
                    //         valuesIds = idx !== set.length - 1 ? valuesIds + resultItem + '-' : valuesIds + resultItem
                    //     })
                    //     item = {value: valuesIds}
                    // }
                })
                return item


                // let materialSet = this.material_sets.find(s => s.material_id === this.material.id)
                // let sets = materialSet['sets']
                // this.item = sele
            }

        },
        computedOptions() {
            if (this.material && this.material_sets) {
                let materialSet = this.material_sets.find(s => s.material_id === this.material.id)
                let sets = materialSet['sets']
                let options = [];
                sets.map(set => {
                    // let plainTextValues = ''
                    // let valuesIds = ''
                    // set.map((setItem, idx) => {
                    //     plainTextValues += setItem.value + ' '
                    //     valuesIds = idx !== set.length - 1 ? valuesIds + setItem.id + '-' : valuesIds + setItem.id
                    // })
                    options.push({text: set.title, value: set.ids.join('-')})
                })
                return options
            }
        }
    },
    watch: {
        clickHandler: {
            handler: function(val) {
                this.item = val
            },
            deep: true
        },
      // material(currentValue, previousValue) {
      //     this.options = this.computedOptions
      //     // console.log(88888)
      //
      // },
      //   selected_variant() {
      //     console.log(1111)
      //   }
    },
    mounted() {

    }
}
</script>

<style scoped>

</style>
