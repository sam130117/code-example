import React, {Component} from 'react';
import {observer, inject} from "mobx-react";
import PropTypes          from 'prop-types';
import Pagination         from "react-js-pagination";
import BanksList          from "./BanksList";


@inject('banksStore')
@observer
class Banks extends Component
{
    constructor(props, context)
    {
        super(props, context);
        this.handleSearch = this.handleSearch.bind(this);
        this.handlePageChange = this.handlePageChange.bind(this);
    }

    componentDidMount()
    {
        this.props.banksStore.getBanks();
    }

    handleSearch(e)
    {
        this.props.banksStore.pagination.page = null;
        this.props.banksStore.search = e.target.value || null;
        this.props.banksStore.getBanks();
    }

    handlePageChange(pageNumber)
    {
        this.props.banksStore.pagination.page = pageNumber;
        this.props.banksStore.getBanks();
    }

    render()
    {
        let {banks, pagination, isLoading} = this.props.banksStore;

        return (
            <div className="panel panel-default">
                <div className="panel-heading clearfix">
                    <h3>
                        <span className="col-sm-3">Banks</span>
                        <div className="col-sm-9">
                            <div className="form-inline pull-right">
                                <input type="text" placeholder="Search for..." style={{width: '300px'}} className="form-control" onChange={this.handleSearch}/>
                            </div>
                            <span className="round-btn pull-right m-5-10" onClick={() => this.props.banksStore.addBank()}>
                                <span className="fa fa-plus"> </span>
                            </span>
                        </div>
                    </h3>
                </div>
                <div className="panel-body">
                    <div className="table-container bold">
                        <div className="header-container header-dark">
                            <div className="data-container" style={{width: '6%'}}></div>
                            <div className="data-container" style={{width: '15%'}}>Bank Name</div>
                            <div className="data-container" style={{width: '8%'}}>Bank Phone</div>
                            <div className="data-container" style={{width: '8%'}}>Contact Name</div>
                            <div className="data-container" style={{width: '8%'}}>Email</div>
                            <div className="data-container" style={{width: '8%'}}>Address</div>
                            <div className="data-container" style={{width: '8%'}}>URL</div>
                            <div className="data-container" style={{width: '10%'}}>Bank Dispute Process</div>
                            <div className="data-container" style={{width: '10%'}}>Bank Hours</div>
                            <div className="data-container" style={{width: '18%'}}>Notes</div>
                        </div>
                        <div className="body-container" style={{minHeight: '600px'}}>
                            <BanksList banks={banks} isLoading={isLoading}/>
                        </div>
                    </div>
                    {pagination.total <= pagination.perPage ? "" :
                        <div className={`text-center`}>
                            <Pagination
                                activePage={pagination.page}
                                itemsCountPerPage={pagination.perPage}
                                totalItemsCount={pagination.total}
                                pageRangeDisplayed={pagination.pageRangeDisplayed}
                                onChange={this.handlePageChange}
                                itemClassPrev="hidden"
                                itemClassNext="hidden"
                                itemClass="page-item"
                                linkClass="page-link"
                            />
                        </div>}
                </div>
            </div>
        )
    }
}

Banks.wrappedComponent.propTypes = {
    banksStore: PropTypes.object.isRequired,
};

export default Banks;