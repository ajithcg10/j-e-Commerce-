import { Link, usePage } from "@inertiajs/react";
import React from "react";
import CurrencyFormatter from "../CurrencyFormatter";
import { productRoute } from "@/helpers";

export default function Navbar() {
    const { auth, totalPrice, totalQuantity, CartItems } = usePage().props;
    const { user } = auth;

    console.log(CartItems);

    return (
        <div className="navbar bg-[#f3f4f6] shadow-sm">
            <div className="flex-1">
                <Link className="text-[#000] text-xl" href="/">
                    JStore
                </Link>
            </div>
            <div className="flex gap-4">
                <div className="dropdown dropdown-end">
                    <div tabIndex={0} role="button" className="btn  btn-circle">
                        <div className="indicator">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                className="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                {" "}
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                />{" "}
                            </svg>
                            <span className="badge bg-blue-700 badge-sm indicator-item">
                                {totalQuantity}
                            </span>
                        </div>
                    </div>
                    <div
                        tabIndex={0}
                        className="card card-compact  dropdown-content bg-base-100 z-1 mt-3 w-[500px] shadow"
                    >
                        <div className="card-body">
                            <span className="text-lg font-bold">
                                {totalQuantity} Items
                            </span>
                            <div className={"my-4 max-h[300px] overflow-auto"}>
                                {CartItems.length === 0 && (
                                    <div className="py-2 text-yellow-500 text-center">
                                        {" "}
                                        You dont have any items yet.{" "}
                                    </div>
                                )}
                                {CartItems.map((item) => {
                                    return (
                                        <div
                                            key={item?.id}
                                            className={"flex gap-4  p-3 "}
                                        >
                                            <Link
                                                href={productRoute(item)}
                                                className="w-16 h-16 justify-center items-center"
                                            >
                                                <img
                                                    src={item?.image}
                                                    alt={item?.title}
                                                    className={`max-w-full  max-h-full`}
                                                />
                                            </Link>
                                            <div className="flex-1">
                                                <h3 className="mb-3 font-semibold ">
                                                    <Link
                                                        href={productRoute(
                                                            item
                                                        )}
                                                    >
                                                        {" "}
                                                        {item?.title}
                                                    </Link>
                                                </h3>
                                                <div
                                                    className={`flex justify-between text-sm`}
                                                >
                                                    <div>
                                                        Quantity :{" "}
                                                        {item?.quantity}
                                                    </div>
                                                    <CurrencyFormatter
                                                        amount={
                                                            item?.quantity *
                                                            item?.price
                                                        }
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                            <span className="text-info text-lg">
                                Subtotal:
                                <CurrencyFormatter amount={totalPrice} />
                            </span>
                            <div className="card-actions">
                                <Link
                                    href={route("cart.index")}
                                    className="btn btn-primary btn-block"
                                >
                                    View cart
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                {user && (
                    <div className="dropdown dropdown-end">
                        <div
                            tabIndex={0}
                            role="button"
                            className="btn btn-ghost btn-circle avatar"
                        >
                            <div className="w-10 rounded-full">
                                <img
                                    alt="Tailwind CSS Navbar component"
                                    src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp"
                                />
                            </div>
                        </div>
                        <ul
                            tabIndex={0}
                            className="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow"
                        >
                            <li>
                                <Link
                                    href={route("profile.edit")}
                                    className="justify-between"
                                >
                                    Profile
                                    <span className="badge">New</span>
                                </Link>
                            </li>
                            <li>
                                <a>Settings</a>
                            </li>
                            <li>
                                <Link
                                    href={route("logout")}
                                    method={"post"}
                                    as="button"
                                >
                                    Logout
                                </Link>
                            </li>
                        </ul>
                    </div>
                )}
                {!user && (
                    <>
                        <Link href={route("login")} className="btn">
                            Login
                        </Link>
                        <Link
                            href={route("register")}
                            className="btn btn-primary"
                        >
                            Register
                        </Link>
                    </>
                )}
            </div>
        </div>
    );
}
