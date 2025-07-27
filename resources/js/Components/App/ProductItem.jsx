import { router } from "@inertiajs/react";
import { Link } from "@inertiajs/react";
import React from "react";
import CurrencyFormatter from "../CurrencyFormatter";

export default function ProductItem({ Product }) {
    const addToCart = () => {
        console.log("Adding to cart", Product?.id, Product);

        router.post(route("cart.store", Product?.id), {
            option_ids: Product?.option_ids ?? [], // send option_ids or fallback to empty array
            preserveScroll: true,
        });
    };

    return (
        <div className="card w-100 bg-gradient-to-br from-white via-gray-100 to-gray-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-700">
            <Link href={route("product.show", Product?.slug)}>
                <figure>
                    <img
                        src={Product?.images}
                        alt={Product?.title}
                        className="w-100 max-h-[300px] object-cover rounded"
                    />
                </figure>
            </Link>
            <div className="card-body">
                <h2 className="card-title">{Product?.title}</h2>
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
                    <button className="btn btn-primary" onClick={addToCart}>
                        Add to Cart
                    </button>
                    <CurrencyFormatter amount={Product.price} />
                </div>
            </div>
        </div>
    );
}
