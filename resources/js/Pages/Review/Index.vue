<template>
    <AuthenticatedLayout>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Отзывы</h3>

                <!--                <div class="card-tools">-->
                <!--                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">-->
                <!--                        <i class="fas fa-minus"></i>-->
                <!--                    </button>-->
                <!--                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">-->
                <!--                        <i class="fas fa-times"></i>-->
                <!--                    </button>-->
                <!--                </div>-->
            </div>
            <div class="card-body p-0">
                <table class="table projects">
                    <thead>
                    <tr>
                        <th style="width: 1%">#</th>
                        <th style="width: 10%">Название</th>
                        <th>Клиент</th>
                        <th>Оценка</th>
                        <th>Содержимое</th>
                        <th style="width: 8%" class="text-center">Статус</th>
                        <th>Фото</th>
                        <th style="width: 20%"></th>
                    </tr>
                    </thead>
                    <tbody v-if="reviews.data && reviews.data.length">
                    <tr v-for="review in reviews.data" :class="{'bg-gray-200': !review.is_viewed}">
                        <td>
                            {{review.id}}
                        </td>
                        <td>
                            <a>
                                {{ review.variant.title }}
                            </a>
                            <br>
                            <small>
                                {{ formatDate(review.created_at) }}
                            </small>
                        </td>
                        <td>
                            <div>
                                {{ review.name }}
                            </div>

                            <!--                                    <img alt="Avatar" class="table-avatar" src="dist/img/avatar.png">-->
                        </td>
                        <td>
                            <!--                            <div class="progress progress-sm">-->
                            <!--                                <div class="progress-bar bg-green" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" style="width: 57%">-->
                            <!--                                </div>-->
                            <!--                            </div>-->
                            <small>
                                {{ review.mark }}
                            </small>
                        </td>
                        <td>
                            <div class="max-w-[200px] max-h-[50px] overflow-hidden"
                                 style="text-overflow: ellipsis; white-space: nowrap;">{{ review.content }}
                            </div>

                        </td>
                        <td>
                            {{ review.published ? 'Опубликован' : 'Не опубликован' }}
                        </td>
                        <td>
                            {{ review.images.length }}
                        </td>
                        <td>
                            <a class="btn btn-info btn-sm mr-2" @click="visitReview(review)">
                                <i class="fas fa-pencil-alt">
                                </i>
                                Редактировать
                            </a>
                            <a class="btn btn-danger btn-sm" @click="deleteReview(review)">
                                <i class="fas fa-trash"></i>
                                Удалить
                            </a>
                            <a class="btn btn-warning btn-sm ml-2" @click="publish(review)"
                               v-if="!review.published">
                                Опубликовать
                            </a>
                            <a v-else class="btn btn-warning btn-sm ml-2" @click="unpublicReview(review)">
                                Снять с публикации
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <Pagination :items="reviews" :fetch-page="fetchPage"/>
            </div>
        </div>



    </AuthenticatedLayout>

</template>

<script>
import axios from 'axios'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {formatDate} from "@/utils/formatDate";
import {router} from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";

export default {
    name: "Index",
    components: {Pagination, AuthenticatedLayout},
    props: ['reviewsData', 'productsData'],
    data() {
        return {
            reviews: this.reviewsData,
            products: this.productsData
        }
    },
    methods: {
        async visitReview(review) {
            review.is_viewed = true
            router.visit(`/admin/reviews/${review.id}`)
        },
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
                this.reviews.data = this.reviews.data.filter(rev => rev.id !== deletedReview.id)
            } catch (e) {
                alert(e)
            }
        },
        async unpublicReview(review) {
            try {
                let response = await axios.patch(`/admin/reviews/${review.id}/unpublic`)
                let existsReview = this.reviews.data.find(rev => rev.id === review.id)
                existsReview.published = 0
            } catch (e) {
                alert(e.message ?? e)
            }
        },
        async publish(review) {
            try {
                let response = await axios.post(`/admin/reviews/${review.id}/publish`, review)
                let newReview = response?.data
                let searchedReview = this.reviews.data.find(rev => rev.id === newReview.id)
                searchedReview.published = 1
                searchedReview.is_viewed = true
            } catch (e) {
                alert(e)
            }
        },
        async saveChanges(review) {
            try {
                let response = await axios.post(`/admin/reviews/${review.id}/save`, review)
                let newReview = response?.data
                let searchedReview = this.reviews.data.find(rev => rev.id === newReview.id)
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
        formatDate(date) {
            return formatDate(date)
        },
        fetchPage(url) {
            router.visit(url)
        }
    },
    mounted() {
        // console.log(this.productsData)
    }

}
</script>

<style scoped>

</style>
