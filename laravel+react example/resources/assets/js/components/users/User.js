import React, {Component} from 'react';
import {observer, inject} from "mobx-react";

@inject('usersStore')
@observer
class User extends Component
{
    constructor(props, context)
    {
        super(props, context);
    }

    render()
    {
        let {user, index, usersStore} = this.props;

        return (
            <tr>
                <th scope="row">{index + 1}</th>
                <td>{user.name}</td>
                <td>{user.email}</td>
                <td>{!isEmpty(user.roles) ? user.roles[0].name : ''}</td>
                <td>{<div className="styled-checkbox-container">
                    <span></span>
                    <label className="rounded-checkbox bg-light-blue" style={{padding: '0 2px'}}>
                        <input type="checkbox" value={index} checked={user.extended_rights} onChange={() => usersStore.updateUserRights(index, user.id)}/>
                        <span className="checkmark"> </span>
                    </label>
                </div>}
                </td>
                <td>{user.created_at}</td>
                <td>
                    <a href={`/work-hours/users/${user.id}`} className="btn btn-success btn-xs mx-5">Work Hours</a>
                    <a href={`/users/${user.id}`} className="btn btn-warning btn-xs mx-5">Edit</a>

                    {usersStore.authUserId != user.id ? <a className="btn btn-danger btn-xs mx-5" onClick={() => usersStore.deleteUser(user.id, index)}>Delete</a> : ''}
                </td>
            </tr>)
    }
}

export default User;
