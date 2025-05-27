import Navbar from "@/Components/App/Navbar";
import { usePage } from "@inertiajs/react";
import { useState } from "react";

export default function AuthenticatedLayout({ header, children }) {
    const { auth, errors } = usePage().props;
    const user = auth.user;
    console.log(errors, "as");

    return (
        <div className="min-h-screen bg-[#e7e9eb]">
            <Navbar />

            <main>{children}</main>
        </div>
    );
}
