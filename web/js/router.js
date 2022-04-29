import Controller from './controller.js'
import * as settings from './settings.js';

const resultsNode = document.getElementById(settings.getApp());

function getRouteInfo() {
    const hash = location.hash ? location.hash.slice(1) : '';
    const [name, id] = hash.split('/');
    return { name, params: { id } };
}

function handleHash() {
    resultsNode.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
            </div>
        `;


    const { name, params } = getRouteInfo();

    if (name) {
        const routeName = name + 'Route';
        Controller[routeName](params);
    }


}


export default {
    init() {
        const { name, params } = getRouteInfo();
        if(name == ''){
            let hash = document.location.hash;
            document.location.hash = hash + "#Overview";
        }
        addEventListener('hashchange', handleHash);
        handleHash();

    }
}