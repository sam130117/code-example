import {observable, action} from "mobx";


class UsersStore
{
    @observable users;
    @observable authUserId;

    constructor()
    {
        this.users = [];
        this.authUserId = null;
    }

    @action getUsers()
    {
        axios.get('/react/users')
            .then((response) => {
                if (response.status === 200) {
                    this.users = response.data.users;
                    this.authUserId = response.data.authUserId;
                }
            })
            .catch((e) => console.error('Error getting users.', e));
    }

    @action deleteUser(id, index)
    {
        if (confirm('Do you really want to delete current user?')) {
            axios.delete(`/react/users/${id}`)
                .then((response) => {
                    if (response.status === 200)
                        this.users.splice(index, 1);
                })
                .catch((e) => console.error('You cannot delete this user.', e))
        }
    }

    @action updateUserRights(index, id)
    {
        axios.put('/react/users/' + id + '/update-rights')
            .then((response) => {
                if (response.status === 200)
                    this.users[index].extended_rights = !this.users[index].extended_rights;
            })
            .catch((e) => console.error('You cannot delete this user.', e))
    }

}

let usersStore = new UsersStore();
export default usersStore;