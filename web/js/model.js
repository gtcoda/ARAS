import * as settings from './settings.js';





export default {

    // Авторизироваться
    login(login = "", password = "") {
        API.res('login');

        return new Promise((resolve, reject) => {
            API.login.post({
                user_login: login,
                user_password: password
            }).then(function (login) {
                resolve(login.jwt);
            },
                function (xnr) {
                    reject(xnr);
                })
        });
    },

    // Получить информацию о пользователе
    // токен из jwt
    getUserNameJWT() {
        API.res('login');

        return new Promise((resolve, reject) => {
            API.login.put({
                jwt: settings.getJWT()
            }).then(function (login) {
                resolve(login);
            },
                function (xnr) {
                    reject(xnr);
                })
        });



    },

    // Добавить пользователя
    setUser(login, password, name) {
        return new Promise((resolve, reject) => {

            API.res('users');
            API.users.post({
                user_login: login,
                user_password: password,
                user_name: name
            }).then(
                function (users) {
                    resolve(users.data);
                },
                function (xnr) {
                    reject(xnr);
                });

        });

    },

    // Получить всех пользователей
    getUsers() {
        return new Promise((resolve, reject) => {

            API.res('users');
            API.users().get({
                jwt: settings.getJWT()
            }).then(function (users) {
                if (users.status == 'success') {
                    resolve(users.data);
                }
                else {
                    reject(new Error("Не удалось получить данные."));
                }
            })

        });

    },


    // Получить все события
    getEvents() {

        return new Promise((resolve, reject) => {

            API.res('events');
            API.events.get().then(function (events) {
                if (events.status == 'success') {
                    resolve(events.data);
                }
                else {
                    reject(new Error("Не удалось получить данные."));
                }
            })

        });

    },

    // Проверить корректность токена
    checkJWT(value) {
        return new Promise((resolve, reject) => {
            API.res('login');
            API.login.put({ jwt: value }).then(function (res) {
                if (res.status == 'success') {
                    resolve(true);
                }
                else {
                    reject(new Error("Нет токена"));
                }
            });
        });
    },

    ////////////////////////////////////////////////////////////// Models /////////////////////////////////////////////////////    

    // Получить список моделей
    getModels() {
        return new Promise((resolve, reject) => {
            API.res('models');
            API.models.get({ jwt: settings.getJWT() }).then(function (res) {
                if (res.status == 'success') {
                    resolve(res.data);
                }
                else {
                    reject(new Error(res.message));
                }
            });
        });
    },

    // Добавить модель
    setModel(value) {



        return new Promise((resolve, reject) => {
            API.res('models');
            API.models.post({
                model_name: value.model_name,
                model_desc: value.model_desc
            }).then(
                function (models) {
                    resolve(models.data);
                },
                function (xnr) {
                    reject(xnr);
                });

        });



    },

////////////////////////////////////////////////////////////// Gilds /////////////////////////////////////////////////////    

    // Получить список цехов
    getGilds(id) {

        if(id){


            return new Promise((resolve, reject) => {
                API.res('gilds');
                API.gilds(id).get({ jwt: settings.getJWT() }).then(function (res) {
                    if (res.status == 'success') {
                        resolve(res.data);
                    }
                    else {
                        reject(new Error(res.message));
                    }
                });
            });


        }
        else{


            return new Promise((resolve, reject) => {
                API.res('gilds');
                API.gilds.get({ jwt: settings.getJWT() }).then(function (res) {
                    if (res.status == 'success') {
                        resolve(res.data);
                    }
                    else {
                        reject(new Error(res.message));
                    }
                });
            });
        }



    },






};




// return new Promise((resolve,reject)=>{      });