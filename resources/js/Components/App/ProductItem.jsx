import { Link } from "@inertiajs/react";
import React from "react";
import CurrencyFormatter from "../CurrencyFormatter";

export default function ProductItem({ Product }) {
    return (
        <div className="card bg-base-100 shadow-xl">
            <Link href={route("product.show", Product?.slug)}>
                <figure>
                    <img
                        src={Product?.images}
                        alt={Product?.title}
                        className="asepect-square object-cover"
                    />
                </figure>
            </Link>
            <div className="card-body">
                <h2 className="card-title"> {Product?.title}</h2>
                <p>
                    by{" "}
                    <Link href="/" className="hover:underline">
                        {Product.user.name}
                    </Link>
                    &nbsp; in{" "}
                    <Link href="/" className="hover:underline">
                        {Product.department.name}
                    </Link>
                </p>
                <div className="card-actions items-center justify-between mt-3">
                    <button className="btn btn-primary">Add to Cart</button>
                    <CurrencyFormatter amount={Product.price} />
                </div>
            </div>
        </div>
    );
}
