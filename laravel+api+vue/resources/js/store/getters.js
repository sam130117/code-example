/* getters - do not change data */

let getters = {
    getCardsTotalValueSum: state => {
        return state.cards.reduce(function (prev, current) {
            return prev + current.total_value;
        }, 0);
    },
    getCardsMaxTotalValue: state => {
        return Math.max.apply(Math, state.cards.map(function(item) { return item.total_value; }))
    },
};
export default getters;
