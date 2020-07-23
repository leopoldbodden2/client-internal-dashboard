<template>
    <div class="container-fluid">
        <div class="row" v-show="alertMessage!=''">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible">
                    <p class="mb-0">{{ alertMessage }}</p>
                    <button type="button" class="close" aria-label="Close" @click="resetAlertMessage">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="row form-inline">
            <div class="form-group date-range-group">
                <label for="dateRange" class="col-auto col-form-label">Date Range</label>
                <div class="col-auto">
                    <select class="form-control" id="dateRange" v-model="dateRange" @change="fetchData">
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="60">Last 60 Days</option>
                        <option value="90">Last 90 Days</option>
                    </select>
                </div>
            </div>
            <div class="form-group ml-auto">
                <button type="button" class="btn btn-success" @click="createMessage">New SMS Message</button>
            </div>
        </div>
        <div class="modal fade" id="modal-sms-message" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">{{ modalTitle }}</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span class="far fa-times"></span>
                        </button>
                    </div>
                    <div class="modal-body justify-content-start">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="errorMessages.length > 0">
                            <ul class="mb-0">
                                <li v-for="error in errorMessages">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-form-label col-4">Name:</label>
                            <div class="col-8">
                                <input type="text" class="form-control" v-model="smsMessage.name" id="create_name" aria-label="Name" autocomplete="full-name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="subject" class="col-form-label col-4">Subject:</label>
                            <div class="col-8">
                                <input type="text" class="form-control" v-model="smsMessage.subject" id="subject" aria-label="Subject" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="body" class="col-form-label col-4">Body:</label>
                            <div class="col-8">
                                <textarea class="form-control" v-model="smsMessage.body" id="body" aria-label="Body" required></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-form-label col-4">Phone:</label>
                            <div class="col-8">
                                <input type="text" class="form-control" v-model="smsMessage.phone" aria-label="Phone" id="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-dismiss="modal">Close</button>
                        <div v-if="editing">
                            <button type="button" class="btn btn-success" @click="sendMessage(smsMessage.id)" v-if="smsMessage.attempted && smsMessage.successful">Re-Send message</button>
                            <button type="button" class="btn btn-success" @click="sendMessage(smsMessage.id)" v-if="!smsMessage.attempted || (smsMessage.attempted && !smsMessage.successful)">Send message</button>
                            <button type="button" class="btn btn-primary" @click="updateMessage" >Save Changes</button>
                        </div>
                        <div v-else>
                            <button type="button" class="btn btn-success" @click="saveSendMessage">Save &amp; Send</button>
                            <button type="button" class="btn btn-primary" @click="saveMessage">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                resourceUrl: '/api/sms-message',
                errorMessages: [],
                dateRange: '7',
                modalTitle: 'SMS Message',
                smsMessageModal: {},
                smsMessage: {
                    name: '',
                    subject: '',
                    body: '',
                    phone: '',
                    attempted: false,
                    successful: false
                },
                editing: false,
                alertMessage: ''
            }
        },
        methods: {
            saveSendMessage(){
                this.saveMessage().then((messageData) => {
                    this.sendMessage(messageData.id);
                });
            },
            resetAlertMessage(){
                this.alertMessage = '';
            },
            sendMessage(message_id){
                return axios.get(window.APP_URL+this.resourceUrl+'/'+message_id+'/send')
                     .then((response) => {
                         if(response.data.messageResponse){
                            if(response.data.messageResponse){
                                
                            }
                         }
                     })
                     .catch(error => {
                         if (typeof error.response.data === 'object') {
                             this.errorMessages = _.flatten(_.toArray(error.response.data.errors));
                         } else {
                             this.errorMessages = ['Something went wrong. Please try again.'];
                         }
                     });
            },
            createMessage(){
                this.modalTitle = 'Create SMS Message';
                this.smsMessageModal.modal('show');
            },
            editMessage(message_id){
                this.editing = true;
                this.modalTitle = 'Edit SMS Message';
                this.getMessage(message_id)
                    .then(() => {
                        this.smsMessageModal.modal('show');
                    });
            },
            getMessage(message_id){
                return axios.get(window.APP_URL+this.resourceUrl+'/'+message_id)
                     .then((response) => {
                        this.smsMessage = response.data;
                     })
                     .catch(error => {
                         if (typeof error.response.data === 'object') {
                             this.errorMessages = _.flatten(_.toArray(error.response.data.errors));
                         } else {
                             this.errorMessages = ['Something went wrong. Please try again.'];
                         }
                     });

            },
            saveMessage(){
                var postData = {
                    'name': this.smsMessage.name,
                    'subject': this.smsMessage.subject,
                    'body': this.smsMessage.body,
                    'phone': this.smsMessage.phone,
                };
                return axios.post(window.APP_URL+this.resourceUrl, postData)
                     .then((response) => {
                        this.smsMessageModal.modal('hide');
                        this.fetchData();
                        return response;
                     })
                     .catch(error => {
                         if (typeof error.response.data === 'object') {
                             this.errorMessages = _.flatten(_.toArray(error.response.data.errors));
                         } else {
                             this.errorMessages = ['Something went wrong. Please try again.'];
                         }
                     });

            },
            updateMessage(){
                var postData = {
                    '_method': 'PUT',
                    'name': this.smsMessage.name,
                    'subject': this.smsMessage.subject,
                    'body': this.smsMessage.body,
                    'phone': this.smsMessage.phone
                };
                axios.post(window.APP_URL+this.resourceUrl+'/' +this.smsMessage.id, postData)
                     .then((response) => {
                        this.smsMessageModal.modal('hide');
                        this.fetchData();
                     })
                     .catch(error => {
                         if (typeof error.response.data === 'object') {
                             this.errorMessages = _.flatten(_.toArray(error.response.data.errors));
                         } else {
                             this.errorMessages = ['Something went wrong. Please try again.'];
                         }
                     });
            },
            resetForm(){
                this.editing = false;
                this.smsMessage = {
                    name: '',
                    subject: '',
                    body: '',
                    phone: '',
                    attempted: false,
                    successful: false
                };
                this.errorMessages = [];
            },
            fetchData(){
                window.LaravelDataTables['dataTableBuilder'].ajax.reload();
            }
        },
        mounted(){
            if (localStorage.dateRange) {
                this.dateRange = localStorage.dateRange;
            }
            this.smsMessageModal = $('#modal-sms-message');
            this.smsMessageModal.on('hidden.bs.modal', this.resetForm);
            window.editCdyneMessage = this.editMessage;
            window.sendCdyneMessage = this.sendMessage;
        }
    }
</script>
