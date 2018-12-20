<template>
    <div>
        <h2>Cards</h2>
        <!--<span>{{ getCardsTotalValueSum }}</span>- -->
        <!--<span>{{ getCardsMaxTotalValue }}</span>-->
        <!--<button v-on:click="reduceTotalValue(5)">sdsd</button>-->
        <div>
            <hr/>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <input class="form-control" type="text" placeholder="Search..." v-on:input="handleFilter" v-model="search">
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <select class="form-control" v-on:change="handleFilter" v-model="cardType">
                        <option selected value="">Select Card Type...</option>
                        <option>{{ this.cardConstants.TYPE_DEBIT }}</option>
                        <option>{{ this.cardConstants.TYPE_CREDIT }}</option>
                    </select>
                </div>
            </div>
            <hr/>
            <div class="cards-scroll-container" v-on:scroll="handleScroll">
                <div id="cardsContainer" class="row">
                    <template v-if="cards && cards.length">
                        <div class="col-lg-3 col-md-4 col-sm-6 p-3 card" v-for="card in cards">
                            <router-link :to="{name: 'case', params: { caseId: card.case_id }}" class="card-link">
                                <h5 class="row">
                                    <span class="col-sm-8">
                                        <span>{{ card.name }}</span>
                                    </span>
                                    <span class="col-sm-4">
                                        <span class="badge" v-bind:class="getColorClassesByType(card.card_type)">
                                            {{ card.card_type }}
                                        </span>
                                    </span>
                                </h5>
                            </router-link>
                            <hr/>
                            <p class="my-1">Last Number: {{ card.last_number }}</p>
                            <p class="my-1">Total Value: {{ card.total_value }}</p>
                            <p class="my-1">Close Date: {{ card.close_date }}</p>
                        </div>
                    </template>
                    <template v-else>
                        <h5>No results.</h5>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {cardConstants} from '../../constants';

    export default {
        name   : "Cards",
        data   : () => {
            return {
                search       : null,
                cardType     : '',
                didScroll    : true,
                page         : 1,
                cardConstants: cardConstants,
                cards        : [],
            }
        },
        methods: {
            getCards(scroll = null)
            {
                let params = this.getFilterParams();
                axios.get(`/storage/cards`, {params: params})
                    .then((response) => {
                        if (response.status === 200) {
                            if (scroll) {
                                this.didScroll = response.data.cards.next_page_url;
                                this.cards = [...this.cards, ...response.data.cards.data];
                            } else
                                this.cards = response.data.cards.data;
                        }
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            },
            getColorClassesByType(type)
            {
                let classes = {};
                if (type === cardConstants.TYPE_CREDIT)
                    classes['badge-primary'] = true;
                else
                    classes['badge-success'] = true;
                return classes;
            },
            getFilterParams()
            {
                return Object.assign({}, {
                    cardType: this.cardType ? this.cardType : null,
                    search  : this.search ? this.search : null,
                    page    : this.page === 1 ? null : this.page,
                });
            },
            handleFilter()
            {
                this.page = 1;
                this.didScroll = true;
                this.getCards();
            },
            handleScroll(e)
            {
                let container = e.target;
                let content = document.getElementById('cardsContainer');
                let scrollTrip = container.scrollTop / (content.scrollHeight - container.clientHeight) * 100;

                if (scrollTrip > 60 && this.didScroll) {
                    this.page++;
                    this.didScroll = false;
                    this.getCards(true);
                }
            }
        },
        created()
        {
            this.getCards();
        }
    }
</script>

