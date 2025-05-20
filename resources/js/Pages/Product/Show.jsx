import Coursel from "@/Components/Coursel";
import CurrencyFormatter from "@/Components/CurrencyFormatter";
import { arraysAreEqual } from "@/helpers";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router, useForm, usePage } from "@inertiajs/react";
import React, { useEffect, useMemo, useState } from "react";

export default function Show({ product, variationOptions }) {
    const form = useForm({
        option_ids: {},
        quantity: 1,
        price: null,
    });

    const { url } = usePage();
    const [selectedOptions, setSelectedOptions] = useState([]);
    const images = useMemo(() => {
        // Check if any selected option has an image
        const selectedImages = Object.values(selectedOptions)
            .filter((option) => option?.image?.length > 0)
            .map((option) => option.image)
            .flat();

        return selectedImages.length > 0 ? selectedImages : product.images;
    }, [product, selectedOptions]);

    const computedProduct = useMemo(() => {
        const selectedOptionsId = Object.values(selectedOptions)
            .map((op) => op.id)
            .sort();

        for (let variation of product?.variations) {
            const optionIds = variation?.variation_type_option_ids
                ?.map((i) => i?.id)
                .sort();
            if (arraysAreEqual(selectedOptionsId, optionIds)) {
                return {
                    price: variation?.price,
                    quantity:
                        variation?.quantity === null
                            ? Number.MAX_VALUE
                            : variation?.quantity,
                };
            }
        }
        return {
            price: product?.price,
            qunatity: product?.qunatity,
        };
    }, [product, selectedOptions]);

    useEffect(() => {
        for (let type of product.variationTypes) {
            const selectedOptionsId = variationOptions[type?.id];

            chooseOptions(
                type.id,
                type.options.find((op) => op.id == selectedOptionsId) ||
                    type.options[0],
                false
            );
        }
    }, []);

    const getOptionIdsMap = (newOptions) => {
        return Object.fromEntries(
            Object.entries(newOptions).map(([a, b]) => {
                return [a, b?.id];
            })
        );
    };

    const chooseOptions = (typeId, option, updateRouter) => {
        setSelectedOptions((prev) => {
            const newOptions = {
                ...prev,
                [typeId]: option,
            };
            if (updateRouter) {
                router.get(
                    url,
                    {
                        options: getOptionIdsMap(newOptions),
                    },
                    {
                        preserveScroll: true,
                        preserveState: true,
                    }
                );
            }
            return newOptions;
        });
    };
    const onQuantityChange = (e) => {
        form.setData("quantity", Number(e.target.value));
    };

    const addtoCart = () => {
        form.post(route("cart.store", product?.id), {
            preserveScroll: true,
            preserveState: true,
            onError: (err) => {
                console.log(err);
            },
        });
    };

    const renderProductVariationTypes = () => {
        return product.variationTypes.map((type) => {
            return (
                <div key={type.id}>
                    <b>{type.name}</b>

                    {type.type === "Image" && (
                        <div className="flex gap-2 mb-4">
                            {type.options.map((option) => (
                                <div
                                    onClick={() =>
                                        chooseOptions(type.id, option, true)
                                    }
                                    key={option?.id}
                                    className="cursor-pointer"
                                >
                                    {option.image && (
                                        <img
                                            src={option.image[0]?.thumb}
                                            alt="Images"
                                            className={
                                                "w-[50px] " +
                                                (selectedOptions[type.id]
                                                    ?.id === option?.id
                                                    ? "outline outline-4 outline-primary"
                                                    : "")
                                            }
                                        />
                                    )}
                                </div>
                            ))}
                        </div>
                    )}

                    {type.type === "Radio" && (
                        <div className="flex join w-full mb-4">
                            {type.options.map((option) => {
                                const isSelected =
                                    selectedOptions[type.id]?.id === option?.id;
                                return (
                                    <label
                                        key={option?.id}
                                        className={`join-item btn w-[100px] cursor-pointer ${
                                            isSelected
                                                ? "bg-white text-black border-blue-800"
                                                : "bg-blue-800 text-white"
                                        }`}
                                    >
                                        <input
                                            type="radio"
                                            value={option?.id}
                                            checked={isSelected}
                                            onChange={() =>
                                                chooseOptions(
                                                    type.id,
                                                    option,
                                                    true
                                                )
                                            }
                                            name={"variation_type" + type?.id}
                                            className="sr-only" // Hides the actual radio input
                                            aria-label={option?.name}
                                        />
                                        {option?.name}
                                    </label>
                                );
                            })}
                        </div>
                    )}
                </div>
            );
        });
    };

    useEffect(() => {
        const idMaps = Object.fromEntries(
            Object.entries(selectedOptions).map(([typeId, option]) => {
                return [typeId, option?.id];
            })
        );

        form.setData("option_ids", idMaps);
    }, [selectedOptions]);

    const renderAddToCartButton = () => {
        return (
            <div className="mb-8 flex gap-4">
                <select
                    value={form.data.quantity}
                    onChange={onQuantityChange}
                    className="select select-bordered w-full bg-white text-gray-800 border-gray-300 hover:border-blue-400 focus:border-blue-500 focus:outline-none"
                >
                    {Array.from({
                        length: Math.min(10, computedProduct?.quantity || 0),
                    }).map((_, i) => (
                        <option value={i + 1} key={i + 1}>
                            Quantity: {i + 1}
                        </option>
                    ))}
                </select>

                <button onClick={() => addtoCart()} className="btn btn-primary">
                    Add To Cart
                </button>
            </div>
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title={product?.title} />
            <div className="container bg-amber-800 mx-auto p-8">
                <div className="grid gap-8 grid-cols-1 lg:grid-cols-12">
                    <div className="col-span-7">
                        <Coursel images={images} />
                    </div>
                    <div className="col-span-5">
                        <h1 className="text-2xl mb-8 to-blue-900">
                            {product?.title}
                        </h1>
                        <div>
                            <div className="text-3xl font-semibold">
                                <CurrencyFormatter
                                    amount={computedProduct?.price}
                                />
                            </div>
                        </div>

                        {renderProductVariationTypes()}
                        {computedProduct?.quantity != undefined &&
                            computedProduct?.quantity < 10 && (
                                <div className="text-error my-4">
                                    <span>
                                        Only {computedProduct?.quantity} left
                                    </span>
                                </div>
                            )}
                        {renderAddToCartButton()}
                        <b className="text-xl"> About the item </b>
                        <div
                            className="wysiwyg-ouput"
                            dangerouslySetInnerHTML={{
                                __html: product?.description,
                            }}
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
