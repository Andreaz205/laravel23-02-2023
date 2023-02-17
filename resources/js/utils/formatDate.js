export const formatDate = (date) => {
    let newDate = date.split('T')[0]
    let newTime = date.split('T')[1].split('.')[0]
    return newDate + ' ' + newTime
}
