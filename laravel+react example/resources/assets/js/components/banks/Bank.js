import React, {Component} from 'react';
import PropTypes          from 'prop-types';
import {observer, inject} from "mobx-react";

@inject('banksStore')
@observer
class Bank extends Component
{
    constructor(props, context)
    {
        super(props, context);
    }

    handleChange(e, propName)
    {
        this.props.bank[propName] = e.target.value;
    }

    getTextAreaItem(bank, propName)
    {
        return bank.is_active ?
            <div className="editable-group form-group">
                <textarea className="form-control" rows="3" value={bank[propName] || ''} onChange={(e) => this.handleChange(e, propName)}/>
            </div> :
            <div className="readonly-group">{bank[propName]}</div>
    }

    getTextItem(bank, propName)
    {
        let errors = this.props.banksStore.errors;

        return bank.is_active ?
            <div className={`editable-group form-group ${errors[propName] ? 'has-error' : ''}`}>
                <input type="text" className="form-control" value={bank[propName] || ''} onChange={(e) => this.handleChange(e, propName)}/>
                {errors[propName] ? <span className="help-block"><strong>{errors[propName]}</strong></span> : ''}
            </div> :
            <div className="readonly-group">{bank[propName]}</div>
    }

    render()
    {
        let {bank, index} = this.props;

        return (
            <div className="row-container save-bank-form">
                <div className="data-container text-center" style={{width: '6%'}}>
                    {bank.is_active ?
                        <span className="round-btn" onClick={() => this.props.banksStore.saveBank(bank.id ? bank.id : '', index)}>
                            <span className="fa fa-check"> </span>
                        </span> :
                        <span>
                            <span className="round-btn" onClick={() => this.props.banksStore.changeBankStatus(index)}>
                                <span className="fa fa-pencil"> </span>
                            </span>
                            <span className="round-btn" onClick={() => this.props.banksStore.deleteBank(bank.id, index)}>
                                <span className="fa fa-times"> </span>
                            </span>
                        </span>
                    }
                </div>
                <div className="data-container" style={{width: '15%'}}>
                    {this.getTextItem(bank, 'bank_name')}
                </div>
                <div className="data-container" style={{width: '8%'}}>
                    {this.getTextItem(bank, 'bank_phone')}
                </div>
                <div className="data-container" style={{width: '8%'}}>
                    {this.getTextItem(bank, 'contact_name')}
                </div>
                <div className="data-container" style={{width: '8%'}}>
                    {this.getTextItem(bank, 'email')}
                </div>
                <div className="data-container" style={{width: '8%'}}>
                    {this.getTextItem(bank, 'address')}
                </div>
                <div className="data-container" style={{width: '8%'}}>
                    {this.getTextItem(bank, 'url')}
                </div>
                <div className="data-container" style={{width: '10%'}}>
                    {this.getTextAreaItem(bank, 'bank_dispute_process')}
                </div>
                <div className="data-container" style={{width: '10%'}}>
                    {this.getTextAreaItem(bank, 'bank_hours')}
                </div>
                <div className="data-container" style={{width: '18%'}}>
                    {this.getTextAreaItem(bank, 'notes')}
                </div>
            </div>
        )
    }
}

Bank.propTypes = {
    bank: PropTypes.object,
};
Bank.wrappedComponent.propTypes = {
    banksStore: PropTypes.object.isRequired,
};
export default Bank;