export const handleError = (error) => {

    let message = error?.response?.message ?? error?.message ?? error
    let errors = error?.response?.data?.errors

    if (error.code === 422) {
        let errorsList = ``
        for (let prop in errors) {
            errors[prop]?.map(propElement => {
                errorsList = errorsList + propElement + '\n'
            })
        }
        message = errorsList
    }

    // errors?.map(e => errorsListOpen + '<li>' + e  + '</li>')
    return {message, errors}
}
