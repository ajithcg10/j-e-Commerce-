import React from "react";

export default function CurrencyFormatter({
    amount,
    currency = "USD",
    locale = "en-US",
}) {
    return new Intl.NumberFormat(locale, {
        style: "currency",
        currency,
        currencyDisplay: "symbol",
    }).format(amount);
}
