import { productRoute } from "@/helpers";
import { Link, router, useForm } from "@inertiajs/react";
import React, { useState } from "react";
import TextInput from "../TextInput";
import CurrencyFormatter from "../CurrencyFormatter";

export default function CartItems({ item }) {
    const data = item;
    const deleForm = useForm({
        option_ids: data?.option_ids,
    });
    console.log(data, "dad");

    const [error, setError] = useState("");

    const onDeletClick = () => {
        deleForm.delete(route("cart.destroy", data?.product_id), {
            preserveScroll: true,
        });
    };
    console.log(data, "Sas");

    const handleQuantityChange = (ev) => {
        setError("");
        if (data?.option_ids) {
            console.log(data?.option_ids);
        }
        router.put(
            route("cart.update", data?.product_id),
            {
                quantity: ev.target.value,
                option_ids: data?.option_ids,
            },
            {
                preserveScroll: true,
                onError: (errors) => {
                    setError(Object.values(errors)[0]);
                },
            }
        );
    };

    return (
        <div className="flex gap-6 p-3">
            <Link
                href={productRoute(data)}
                className="w-32 min-w-32 min-h-32 flex justify-center self-start"
            >
                <img
                    src={data?.image}
                    alt={data?.title}
                    className="max-w-full max-h-full"
                />
            </Link>
            <div className="flex-1 flex flex-col">
                <div className="flex-1">
                    <h3 className="mb-3 text-sm font-semibold">
                        <Link href={productRoute(data)}>{data?.title}</Link>
                    </h3>
                    <div className="text-xs">
                        {data?.option?.map((option) => (
                            <div key={option?.id}>
                                <strong className="font-bold mr-1">
                                    {option?.type?.name}:
                                </strong>
                                {option.name}
                            </div>
                        ))}
                    </div>
                </div>
                <div className="flex justify-between items-center mt-4">
                    <div className="flex gap-2 items-center">
                        <div className="text-sm">Quantity:</div>
                        <div
                            className={
                                error
                                    ? "tooltip tooltip-open tooltip-error"
                                    : ""
                            }
                            data-tip={error}
                        >
                            <TextInput
                                type="number"
                                defaultValue={data?.quantity}
                                onBlur={handleQuantityChange}
                                className="input-sm w-16"
                            />
                        </div>
                        <button
                            onClick={onDeletClick}
                            className="btn btn-sm btn-ghost"
                        >
                            Delete
                        </button>
                        <button className="btn btn-sm btn-ghost">
                            Save For Later
                        </button>
                        <div className="font-bold text-lg">
                            <CurrencyFormatter
                                amount={data.quantity * data.price}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
