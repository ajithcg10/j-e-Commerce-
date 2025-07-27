import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { XCircleIcon } from "@heroicons/react/24/solid";
import { Head, Link } from "@inertiajs/react";
import React from "react";

export default function Failure(message) {
    return (
        <AuthenticatedLayout>
            <Head title="Payment Failed" />
            <div className="w-[480px] mx-auto py-8 px-4">
                <div className="flex flex-col gap-2 justify-center items-center">
                    <div className="text-6xl text-red-600">
                        <XCircleIcon className="size-24" />
                    </div>
                    <div className="text-3xl text-red-500">Payment Failed</div>
                    <div className="my-6 text-lg text-center text-red-500">
                        {message}
                    </div>
                </div>

                <div className="flex justify-center gap-4 mt-6">
                    <Link
                        href={route("cart.index")}
                        className="btn btn-primary"
                    >
                        Back to Cart
                    </Link>
                    <Link href={route("dashboard")} className="btn">
                        Go to Dashboard
                    </Link>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
