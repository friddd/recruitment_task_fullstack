import React, {Component} from 'react';
import axios from "axios";
import CurrencyCard from "./CurrencyCard";

class ExchangeRates extends Component {

    constructor() {
        super();
        const urlParameters = new URLSearchParams(window.location.search);
        const selectedDate = urlParameters.get('date');
        this.state = {
            loading: true,
            exchangeRates: [],
            date: selectedDate || new Date().toISOString().slice(0, 10),
            errorMessage: null
        };
    }

    componentDidMount() {
        this.getExchangeRates();
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.state.date !== prevState.date) {
            this.getExchangeRates();
        }
    }

    getExchangeRates() {
        axios.get('/api/exchange-rates/' + this.state.date).then(response => {
            this.setState({
                loading: false,
                exchangeRates: response.data
            });
        })
        .catch(error => {
            this.setState({
                loading: false,
                errorMessage: error.response?.data?.error
            });
        });
    }

    handleDateChange = (event) => {
        const newDate = event.target.value;
        this.setState({ date: newDate }, () => {
            window.location.href = `/exchange-rates?date=${newDate}`;
        });
    }

    render() {
        const { loading, exchangeRates, date, errorMessage} = this.state;
        return(
            <div>
                <section className="row-section">
                    <div className="container">
                        <div className="row mt-5">
                            <div className="col-sm-12">
                                <h2 className="text-center">Exchange Rates</h2>

                                <div className="text-center mt-4">
                                    <label>
                                        <input type="date" value={date} onChange={this.handleDateChange}/>
                                    </label>
                                </div>

                                {loading ? (
                                    <div className={'text-center'}>
                                        <span className="fa fa-spin fa-spinner fa-4x"></span>
                                    </div>
                                ) : (
                                    errorMessage ? (
                                        <div className="text-center">
                                            <h3><span className="badge badge-danger mt-3">{errorMessage}</span></h3>
                                        </div>
                                    ) : (
                                        <div className="row justify-content-center align-items-center">
                                            {exchangeRates.map((rates, index) => (
                                                <CurrencyCard
                                                    key={index}
                                                    selectedRate={rates.selectedRate}
                                                    currentRate={rates.currentRate}
                                                />
                                            ))}
                                        </div>
                                    )
                                )}

                            </div>
                        </div>
                    </div>
                </section>
            </div>
        )
    }
}

export default ExchangeRates;
