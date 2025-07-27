import { useForm, usePage } from "@inertiajs/react";
import React, { useState } from "react";
import PrimaryButton from "../PrimaryButton";
import InputLabel from "../InputLabel";
import TextInput from "../TextInput";
import InputError from "../InputError";
import Modal from "../Modal";
import SecondaryButton from "../SecondaryButton";

export default function VendorDetails() {
    const [showBecomeVendorConfirmation, setShowBecomeVendorConfirmation] =
        useState(false);
    const [sucessMessage, setSuccessMessage] = useState("");
    const user = usePage().props.auth.user;
    const token = usePage().props.token;

    const { data, setData, errors, post, processing, recentlySuccessful } =
        useForm({
            store_name:
                user.vendor?.store_name ||
                user?.name.toLowerCase().replace(/\s+/g, "-"),
            store_address: user.vendor?.store_address || "",
        });

    const onChangeStoreName = (e) => {
        setData(
            "store_name",
            e.target.value.toLowerCase().replace(/\s+/g, "-")
        );
    };

    const becomeVendor = (e) => {
        e.preventDefault();
        post(route("vendor.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closemodal();
                setSuccessMessage("You can now manage your store");
            },
            onError: () => {
                setSuccessMessage("Something went wrong");
            },
        });
    };

    const updateVendor = (e) => {
        e.preventDefault();
        post(route("vendor.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closemodal();
                setSuccessMessage("You can now manage your store");
            },
            onError: () => {
                setSuccessMessage("Something went wrong");
            },
        });
    };

    const closemodal = () => {
        setShowBecomeVendorConfirmation(false);
    };

    // Extract status label if it's an object (fixing the error)
    const vendorStatus = user?.vendor?.status;
    const statusLabel =
        typeof user?.vendor?.status_label === "object"
            ? user.vendor.status_label[vendorStatus]
            : user?.vendor?.status_label;

    return (
        <section>
            {recentlySuccessful && (
                <div className="toast toast-top toast-end">
                    <div className="alert alert-success">
                        <span>{sucessMessage}</span>
                    </div>
                </div>
            )}
            <header>
                <h2 className="flex justify-between mb-8 text-lg font-medium text-gray-900 dark:text-gray-100">
                    Vendor Details
                    {vendorStatus === "pending" && (
                        <span className="badge badge-warning">
                            {statusLabel}
                        </span>
                    )}
                    {vendorStatus === "approved" && (
                        <span className="badge badge-success">
                            {statusLabel}
                        </span>
                    )}
                    {vendorStatus === "rejected" && (
                        <span className="badge badge-error">{statusLabel}</span>
                    )}
                </h2>
            </header>
            <div>
                {!user.vendor && (
                    <PrimaryButton
                        disabled={processing}
                        onClick={() => setShowBecomeVendorConfirmation(true)}
                    >
                        Become a Vendor
                    </PrimaryButton>
                )}
                {user.vendor && (
                    <>
                        <form onSubmit={updateVendor}>
                            <div className="mb-4">
                                <InputLabel for="name" value="Store Name" />
                                <TextInput
                                    id="name"
                                    className="mt-1 block w-full"
                                    value={data.store_name}
                                    onChange={onChangeStoreName}
                                    required
                                    isFocused
                                    autoComplete="name"
                                />
                                <InputError
                                    className="mt-2"
                                    message={errors.store_name}
                                />
                            </div>
                            <div className="mb-4">
                                <InputLabel
                                    for="store_address"
                                    value="Store Address"
                                />
                                <textarea
                                    className="textarea textarea-bordered w-full mt-1"
                                    value={data?.store_address}
                                    onChange={(e) =>
                                        setData("store_address", e.target.value)
                                    }
                                    placeholder="Enter your store address"
                                ></textarea>
                                <InputError
                                    className="mt-2"
                                    message={errors.store_address}
                                />
                            </div>
                            <div className="flex items-center gap-4">
                                <PrimaryButton disabled={processing}>
                                    Update
                                </PrimaryButton>
                            </div>
                        </form>
                        <form
                            action={route("stripe.connect")}
                            method="POST"
                            className="my-8"
                        >
                            <input type="hidden" name="_token" value={token} />
                            {user?.stripe_account_active && (
                                <div className="text-center text-gray-500 my-4 text-sm">
                                    You are successfully connected to Stripe.
                                </div>
                            )}
                            <button
                                className="btn btn-primary w-full"
                                disabled={user?.stripe_account_active}
                            >
                                Connect to Stripe
                            </button>
                        </form>
                    </>
                )}
            </div>
            <Modal show={showBecomeVendorConfirmation} onClose={closemodal}>
                <form onSubmit={becomeVendor} className="p-8">
                    <h2 className="text-lg font-medium text-gray-900 mb-4">
                        Are You Sure You Want to Become a Vendor?
                    </h2>
                    <div className="mt-6 flex justify-end">
                        <SecondaryButton onClick={closemodal}>
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton className="ms-3" disabled={processing}>
                            Confirm
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>
        </section>
    );
}
