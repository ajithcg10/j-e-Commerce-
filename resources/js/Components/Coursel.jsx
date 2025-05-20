import React, { useEffect, useState } from "react";

export default function Coursel({ images }) {
    const [MainImg, setMainImag] = useState([]);

    useEffect(() => {
        if (images) {
            setMainImag([]);
        }
    }, [images]);

    return (
        <div>
            <div className="flex items-start gap-8">
                <div className="flex flex-col items-center gap-2 py-2">
                    {images?.map((image, i) => {
                        return (
                            <a
                                href={"#item" + i}
                                className="border-2 hover:border-blue-500"
                                key={image?.id}
                                onClick={() => setMainImag(image)}
                            >
                                <img
                                    src={image?.thumb}
                                    alt="images"
                                    className="w-[50px]"
                                />
                            </a>
                        );
                    })}
                </div>
                <div>
                    {images && images.length > 0 && (
                        <div className="carousel-item w-full">
                            <img
                                src={
                                    MainImg.large
                                        ? MainImg.large
                                        : images[0]?.large
                                }
                                alt="image"
                                className="w-full"
                            />
                        </div>
                    )}

                    {/* {images?.map((image, i) => {
                        console.log();

                        return (
                         
                        );
                    })} */}
                </div>
            </div>
        </div>
    );
}
