import {observable, action} from "mobx";

class BanksStore
{
    search;
    pagination;
    @observable banks;
    @observable errors;
    @observable isLoading;

    constructor()
    {
        this.isLoading = true;
        this.search = null;
        this.banks = [];
        this.errors = [];
        this.pagination = {
            pageRangeDisplayed: 10,
            total             : null,
            perPage           : null,
            page              : null,
        };
    }

    @action changeBankStatus(index)
    {
        this.banks[index].is_active = !this.banks[index].is_active;
    }

    @action("Get banks data")
    getBanks()
    {
        if (this.search && this.search.length < 2)
            return;

        this.isLoading = true;
        axios.get('/react/banks', {params: {page: this.pagination.page, search: this.search}})
            .then((response) => {
                if (response.status === 200) {
                    this.isLoading = false;
                    this.pagination.page = response.data.current_page;
                    this.pagination.perPage = response.data.per_page;
                    this.pagination.total = response.data.total;

                    this.banks = response.data.data;
                }
            })
            .catch((e) => console.error('Error getting banks.', e));
    }

    @action deleteBank(id, index)
    {
        if (confirm('Do you really want to delete it?')) {
            axios.delete('/react/banks/' + id)
                .then((response) => {
                    if (response.status === 200)
                        this.banks.splice(index, 1);
                })
                .catch((e) => console.error('You cannot delete this bank.', e))
        }
    }

    @action addBank()
    {
        let banks = this.banks;
        isEmpty(banks) || banks[0].id ?
            banks.unshift({
                id: null, bank_name: '', bank_phone: '', contact_name: '', email: '', address: '', url: '', bank_dispute_process: '', bank_hours: '', notes: '', is_active: true
            }) : banks.shift();
    }

    @action saveBank(id = '', index)
    {
        let url = id ? '/react/banks/' + id : '/react/banks';
        let method = id ? 'put' : 'post';

        axios[method](url, this.banks[index])
            .then((response) => {
                if (response.status === 200)
                    this.changeBankStatus(index);
            })
            .catch((e) => {
                if (e.response.status === 422)
                    this.errors = e.response.data.errors;
                else
                    console.error(e.response);
            })
    }
}

const bankStore = new BanksStore();
export default bankStore;