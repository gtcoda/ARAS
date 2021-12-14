import * as settings from './settings.js';

// Преобразуем файл в base64
function _arrayBufferToBase64(buffer) {
    var binary = '';
    var bytes = new Uint8Array(buffer);
    var len = bytes.byteLength;
    for (var i = 0; i < len; i++) {
        binary += String.fromCharCode(bytes[i])
    }
    return window.btoa(binary);
}



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


//########################################### Users
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
            }).then(function (response) {
                resolve(response.data);
            },
            function (xnr) {
                reject(xnr);
            })

        });

    },

//########################################### Events
    // Получить все события конкретной машины
    getEvents(machine_id) {

        return new Promise((resolve, reject) => {

            API.res('events');
            API.events('machine/'+machine_id).get().then(function (events) {
                if (events.status == 'success') {
                    resolve(events.data);
                }
                else {
                    reject(new Error("Не удалось получить данные."));
                }
            })

        });

    },

        // Получить все события конкретной машины обьедененные по repair_id
    getEventsUnionRepair(machine_id) {

        return new Promise((resolve, reject) => {

            API.res('events');
            API.events('machine/'+machine_id).get({'filter':'repair'}).then(function (response) {
                resolve(response.data);
            },
            function (xnr) {
                reject(xnr);
            })

        });

    },

    // Добавить событие
    setEvents(event) {

        return new Promise((resolve, reject) => {

            API.res('events');
            API.events.post(event).then(function (response) {
                resolve(response.data);
            },
            function (xnr) {
                reject(xnr);
            })

        });

    },

//########################################### Repair    
    // открыть ремонт
    openRepair(){
        return new Promise((resolve, reject) => {

            API.res('repairs');
            API.repairs.post({ jwt: settings.getJWT() }).then(function (response) {
                resolve(response.repair_id);
            },
            function (xnr) {
                reject(xnr);
            })

        });
    },

    // Получить Последний открытый repair_id
    curentRepair(machine_id){
        return new Promise((resolve, reject) => {

            API.res('repairs');
            API.repairs(machine_id).get({ jwt: settings.getJWT() }).then(function (response) {
                resolve(response.repair_id);
            },
            function (xnr) {
                reject(xnr);
            })

        });
    },

//########################################### Upload
    uploadImg(value){
        
        return new Promise((resolve, reject) => {

            var file = value.img;
            var fileReader = new FileReader();


            fileReader.onloadend = function () {
                var base64img = "data:" + file.type + ";base64," + _arrayBufferToBase64(fileReader.result);
    
                API.res('img');
    
    
                API.img.post({
                    img: base64img,
                    jwt: value.jwt,
                    machine_id: value.machine_id
                }).then(function (response) {
                    resolve(response.data);
                },
                    function (xnr) {
                        reject(xnr);
                    })
    
            };
            fileReader.readAsArrayBuffer(file);

        
        });

    },

 //########################################### Models 

    // Получить список моделей или модель
    getModels(id) {
        if (id) {
            return new Promise((resolve, reject) => {
                API.res('models');
                API.models(id).get({ jwt: settings.getJWT() }).then(function (res) {
                    if (res.status == 'success') {
                        resolve(res.data);
                    }
                    else {
                        reject(new Error(res.message));
                    }
                });
            });
        }
        else {
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
        }

    },

    // Получить название моделей станкой проиндексированых по model_id
    getModelsIndex() {

        return new Promise((resolve, reject) => {
            API.res('models');
            API.models.get({ jwt: settings.getJWT(), view:"{model_id,model_name}",format:"index" }).then(function (res) {
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

    updateModel(value) {
        return new Promise((resolve, reject) => {
            API.res('models');
            API.models(value.model_id).put({
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

//########################################### Gilds 

    // Получить список цехов
    getGilds(id) {

        if (id) {
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
        else {


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

    // Получить все станки конкретного цеха
    getGildsM(id) {
        return new Promise((resolve, reject) => {
            API.res('gilds');
            API.gilds(id + "/machines").get({ jwt: settings.getJWT() }).then(function (res) {
                if (res.status == 'success') {
                    resolve(res.data);
                }
                else {
                    reject(new Error(res.message));
                }
            });
        });
    },
    // Получить все станки конкретного цеха сгрупированые по модели
    getGildsMIndex(id) {
        return new Promise((resolve, reject) => {
            API.res('gilds');
            API.gilds(id + "/machines").get({ jwt: settings.getJWT(),view:"{model_id,machine_id,machine_number,gild_id,machine_desc}",format:"index" }).then(function (res) {
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
    setGild(value) {

        return new Promise((resolve, reject) => {
            API.res('gilds');
            API.gilds.post(value).then(
                    function (response) {
                        resolve(response.data);
                    },
                    function (xnr) {
                        reject(xnr);
                    });

        });



    },
//########################################### Machines 


    // Добавить станок
    setMachines(value) {
        return new Promise((resolve, reject) => {
            API.res('machines');
            API.machines.post({
                model_id: value.model_id,
                machine_number: value.machine_number,
                gild_id: value.gild_id,
                machine_desc: value.machine_desc,
                machine_posX: value.machine_posX,
                machine_posY: value.machine_posY
            }).then(function (models) {
                resolve(models.data);
            },
                function (xnr) {
                    reject(xnr);
                });
        });

    },



};
