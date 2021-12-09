let jwt = localStorage.jwt
let eventsSort = localStorage.eventsSort || 'event_id';
let overviewMT = localStorage.overviewMT || 'plan'; // table - таблица, plan - графический план


let app = 'app';
let templateApp = 'templates';




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

// id div с app
export function getApp(){
    return app;
}

// id div с app
export function getTemplateApp(){
    return templateApp;
}

export function getOverviewMT(){
    if(overviewMT == "undefined"){
        setOverviewMT("plan");
    }
    return overviewMT;
}

export function setOverviewMT(value){
    overviewMT = value;
    localStorage.overviewMT = value;
}