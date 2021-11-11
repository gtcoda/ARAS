export default {

    login(login = "", password = "") {
        return new Promise((resolve, reject) => {
            API.res('users');
            API.users('gtcoda').get().then(function (users) {
                if (users.status == 'success') {
                    resolve(users);
                }
                else {
                    reject(new Error("Не удалось получить данные."));
                }


            })
        });
    },

    // Получить всех пользователей
    getUsers() {
        return new Promise((resolve, reject) => {

            API.res('users');
            API.users().get().then(function (users) {
                if (users.status == 'success') {
                    resolve(users);
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
                    resolve(events);
                }
                else {
                    reject(new Error("Не удалось получить данные."));
                }


            })

        });

    },




};