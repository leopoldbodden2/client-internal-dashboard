<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <div class="col">
            <table class="table table-bordered table-striped table-hover mb-1 px-3">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Date</th>
                        <th>Post</th>
                        <th>Publisher</th>
                        <th>Topic</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody v-if="posts.length > 0">
                <tr v-for="(post,index) in posts">
                    <!-- Day -->
                    <td style="vertical-align: middle;">
                        {{ formatDayFromDate(post.publish_date) }}<br>
                    </td>

                    <!-- Date -->
                    <td style="vertical-align: middle;">
                        <input :name="'post['+index+'][publish_date]'" type="date" id="post_date" class="form-control" v-model="post.publish_date">
                    </td>

                    <!-- Post Url -->
                    <td style="vertical-align: middle;">
                        <input :name="'post['+index+'][post_url]'" type="url" id="post_url" class="form-control" v-model="post.post_url" placeholder="Enter a url here">
                    </td>

                    <!-- Post Publisher -->
                    <td style="vertical-align: middle;">
                        <input :name="'post['+index+'][post_publisher]'" type="text" id="post_publisher" class="form-control" v-model="post.post_publisher">
                    </td>

                    <!-- Post Topic -->
                    <td style="vertical-align: middle;">
                        <input :name="'post['+index+'][post_topic]'" type="text" id="post_topic" class="form-control" v-model="post.post_topic">
                    </td>

                    <!-- Delete Button -->
                    <td style="vertical-align: middle;">
                        <button type="button" class="btn btn-outline-danger" @click="destroyPost(index,post)">
                            Delete
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="form-group">
                <button type="button" class="btn btn-success" @click="addCalendarRow()">
                    <span class="fa fa-plus"></span> Add Calendar Post
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        /*
         * The component's data.
         */
        data() {
            return {
                posts: [],
            };
        },

        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
            this.prepareComponent();
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.prepareComponent();
        },

        computed: {
        },

        methods: {
            formatDayFromDate(postDate) {
                var day_string = '';
                var post_date = moment(postDate);
                if(post_date.isValid()) {
                    day_string = post_date.format('dddd');
                }
                return day_string;
            },
            /**
             * Prepare the component.
             */
            prepareComponent() {
                this.initCalendars();
            },

            /**
             * Get all of the OAuth clients as admin.
             */
            initCalendars() {

            },

            addCalendarRow() {
                this.posts.push({
                    publish_date: '',
                    post_url: '',
                    post_publisher: '',
                    post_topic: ''
                })
            },

            /**
             * Destroy the given client.
             */
            destroyPost(index,post) {
                this.posts.splice(index, 1)
            }
        }
    }
</script>
