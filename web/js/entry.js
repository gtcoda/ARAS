import Router from './router.js'


(async () => {
    try {
    
        Router.init();
    }
    catch (e) {
        console.error(e);
        alert('Ошибка ' + e.message);
    }


})();
