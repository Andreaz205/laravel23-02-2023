<template>
    <AuthenticatedLayout>
        <div class="d-flex justify-center items-center text-lg mb-5">
            Панель редактирования отзывов
        </div>

        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-one-home-tab" data-toggle="pill"
                           href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                           aria-selected="false">Не опубликованные</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-profile-tab" data-toggle="pill"
                           href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile"
                           aria-selected="true">Опубликованные</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade" id="custom-tabs-one-home" role="tabpanel"
                         aria-labelledby="custom-tabs-one-home-tab">
                        <div v-for="review in reviews" class="row">

                            <div v-if="!review.published" class="border-t border-t-black my-3 p-2 col-12">
                                <div>{{ review.id }}</div>
                                <div contenteditable="true" @input="setReviewName($event, review)" class="outline-none">
                                    {{ review.name }}
                                </div>
                                <div contenteditable="true" @input="setReviewContent($event, review)" class="outline-none">
                                    {{ review.content }}
                                </div>
                                <div>Оценка: {{ review.mark }}</div>
                                <div>{{ review.variant_id }}</div>
                                <div v-if="review.images && review.images.length" class="d-flex justify-start items-center m-3">

                                    <div v-for="image in review.images" class="mx-5 relative">
                                        <div class="absolute right-0 top-0 bg-white cursor-pointer">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <img :src="image.image_url" alt="" width="100" height="100">
                                    </div>
                                </div>

                                <button class="btn btn-primary" @click="saveChangesAndPublic(review)">
                                    Сохранить и опубликовать
                                </button>

                                <button class="btn btn-danger" @click="deleteReview(review)">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade active show" id="custom-tabs-one-profile" role="tabpanel"
                         aria-labelledby="custom-tabs-one-profile-tab">
                        <div v-if="reviews" class="row">
                            <div v-for="review in reviews" class="col-12">
                                <div v-if="review.published" class="border-t border-t-black my-3">
                                    <div>{{ review.variant.product.title }} {{ review.variant.title }}</div>
                                    <div contenteditable="true" @input="setReviewName($event, review)" class="outline-none">
                                        {{ review.name }}
                                    </div>
                                    <div contenteditable="true" @input="setReviewContent($event, review)" class="outline-none">
                                        {{ review.content }}
                                    </div>
                                    <div>Оценка: {{ review.mark }}</div>

                                    <div v-if="review.images && review.images.length" class="d-flex justify-start items-center m-3">
                                        <div v-for="image in review.images" class="mx-5 relative">
                                            <div class="absolute right-0 top-0 bg-white cursor-pointer"
                                                 @click="deleteImage(review, image)">
                                                <i class="fas fa-times"></i>
                                            </div>
                                            <img :src="image.image_url" alt="" width="100" height="100">
                                        </div>
                                    </div>
                                    <button class="btn btn-primary" @click="saveChanges(review)">
                                        Сохранить
                                    </button>
                                    <button class="btn btn-danger" @click="deleteReview(review)">
                                        Удалить
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </AuthenticatedLayout>

</template>

<script>
import axios from 'axios'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    name: "Index",
    components: {AuthenticatedLayout},
    props: ['reviewsData', 'productsData'],
    data() {
        return {
            reviews: this.reviewsData,
            products: this.productsData
        }
    },
    methods: {
        deleteImage(review, image) {
            if (review.images_for_delete && review.images_for_delete.length) {
                review.images_for_delete.push(image.id)
            } else {
                review.images_for_delete = []
                review.images_for_delete.push(image.id)
            }
            review.images = review.images.filter(img => img.id !== image.id)
        },
        async deleteReview(review) {
            try {
                let response = await axios.delete(`/admin/reviews/${review.id}/delete`)
                let deletedReview = response?.data
                this.reviews = this.reviews.filter(rev => rev.id !== deletedReview.id)
            } catch (e) {
                alert(e)
            }
        },
        async saveChangesAndPublic(review) {
            try {
                let response = await axios.post(`/admin/reviews/${review.id}/save-public`, review)
                let newReview = response?.data
                let searchedReview = this.reviews.find(rev => rev.id === newReview.id)
                searchedReview.published = 1
            } catch (e) {
                alert(e)
            }
        },
        async saveChanges(review) {
            try {
                let response = await axios.post(`/admin/reviews/${review.id}/save`, review)
                let newReview = response?.data
                let searchedReview = this.reviews.find(rev => rev.id === newReview.id)
                searchedReview = newReview
            } catch (e) {
                alert(e)
            }
        },
        setReviewName(event, review) {
            let value = event.target.innerText
            review.name = value
            review.edited = true
        },
        setReviewContent(event, review) {
            let value = event.target.innerText
            review.content = value
            review.edited = true
        },
    },
    mounted() {
        console.log(this.productsData)
    }

}
</script>

<style scoped>

</style>
