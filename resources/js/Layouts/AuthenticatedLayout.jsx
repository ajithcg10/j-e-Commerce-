import Navbar from "@/Components/App/Navbar";
import { usePage } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";

export default function AuthenticatedLayout({ header, children }) {
    const { auth, errors, success } = usePage().props;
    const user = auth.user;
    console.log(success, "as");
    const [successMessage, setSuccessMessage] = useState([]);
    const timeoutRefs = useRef({});

    useEffect(() => {
        if (success.message) {
            const newMessage = {
                ...success,
                id: success.time,
            };
            setSuccessMessage((prevMessages) => [newMessage, ...prevMessages]);
            const timeoutId = setTimeout(() => {
                setSuccessMessage((prevMessages) =>
                    prevMessages.filter((msg) => msg.id !== newMessage.id)
                );
                delete timeoutRefs.current[newMessage.id];
            }, 5000);
        }
    }, [success]);

    return (
        <div className="min-h-screen bg-[#e7e9eb]">
            <Navbar />
            {successMessage.length > 0 && (
                <div className="toast toast-top toast-end z-[1000] mt-16">
                    {successMessage.map((message) => (
                        <div
                            key={message.id}
                            className="alert alert-success shadow-lg"
                        >
                            <div>
                                <span>{message.message}</span>
                                <button
                                    className="btn btn-sm btn-circle btn-ghost"
                                    onClick={() => {
                                        setSuccessMessage((prevMessages) =>
                                            prevMessages.filter(
                                                (msg) => msg.id !== message.id
                                            )
                                        );
                                        clearTimeout(
                                            timeoutRefs.current[message.id]
                                        );
                                    }}
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
            <main>{children}</main>
        </div>
    );
}
