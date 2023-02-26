<template>
    <AuthenticatedLayout>
        <div class="card">
            <div class="card-header">
                <div class="flex items-center">
                    <button class="btn btn-default mr-3">
                        <Link href="/admin/reviews">
                            Назад
                        </Link>
                    </button>
                    <div>
                        Отзыв
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-2">
                        <div>
                            Клиент:
                        </div>
                    </div>
                    <div class="col-10">
                        <div contenteditable="true" @input="setReviewName($event, review)" class="outline-none">
                            {{ review.name }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2">
                        Содержимое отзыва:
                    </div>
                    <div class="col-10">
                        <div contenteditable="true" @input="setReviewContent($event, review)" class="outline-none">
                            {{ review.content }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2">
                        Оценка:
                    </div>
                    <div class="col-10">
                        <div contenteditable="true" @input="setReviewMark($event, review)" class="outline-none">{{ review.mark }}</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2">
                        Товар:
                    </div>
                    <div class="col-10">
                        <div>{{review.variant.product.title}} {{ review.variant.title }}</div>
                    </div>
                </div>

                <template v-if="review.images && review.images.length">
                    <div class="row my-5">
                        <div class="col-12 text-xl">
                            Прикрепленные изображения
                        </div>
                    </div>

                    <div class="row" >
                        <div class="col-2" v-for="image in review.images">
                            <div class="relative w-[200px] h-[120px]">
                                <div class="absolute right-0 top-0 bg-gray opacity-50 w-[20px] h-[20px] cursor-pointer flex justify-center items-center">
                                    <i class="fas fa-times text-black"  @click="deleteImage(review, image)"></i>
                                </div>
                                <img :src="image.image_url" alt="" class="w-full h-full">
                            </div>
                        </div>
                    </div>
                </template>

                <div class="row my-5">
                    <div class="col-12 text-xl">
                        Ответ менеджера
                    </div>
                </div>

                <template v-if="review.answer">
                    <div class="row">
                        <div class="col-1">
                            {{review.answer.manager?.name}}
                        </div>

                        <div class="col-7">
                            {{review.answer.content}}
                        </div>
                        <div class="col-2">
                            {{formatDate(review.answer.created_at)}}
                        </div>
                        <div class="col-2">
                            <button class="btn btn-danger" @click="deleteAnswer">
                                Удалить
                            </button>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div class="form-group">
                        <label>Ответ</label>
                        <textarea class="form-control" v-model="newAnswer"/>
                    </div>
                </template>


                <div class="row mt-3">
                    <button class="btn btn-primary" @click="saveChanges(review)">
                        Сохранить
                    </button>
                </div>

            </div>
        </div>
<!--        <div class="border-t border-t-black my-3 p-2 col-12">-->



<!--            <div v-if="review.images && review.images.length" class="d-flex justify-start items-center m-3">-->

<!--                <div v-for="image in review.images" class="mx-5 relative">-->

<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {Link} from '@inertiajs/vue3'
import axios from "axios";
import {router} from "@inertiajs/vue3";
import {formatDate} from "@/utils/formatDate";
export default {
    name: "Show",
    components: {AuthenticatedLayout, Link},
    props: ['reviewData', 'user'],
    data() {
        return {
            review: this.cloneObject(this.reviewData),
            newAnswer: null
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
        setReviewMark(event, review) {
            let value = event.target.innerText
            review.mark = value
            review.edited = true
        },
        async deleteAnswer() {
            try {
                await axios.delete(`/admin/reviews/${this.review.id}/answer`)
                this.review.answer = null
            } catch (e) {
                alert(e.message ?? e)
            }
        },
        async saveChanges(review) {
            try {
                let data = {
                    ...review,
                    answer: this.newAnswer ?  {
                        content: this.newAnswer,
                        admin_id: this.user.id
                    } : null,
                }
                await axios.post(`/admin/reviews/${review.id}/save`, data)
                router.visit('/admin/reviews')
            } catch (e) {
                alert(e)
            }
        },
        cloneObject(obj) {
            return JSON.parse(JSON.stringify(obj))
        },
        formatDate(date) {
            return formatDate(date)
        }
    }
}
</script>

<style scoped>

</style>
