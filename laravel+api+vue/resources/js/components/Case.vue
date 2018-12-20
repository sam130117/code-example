<template>
    <div class="container">
        <h4 class="mt-4">
            <span>Case</span>
            <template v-if="this.mode === this.commonConstants.GENERAL_MODE">
                <span class="btn fa fa-trash float-right mx-2" v-on:click="showDeleteModal(null)"></span>
                <span class="btn fa fa-edit float-right mx-2" v-on:click="changeMode"></span>
            </template>
            <template v-else>
                <span class="btn fa fa-save float-right" v-on:click="save"></span>
            </template>
        </h4>
        <hr/>
        <div class="row">
            <div class="col-lg-4 ">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th width="30%">Title</th>
                        <td>
                            <div class="form-group mb-0">
                                <span v-model="caseData.title" v-if="this.mode === this.commonConstants.GENERAL_MODE">{{ caseData.title }}</span>
                                <input class="form-control" v-bind:class="{'is-invalid': this.errors.title}" type="text" v-model="caseData.title" v-else/>
                                <strong class="invalid-feedback">{{this.errors.title ? this.errors.title[0] : ''}}</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Client Email</th>
                        <td>
                            <div class="form-group mb-0">
                                <span v-model="caseData.client_email" v-if="this.mode === this.commonConstants.GENERAL_MODE">{{ caseData.client_email }}</span>
                                <input class="form-control" v-bind:class="{'is-invalid': this.errors.client_email}" type="text" v-model="caseData.client_email" v-else/>
                                <strong class="invalid-feedback">{{this.errors.client_email ? this.errors.client_email[0] : ''}}</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Website</th>
                        <td>
                            <div class="form-group mb-0">
                                <span v-model="caseData.website" v-if="this.mode === this.commonConstants.GENERAL_MODE">{{ caseData.website }}</span>
                                <input class="form-control" v-bind:class="{'is-invalid': this.errors.website}" type="text" v-model="caseData.website" v-else/>
                                <strong class="invalid-feedback">{{this.errors.website ? this.errors.website[0] : ''}}</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td>
                            <div class="form-group mb-0">
                                <span v-model="caseData.country" v-if="this.mode === this.commonConstants.GENERAL_MODE">{{ caseData.country }}</span>
                                <input class="form-control" v-bind:class="{'is-invalid': this.errors.country}" type="text" v-model="caseData.country" v-else/>
                                <strong class="invalid-feedback">{{this.errors.country ? this.errors.country[0] : ''}}</strong>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Agent</th>
                        <td>
                            <div class="form-group mb-0">
                                <span v-model="caseData.user_id" v-if="this.mode === this.commonConstants.GENERAL_MODE">{{ caseData.user ? caseData.user.name : '' }}</span>
                                <select class="form-control" v-bind:class="{'is-invalid': this.errors.user_id}" v-model="caseData.user_id" v-else>
                                    <option selected :value="null">Select Agent</option>
                                    <option v-for="user in users" :value="user.id">{{ user.name}}</option>
                                </select>
                                <strong class="invalid-feedback">{{this.errors.user_id ? this.errors.user_id[0] : ''}}</strong>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-8 ">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="25%">Name</th>
                        <th width="12%">Last Number</th>
                        <th>Total Value</th>
                        <th width="12%">Card Type</th>
                        <th width="14%">Close Date</th>
                        <th width="60px"></th>
                    </tr>
                    </thead>phpcoder2020@outlook.com kg$&85ruGFI


                    <tbody>
                    <cards-table-item
                        :key="card.id"
                        :card="card"
                        :index="index"
                        :mode="mode"
                        :errors="errors"
                        @deleteCard="showDeleteModal(index)"
                        v-for="(card, index) in cards"></cards-table-item>
                    </tbody>
                </table>
            </div>
        </div>

        <modals-container @close="hideDeleteModal"/>
    </div>
</template>

<script>
    import CardsTableItem          from './cards/CardsTableItem';
    import {commonConstants}       from '../constants';
    import DeleteConfirmationModal from "./common/DeleteConfirmationModal";

    export default {
        name      : "Case",
        components: {
            CardsTableItem,
        },
        data      : () => {
            return {
                caseData       : {},
                errors         : {},
                users          : [],
                mode           : commonConstants.GENERAL_MODE,
                commonConstants: commonConstants,
            }
        },
        computed  : {
            cards()
            {
                return this.caseData.cards;
            }
        },
        methods   : {
            getCase()
            {
                let id = this.$route.params.caseId;
                axios.get(`/storage/cases/${id}`)
                    .then((response) => {
                        if (response.status === 200) {
                            this.caseData = response.data.case;
                            this.users = response.data.users;
                        }
                    })
                    .catch((error) => console.error(error));
            },
            changeMode()
            {
                this.mode = (this.mode === commonConstants.GENERAL_MODE) ? commonConstants.EDIT_MODE : commonConstants.GENERAL_MODE;
            },
            save()
            {
                this.beforeSave();
                let id = this.caseData.id;
                axios.put(`/storage/cases/${id}`, this.caseData)
                    .then((response) => {
                        if (response.status === 200) {
                            this.mode = (this.mode === commonConstants.GENERAL_MODE) ? commonConstants.EDIT_MODE : commonConstants.GENERAL_MODE;
                        }
                    })
                    .catch((error) => {
                        if (error.response) {
                            this.errors = error.response.data.errors;
                        }
                        console.error(error)
                    });
            },
            beforeSave()
            {
                if (!this.caseData.user_id)
                    this.caseData.user_id = null;
            },
            deleteCard(index)
            {
                axios.delete(`/storage/cards/${this.cards[index].id}`)
                    .then((response) => {
                        this.cards.splice(index, 1);
                    })
                    .catch((error) => console.error(error));
            },
            deleteCase()
            {
                axios.delete(`/storage/cases/${this.caseData.id}`)
                    .then((response) => {
                        this.$router.push({ name: 'cards', params: { userId: '123' } })

                    })
                    .catch((error) => console.error(error));
            },
            showDeleteModal(index)
            {
                let questionText = index !== null
                    ? `Are you sure you want to delete card ${this.cards[index].name}?`
                    : `Are you sure you want to delete case ${this.caseData.title}?`;
                let data = {text: questionText};
                if (index)
                    data.index = index;

                this.$modal.show(DeleteConfirmationModal, data,
                    {height: '180px', pivotY: 0.1, draggable: true},
                );
            },
            hideDeleteModal(data)
            {
                let response = data.response;
                let index = data.index;
                if (response === 'yes') {
                    if (index)
                        this.deleteCard(index);
                    else
                        this.deleteCase();
                }
            }
        },
        created()
        {
            this.getCase();
        },
    }
</script>

<style scoped>

</style>
