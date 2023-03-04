export const handleError = (error) => {
    let message = error?.response?.message ?? error?.message ?? error
    let errors = error?.response?.data?.errors

    let errorsList = ``
    for (let prop in errors) {
        errors[prop]?.map(propElement => {
            errorsList = errorsList + propElement + '\n'
        })
    }
    // errors?.map(e => errorsListOpen + '<li>' + e  + '</li>')
    return {message, errors, errorsList}
}
