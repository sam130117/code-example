<template>
    <div>

        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <h2> Cases </h2>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-6">
                <div class="row">
                    <div class="col-lg-4 offset-lg-8 col-md-6 offset-md-6">
                        <input class="form-control" type="text" placeholder="Search..." v-on:input="handleFilter" v-model="search">
                    </div>
                </div>
            </div>
        </div>
        <!--<tabs>-->
        <!--<tab name="First tab">-->
        <!--First tab content-->
        <!--</tab>-->
        <!--<tab name="Second tab">-->
        <!--Second tab content-->
        <!--</tab>-->
        <!--<tab name="Third tab">-->
        <!--Third tab content-->
        <!--</tab>-->
        <!--</tabs>-->
        <table class="table table-bordered my-2">
            <tbody>
            <tr v-for="caseData in cases.data" :key="caseData.id">
                <td>
                    <router-link :to="{name: 'case', params: { caseId: caseData.id }}" class="card-link">
                        {{ caseData.title}}
                    </router-link>
                </td>
            </tr>
            </tbody>
        </table>
        <pagination class="justify-content-center" :data="cases" :show-disabled="true" :limit="5" @pagination-change-page="getCases"></pagination>
    </div>
</template>

<script>
    import Pagination from 'laravel-vue-pagination';

    export default {
        name      : "Cases",
        data      : () => {
            return {
                cases : {},
                search: null,
            };
        },
        components: {
            Pagination
        },
        methods   : {
            getCases(page = 1)
            {
                let params = {page: page};
                if(this.search)
                    params.search = this.search;

                axios.get('/storage/cases', {params: params})
                    .then((response) => {
                        if (response.status === 200) {
                            console.log(response);
                            this.cases = response.data.cases;
                        }
                    }).catch((error) => console.error(error));
            },
            handleFilter()
            {
                this.getCases(1);
            }
        },
        created()
        {
            this.getCases();
        }
    }
</script>

<style scoped>

</style>
