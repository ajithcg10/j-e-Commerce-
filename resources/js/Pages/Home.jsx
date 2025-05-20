import HeroPage from "@/Components/App/HeroPage";
import ProductItem from "@/Components/App/ProductItem";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

export default function Home(products) {
    const data = products?.products?.data;

    return (
        <AuthenticatedLayout>
            <Head title="Welcome" />
            <HeroPage />
            <div className="grid grid-cols-3 gap-8 md:grid-cols-2 lg:grid-cols-3 p-8">
                {data.map((item) => {
                    return <ProductItem key={item.id} Product={item} />;
                })}
            </div>
        </AuthenticatedLayout>
    );
}
