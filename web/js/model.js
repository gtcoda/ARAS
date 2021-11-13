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


};




// return new Promise((resolve,reject)=>{      });