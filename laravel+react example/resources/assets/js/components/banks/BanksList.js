import React, {Component}     from 'react';
import PropTypes              from 'prop-types';
import {observer}             from "mobx-react";
import {Bank, LoadingSpinner} from "../index";


@observer
class BanksList extends Component
{
    constructor(props, context)
    {
        super(props, context);
    }

    render()
    {
        let {banks, isLoading} = this.props;
        if (!banks.length && !isLoading)
            return <div className="row-container"><div className="data-container"><h4 className="text-center">No results.</h4></div></div>;

        return (
            <LoadingSpinner isLoading={isLoading}>
                {banks.map((bank, index) => (
                    <Bank key={bank.id}
                          bank={bank}
                          index={index}
                    />))}
            </LoadingSpinner>
        )
    }
}

BanksList.propTypes = {
    banks: PropTypes.array,
};

export default BanksList;