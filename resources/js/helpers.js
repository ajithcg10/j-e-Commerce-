export const arraysAreEqual = (arr1, arr2) => {
    if (arr1.length !== arr2.length) return false;

    return arr1.every((value, index) => value === arr2[index]);
};

export const productRoute = (item) => {
    const option_ids = JSON.parse(item?.option_ids);

    const params = new URLSearchParams();
    Object.entries(option_ids).forEach(([typeId, optionId]) =>
        params.append(`options[${typeId}]`, optionId + "")
    );

    return route("product.show", item?.slug) + "?" + params?.toString();
};
