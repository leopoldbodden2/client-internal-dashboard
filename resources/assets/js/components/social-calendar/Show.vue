<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
    <div>
        <table class="table table-bordered table-striped table-hover mb-1 px-3">
            <thead>

            <tr>
                <th>Day</th>
                <th>Date</th>
                <th>Post</th>
                <th>Publisher</th>
                <th>Topic</th>
                <th>Comment</th>
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
                    {{ formatFromDate(post.publish_date) }}
                </td>

                <!-- Post Url -->
                <td style="vertical-align: middle;">
                    <a :href="post.post_url" target="_blank">
                        {{ post.post_url }}
                    </a>
                </td>

                <!-- Post Publisher -->
                <td style="vertical-align: middle;">
                    {{ post.post_publisher }}
                </td>

                <!-- Post Topic -->
                <td style="vertical-align: middle;">
                    {{ post.post_topic }}
                </td>

                <!-- Post Topic -->
                <td style="vertical-align: middle;">
                    <span
                        v-if="post.user_approved == true || post.user_approved == false" v-html="post.client_comment_html"></span>
                    <span
                        v-if="post.admin_comment!=''" v-html="'<br>'+post.admin_comment_html">
                    </span>
                </td>

                <!--  -->
                <td style="vertical-align: middle;">
                    <span class="badge badge-success" v-if="post.user_approved == true">Approved</span>
                    <span class="badge badge-danger" v-if="post.user_approved == false">Denied</span>
                    <button class="btn btn-sm btn-outline-success" @click="approvePost(index,post)"
                            v-if="post.user_approved == null">
                        Approve
                    </button>
                    <br>
                    <br>
                    <button class="btn btn-sm btn-outline-danger" @click="denyPost(index,post)"
                            v-if="post.user_approved == null">
                        Deny
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Modal -->
        <div class="modal fade" id="editCalendarPost" tabindex="-1" role="dialog" aria-labelledby="editCalendarPostTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="flex-column">
                            <h5 class="modal-title" id="editCalendarPostTitle" v-html="editModalBodyText"></h5>
                            <p>Feel free to leave a comment below.</p>
                        </div>
                        <button type="button" class="close"  @click="closeModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control"
                                  v-model="editClientComment"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" @click="closeModal">Close</button>
                        <button type="button" class="btn btn-primary" @click="updatePost">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['calenderid'],
        /*
         * The component's data.
         */
        data() {
            return {
                posts: [],
                editpost: {},
                editClientComment: '',
                editModal: {},
                editModalBodyText: ''
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

        computed: {},

        methods: {
            formatDayFromDate(postDate) {
                var day_string = '';
                var post_date = moment(postDate);
                if (post_date.isValid()) {
                    day_string = post_date.format('dddd');
                }
                return day_string;
            },
            formatFromDate(postDate) {
                var day_string = '';
                var post_date = moment(postDate);
                if (post_date.isValid()) {
                    day_string = post_date.format('D-MMM');
                }
                return day_string;
            },
            formatPostAsString(post) {
                return '<br>Day: ' + this.formatDayFromDate(post.publish_date) + ' <br>' +
                    'Date: ' + this.formatFromDate(post.publish_date) + ' <br>' +
                    'Url: ' + post.post_url + ' <br>' +
                    'Publisher: ' + post.post_publisher + ' <br>' +
                    'Topic: ' + post.post_topic;
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
                this.editModal = $('#editCalendarPost');
                axios['get'](`${APP_URL}/api/social-calendar/${this.calenderid}`).then(response => {
                    this.posts = response.data.posts;
                }).catch(error => {

                });
            },

            addCalendarRow() {
                this.posts.push({
                    publish_date: '',
                    post_url: '',
                    post_publisher: '',
                    post_topic: '',
                });
            },

            /**
             * Approve the given post.
             */
            approvePost(index, post) {
                this.editpost = post;
                this.editpost['user_approved'] = true;
                this.editModalBodyText = 'Approved';
                this.openClearModal();

            },
            /**
             * Destroy the given post.
             */
            denyPost(index, post) {
                this.editpost = post;
                this.editpost['user_approved'] = false;
                this.editModalBodyText = 'Declined';
                this.openClearModal();
            },

            updatePost() {
                var editpost = this.editpost;
                editpost['client_comment'] = this.editClientComment;
                editpost['_method'] = 'PUT';
                axios['put'](`${APP_URL}/api/social-calendar-post/${editpost.id}`, editpost).then(response => {
                    this.initCalendars()
                    this.editModal.modal('hide');
                }).catch(error => {

                });
            },

            openClearModal() {
                this.editClientComment = '';
                this.editModal.modal('show');
            },

            closeModal() {
                this.editModal.modal('hide');
                this.initCalendars()
            }
        },
    };
</script>
