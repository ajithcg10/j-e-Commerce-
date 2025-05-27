import CurrencyFormatter from "@/Components/CurrencyFormatter";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { CheckCircleIcon } from "@heroicons/react/24/solid";
import { Head, Link } from "@inertiajs/react";
import React from "react";

export default function Success({ orders }) {
    console.log(orders[0].data, "orders in success page");
    const data = orders[0].data.orderItems;

    return (
        <AuthenticatedLayout>
            <Head title="Payment Success" />
            <div className="w-[480px] mx-auto  py-8 px-4">
                <div className="flex flex-col gap-2 justify-center items-center">
                    <div className="text-6xl text-emerald-600">
                        <CheckCircleIcon className="size-24" />
                    </div>
                    <div className="text-3xl   text-green-500">
                        Payment was Completed
                    </div>
                    <div className="my-6 text-lg text-center text-green-500">
                        Thanks for your purchase! Your payment was completed
                        successfully.
                    </div>
                </div>

                {data?.map((order, index) => (
                    <div
                        key={order.id || index}
                        className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-4"
                    >
                        <h3 className="text-2xl mb-3">Order Summary</h3>

                        <div className="flex justify-between mb-2 font-bold">
                            <div className="text-gray-400">Seller</div>
                            <div>
                                <Link href="#" className="hover:underline">
                                    {orders[0]?.data?.vendorUser?.name ||
                                        orders[0]?.data?.vendorUser?.email}
                                </Link>
                            </div>
                        </div>

                        <div className="flex justify-between mb-2">
                            <div className="text-gray-500">Order Number</div>
                            <div>{order?.id}</div>
                        </div>

                        <div className="flex justify-between mb-2">
                            <div className="text-gray-500">Items</div>
                            <div>{data?.length}</div>
                        </div>

                        <div className="flex justify-between mb-2">
                            <div className="text-gray-500">Total</div>
                            <div>
                                <CurrencyFormatter amount={order?.price} />
                            </div>
                        </div>

                        <div className="mt-4">
                            <div className="font-semibold mb-2">Items:</div>
                            <ul className="list-disc list-inside text-gray-700 dark:text-gray-300">
                                {order?.orderItems?.map((item) => (
                                    <li key={item.id}>
                                        Qty: {item.quantity} – Price:{" "}
                                        <CurrencyFormatter
                                            amount={item.price}
                                        />
                                    </li>
                                ))}
                            </ul>
                        </div>

                        <div className="flex justify-between mt-6">
                            <Link href="#" className="btn btn-primary">
                                View Order Details
                            </Link>
                            <Link href={route("dashboard")} className="btn">
                                Back to Home
                            </Link>
                        </div>
                    </div>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}
