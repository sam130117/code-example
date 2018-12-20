import React, {Component} from 'react';
import {observer, inject} from "mobx-react";
import User               from './User';

@inject('usersStore')
@observer
class Users extends Component
{
    constructor(props, context)
    {
        super(props, context);
    }

    componentDidMount()
    {
        this.props.usersStore.getUsers();
    }

    render()
    {
        let {users} = this.props.usersStore;

        return (
            <div className="panel panel-default">
                <div className="panel-heading clearfix">
                    <h3><span>Users</span> <a href="/register" className={`btn btn-success pull-right`}>Add User <span className={`fa fa-plus`}></span></a></h3>
                </div>
                <div className="panel-body">
                    <table className="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Can move cards</th>
                            <th scope="col">Created</th>
                            <th scope="col" width="15%">Settings</th>
                        </tr>
                        </thead>
                        <tbody>
                        {users.map((user, index) => (
                            <User key={user.id} user={user} index={index}/>
                        ))}
                        </tbody>
                    </table>
                </div>
            </div>
        )
    }
}

export default Users;