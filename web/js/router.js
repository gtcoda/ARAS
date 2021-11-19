import Controller from './controller.js'
import Template from './template.js'
import * as settings from './settings.js';

function getRouteInfo() {
    const hash = location.hash ? location.hash.slice(1) : '';
    const [name, id] = hash.split('/');
    return { name, params: { id } };
}

function handleHash() {
    const { name, params } = getRouteInfo();

    // Загрузим шаблоны
    Template.getTemplate(name)


    if (name) {
        const routeName = name + 'Route';
        Controller[routeName](params);
    }


}


export default {
    init() {
        addEventListener('hashchange', handleHash);
        handleHash();

        Template.getTemplate('sing');
    }
}