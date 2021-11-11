import Model from './model.js';
import View from './view.js';
import Router from './router.js'

(async () => {
    try {

        const resp = await Model.login();
        const app = document.getElementById("app");
        app.innerHTML = View.render('user',resp.data);


        Router.init();
    }
    catch (e) {
        console.error(e);
        alert('Ошибка ' + e.message);
    }


})();