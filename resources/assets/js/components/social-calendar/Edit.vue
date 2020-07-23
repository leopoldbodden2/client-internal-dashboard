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
                        <th>Status/Comment</th>
                        <th>Admin Comment</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                <tr v-for="(post,index) in editposts">
                    <!-- Day -->
                    <td style="vertical-align: middle;">
                        {{ formatDayFromDate(post.publish_date) }}<br>
                    </td>

                    <!-- Date -->
                    <td style="vertical-align: middle;">
                        <input :name="'editpost['+index+'][publish_date]'" type="date" id="editpost_date" class="form-control" v-model="post.publish_date">
                    </td>

                    <!-- Post Url -->
                    <td style="vertical-align: middle;">
                        <input :name="'editpost['+index+'][post_url]'" type="url" id="editpost_url" class="form-control" v-model="post.post_url" placeholder="Enter a url here">
                    </td>

                    <!-- Post Publisher -->
                    <td style="vertical-align: middle;">
                        <input :name="'editpost['+index+'][post_publisher]'" type="text" id="editpost_publisher" class="form-control" v-model="post.post_publisher">
                    </td>

                    <!-- Post Topic -->
                    <td style="vertical-align: middle;">
                        <input :name="'editpost['+index+'][post_topic]'" type="text" id="editpost_topic" class="form-control" v-model="post.post_topic">
                    </td>

                    <td style="vertical-align: middle;">
                        <span class="badge badge-success" v-if="post.user_approved == true">Approved</span>
                        <span class="badge badge-danger" v-if="post.user_approved == false">Denied</span>

                        <span v-if="post.client_comment!=''">
                            <br>
                            {{post.client_comment}}
                        </span>
                    </td>

                    <td style="vertical-align: middle;">
                        <textarea :name="'editpost['+index+'][admin_comment]'" id="edit_admin_comment" class="form-control"
                              v-model="post.admin_comment"></textarea>
                    </td>

                    <!--  -->
                    <td style="vertical-align: middle;">
                        <!-- <button class="btn btn-outline-primary" @click="updatePost(index,post)">
                            Update
                        </button> -->
                        <button type="button" class="btn btn-outline-danger" @click="deletePost(index,post)">
                            Remove
                        </button>
                        <input :name="'editpost['+index+'][id]'" type="hidden" id="editpost_id" class="form-control" v-model="post.id">
                    </td>
                </tr>

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

                    <td></td>

                    <td>
                        <textarea :name="'post['+index+'][admin_comment]'" id="admin_comment" class="form-control"
                                  v-model="post.admin_comment"></textarea>
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
        <div class="col">
            <pre>{{calendarurl}}</pre>
        </div>
        <div class="col">
            <button type="button"
                    v-clipboard:copy="calendarurl"
                    v-clipboard:success="onCopy"
                    v-clipboard:error="onError"
                    class="btn btn-outline-info">{{calendarBtntext}}</button>
        </div>

    </div>
</template>

<script>
    export default {
        props: ['calendarid','calendarurl'],
        /*
         * The component's data.
         */
        data() {
            return {
                editposts: [],
                posts: [],
                calendarBtntext: 'Copy calendar url'
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
            formatFromDate(postDate) {
                var day_string = '';
                var post_date = moment(postDate);
                if(post_date.isValid()) {
                    day_string = post_date.format('D-MMM');
                }
                return day_string;
            },
            formatPostAsString(post) {
                return "\nDay: " + this.formatDayFromDate(post.publish_date) + " \n" +
                    "Date: " + this.formatFromDate(post.publish_date) + " \n" +
                    "Url: " + post.post_url + " \n" +
                    "Publisher: " + post.post_publisher + " \n" +
                    "Topic: " + post.post_topic;
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
                axios['get'](`${APP_URL}/api/social-calendar/${this.calendarid}`)
                    .then(response => {
                        this.editposts = response.data.posts;
                    })
                    .catch(error => {

                    });
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
             * Update the given post.
             */
            updatePost(index,post) {
                post['_method'] = 'PUT';
                post['user_approved'] = true;
                this.posts.splice(index, 1)
                axios['put'](`/api/social-calendar-post/${post.id}`,post)
                    .then(response => {
                        this.initCalendars()
                    })
                    .catch(error => {

                    });

            },

            /**
             * Destroy the given client.
             */
            deletePost(index,post) {
                if(confirm('Are you sure you want to delete this row?')) {
                    this.posts.splice(index, 1)
                    axios['delete'](`${APP_URL}/api/social-calendar-post/${post.id}`)
                    .then(response => {
                        this.initCalendars()
                    })
                    .catch(error => {

                    });
                }
            },

            /**
             * Destroy the given client.
             */
            destroyPost(index,post) {
                this.posts.splice(index, 1)
            },
            onCopy: function (e) {
                this.calendarBtntext = 'Url copied!';

                var self = this;
                setTimeout(() => {
                    self.calendarBtntext = 'Copy calendar url';
                },2500);
            },
            onError: function (e) {

            }
        }
    }
</script>
