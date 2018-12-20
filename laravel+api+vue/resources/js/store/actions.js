/* actions - are called with "dispatch"
- for HTTP queries mostly,
- they also must call mutations after data is received */

let actions = {
    getCards: (context, params) => {

        axios.get(`/storage/cards`, {params: params})
            .then((response) => {
                if (response.status === 200) {
                    context.commit('getCards', response.data.cards, params ? params.page : null);
                }
            })
            .catch((error) => {
                console.error(error);
            });
    }
};

export default actions;
