import CartItems from "@/Components/App/CartItems";
import CurrencyFormatter from "@/Components/CurrencyFormatter";
import PrimaryButton from "@/Components/PrimaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import React from "react";
import { CreditCardIcon } from "@heroicons/react/24/solid";

export default function Index({
    csrf_token,
    cartItems,
    totalQuantity,
    totalPrice,
}) {
    console.log(cartItems, "ass");

    return (
        <AuthenticatedLayout>
            <Head title="You Cart" />
            <div className="conatiner mx-auto p-8 flex  flex-col lg:flex-row gap-4">
                <div className="card flex-1 bg-white dark:bg-gray-800 order-2 lg:order-1">
                    <div className="card-body">
                        <h2 className="text-lg font-bold">shopping cart</h2>
                        <div className="my-4">
                            {Object.keys(cartItems)?.length === 0 && (
                                <div className="py-2 text-gray-500 text-center">
                                    You dont have any items yet .
                                </div>
                            )}
                            {Object.values(cartItems)?.map((item) => {
                                return (
                                    <div key={item?.user.id}>
                                        <div
                                            className={`flex items-center justify-between pb-4 border-b border-gray-300 mb-4`}
                                        >
                                            <Link
                                                href="/"
                                                className="underline"
                                            >
                                                {item?.user?.name}
                                            </Link>
                                            <form
                                                action={route("cart.checkout")}
                                                method="post"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="_token"
                                                    value={csrf_token}
                                                />
                                                <PrimaryButton className="rounded-full">
                                                    <CreditCardIcon className="size-6 text-blue-500" />
                                                    Proceed to checkout
                                                </PrimaryButton>
                                            </form>
                                        </div>

                                        {item?.items?.map((data) => {
                                            return (
                                                <CartItems
                                                    item={data}
                                                    key={data?.id}
                                                />
                                            );
                                        })}
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
                <div className=" card bg-white dark:bg-gray-800 lg:min-w-[260px] order-1 lg:order-2">
                    <div className="card-body">
                        SubTotal ({totalQuantity} items) :&nbsp;
                        <CurrencyFormatter amount={totalPrice} />
                        <form action={route("cart.checkout")} method="post">
                            <input
                                type="hidden"
                                name="_token" // ✅ this is required by Laravel
                                value={csrf_token}
                            />
                            <PrimaryButton
                                type="submit"
                                className="rounded-full"
                            >
                                <CreditCardIcon className="size-6 text-blue-500" />
                                Proceed to checkout
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
