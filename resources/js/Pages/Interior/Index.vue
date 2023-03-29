<template>

    <div class="modal fade " id="appendVariantModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true"
         style="z-index: 50000"
    >
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Редактировать свойства</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <Errors :errors="errors"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-9">
                            <div class="card">
                                <div data-v-6ef2e16d="" class="card-tools">
                                    <div data-v-6ef2e16d="" class="input-group input-group-sm" style="width: 100%;">
                                        <input data-v-6ef2e16d="" type="text" name="table_search" class="form-control float-right" placeholder="Search" @keydown.enter="search" autocomplete="off" v-model="term">
                                        <div data-v-6ef2e16d="" class="input-group-append w-[50px]">
                                            <button data-v-6ef2e16d="" class="btn btn-default w-full" @click="search">
                                                <i data-v-6ef2e16d="" class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 flex gap-2">
                            <span> На странице</span>
                            <div>
                                <label for="fist-radio">25</label>
                                <input type="radio" class="form-control cursor-pointer" v-model="variantsPerPage" :value="25" id="fist-radio">
                            </div>
                            <div>
                                <label for="second-radio">50</label>
                                <input type="radio" class="form-control cursor-pointer" v-model="variantsPerPage" :value="50" id="second-radio">
                            </div>
                        </div>
                    </div>

                    <div class="row" v-if="fetchedVariants && fetchedVariants.data && fetchedVariants.data.length">
                            <div class="col-12" v-for="variant in fetchedVariants.data">
                            <div class="flex gap-4 items-center">

                                <div class="w-[100px] h-[100px] bg-gray-50 rounded-xl overflow-hidden">
                                    <img class="object-contain w-full h-full" :src="variant?.images[0]?.image_url ?? '/storage/images/no-image.jpg'" alt="">
                                </div>

                                {{variant.title}}

                                <div>
                                    <button class="btn btn-primary" @click="appendVariantForm(variant)">
                                        Добавить
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">

<!--                                    <li class="page-item">-->
<!--                                        <a class="page-link" href="#" aria-label="Previous">-->
<!--                                            <span aria-hidden="true">&laquo;</span>-->
<!--                                            <span class="sr-only">Previous</span>-->
<!--                                        </a>-->
<!--                                    </li>-->
<!--                                    <li class="page-item">-->
<!--                                        <a class="page-link" href="#">1</a>-->
<!--                                    </li>-->

                                    <li class="page-item"  v-for="(link, key) in fetchedVariants.links.slice(1, fetchedVariants.links.length - 1)" :key="key">
                                        <a class="page-link cursor-pointer" @click="searchWithPage(link.url)">{{link.label}}</a>
                                    </li>

<!--                                    <li class="page-item">-->
<!--                                        <a class="page-link" href="#">{{fetchedVariants.last_page}}</a>-->
<!--                                    </li>-->
<!--                                    <li class="page-item">-->
<!--                                        <a class="page-link" href="#" aria-label="Next">-->
<!--                                            <span aria-hidden="true">&raquo;</span>-->
<!--                                            <span class="sr-only">Next</span>-->
<!--                                        </a>-->
<!--                                    </li>-->
                                </ul>
                            </nav>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button ref="close" type="button" class="btn btn-secondary bg-gray-500" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <!--TODO: Создание -->
    <div class="modal fade" id="appendImageModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Добавить интерьер</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <Errors :errors="errors"/>
                        </div>
                    </div>
                    <template v-if="file">
                        <div class="row">
                            <div class="col-12 text-center">
                                Укажите точку на фотографии указывающую на вариант
                            </div>
                        </div>
                        <div class="row">
<!--                            <div class="flex justify-end">-->
<!--                                <button v-if="selectedInterior.image" class="btn btn-danger" @click="clearInterior">-->
<!--                                    Очистить-->
<!--                                </button>-->
<!--                            </div>-->
                            <div class="col-12 flex justify-center">
                                <div class="max-w-[500px] relative user-select-none" @click="handleImageClick($event, 'create')">
                                    <img :src="dataUrl ?? '/storage/images/no-image.jpg'" alt="" class="w-full h-full pointer-events-none" >
