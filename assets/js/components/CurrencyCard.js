import React from 'react';

const Card = ({ selectedRate, currentRate }) => {

    const currencyCode = selectedRate.currency.code || currentRate.currency.code || '-';
    const currencyName = selectedRate.currency.name || currentRate.currency.name || '-';

    const renderRate = (rate) => {
        if (rate != null) {
            return parseFloat(rate).toFixed(6);
        } else {
            return "-";
        }
    };

    return (
        <div className="card col-xs-3 m-2">
            <div className="card-body p-1 pb-3">
                <table>
                    <tbody>
                        <tr>
                            <td rowSpan={2} className="pl-3">
                                <h2><span className="badge badge-info">{currencyCode}</span></h2>
                            </td>
                            <td colSpan={2} className="text-center">
                                {currencyName}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span className="badge badge-secondary ml-3 mr-3">{selectedRate.date}</span>
                            </td>
                            <td>
                                <span className="badge badge-secondary ml-3 mr-3">Today</span>
                            </td>
                        </tr>
                        <tr>
                            <td className="text-center">
                                <span className="badge badge-success mt-2">BUY</span>
                            </td>
                            <td className="text-center">
                                {renderRate(selectedRate.buyRate)}
                            </td>
                            <td className="text-center">
                                {renderRate(currentRate.buyRate)}
                            </td>
                        </tr>
                        <tr>
                            <td className="text-center">
                                <span className="badge badge-danger mt-2">SELL</span>
                            </td>
                            <td className="text-center">
                                {renderRate(selectedRate.sellRate)}
                            </td>
                            <td className="text-center">
                                {renderRate(currentRate.sellRate)}
                            </td>
                        </tr>
                        <tr>
                            <td className="text-center">
                                <span className="badge badge-dark mt-2">NBP</span>
                            </td>
                            <td className="text-center">
                                {renderRate(selectedRate.nbpRate)}
                            </td>
                            <td className="text-center">
                                {renderRate(currentRate.nbpRate)}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default Card;