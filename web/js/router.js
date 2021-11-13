import Controller from './controller.js'
import * as settings from './settings.js';

function getRouteInfo() {
    const hash = location.hash ? location.hash.slice(1) : '';
    const [name, id] = hash.split('/');
    return { name, params: { id } };
}

function handleHash() {
    const { name, params } = getRouteInfo();
    if (name) {
        const routeName = name + 'Route';
        console.log(routeName);
        Controller[routeName](params);
    }
}


export default {
    init() {
        addEventListener('hashchange', handleHash);
        handleHash();
    }
}