<!--                                Point-->
                                    <div
                                        v-for="point in points"
                                        :key="point.id"
                                        class="absolute pointer-events-none -top-[10px] -left-[10px] h-[20px] w-[20px] bg-red rounded-full text-center"
                                        :ref="'point-' + point.id"
                                    >
                                        <span class="text-black">
                                            {{point.id}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row border-y-2" v-for="point in points" :key="point.id">
                            <div class="col-12 flex justify-between items-center mt-3">
                                <div>
                                    {{point.id}}
                                    <button v-if="selectedPointId !== point.id" class="btn btn-default" @click="this.selectedPointId = point.id">
                                        Выбрать
                                    </button>
                                    <button class="btn btn-danger ml-2" @click="deletePoint(point, 'create')">
                                        Удалить
                                    </button>
                                </div>

                                <button v-if="!point.variant" type="button" class="btn btn-primary bg-blue"
                                        data-toggle="modal" data-target="#appendVariantModal"
                                        @click="handleAppendVariant(point, 'create')"
                                >
                                    Добавить вариант
                                </button>

                                <div v-else class="flex">
                                    <div class="w-[100px] h-[100px] bg-gray-50 rounded-xl overflow-hidden">
                                        <img class="object-contain w-full h-full" :src="point.variant?.images[0]?.image_url ?? '/storage/images/no-image.jpg'" alt="">
                                    </div>
                                    <div>
                                        {{ point.variant.title }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                Описание
                            </div>
                            <div class="col-12">
                                <textarea class="form-control" v-model="point.description"></textarea>
                            </div>
                        </div>
                        <div class="row mt-3">
                           <div class="col-12">
                               <button type="button" class="btn btn-primary bg-blue" @click="handleAddPoint('create')">
                                   Добавить точку
                               </button>
                           </div>
                        </div>
                    </template>

                </div>
                <div class="modal-footer">
                    <button
                        ref="pointsClose"
                        type="button"
                        class="btn btn-secondary bg-gray-500"
                        data-dismiss="modal"
                    >
                        Закрыть
                    </button>
                    <button
                        class="btn btn-primary"
                        @click="storeInterior"
                    >
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--TODO: Редактирование -->
    <div class="modal fade" id="editInteriorModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Редактировать интерьер</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <Errors :errors="errors"/>
                        </div>
                    </div>
                    <template v-if="selectedInterior">
                        <div class="flex justify-end">
                            <button class="btn btn-danger" @click="clearInterior">
                                Очистить
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center">
                                Укажите точку на фотографии указывающую на вариант
                            </div>
                        </div>
                        <div class="row">
                            <!--                            <div class="flex justify-end">-->
                            <!--                                <button v-if="selectedInterior.image" class="btn btn-danger" @click="clearInterior">-->
                            <!--                                    Очистить-->
                            <!--                                </button>-->
                            <!--                            </div>-->
                            <div class="col-12 flex justify-center">
                                <div class="max-w-[500px] relative user-select-none" @click="handleImageClick($event, 'edit')">
                                    <img :src="selectedInterior?.image?.image_url ?? '/storage/images/no-image.jpg'" alt="" class="w-full h-full pointer-events-none" >
                                    <!--                                Point-->
                                    <div
                                        v-for="point in editPoints"
                                        :key="point.id"
                                        class="absolute pointer-events-none -top-[10px] -left-[10px] h-[20px] w-[20px] bg-red rounded-full text-center"
                                        :ref="'edit-point-' + point.id"
                                    >
                                        <span class="text-black">
                                            {{point.id}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row border-y-2" v-for="point in editPoints" :key="point.id">
                            <div class="col-12 flex justify-between items-center mt-3">
                                <div>
                                    {{point.id}}
                                    <button v-if="selectedPointId !== point.id" class="btn btn-default" @click="this.selectedPointId = point.id">
                                        Выбрать
                                    </button>
                                    <button class="btn btn-danger ml-2" @click="deletePoint(point, 'edit')">
                                        Удалить
                                    </button>
                                </div>

                                <button v-if="!point.variant" type="button" class="btn btn-primary bg-blue"
                                        data-toggle="modal" data-target="#appendVariantModal"
                                        @click="handleAppendVariant(point, 'edit')"
                                >
                                    Добавить вариант
                                </button>

                                <div v-else class="flex">
                                    <div class="w-[100px] h-[100px] bg-gray-50 rounded-xl overflow-hidden">
                                        <img class="object-contain w-full h-full" :src="point.variant?.images[0]?.image_url ?? '/storage/images/no-image.jpg'" alt="">
                                    </div>
                                    <div>
                                        {{ point.variant.title }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                Описание
                            </div>
                            <div class="col-12">
                                <textarea class="form-control" v-model="point.description"></textarea>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary bg-blue" @click="handleAddPoint">
                                    Добавить точку
                                </button>
                            </div>
                        </div>
                    </template>

                </div>
                <div class="modal-footer">
                    <button
                        ref="pointsClose"
                        type="button"
                        class="btn btn-secondary bg-gray-500"
                        data-dismiss="modal"
                    >
                        Закрыть
                    </button>
                    <button
                        class="btn btn-primary"
                        @click="updateInterior"
                    >
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
    <Spinner v-if="isLoading"/>
    <AuthenticatedLayout>
        <FlashMessage />

        <div class="card">

            <div class="card-header text-center text-xl">
                Интерьеры
            </div>

            <div class="card-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-2">
                            Название
                        </div>
                        <div class="col-6">
                            Изображение
                        </div>
                        <div class="col-4">
                            Вариант
                        </div>
                    </div>
                    <div class="row border p-3" v-for="interior in interiors" :key="interior.id">

                        <div class="col-2">
                            {{ interior.title }}
                        </div>

                        <div class="col-6">
                            <div :ref="`dropzone-${interior.id}`"
                                 @click="handleDropzoneClick"
                                 :class="['w-[200px] h-[150px] bg-gray rounded-xl flex justify-center items-center p-4 cursor-pointer', interior.image && 'hidden']">
                                Нажмите либо
                                поместите изображение
                            </div>

                            <div v-if="interior.image" class="w-[200px] h-[150px] relative overflow-hidden rounded-xl bg-gray-50">
                                <img :src="interior.image.image_url" alt="" class="h-full w-full object-contain">
                                <button
                                    class="btn btn-warning bg-yellow absolute top-0 right-0"
                                    type="button"
                                    data-toggle="modal" data-target="#editInteriorModal"
                                    @click="edit(interior)"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-4 flex items-center">
                            <template v-if="interior.variants">
                                <div v-for="variant in interior.variants" class="flex items-center gap-2">
                                    <div class="w-[100px] h-[100px] bg-gray-50 overflow-hidden mr-2">
                                        <img :src="variant.images[0]?.image_url" alt="" class="w-full h-full object-contain">
                                    </div>
                                    <div>{{ variant.title }}</div>
<!--                                    <button @click="deleteVariant(interior)" class="btn btn-danger">-->
<!--                                        <i class="fas fa-times"></i>-->
<!--                                    </button>-->
                                </div>
                            </template>


                            <button
                                type="button" class="btn btn-primary bg-blue"
                                data-toggle="modal" data-target="#appendVariantModal"
                                v-else
                                @click="selectedInterior = interior"
                            >
                                Добавить
                            </button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <button ref="upload-image" class="hidden"
                type="button"
                data-toggle="modal" data-target="#appendImageModal"
        ></button>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Dropzone from "dropzone";
import Errors from "@/Components/Errors/Errors.vue";
import Spinner from "@/Components/Spinner.vue";
import FlashMessage from "@/Components/FlashMessage.vue";

const maxPointCount = 5

export default {
    name: "Index",
    props: ['interiors'],
    components: {FlashMessage, Spinner, Errors, AuthenticatedLayout},
    data() {
        return {
            editPoints: [],
            type: 'create',
            points:
            [
                {
                    id: 0,
                    point: {
                        xAsPercents: 0,
                        yAsPercents: 0
                    },
                    variant: null,
                    description: '',
                }
            ],
            selectedPointId: 0,
            file: null,
            dataUrl: null,
            selectedInterior: null,
            fetchedVariants: null,
            variantsPerPage: 25,
            term: null,
            isLoading: false,
            errors: null,
            interiorsForDropzone: this.interiors.map(interior => interior.id).slice()
        }
    },
    methods: {
        handleDropzoneClick() {

        },
        checkVariantsRepeats(points) {
            points.map(point => {
                if (!point?.variant?.id) throw Error(`Не для всех точек указаны варианты (${point.id})`)
            })

            for (let i = 0; i < points.length; i++) {
                let currentVariantId = points[i].variant.id

                for (let k = 0; k < points.length; k++) {
                    if (k === i) continue;
                    if (points[k].variant.id === currentVariantId) {
                        let variant = points[k].variant.title
                        throw Error('Варианты не должны повторяться -' + variant)
                    }
                }
            }
            // points.map((point, idx) => {
            //     if (!point?.variant.id) console.log(111)
            //         // throw Error('Не указан вариант для точки ' + point.id)
            //     let variantId = point.variant.id
            //     let pointsWithoutCurrent = points.slice(idx, 1)
            //     console.log(pointsWithoutCurrent)
            //     pointsWithoutCurrent.map(pointWithoutCurrent => {
            //         if (pointWithoutCurrent.variant.id === variantId)
            //             console.log('error')
            //             // throw Error('Варианты не должны повторяться!')
            //     })
            // })
        },
        edit(interior) {
            this.file = null
            this.selectedInterior = interior
            let points = []
            let id = 0
            this.selectedInterior.variants.map(variant => {
                let coords = {xAsPercents: +variant.pivot.left, yAsPercents: +variant.pivot.top}
                let point = {id: id++, variant: variant, point: coords, description: variant.pivot.description}
                points.push(point)
            })
            this.selectedPointId = null
            this.editPoints = points
        },
        deletePoint(point, type = 'create') {
            let currentPoints = type === 'create' ? this.points : this.editPoints
            if (currentPoints.length === 1) return alert('Нельзя удалить последнюю точку!')
            if (this.selectedPointId === point.id) {
                if (this.selectedPointId === 0) {
                    this.selectedPointId = 1
                } else {
                    this.selectedPointId = 0
                }
            }
            let index = currentPoints.indexOf(point)
            currentPoints.splice(index, 1)
            let id = 0
            currentPoints.map(p => {
                p.id = id++
            })
        },
        handleAddPoint(type = 'create') {
            let currentPoints = type === 'create' ? this.points : this.editPoints
            if (currentPoints.length === maxPointCount) return

            let newId = currentPoints.length
                ? currentPoints[currentPoints.length - 1].id + 1
                : 0
            let newPoint = {
                id: newId,
                point: {
                    xAsPercents: 0,
                    yAsPercents: 0,
                },
                variant: null
            }
            currentPoints.push(newPoint)
        },
        handleImageClick(event, type = 'create') {

            let rect = event.target.getBoundingClientRect()
            let width = rect.width
            let height = rect.height
            let offsetX = event.offsetX
            let offsetY = event.offsetY
            let offsetXAsPercents = Math.round(100 * (offsetX / width))
            let offsetYAsPercents = Math.round(100 * (offsetY / height))
            let point
            if (type === 'create') {
                 point = this.points.find(p => p.id === this.selectedPointId)
            } else {
                point = this.editPoints.find(p => p.id === this.selectedPointId)
            }
            if (point) {
                point.point = {
                    xAsPercents: offsetXAsPercents,
                    yAsPercents: offsetYAsPercents
                }
            }
        },
        appendVariantForm(variant) {
            let currentPoints = this.type === 'create' ? this.points : this.editPoints
            let searchedPoint = currentPoints.find(p => p.id === this.selectedPointId)
            searchedPoint.variant = variant
        },
        async handleUploadImage(file, interiorId) {
            this.selectedInterior = this.interiors.find(interior => interior.id === interiorId)
            const reader = new FileReader()
            reader.readAsDataURL(file)
            let vm = this
            reader.onload = (event) => vm.dataUrl = event.target.result
            this.$refs["upload-image"].click()
            this.file = file
            this.selectedPointId = 0
        },
        async storeInterior() {
           try {
               this.isLoading = true
               let points = []
               this.points.map(point => points.push(point))
               let formData = new FormData
               formData.append('image', this.file)
               points.map((point, idx) => {
                   formData.append(`points[${idx}][left]`, point?.point?.xAsPercents)
                   formData.append(`points[${idx}][top]`, point?.point?.yAsPercents)
                   formData.append(`points[${idx}][variant_id]`, point?.variant?.id)
                   formData.append(`points[${idx}][description]`, point?.description)
               })
               await axios.post(`/admin/interiors/${this.selectedInterior.id}`, formData)
               location.reload()
               this.isLoading = false
               this.$refs.pointsClose.click()
           } catch (e) {
               this.isLoading = false
               if (e?.response?.status === 422) return this.errors = e.response.data.errors
               alert(e?.message ?? e)
           }
        },
        async updateInterior() {
            try {
                this.isLoading = true
                let points = []
                this.editPoints.map(point => points.push({
                    description: point?.description,
                    left: point?.point?.xAsPercents,
                    top: point?.point?.yAsPercents,
                    variant_id: point?.variant?.id,
                }))
                this.checkVariantsRepeats(this.editPoints)
                await axios.patch(`/admin/interiors/${this.selectedInterior.id}/update`, {points})
                location.reload()
                this.isLoading = false
                this.$refs.pointsClose.click()
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                alert(e?.message ?? e)
            }
        },
        async clearInterior() {
          try {
              this.isLoading = true
              await axios.delete(`/admin/interiors/${this.selectedInterior.id}`)
              location.reload()
              this.isLoading = false
          } catch (e) {
              this.isLoading = false
              alert(e?.message ?? e)
          }
        },

        handleAppendVariant(point, type = 'create') {
            this.type = type
            this.selectedPointId = point.id
        },
        async search() {
            try {
                if (!this.term) return this.errors = [{search: 'Строка для поиска не должна быть пустой!'}]
                this.isLoading = true
                let response = await axios.get(`/admin/variants/search?term=${this.term}&count=${this.variantsPerPage}`)
                this.fetchedVariants = response.data
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status) return this.errors = e.response.data.errors
                alert(e.message ?? e)
            }
        },
        async searchWithPage(url) {
            try {
                this.isLoading = true
                let pageIndex = url.indexOf('=')
                let pageNumber = url.slice(pageIndex + 1)
                let response = await axios.get(`/admin/variants/search?term=${this.term}&count=${this.variantsPerPage}&page=${pageNumber}`)
                this.fetchedVariants = response.data
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                alert(e.message ?? e)
            }
        },
    },


    mounted() {
        this.interiorsForDropzone.map(item => {
            let obj = new Dropzone(this.$refs[`dropzone-${item}`][0], {
                url: '/',
                autoProcessQueue: false,
                maxFiles: 10,
                disablePreviews: true
            })
            obj.on("addedfile", (file) => {
                this.handleUploadImage(file, item)
            })
        })
    },

    watch: {
        term () {
            this.fetchedVariants = null
        },
        points: {
            handler: function (val)  {

                if (this.selectedPointId !== null) {
                    if (val && val[this.selectedPointId] && val[this.selectedPointId].point) {
                        const point = this.$refs[`point-${this.selectedPointId}`] ? this.$refs[`point-${this.selectedPointId}`][0] : null
                        if (point) {
                            point.style.top = `calc(${val[this.selectedPointId].point.yAsPercents}% - 10px)`
                            point.style.left = `calc(${val[this.selectedPointId].point.xAsPercents}% - 10px)`
                        }
                    }
                }
            },
            deep: true,
        },
        editPoints: {
            handler: function (val) {
                setTimeout(() => {
                    val.map(point => {
                        let pointRef = this.$refs[`edit-point-${point.id}`] ? this.$refs[`edit-point-${point.id}`][0] : null
                        if (pointRef) {
                            pointRef.style.top = `calc(${point.point.yAsPercents}% - 10px)`
                            pointRef.style.left = `calc(${point.point.xAsPercents}% - 10px)`
                        }
                    })
                })
            },
            deep: true
        }

    }
}
</script>

<style scoped>

</style>
