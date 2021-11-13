let jwt = localStorage.jwt


let eventsSort = localStorage.eventsSort || 'event_id';

export function setEventsSort(sort){
    eventsSort = sort;
    localStorage.eventsSort = sort;
}


export function getEventsSort(){
    return eventsSort;
}

// Сохранить полученый токен
export function setJWT(value){
    jwt = value;
    localStorage.jwt = jwt;
}

// Получить токен
export function getJWT(){
    return jwt;
}